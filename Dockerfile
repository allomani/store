FROM php:5.6-apache
RUN apt-get update && apt-get install -y git sendmail libpng-dev
RUN docker-php-ext-install mysql
RUN docker-php-ext-install gd
RUN a2enmod rewrite
WORKDIR /var/www/html
COPY . .
RUN chown -R www-data.www-data *