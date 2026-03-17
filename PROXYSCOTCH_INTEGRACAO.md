# Integração Proxyscotch no apiresultados

O **Proxyscotch** ([hoppscotch/proxyscotch](https://github.com/hoppscotch/proxyscotch)) é um proxy HTTP genérico em Go que pode contornar bloqueios do Cloudflare quando rodado em outro servidor (ex.: Coolify).

## Como funciona

1. O apiresultados envia **POST** para o Proxyscotch com JSON contendo a URL de destino
2. O Proxyscotch faz a requisição ao bichocerto.com a partir do **IP do servidor onde está rodando**
3. Retorna o HTML/JSON da resposta

## Configuração no Coolify

### 1. Deploy do Proxyscotch

No Coolify, crie um novo recurso do tipo **Docker Image**:

- **Image:** `hoppscotch/proxyscotch:v0.1.4`
- **Port:** 9159 (padrão)
- **Variáveis de ambiente (opcional):**
  - `PROXYSCOTCH_ALLOWED_ORIGINS=*` — permite qualquer origem (inclui requisições server-side sem Origin)
  - `PROXYSCOTCH_TOKEN` — token de acesso (deixe vazio para permitir todos)

### 2. Configurar no apiresultados

No projeto **apiresultados**, em **Environment Variables**:

| Variável | Valor | Descrição |
|----------|-------|-----------|
| `PROXYSCOTCH_URL` | `https://proxyscotch-xxx.agenclamidas.co` | URL base do Proxyscotch (sem barra no final) |
| `PROXYSCOTCH_TOKEN` | *(opcional)* | Se o Proxyscotch tiver token configurado |

**Prioridade:** Se `PROXYSCOTCH_URL` estiver definido, ele tem prioridade sobre `PROXY_URL` (proxy.php).

### 3. Redeploy

Após configurar, faça redeploy do apiresultados.

## Testar

```bash
# Testar se o Proxyscotch está respondendo
curl -X POST https://proxyscotch-xxx.agenclamidas.co/ \
  -H "Content-Type: application/json" \
  -d '{"Method":"POST","Url":"https://bichocerto.com/resultados/base/resultado/","Headers":{"Content-Type":"application/x-www-form-urlencoded"},"Data":"l=ln&d=2026-03-17"}
```

Se retornar JSON com `"success": true` e `"data"` contendo HTML, está funcionando.

## Diferença: Proxyscotch vs proxy.php

| | Proxyscotch | proxy.php |
|--|-------------|-----------|
| **Linguagem** | Go (binário) | PHP |
| **Formato** | POST com JSON genérico | GET com ?loteria=&data= |
| **Deploy** | Docker (hoppscotch/proxyscotch) | Arquivo PHP em qualquer host |
| **Uso** | Qualquer URL de destino | Só bichocerto.com |

O Proxyscotch é mais flexível e pode ser usado para outras APIs no futuro. O proxy.php é mais simples para hospedagem compartilhada.

## Variáveis de ambiente

| Variável | Onde | Descrição |
|----------|------|-----------|
| `PROXYSCOTCH_URL` | apiresultados | URL base do Proxyscotch |
| `PROXYSCOTCH_TOKEN` | apiresultados | Token (se Proxyscotch tiver token) |
| `PROXYSCOTCH_ALLOWED_ORIGINS` | Proxyscotch | Origens permitidas (use `*` para todas) |
| `PROXYSCOTCH_TOKEN` | Proxyscotch | Token para restringir acesso |
| `PROXYSCOTCH_BANNED_DESTS` | Proxyscotch | Hosts bloqueados |
