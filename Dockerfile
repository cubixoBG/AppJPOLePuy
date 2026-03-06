FROM php:8.3-apache

# PHP deps
RUN apt-get update && apt-get install -y \
    curl git unzip libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Installer Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

# Enable Apache rewrite
RUN a2enmod rewrite

# Backend
WORKDIR /var/www/html
COPY ./backend/ /var/www/html/

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Installer les dépendances PHP
RUN composer install

RUN chown -R www-data:www-data /var/www/html/var /var/www/html/vendor /var/www/html/public
RUN chmod -R 755 /var/www/html/var /var/www/html/vendor /var/www/html/public
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# DocumentRoot vers public/
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Lancer Apache
CMD bash -c "apache2-foreground"