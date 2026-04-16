FROM php:8.2-apache

# Apache mod_rewrite enable
RUN a2enmod rewrite

# Copy all files
COPY . /var/www/html/

# Allow .htaccess
RUN echo "<Directory /var/www/html>\n\
    AllowOverride All\n\
</Directory>" >> /etc/apache2/apache2.conf

EXPOSE 80
