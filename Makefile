# development
init: docker-down-clean docker-pull docker-build docker-up api-init
up: docker-up
down: docker-down
restart: down up
ps: docker-ps
api-init: api-composer-install
lint: api-lint

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

# build
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

# pull in private registry
push: push-gateway push-frontend push-api

push-gateway:
	docker push ${REGISTRY}/autosnapshots-gateway:${IMAGE_TAG}

push-frontend:
	docker push ${REGISTRY}/autosnapshots-frontend:${IMAGE_TAG}

push-api:
	docker push ${REGISTRY}/autosnapshots-api:${IMAGE_TAG}
	docker push ${REGISTRY}/autosnapshots-api-php-fpm:${IMAGE_TAG}

# deploy
deploy:
	ssh {HOST} -p ${PORT} 'rm -rf site_${BUILD_NUMBER}'
	ssh {HOST} -p ${PORT} 'mkdir site_${BUILD_NUMBER}'
	scp ${PORT} docker-compose-production.yml ${HOST}:site_${BUILD_NUMBER}/docker-compose-production.yml
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "COMPOSE_PROJECT_NAME=autosnapshots" >> .env'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "REGISTRY=${REGISTRY}" >> .env'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "IMAGE_TAG=${IMAGE_TAG}" >> .env'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker-compose -f docker-compose-production.yml pull'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker-compose -f docker-compose-production.yml up --build --remove-orphans -d'
	ssh ${HOST} -p ${PORT} 'rm -f site'
	ssh ${HOST} -p ${PORT} 'ln -sr site_${BUILD_NUMBER} site'

# rollback
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker-compose -f docker-compose-production.yml pull'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker-compose -f docker-compose-production.yml up --build --remove-orphans -d'
	ssh ${HOST} -p ${PORT} 'rm -f site'
	ssh ${HOST} -p ${PORT} 'ln -sr site_${BUILD_NUMBER} site'
