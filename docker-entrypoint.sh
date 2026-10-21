#!/bin/bash
set -e

# Default port to 80 if PORT is not set
PORT="${PORT:-80}"

echo "Starting Om Yaman Spices on Port: $PORT..."

# Update Apache listening port dynamically for Cloud / Render
sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf
sed -i "s/:80>/:$PORT>/g" /etc/apache2/sites-available/000-default.conf

# Ensure data directory has proper permissions for JSON persistence
if [ -d "/var/www/html/data" ]; then
    chmod -R 777 /var/www/html/data
fi

# Execute CMD
exec "$@"
