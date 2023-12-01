FROM php:7.4-fpm

RUN apt-get update && apt-get install -y \
        mc \
        git \
        nodejs \
        npm \
        sudo \
        iproute2 \
        telnet

RUN apt-get -y install gcc make autoconf libc-dev pkg-config \
    && apt-get -y install libssh2-1-dev \
    && pecl install ssh2-alpha \
    && bash -c "echo extension=ssh2.so > /usr/local/etc/php/conf.d/ssh2.ini"

RUN apt-get clean

RUN useradd -ms /bin/bash docker

WORKDIR /

CMD ["php-fpm"]
