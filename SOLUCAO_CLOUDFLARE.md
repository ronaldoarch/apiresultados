# 🛡️ Solução para Bloqueio Cloudflare

## 🔴 Problema Identificado

O Cloudflare está bloqueando o IP do servidor (`147.93.147.33`) com erro HTTP 403, mesmo com headers melhorados e PHPSESSID configurado.

## ✅ Soluções Disponíveis

### **Solução 1: Requisições do Lado do Cliente (Recomendado)** ⭐

Fazer as requisições diretamente do navegador do usuário, evitando o bloqueio do servidor.

**Vantagens:**
- ✅ Não depende do IP do servidor
- ✅ Usa cookies do navegador do usuário
- ✅ Mais difícil de ser bloqueado
- ✅ Funciona mesmo com Cloudflare

**Como usar:**

```javascript
// No seu frontend
async function buscarResultados(loteria, data) {
    const formData = new URLSearchParams();
    formData.append('l', loteria);
    formData.append('d', data);
    
    const response = await fetch('https://bichocerto.com/resultados/base/resultado/', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: formData,
        credentials: 'include'
    });
    
    const html = await response.text();
    // Parse HTML e extrai resultados
    // (use a lógica do verificar_resultados.php)
}
```

**Arquivo criado:** `api_resultados_client.php` - Versão que funciona do lado do cliente

---

### **Solução 2: Usar Proxy/Servidor Intermediário**

Usar um servidor intermediário que não está bloqueado.

**Opções:**
1. **Servidor próprio** com IP diferente
2. **Serviço de proxy** (pago)
3. **Cloudflare Workers** como proxy

---

### **Solução 3: Configurar Proxy no Coolify**

Se o Coolify suportar, configure um proxy reverso.

---

### **Solução 4: Usar API Pública Alternativa**

Se houver outra fonte de dados disponível.

---

## 🎯 Implementação Recomendada

### Para Frontend (JavaScript):

```javascript
// exemplo_frontend.html já tem essa funcionalidade
// Basta ajustar para fazer requisição direta ao bichocerto.com
```

### Para Backend (PHP):

Se realmente precisar fazer do servidor:

1. **Use um servidor proxy** (VPS com IP limpo)
2. **Configure rate limiting** (não fazer muitas requisições)
3. **Use delays** entre requisições
4. **Rotacione User-Agents**

---

## 📝 Arquivo Criado

- `api_resultados_client.php` - Versão client-side que evita bloqueio

---

## ⚠️ Limitações

- **CORS**: bichocerto.com pode não permitir CORS
- **Cookies**: Requer cookies do navegador
- **Parse HTML**: Precisa fazer parsing no cliente

---

## 💡 Recomendação Final

**Use requisições do lado do cliente** quando possível. É a solução mais confiável para contornar bloqueios do Cloudflare.

Para aplicações que realmente precisam fazer do servidor, considere usar um VPS com IP diferente ou serviço de proxy.
