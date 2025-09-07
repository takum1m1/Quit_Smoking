FROM php:8.4-fpm

# OS パッケージ & PHP 拡張
RUN apt-get update && apt-get install -y \
      libzip-dev zip unzip git libonig-dev libicu-dev libpq-dev \
      curl wget \
    && docker-php-ext-install pdo_mysql mbstring zip intl \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# 権限調整（root のままでも動くが、CI/CD を意識）
RUN addgroup --system app && adduser --system --group app \
    && chown -R app:app /var/www/html
USER app

# 環境変数の設定
ENV APP_ENV=local
ENV APP_DEBUG=true
ENV APP_URL=http://localhost:8000
ENV DB_CONNECTION=sqlite
ENV DB_DATABASE=/var/www/html/database/database.sqlite
ENV SESSION_DRIVER=database
ENV CACHE_STORE=database
ENV QUEUE_CONNECTION=database
ENV SANCTUM_STATEFUL_DOMAINS=localhost:3000,127.0.0.1:3000
ENV SESSION_DOMAIN=localhost

# 本番環境向け最終 CMD（開発中は artisan serve で十分）
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]