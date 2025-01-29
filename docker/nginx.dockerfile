FROM nginx:stable-alpine

WORKDIR /var/www/html

COPY ./nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf
