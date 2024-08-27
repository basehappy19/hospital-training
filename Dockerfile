FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    nodejs \
    npm

RUN docker-php-ext-install pdo pdo_mysql

RUN pecl install xdebug && docker-php-ext-enable xdebug

RUN a2enmod rewrite

RUN echo "<Directory /var/www/html/>" >> /etc/apache2/apache2.conf
RUN echo "    AllowOverride All" >> /etc/apache2/apache2.conf
RUN echo "</Directory>" >> /etc/apache2/apache2.conf

RUN echo "upload_max_filesize = 10M" >> /usr/local/etc/php/conf.d/uploads.ini
RUN echo "post_max_size = 10M" >> /usr/local/etc/php/conf.d/uploads.ini
RUN echo "max_file_uploads = 50" >> /usr/local/etc/php/conf.d/uploads.ini

COPY php.ini /usr/local/etc/php/conf.d/custom.ini

COPY ./src /var/www/html

WORKDIR /var/www/html

RUN npm install -D tailwindcss

COPY ./src/tailwind.config.js .

CMD npx tailwindcss -i ./dist/input.css -o ./dist/styles.css --watch & apache2-foreground