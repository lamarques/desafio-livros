# PHP 8.4 com alpine
FROM php:8.4-fpm-alpine

# Instalar dependências e extensões do PHP
RUN apk add --no-cache \
    autoconf \
    g++ \
    make \
    libzip-dev \
    zlib-dev \
    zip \
    unzip \
    git \
    curl \
    oniguruma-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libxml2-dev && \
    docker-php-ext-configure gd --with-freetype=/usr/include --with-jpeg=/usr/include && \
    docker-php-ext-install gd pdo_mysql zip exif pcntl bcmath xml opcache && \
    pecl install pcov && docker-php-ext-enable pcov

# Definir diretório de trabalho
WORKDIR /app

# Instalar Composer e dependências do projeto
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY . /app

# Expor portas
EXPOSE 8000 9000

# Inicio do servidor PHP-FPM
CMD ["php-fpm"]
