FROM php:8.2.12-cli-alpine3.17@sha256:8e27ee972f27c463724bc3667fba27e1d060852864115d01749d48da5dc6d182
RUN addgroup -S nonroot && adduser -S nonroot -G nonroot

RUN apk update && \
    apk upgrade --no-cache && \
    apk add git \
        libcurl \
        curl-dev \
        ${PHPIZE_DEPS}

COPY /docker/custom.ini /usr/local/etc/php/conf.d/custom.ini

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN git clone --branch v5.1.0 https://github.com/swoole/swoole-src.git && \
cd swoole-src && \
phpize && \
./configure --enable-openssl --enable-swoole-curl --enable-mysqlnd && \
make && make install

RUN docker-php-ext-configure pcntl --enable-pcntl \
  && docker-php-ext-install pcntl mysqli pdo pdo_mysql

RUN pecl install redis && docker-php-ext-enable redis swoole

WORKDIR /var/www/html

COPY . /var/www/html/

RUN composer install --profile

USER nonroot

EXPOSE 9501