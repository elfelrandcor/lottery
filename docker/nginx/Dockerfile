FROM nginx:stable

RUN adduser -D -H -u 1000 -s /bin/bash www-data

COPY ./vhost.conf /etc/nginx/conf.d/default.conf
COPY ./nginx.conf /etc/nginx/

ARG PHP_UPSTREAM_CONTAINER=php-fpm
ARG PHP_UPSTREAM_PORT=9000

RUN echo "upstream php-fpm { server ${PHP_UPSTREAM_CONTAINER}:${PHP_UPSTREAM_PORT}; }" > /etc/nginx/conf.d/upstream.conf

WORKDIR /var/www

CMD ["nginx"]

EXPOSE 80