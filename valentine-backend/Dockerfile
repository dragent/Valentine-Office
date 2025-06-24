# Utiliser l'image PHP avec Apache
FROM php:8.2-apache

# Configuration Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Installer les extensions et dépendances nécessaires
RUN apt-get update && apt-get install -y \
    git unzip zip libicu-dev libzip-dev libpng-dev libonig-dev libxml2-dev \
    libjpeg-dev libfreetype6-dev libonig-dev libssl-dev libcurl4-openssl-dev \
    && docker-php-ext-install intl pdo pdo_mysql zip

# Copier le fichier de configuration Apache personnalisé
COPY my-apache.conf /etc/apache2/sites-available/000-default.conf

# Activer mod_rewrite
RUN a2enmod rewrite

# Installer Composer (depuis image officielle)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier les fichiers du projet Symfony dans le conteneur
COPY . /var/www/html

# Définir le répertoire de travail
WORKDIR /var/www/html

# Donner les bons droits à Apache
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

RUN git config --global --add safe.directory /var/www/html

# Installer les dépendances PHP de Symfony
RUN composer install 
RUN php bin/console cache:clear --env=dev


# Exposer le port HTTP
EXPOSE 80
