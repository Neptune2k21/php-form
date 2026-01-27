FROM php:8.2-apache
RUN docker-php-ext-install pdo pdo_mysql
COPY index.php /var/www/html/
COPY migrate.php /var/www/html/
COPY migrations.json /var/www/html/
COPY france.jpg /var/www/html/
EXPOSE 80
CMD ["apache2-foreground"]