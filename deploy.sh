#!/bin/bash

# Atualizar sistema
sudo apt-get update
sudo apt-get upgrade -y

# Instalar dependências
sudo apt-get install -y nginx mysql-server php8.1-fpm php8.1-mysql php8.1-mbstring php8.1-xml php8.1-curl php8.1-zip unzip git

# Configurar MySQL
sudo mysql -e "CREATE DATABASE IF NOT EXISTS finbrain;"
sudo mysql -e "CREATE USER IF NOT EXISTS 'finbrain_user'@'localhost' IDENTIFIED BY 'sua_senha_aqui';"
sudo mysql -e "GRANT ALL PRIVILEGES ON finbrain.* TO 'finbrain_user'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"

# Configurar Nginx
sudo tee /etc/nginx/sites-available/finbrain << EOF
server {
    listen 80;
    server_name seu-dominio.com;
    root /var/www/finbrain/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

# Ativar o site
sudo ln -sf /etc/nginx/sites-available/finbrain /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default

# Reiniciar Nginx
sudo systemctl restart nginx

# Instalar Composer
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer

# Configurar diretório do projeto
sudo mkdir -p /var/www/finbrain
sudo chown -R $USER:www-data /var/www/finbrain
sudo chmod -R 775 /var/www/finbrain

# Instalar dependências do projeto
cd /var/www/finbrain
composer install --no-dev --optimize-autoloader

# Configurar ambiente
cp .env.production .env
php artisan key:generate
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Configurar permissões
sudo chown -R www-data:www-data /var/www/finbrain
sudo chmod -R 775 /var/www/finbrain/storage
sudo chmod -R 775 /var/www/finbrain/bootstrap/cache

echo "Deploy concluído! Configure seu DNS para apontar para o IP do servidor." 