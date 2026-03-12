FROM php:8.2-apache

RUN apt-get update && apt-get install -y libcurl4-openssl-dev \
    && docker-php-ext-install pdo pdo_mysql curl \
    && a2enmod ssl rewrite \
    && rm -rf /var/lib/apt/lists/*

COPY apache/ssl.conf /etc/apache2/sites-available/ssl.conf
RUN a2ensite ssl.conf && a2dissite 000-default.conf

COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80 443
