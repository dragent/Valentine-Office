FROM php:8.2-apache

# Dockerfile
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Install dependencies
RUN apt-get update && apt-get install -y \
    git unzip zip libicu-dev libzip-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install intl pdo pdo_mysql zip

# Installe Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copie le code source
COPY . /var/www/html

# Se place dans le bon répertoire
WORKDIR /var/www/html

# Donne les permissions (optionnel mais souvent utile)
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

# Installe les dépendances PHP
RUN composer install --no-dev --optimize-autoloader

# Active le mod rewrite pour Apache
RUN a2enmod rewrite

# Utiliser l'image de base Apache
FROM php:8.2-apache

# Copier le fichier de configuration personnalisé dans le conteneur
COPY my-apache.conf /etc/apache2/sites-available/000-default.conf

# Activer les modules nécessaires
RUN a2enmod rewrite

# Copier l'application dans le conteneur
COPY . /var/www/html/

# Définir le port d'écoute d'Apache
EXPOSE 80
