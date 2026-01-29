# 🚀 Deploy no Coolify

## 📋 Passo a Passo

### 1. Conectar Repositório GitHub

1. No Coolify, vá em **Sources** (no menu lateral)
2. Clique em **"Add Source"**
3. Selecione **GitHub**
4. Autorize o acesso ao repositório `ronaldoarch/apiresultados`
5. Selecione o repositório

### 2. Criar Novo Projeto

1. Vá em **Projects** → **New Project**
2. Nome: `apideresultados`
3. Clique em **Create**

### 3. Criar Ambiente

1. Dentro do projeto, clique em **"New Environment"**
2. Nome: `production` (ou `staging`)
3. Clique em **Create**

### 4. Adicionar Resource (Aplicação)

1. Na página de **Resources**, clique em **"+ Add Resource"**
2. Selecione **"Application"**
3. Configure:

#### Configurações Básicas:
- **Name**: `api-resultados`
- **Source**: Selecione o repositório GitHub conectado
- **Branch**: `main`
- **Build Pack**: `PHP` (ou `Docker`)

#### Configurações de Build:
- **Build Command**: (deixe vazio ou `composer install` se tiver)
- **Start Command**: (deixe vazio - Coolify detecta automaticamente)

#### Configurações de Porta:
- **Port**: `80` ou `8000` (Coolify geralmente detecta automaticamente)

### 5. Variáveis de Ambiente (Opcional)

Se precisar configurar `PHPSESSID`:

1. Vá em **Shared Variables**
2. Adicione:
   - **Key**: `PHPSESSID`
   - **Value**: (seu PHPSESSID do bichocerto.com)

### 6. Deploy

1. Clique em **"Deploy"** ou **"Save"**
2. Aguarde o build e deploy completar
3. Acesse a URL fornecida pelo Coolify

---

## 📁 Arquivos Necessários

### Arquivo: `Dockerfile` (Opcional - se usar Docker)

Crie `Dockerfile` na raiz:

```dockerfile
FROM php:8.1-apache

# Instalar extensões necessárias
RUN docker-php-ext-install curl dom

# Copiar arquivos
COPY . /var/www/html/

# Configurar Apache
RUN a2enmod rewrite

EXPOSE 80
```

### Arquivo: `composer.json` (Opcional)

Se quiser usar Composer:

```json
{
  "name": "ronaldoarch/apiresultados",
  "description": "API de Resultados - Jogo do Bicho",
  "require": {
    "php": ">=7.4"
  }
}
```

### Arquivo: `.coolify.yml` (Configuração Coolify)

Crie `.coolify.yml` na raiz:

```yaml
version: '1'

services:
  api:
    build:
      context: .
      dockerfile: Dockerfile
    ports:
      - "80:80"
    environment:
      - PHP_SESSID=${PHPSESSID:-}
    volumes:
      - ./:/var/www/html
```

---

## 🔧 Configuração PHP no Coolify

### Opção 1: Build Pack PHP (Recomendado)

Coolify detecta automaticamente arquivos PHP e configura o servidor.

**Requisitos:**
- Arquivo `index.php` ou `api_resultados.php` na raiz
- Coolify usa PHP-FPM + Nginx automaticamente

### Opção 2: Docker

Use o `Dockerfile` fornecido acima.

---

## 📝 Estrutura de Arquivos para Coolify

```
apiresultados/
├── api_resultados.php      # Endpoint principal da API
├── verificar_resultados.php # Classe principal
├── index.php               # Página inicial (opcional)
├── .htaccess               # Configuração Apache
├── Dockerfile              # (se usar Docker)
└── README.md
```

---

## 🌐 Configuração de Domínio

1. No Coolify, vá em **Destinations**
2. Adicione um domínio ou use o subdomínio fornecido
3. Configure DNS apontando para o servidor Coolify

---

## ✅ Checklist de Deploy

- [ ] Repositório GitHub conectado
- [ ] Projeto criado no Coolify
- [ ] Ambiente criado (production/staging)
- [ ] Resource (Application) adicionada
- [ ] Build Pack configurado (PHP)
- [ ] Variáveis de ambiente configuradas (se necessário)
- [ ] Deploy executado
- [ ] URL de acesso testada

---

## 🧪 Testar API Após Deploy

Após o deploy, teste a API:

```bash
# Substitua pela URL fornecida pelo Coolify
curl "https://seu-dominio.com/api_resultados.php?acao=buscar&loteria=fd&data=2026-01-28"
```

Ou acesse no navegador:
```
https://seu-dominio.com/api_resultados.php?acao=buscar&loteria=fd&data=2026-01-28
```

---

## 🔍 Troubleshooting

### Erro: "File not found"
- Verifique se `api_resultados.php` está na raiz do projeto
- Verifique permissões de arquivo

### Erro: "Class not found"
- Verifique se `verificar_resultados.php` está no mesmo diretório
- Verifique o `require_once` no `api_resultados.php`

### Erro: "cURL not available"
- Certifique-se de que a extensão cURL está habilitada
- No Dockerfile, adicione: `RUN docker-php-ext-install curl`

### Erro: "DOM extension not available"
- No Dockerfile, adicione: `RUN docker-php-ext-install dom`

---

## 📊 Monitoramento

No Coolify você pode:
- Ver logs em tempo real
- Monitorar uso de recursos
- Configurar health checks
- Configurar backups automáticos

---

## 🚀 Deploy Automático

Coolify faz deploy automático quando você faz push para o GitHub:

1. Faça push: `git push origin main`
2. Coolify detecta automaticamente
3. Faz build e deploy
4. Notifica quando concluído

---

## 💡 Dicas

1. **Use variáveis de ambiente** para configurações sensíveis
2. **Configure health checks** para monitorar a API
3. **Use staging** para testar antes de produção
4. **Configure backups** para dados importantes

---

**Última atualização:** 29/01/2026
