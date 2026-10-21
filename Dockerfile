# Use official PHP 8.2 with Apache runtime
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Enable Apache rewrite module
RUN a2enmod rewrite headers

# Configure PHP settings
RUN echo "session.gc_maxlifetime = 86400" > /usr/local/etc/php/conf.d/session.ini \
    && echo "upload_max_filesize = 20M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 20M" >> /usr/local/etc/php/conf.d/uploads.ini

# Copy entrypoint script first
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Copy application files
COPY . /var/www/html/

# Ensure web server ownership and write permissions on data directory
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/data

# Expose standard port (entrypoint script adapts dynamically to $PORT on Render/Cloud)
EXPOSE 80

# Use our dynamic entrypoint
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]

# Default command to start Apache
CMD ["apache2-foreground"]
