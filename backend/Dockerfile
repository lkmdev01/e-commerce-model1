FROM php:8.4-apache

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
  git \
  curl \
  libpng-dev \
  libonig-dev \
  libxml2-dev \
  zip \
  unzip

# Limpar cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensões PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Obter Composer mais recente
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Habilitar mod_rewrite para o Apache
RUN a2enmod rewrite

# Definir diretório de trabalho
WORKDIR /var/www/html

# Copiar arquivos do projeto
COPY . .

# Ajustar permissões
RUN chown -R www-data:www-data /var/www/html && \
  chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache && \
  chmod -R 775 /var/www/html/storage/framework/views && \
  chmod -R 775 /var/www/html/storage/framework/cache && \
  chmod -R 775 /var/www/html/storage/framework/sessions && \
  chmod -R 775 /var/www/html/storage/logs

# Instalar dependências do Composer
RUN composer install --no-interaction --no-scripts

# Executar comandos do Laravel
RUN php artisan key:generate && \
  php artisan config:cache && \
  php artisan view:cache

# Configurar o DocumentRoot do Apache
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

USER www-data

EXPOSE 80

CMD ["apache2-foreground"] 