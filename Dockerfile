# Dockerfile para API de Resultados
# Coolify pode usar este arquivo se você escolher Docker como Build Pack

FROM php:8.1-apache

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    libxml2-dev \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensões PHP necessárias
RUN docker-php-ext-install curl dom xml

# Habilitar módulos do Apache
RUN a2enmod rewrite headers

# Copiar arquivos para o servidor
COPY . /var/www/html/

# Configurar permissões
RUN chown -R www-data:www-data /var/www/html

# Expor porta 80
EXPOSE 80

# Apache já inicia automaticamente
# Não precisa de CMD adicional
