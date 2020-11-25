FROM php:7.4-fpm-alpine
COPY wait-for-it.sh /usr/bin/wait-for-it
COPY php.ini $PHP_INI_DIR/conf.d/php.ini
RUN chmod +x /usr/bin/wait-for-it
RUN apk --update --no-cache add git bash autoconf automake make g++ libtool pkgconfig rabbitmq-c rabbitmq-c-dev gcc supervisor \
      && pecl install amqp \
      && docker-php-ext-enable amqp \
      && docker-php-ext-install pdo_mysql
COPY --from=composer /usr/bin/composer /usr/bin/composer
WORKDIR /var/www
ARG supervisor_conf
COPY ${supervisor_conf} /supervisord.conf
ARG database_service
ENV DB_SERVICE ${database_service}
ARG database_port
ENV DB_PORT ${database_port}
CMD composer install ; \
    wait-for-it ${DB_SERVICE}:${DB_PORT} -- bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration ; \
    wait-for-it rabbitmq:5672 ; \
    supervisord -c /supervisord.conf
EXPOSE 9000