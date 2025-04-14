FROM php:8.1-apache

COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

RUN a2enmod rewrite

# Install necessary packages
RUN apt-get update && \
    apt-get install \
    libzip-dev \
    wget \
    git \
    unzip \
    -y --no-install-recommends

# Install PHP Extensions
RUN docker-php-ext-install zip pdo_mysql

# Copy composer installable
COPY ./install-composer.sh ./

# Cleanup packages and install composer
RUN apt-get purge -y g++
RUN apt-get autoremove -y
RUN rm -r /var/lib/apt/lists/*
RUN rm -rf /tmp/*
RUN sh ./install-composer.sh
RUN rm ./install-composer.sh

WORKDIR /var/www

# RUN chown -R www-data:www-data /var/www/storage
# RUN touch storage/database.db

CMD ["apache2-foreground"]
