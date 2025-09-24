ARG PHP_VERSION=8.3

FROM node:20-alpine AS node

FROM ghcr.io/sylius/sylius-php:${PHP_VERSION}-xdebug-alpine

RUN apk add --no-cache \
    bash \
    make \
    chromium \
    chromium-chromedriver

COPY --from=node /usr/lib /usr/lib
COPY --from=node /usr/local/share /usr/local/share
COPY --from=node /usr/local/lib /usr/local/lib
COPY --from=node /usr/local/include /usr/local/include
COPY --from=node /usr/local/bin /usr/local/bin
COPY --from=node /opt /opt

CMD [ "php-fpm" ]

ENTRYPOINT [ "docker-php-entrypoint" ]
