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

# Criar diretórios necessários e ajustar permissões
RUN mkdir -p /var/www/html/storage/framework/{sessions,views,cache} \
  && mkdir -p /var/www/html/storage/logs \
  && chown -R www-data:www-data /var/www/html \
  && find /var/www/html/storage -type d -exec chmod 775 {} \; \
  && find /var/www/html/storage -type f -exec chmod 664 {} \; \
  && chmod -R 775 /var/www/html/bootstrap/cache

# Instalar dependências do Composer
RUN composer install --no-interaction --no-scripts

# Executar comandos do Laravel
RUN php artisan key:generate && \
  php artisan config:cache && \
  php artisan view:cache

# Configurar o DocumentRoot do Apache
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Manter o usuário root para permitir ajustes de permissão em tempo de execução
USER root

EXPOSE 80

CMD ["apache2-foreground"] 