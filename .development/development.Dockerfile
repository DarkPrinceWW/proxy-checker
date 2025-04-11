FROM php:8.4-fpm-alpine

RUN apk add --no-cache  \
        bash \
        su-exec \
        gettext-dev \
        icu-dev \
        libjpeg-turbo-dev \
        libmcrypt-dev \
        libpng-dev \
        libzip-dev \
        oniguruma-dev \
        mysql-client \
        mariadb-connector-c-dev && \
    apk add --no-cache --virtual .build-deps \
        autoconf \
        binutils \
        gcc \
        libc-dev \
        linux-headers \
        make \
        musl-dev && \
    docker-php-ext-configure gd --with-jpeg && \
    docker-php-ext-configure intl && \
    docker-php-ext-install -j$(nproc) \
        bcmath \
        exif \
        gd \
        gettext \
        intl \
        mbstring \
        opcache \
        pcntl \
        pdo \
        pdo_mysql \
        zip && \
    pecl install -o -f redis xdebug && \
    docker-php-ext-enable redis xdebug && \
    # clean
    apk del .build-deps && \
    rm -rf /var/cache/apk/* /tmp/*

# php
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
COPY php "$PHP_INI_DIR/conf.d"

# composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ARG UNAME=testuser
ARG UID=1000
ARG GID=1000
RUN addgroup -g $GID $UNAME && \
    adduser -D -u $UID -G $UNAME -s /bin/bash $UNAME

USER $UNAME

RUN echo "alias ll='ls -la'" >> ~/.bashrc && \
    echo "alias pa='php artisan'" >> ~/.bashrc && \
    echo "alias pam='pa ide-helper:models -M'" >> ~/.bashrc && \
    echo "alias ms='pa migrate:fresh --seed'" >> ~/.bashrc && \
    echo "alias t='pa test'" >> ~/.bashrc && \
    echo "alias stan='./vendor/bin/phpstan analyse --memory-limit=2G'" >> ~/.bashrc && \
    echo "alias pint='./vendor/bin/pint'" >> ~/.bashrc && \
    echo "alias tf='t --filter'" >> ~/.bashrc && \
    echo "alias pu='clear && t'" >> ~/.bashrc && \
    echo "alias pf='pu --filter'" >> ~/.bashrc

WORKDIR /var/www/html
