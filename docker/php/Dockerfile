FROM php:8.4-apache

# --- 必要なシステムパッケージ ---
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libonig-dev \
    default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

# --- PHP拡張 (Laravel + MySQL 用) ---
RUN docker-php-ext-install pdo_mysql mbstring zip bcmath

# --- Xdebug (学習のメイン) ---
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# --- Composer ---
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# --- Apache: mod_rewrite + Laravel public をドキュメントルートに ---
RUN a2enmod rewrite
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# --- PHP / Xdebug の設定を追加 ---
COPY conf.d/php.ini /usr/local/etc/php/conf.d/zzz-custom-php.ini
COPY conf.d/xdebug.ini /usr/local/etc/php/conf.d/zzz-xdebug.ini

WORKDIR /var/www/html
