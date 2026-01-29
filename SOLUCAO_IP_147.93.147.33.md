# 🚫 Solução para IP Bloqueado: 147.93.147.33

## 🔴 Problema Confirmado

**Seu IP do servidor:** `147.93.147.33`  
**Status:** Bloqueado pelo Cloudflare (HTTP 403)

O Cloudflare bloqueia baseado em:
- ✅ IP do servidor (seu caso)
- ✅ Cookies/Sessão (PHPSESSID ajuda, mas não resolve se IP está bloqueado)
- ✅ Comportamento suspeito

---

## ✅ Soluções Práticas

### **Solução 1: Aguardar** ⏰ (Mais Simples)

O bloqueio pode ser temporário:

1. **Aguarde 4-6 horas**
2. **Faça redeploy** no Coolify
3. **Teste novamente**

**Por quê funciona?** Cloudflare pode liberar IPs após algumas horas se não houver mais atividade suspeita.

---

### **Solução 2: Usar Proxy em Outro Servidor** ⭐ (Recomendado)

Se você tem acesso a outro servidor (Hostinger, HostGator, VPS, etc.):

#### Passo a Passo:

1. **Suba `proxy.php` em outro servidor**
   - Copie o arquivo `proxy.php` para outro servidor
   - Acesse: `https://outro-servidor.com/proxy.php?loteria=ln&data=2026-01-28`

2. **Configure no Coolify:**
   - Variável: `PROXY_URL`
   - Valor: `https://outro-servidor.com` (sem `/proxy.php`)
   - Faça redeploy

3. **Pronto!** O código detecta automaticamente e usa o proxy.

---

### **Solução 3: Usar Servidor Local/Desenvolvimento**

Se você tem outro servidor disponível:

1. Suba a API completa nesse servidor
2. Use como proxy
3. Configure `PROXY_URL` no Coolify

---

### **Solução 4: Contatar Suporte Cloudflare** (Não Recomendado)

Você pode tentar contatar o suporte do bichocerto.com, mas provavelmente não vão ajudar.

---

## 🔧 Configuração Rápida de Proxy

### Se você tem outro servidor:

1. **Copie `proxy.php` para o outro servidor**
2. **Teste diretamente:**
   ```
   https://outro-servidor.com/proxy.php?loteria=ln&data=2026-01-28
   ```

3. **Se funcionar, configure no Coolify:**
   ```
   PROXY_URL=https://outro-servidor.com
   ```

4. **Faça redeploy**

---

## 📊 Status Atual

| Item | Status |
|------|--------|
| PHPSESSID | ✅ Configurado corretamente |
| Código | ✅ Funcionando |
| IP Servidor | ❌ Bloqueado pelo Cloudflare |
| Solução | ⏳ Aguardar ou usar proxy |

---

## 💡 Recomendação Imediata

**Opção A (Rápida):**
- Aguarde 4-6 horas
- Faça redeploy
- Teste novamente

**Opção B (Definitiva):**
- Use outro servidor como proxy
- Configure `PROXY_URL` no Coolify
- Funciona imediatamente

---

## 🎯 Teste Rápido

Após configurar proxy ou aguardar, teste:

```
https://rk48ccsoo8kcooc00wwwog04.agenciamidas.com/api_resultados.php?acao=buscar&loteria=ln&data=2026-01-28
```

Se ainda der erro 403, o IP continua bloqueado e você precisa:
- Aguardar mais tempo, OU
- Usar proxy em outro servidor

---

**Última atualização:** 29/01/2026
