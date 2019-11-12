# vim: ft=dockerfile

FROM bitnami/php-fpm

RUN apt-get update &&   \
    apt-get install -y  \
        libmemcached-dev\
        php-gd          \
        php-mbstring    \
        php-mysql       \
        php-memcached   \
        php-zip

COPY cache-control.ini /opt/bitnami/php/etc/conf.d/

