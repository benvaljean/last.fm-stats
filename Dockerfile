FROM php:8.3-apache
COPY ./webroot /var/www/html
COPY ./docker/dbconnect.php /var/www/html/dbconnect.php
RUN docker-php-ext-install mysqli
