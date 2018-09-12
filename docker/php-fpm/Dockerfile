FROM php:7.0-fpm

RUN apt-get update && apt-get install -y \
    libmcrypt-dev mysql-client zlib1g-dev

RUN docker-php-ext-install pdo_mysql mbstring zip
RUN docker-php-ext-enable pdo_mysql mbstring zip

ARG INSTALL_XDEBUG=false

RUN if [ ${INSTALL_XDEBUG} = true ]; then \
    pecl install xdebug && \
    docker-php-ext-enable xdebug \
;fi

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
	    && composer --version

COPY ./php.ini /usr/local/etc/php/conf.d
COPY ./xdebug.ini /usr/local/etc/php/conf.d
COPY ./php-fpm-pool.conf /usr/local/etc/php-fpm.d

USER root

# Clean up
RUN apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* && \
    rm /var/log/lastlog /var/log/faillog

RUN usermod -u 1000 www-data

WORKDIR /var/www

CMD ["php-fpm"]

EXPOSE 9000
