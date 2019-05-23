# vim: ft=dockerfile

FROM bitnami/php-fpm

RUN apt-get update &&   \
    apt-get install -y  \
        php-gd          \
        php-mbstring    \
        php-mysql       \
        php-zip

