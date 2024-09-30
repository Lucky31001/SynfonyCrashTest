FROM php:8.2.19-fpm

ENV OS=linux
ENV COMPOSER_HOME /var/composer
ENV COMPOSER_ALLOW_SUPERUSER 1

WORKDIR /app

# Install Dependencies
RUN apt update && apt install -y wget curl git libcurl4-gnutls-dev zlib1g-dev libicu-dev g++ libxml2-dev libpq-dev zip libzip-dev unzip \
 libfreetype6-dev libjpeg62-turbo-dev libmcrypt-dev libpng-dev libxpm-dev libjpeg-dev libwebp-dev gnupg2 poppler-utils \
 libmagickwand-dev imagemagick ghostscript libc-client-dev libkrb5-dev \
 --no-install-recommends
RUN apt autoremove && apt autoclean \
 && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-configure imap --with-kerberos --with-imap-ssl
RUN docker-php-ext-install gettext sockets pdo pdo_pgsql intl opcache gd zip bcmath ftp imap

# Install Composer
RUN mkdir /var/composer
RUN mkdir /var/composer/cache
RUN chmod -R 777 /var/composer/cache
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --2.2

# Symfony CLI
RUN wget https://get.symfony.com/cli/installer -O - | bash && mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

COPY composer.json composer.lock symfony.lock ./

RUN set -eux; \
 composer install --prefer-dist --no-scripts --no-progress --no-suggest; \
 composer clear-cache

COPY ./ ./

RUN bin/console cache:warmup

EXPOSE 8000

CMD ["php-fpm"]