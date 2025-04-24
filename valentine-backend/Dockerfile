# Utiliser l'image PHP avec Apache
FROM php:8.2-apache

# Définir un ServerName pour Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Installer les dépendances nécessaires
RUN apt-get update && apt-get install -y \
    git unzip zip libicu-dev libzip-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install intl pdo pdo_mysql zip

# Installer Composer depuis l'image officielle Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier le code source de l'application dans le conteneur
COPY . /var/www/html

# Définir le répertoire de travail dans le conteneur
WORKDIR /var/www/html

COPY my-apache.conf /etc/apache2/sites-available/000-default.conf


# Assurer que les bonnes permissions sont données au code source
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

# Installer les dépendances PHP avec Composer
RUN composer install --no-dev --optimize-autoloader

# Activer le module Apache rewrite
RUN a2enmod rewrite

# Copier le fichier de configuration personnalisé pour Apache (si tu en as un)
COPY my-apache.conf /etc/apache2/sites-available/000-default.conf

# Exposer le port 80 pour Apache
EXPOSE 80
