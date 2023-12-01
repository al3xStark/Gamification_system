FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
        curl \
        wget \
        mc \
        git \
        mariadb-client \
        supervisor \
        sudo

RUN apt-get install -y \
        iproute2 \
        telnet \
    && docker-php-ext-install -j$(nproc) mysqli pdo_mysql \
    && apt-get install -y libssh2-1-dev libxml2-dev libgmp-dev

RUN docker-php-ext-install soap \
    && docker-php-ext-install gmp

RUN apt-get install -y librabbitmq-dev \
    && pecl install amqp \
    && bash -c "echo extension=amqp.so > /usr/local/etc/php/conf.d/amqp.ini"

RUN apt-get install -y gcc make autoconf libc-dev pkg-config \
    && apt-get -y install libssh2-1-dev \
    && pecl install ssh2-alpha \
    && bash -c "echo extension=ssh2.so > /usr/local/etc/php/conf.d/ssh2.ini"

RUN pecl install -o -f redis \
    &&  rm -rf /tmp/pear \
    &&  docker-php-ext-enable redis

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN apt-get clean

RUN useradd -ms /bin/bash docker

WORKDIR /

CMD ["php-fpm"]