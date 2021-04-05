FROM php:7.4.16-fpm-alpine3.13 AS base

RUN apk add --no-cache --update --virtual build-deps ${PHPIZE_DEPS} \
 && docker-php-ext-install -j$(nproc) bcmath \
 && pecl install redis-5.3.4 \
 && docker-php-ext-enable redis \
 && apk del --purge build-deps

ADD docker/scripts/ /usr/local/bin/

RUN cd /usr/local/bin/ && ./install-composer.sh

WORKDIR /var/www/app

VOLUME /tmp

ENTRYPOINT ["/usr/local/bin/local-entrypoint.sh"]
CMD ["php-fpm"]

FROM base AS cron

ADD docker/cron/conf/crontab /root/crontab
ADD docker/cron/scripts/start-cron.sh /usr/local/bin/

RUN apk add --no-cache dcron && /usr/bin/crontab /root/crontab \
 && mkfifo -m 0666 /var/log/cron.log

CMD ["/usr/local/bin/start-cron.sh"]
