FROM nginx:1.21.1-alpine

RUN apk add --no-cache curl

COPY ./common/nginx/snippets /etc/nginx/snippets
COPY ./development/nginx/conf.d /etc/nginx/conf.d

WORKDIR /app
