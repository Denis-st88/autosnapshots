# docker
init: docker-down-clean docker-pull docker-build docker-up api-clear api-init
up: docker-up
down: docker-down
restart: down up
ps: docker-ps

# style code
lint: api-lint
analyze: api-analyze
check: lint analyze validate-schema test

# tests
test: api-test
test-coverage: api-test-coverage
test-unit: api-test-unit
test-unit-coverage: api-test-unit-coverage
test-functional: api-test-functional
test-functional-coverage: api-test-functional-coverage

# db
validate-schema: api-validate-schema


docker-ps:
	docker-compose ps

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans # остановить все сервисы с префиксом autosnapshots

docker-down-clean:
	docker-compose down -v --remove-orphans

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build

api-composer-install:
	docker-compose run --rm api-php-cli composer install

api-lint:
	docker-compose run --rm api-php-cli composer lint
	docker-compose run --rm api-php-cli composer cs-check

api-analyze:
	docker-compose run --rm api-php-cli composer psalm

api-init: api-permissions api-composer-install api-wait-db api-migrations

api-clear:
	docker run --rm -v ${PWD}/api:/app -w /app alpine sh -c 'rm -rf var/*'

api-permissions:
	docker run --rm -v ${PWD}/api:/app -w /app alpine chmod 777 var

###################### DB
api-wait-db:
	docker-compose run --rm api-php-cli wait-for-it api-postgres:5432 -t 30

api-migrations:
	docker-compose run --rm api-php-cli composer app migrations:migrate --no-interaction

api-validate-schema:
	docker-compose run --rm api-php-cli composer app orm:validate-schema


###################### TESTS
api-test:
	docker-compose run --rm api-php-cli composer test

api-test-coverage:
	docker-compose run --rm api-php-cli composer test-coverage


api-test-unit:
	docker-compose run --rm api-php-cli composer test -- --testsuite=unit

api-test-unit-coverage:
	docker-compose run --rm api-php-cli composer test-coverage -- --testsuite=unit

api-test-functional:
	docker-compose run --rm api-php-cli composer test -- --testsuite=functional

api-test-functional-coverage:
	docker-compose run --rm api-php-cli composer test-coverage -- --testsuite=functional


###################### BUILD
build: build-gateway build-frontend build-api

build-gateway:
	docker --log-level=debug build --pull --file=gateway/docker/production/nginx/Dockerfile --tag=${REGISTRY}/autosnapshots-gateway:${IMAGE_TAG} gateway/docker

build-frontend:
	docker --log-level=debug build --pull --file=frontend/docker/production/nginx/Dockerfile --tag=${REGISTRY}/autosnapshots-frontend:${IMAGE_TAG} frontend

build-api:
	docker --log-level=debug build --pull --file=api/docker/production/nginx/Dockerfile --tag=${REGISTRY}/autosnapshots-api:${IMAGE_TAG} api
	docker --log-level=debug build --pull --file=api/docker/production/php-fpm/Dockerfile --tag=${REGISTRY}/autosnapshots-api-php-fpm:${IMAGE_TAG} api

try-build:
	REGISTRY=localhost IMAGE_TAG=0 make build


###################### PRIVATE REGISTRY
push: push-gateway push-frontend push-api

push-gateway:
	docker push ${REGISTRY}/autosnapshots-gateway:${IMAGE_TAG}

push-frontend:
	docker push ${REGISTRY}/autosnapshots-frontend:${IMAGE_TAG}

push-api:
	docker push ${REGISTRY}/autosnapshots-api:${IMAGE_TAG}
	docker push ${REGISTRY}/autosnapshots-api-php-fpm:${IMAGE_TAG}


###################### DEPLOY
deploy:
	ssh {HOST} -p ${PORT} 'rm -rf site_${BUILD_NUMBER}'
	ssh {HOST} -p ${PORT} 'mkdir site_${BUILD_NUMBER}'
	scp ${PORT} docker-compose-production.yml ${HOST}:site_${BUILD_NUMBER}/docker-compose.yml
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "COMPOSE_PROJECT_NAME=autosnapshots" >> .env'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "REGISTRY=${REGISTRY}" >> .env'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "IMAGE_TAG=${IMAGE_TAG}" >> .env'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker-compose pull'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker-compose up --build -d api-postgres'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker-compose run --rm api-php-cli wait-for-it api-postgres:5432 -t 60'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker-compose run --rm php bin/app.php migrations:migrate --no-interaction'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker-compose up --build --remove-orphans -d'
	ssh ${HOST} -p ${PORT} 'rm -f site'
	ssh ${HOST} -p ${PORT} 'ln -sr site_${BUILD_NUMBER} site'


###################### ROLLBACK
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker-compose -f docker-compose pull'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker-compose -f docker-compose up --build --remove-orphans -d'
	ssh ${HOST} -p ${PORT} 'rm -f site'
	ssh ${HOST} -p ${PORT} 'ln -sr site_${BUILD_NUMBER} site'
