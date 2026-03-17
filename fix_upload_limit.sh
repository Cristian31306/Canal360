#!/bin/bash
echo "Aumentando el límite de subida en Nginx a 100MB..."
sudo sed -i '/client_max_body_size/d' /etc/nginx/nginx.conf
sudo sed -i '/http {/a \    client_max_body_size 100M;' /etc/nginx/nginx.conf

PHP_VER=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
FPM_INI="/etc/php/$PHP_VER/fpm/php.ini"

if [ -f "$FPM_INI" ]; then
    echo "Aumentando el límite en PHP-FPM ($PHP_VER) a 100MB..."
    sudo sed -i 's/upload_max_filesize = .*/upload_max_filesize = 100M/' "$FPM_INI"
    sudo sed -i 's/post_max_size = .*/post_max_size = 100M/' "$FPM_INI"
    sudo systemctl restart "php$PHP_VER-fpm"
fi

sudo systemctl restart nginx
echo "¡Limites actualizados correctamente! Ya deberías poder subir imágenes pesadas."
