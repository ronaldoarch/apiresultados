# 🚀 Deploy no Cloudflare

## Opções de Deploy

### Opção 1: Cloudflare Pages (Recomendado para Frontend)

Ideal para os arquivos HTML/JavaScript estáticos.

### Opção 2: Cloudflare Workers (Para API PHP)

Para executar a API PHP, você precisará converter ou usar Workers.

---

## 📋 Opção 1: Cloudflare Pages (Frontend)

### Passo a Passo

1. **Acesse Cloudflare Dashboard**
   - Vá em: https://dash.cloudflare.com
   - Clique em "Pages" → "Create a project"

2. **Conecte com GitHub**
   - Selecione o repositório: `ronaldoarch/apiresultados`
   - Branch: `main`

3. **Configurações de Build**
   - **Framework preset**: None (ou Static)
   - **Build command**: (deixe vazio)
   - **Build output directory**: `/` (raiz)

4. **Deploy**
   - Clique em "Save and Deploy"
   - Aguarde o deploy completar

### Arquivos que Funcionarão

- ✅ `exemplo_frontend.html` - Interface web
- ✅ `visualizar_resultados.html` - Visualização de resultados
- ✅ Arquivos `.md` - Documentação

### ⚠️ Limitação

**Cloudflare Pages não executa PHP diretamente!**

Para usar a API PHP, você tem duas opções:

**A) Usar API em outro servidor**
- Suba `api_resultados.php` em um servidor PHP (ex: Hostinger, HostGator)
- Configure a URL da API no frontend

**B) Converter para Cloudflare Workers** (veja Opção 2)

---

## 📋 Opção 2: Cloudflare Workers (API)

### Converter PHP para Workers

Cloudflare Workers usa JavaScript/TypeScript. Você precisará:

1. **Criar Worker para API**

Crie um arquivo `worker.js`:

```javascript
// worker.js
export default {
  async fetch(request) {
    const url = new URL(request.url);
    
    // Rota: /api/buscar?loteria=fd&data=2026-01-28
    if (url.pathname === '/api/buscar') {
      const loteria = url.searchParams.get('loteria');
      const data = url.searchParams.get('data');
      
      // Fazer requisição para bichocerto.com
      const response = await fetch('https://bichocerto.com/resultados/base/resultado/', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `l=${loteria}&d=${data}`
      });
      
      const html = await response.text();
      
      // Parse HTML e retorna JSON
      // (implementar lógica de parsing aqui)
      
      return new Response(JSON.stringify({
        erro: null,
        dados: {}
      }), {
        headers: { 'Content-Type': 'application/json' }
      });
    }
    
    return new Response('Not Found', { status: 404 });
  }
}
```

2. **Deploy do Worker**

```bash
# Instalar Wrangler CLI
npm install -g wrangler

# Login
wrangler login

# Criar projeto
wrangler init api-resultados

# Deploy
wrangler deploy
```

---

## 🎯 Opção Recomendada: Híbrida

### Estrutura Ideal

1. **Cloudflare Pages** → Frontend (HTML/JS)
2. **Servidor PHP** → API (`api_resultados.php`)

### Configuração

1. **Deploy Frontend no Cloudflare Pages**
   - Conecte repositório GitHub
   - Deploy automático

2. **Configure API no Frontend**

Edite `exemplo_frontend.html`:

```javascript
// Se sua API está em outro servidor
const API_URL = 'https://seu-servidor-php.com/api_resultados.php';

// Ou se você criou um Worker
const API_URL = 'https://api-resultados.seu-worker.workers.dev/api/buscar';
```

---

## 📝 Arquivo wrangler.toml (Workers)

Se optar por Workers, crie `wrangler.toml`:

```toml
name = "api-resultados"
main = "worker.js"
compatibility_date = "2026-01-29"

[env.production]
routes = [
  { pattern = "api-resultados.seusite.com/*", zone_name = "seusite.com" }
]
```

---

## 🔧 Configuração para Cloudflare Pages

### Arquivo: `_redirects` (opcional)

Crie um arquivo `_redirects` na raiz:

```
/api/* https://seu-servidor-php.com/api_resultados.php/:splat 200
```

### Arquivo: `_headers` (opcional)

Crie um arquivo `_headers` na raiz:

```
/*
  X-Frame-Options: DENY
  X-Content-Type-Options: nosniff
  Referrer-Policy: strict-origin-when-cross-origin
```

---

## ✅ Checklist de Deploy

### Para Cloudflare Pages:
- [ ] Repositório conectado ao GitHub
- [ ] Branch `main` selecionada
- [ ] Build settings configuradas
- [ ] Frontend funcionando
- [ ] API configurada (em servidor separado ou Worker)

### Para Cloudflare Workers:
- [ ] Wrangler CLI instalado
- [ ] Worker criado e testado localmente
- [ ] Deploy realizado
- [ ] Domínio configurado (opcional)

---

## 🚀 Comandos Rápidos

### Deploy via GitHub (Pages)

1. Faça push para GitHub:
```bash
git push origin main
```

2. Cloudflare Pages detecta automaticamente e faz deploy

### Deploy via Wrangler (Workers)

```bash
# Login
wrangler login

# Deploy
wrangler deploy

# Ver logs
wrangler tail
```

---

## 📞 Suporte

- **Cloudflare Pages Docs**: https://developers.cloudflare.com/pages/
- **Cloudflare Workers Docs**: https://developers.cloudflare.com/workers/

---

## 💡 Dica

**Para começar rápido:**

1. Deploy do frontend no Cloudflare Pages (automático via GitHub)
2. Suba a API PHP em um servidor gratuito (ex: 000webhost, InfinityFree)
3. Configure a URL da API no frontend

Isso é mais simples que converter tudo para Workers!

---

**Última atualização:** 29/01/2026
