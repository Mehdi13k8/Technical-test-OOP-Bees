FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libicu-dev \
    libpq-dev \
    git \
    unzip \
    wget \
    && docker-php-ext-install intl pdo pdo_mysql opcache

RUN a2enmod rewrite

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy the project files
COPY . .

# Remove composer.lock (if exists)
RUN rm -f composer.lock

# Allow Composer to run as superuser
ENV COMPOSER_ALLOW_SUPERUSER 1

# Install Symfony dependencies
RUN composer install --no-interaction --optimize-autoloader

# Install Symfony CLI
RUN wget https://get.symfony.com/cli/installer -O - | bash
# Move the Symfony binary to a directory that's in the PATH
RUN mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

RUN composer require symfony/orm-pack
RUN composer require --dev symfony/maker-bundle

# Copy wait-for-it script
COPY wait-for-it.sh /usr/local/bin/wait-for-it.sh
# Make it executable
RUN chmod +x /usr/local/bin/wait-for-it.sh

# Expose the appropriate port for Symfony
EXPOSE 8000
