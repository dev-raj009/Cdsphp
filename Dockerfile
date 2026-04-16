FROM php:8.2-apache

RUN a2enmod rewrite

# IMPORTANT: ensure correct path
COPY . /var/www/html/

# Fix Apache config
RUN echo "<Directory /var/www/html>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>" > /etc/apache2/conf-available/custom.conf \
    && a2enconf custom

EXPOSE 80
