# 🚫 Solução para IP Bloqueado pelo Cloudflare

## 🔴 Problema Confirmado

O teste mostra que:
- ✅ PHPSESSID está configurado corretamente
- ✅ Variáveis de ambiente funcionando
- ❌ **Cloudflare bloqueou o IP do servidor** (`10.0.1.63`)

## ✅ Soluções Disponíveis

### **Solução 1: Usar Servidor Intermediário/Proxy** ⭐ (Recomendado)

Crie um proxy simples em outro servidor que não está bloqueado.

#### Opção A: Proxy PHP Simples

Crie um arquivo `proxy.php` em outro servidor (com IP diferente):

```php
<?php
// proxy.php - Coloque em outro servidor
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$loteria = $_GET['loteria'] ?? $_POST['loteria'] ?? 'ln';
$data = $_GET['data'] ?? $_POST['data'] ?? date('Y-m-d');

$url = 'https://bichocerto.com/resultados/base/resultado/';
$postData = http_build_query(['l' => $loteria, 'd' => $data]);

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $postData,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/x-www-form-urlencoded',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
    ]
]);

$html = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo $html; // Retorna HTML para o servidor principal fazer parse
} else {
    echo json_encode(['erro' => "HTTP $httpCode"]);
}
?>
```

Depois configure no Coolify para usar esse proxy.

#### Opção B: Usar Serviço de Proxy Pago

Serviços como:
- ProxyMesh
- Bright Data
- Smartproxy

---

### **Solução 2: Aguardar e Tentar Novamente**

O bloqueio do Cloudflare pode ser temporário:

1. **Aguarde 2-4 horas**
2. **Faça redeploy** no Coolify
3. **Teste novamente**

---

### **Solução 3: Usar Outro Servidor**

Se você tem acesso a outro servidor/VPS:

1. Suba a API nesse servidor
2. Configure como proxy
3. Use do Coolify

---

### **Solução 4: Rate Limiting Inteligente**

Modificar o código para fazer requisições mais espaçadas:

```php
// Adicionar delay maior entre requisições
sleep(5); // 5 segundos entre requisições
```

---

### **Solução 5: Usar Cloudflare Workers como Proxy**

Criar um Worker no Cloudflare que faz as requisições:

```javascript
// worker.js
export default {
  async fetch(request) {
    const url = new URL(request.url);
    const loteria = url.searchParams.get('loteria');
    const data = url.searchParams.get('data');
    
    const response = await fetch('https://bichocerto.com/resultados/base/resultado/', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `l=${loteria}&d=${data}`
    });
    
    return response;
  }
}
```

---

## 🎯 Solução Rápida: Proxy Simples

### Passo a Passo:

1. **Crie `proxy.php` em outro servidor** (Hostinger, HostGator, etc.)
2. **Configure URL do proxy no Coolify** (variável de ambiente)
3. **Modifique `verificar_resultados.php`** para usar proxy quando disponível

---

## 📝 Código para Usar Proxy

Vou criar uma versão que suporta proxy. Veja o próximo commit.

---

## ⚠️ Importante

**O IP do servidor está bloqueado pelo Cloudflare.** Mesmo com PHPSESSID correto, o Cloudflare bloqueia baseado no IP, não apenas em cookies.

**Soluções práticas:**
1. ✅ Usar outro servidor como proxy
2. ✅ Aguardar algumas horas
3. ✅ Usar serviço de proxy pago
4. ✅ Configurar Cloudflare Workers

---

**Última atualização:** 29/01/2026
