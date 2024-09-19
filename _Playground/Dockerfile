# Utiliser une image PHP avec Apache préinstallé
FROM php:8.1-apache

# Copier les fichiers du projet dans le répertoire approprié du conteneur
COPY src/ /var/www/html/

# Activer le module rewrite d'Apache
RUN a2enmod rewrite

# Exposer le port 80 pour accéder à l'application via HTTP
EXPOSE 80

# Lancer Apache lorsque le conteneur démarre
CMD ["apache2-foreground"]