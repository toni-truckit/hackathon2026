FROM php:8.3-fpm-bookworm AS php-base

RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    curl \
    supervisor \
    chromium \
    nodejs \
    npm \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    libmagickwand-dev \
    imagemagick \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql bcmath exif pcntl zip intl gd \
    && pecl install imagick \
    && docker-php-ext-enable imagick

RUN curl -fsSL -o /usr/local/share/ca-certificates/rds-global.pem \
    https://truststore.pki.rds.amazonaws.com/global/global-bundle.pem || true

FROM node:20-bookworm AS frontend

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
RUN npm run build

FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

FROM php-base AS production

WORKDIR /var/www

COPY --from=vendor /app/vendor ./vendor
COPY --from=frontend /app/public/build ./public/build
COPY . .

COPY docker/supervisor/supervisord.conf /etc/supervisor/supervisord.conf
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh

RUN chmod +x /usr/local/bin/docker-entrypoint.sh \
    && mkdir -p storage/logs bootstrap/cache \
    && chown -R www-data:www-data /var/www

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]

FROM php-base AS development

WORKDIR /var/www

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY docker/supervisor/supervisord.conf /etc/supervisor/supervisord.conf
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh

RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]
