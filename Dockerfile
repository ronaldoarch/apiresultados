# Dockerfile para API de Resultados
# Coolify pode usar este arquivo se você escolher Docker como Build Pack

FROM php:8.1-apache

# Instalar extensões PHP necessárias
RUN docker-php-ext-install curl dom

# Habilitar módulo rewrite do Apache
RUN a2enmod rewrite

# Copiar arquivos para o servidor
COPY . /var/www/html/

# Configurar permissões
RUN chown -R www-data:www-data /var/www/html

# Expor porta 80
EXPOSE 80

# Apache já inicia automaticamente
# Não precisa de CMD adicional
