# Imagem com a versão mais recente do PHP
FROM php:8.2-fpm

# Definir o diretório de trabalho
WORKDIR /var/www

# Instalar apenas as dependências necessárias para o Laravel
RUN apt-get update \
    && apt-get install -y libpq-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libzip-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Configurar e instalar extensões PHP necessárias
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && docker-php-ext-install mbstring zip exif pcntl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd dom

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ENV TZ="America/Sao_Paulo"

EXPOSE 9000
CMD bash -c "composer install --no-dev --optimize-autoloader && php-fpm"
