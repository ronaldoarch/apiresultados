# ⚙️ Configurar Proxy no Coolify

## ✅ Arquivo na Hostinger

Você já subiu o `proxy.php` na Hostinger! ✅

**Domínio:** `forestgreen-monkey-902898.hostingersite.com`

---

## 🧪 Passo 1: Testar o Proxy

Teste se o proxy está funcionando diretamente:

```
https://forestgreen-monkey-902898.hostingersite.com/proxy.php?loteria=ln&data=2026-01-28
```

**O que deve acontecer:**
- ✅ Retorna HTML com resultados (não JSON)
- ✅ Se retornar erro 403, o IP da Hostinger também pode estar bloqueado
- ✅ Se retornar HTML, está funcionando!

---

## ⚙️ Passo 2: Configurar no Coolify

1. **No Coolify**, vá para seu projeto `apideresultados`
2. **Clique no ambiente** (production)
3. **Vá em "Environment Variables"**
4. **Adicione nova variável:**
   - **Key:** `PROXY_URL`
   - **Value:** `https://forestgreen-monkey-902898.hostingersite.com`
   - ⚠️ **IMPORTANTE:** Sem `/proxy.php` no final!
5. **Salve**

---

## 🔄 Passo 3: Redeploy

Após adicionar a variável:
1. **Faça Redeploy** no Coolify
2. Aguarde o deploy completar

---

## ✅ Passo 4: Testar API

Após o redeploy, teste:

```
https://rk48ccsoo8kcooc00wwwog04.agenciamidas.com/api_resultados.php?acao=buscar&loteria=ln&data=2026-01-28
```

**Se funcionar:** Você verá os resultados em JSON! 🎉

**Se ainda der erro:** Verifique:
- Se o proxy está acessível
- Se a URL está correta (sem `/proxy.php`)
- Se fez redeploy após configurar

---

## 🔍 Verificar Configuração

Acesse o teste:

```
https://rk48ccsoo8kcooc00wwwog04.agenciamidas.com/test_phpsessid.php
```

Se o proxy estiver configurado, você verá uma mensagem informando.

---

## 📋 Resumo da Configuração

| Item | Valor |
|------|-------|
| Arquivo na Hostinger | `proxy.php` ✅ |
| Domínio Hostinger | `forestgreen-monkey-902898.hostingersite.com` |
| Variável no Coolify | `PROXY_URL` |
| Valor da Variável | `https://forestgreen-monkey-902898.hostingersite.com` |

---

## ⚠️ Importante

- **URL do proxy:** Sem `/proxy.php` no final
- **Protocolo:** Use `https://` se disponível
- **Redeploy:** Sempre faça redeploy após adicionar variáveis

---

**Próximo passo:** Configure `PROXY_URL` no Coolify e faça redeploy! 🚀
