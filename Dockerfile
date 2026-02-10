FROM php:8.3-apache

# PHP deps
RUN apt-get update && apt-get install -y \
    curl git unzip libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Node 20
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Enable Apache rewrite
RUN a2enmod rewrite

# Backend
WORKDIR /var/www/html
COPY ./backend/ /var/www/html/

# Frontend
WORKDIR /app/frontend
COPY ./frontend/package*.json ./
RUN npm install
COPY ./frontend/ ./

EXPOSE 80 3000

# Lancer Apache + Next.js en dev
CMD bash -c "apache2-foreground & npm --prefix /app/frontend run dev"