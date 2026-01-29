# 🔐 Como Configurar PHPSESSID no Coolify

O PHPSESSID permite acessar resultados históricos (mais de 10 dias) e pode ajudar a passar pelo bloqueio do Cloudflare.

## 📋 Passo a Passo no Coolify

### 1. Obter PHPSESSID

1. Acesse https://bichocerto.com
2. Faça login na sua conta
3. Abra DevTools (F12)
4. Vá em **Application** → **Cookies** → `https://bichocerto.com`
5. Copie o valor de `PHPSESSID`

### 2. Configurar no Coolify

1. No Coolify, vá para seu projeto `apideresultados`
2. Clique no ambiente (production)
3. Vá em **Environment Variables** ou **Shared Variables**
4. Adicione nova variável:
   - **Key**: `PHPSESSID`
   - **Value**: `45c16270330406d85326a05c4058334b` (seu PHPSESSID)
5. Salve e faça redeploy

### 3. Verificar se Funcionou

Após o redeploy, teste:
```
https://sua-url.com/api_resultados.php?acao=buscar&loteria=ln&data=2026-01-17
```

Se funcionar, você verá os resultados mesmo para datas antigas.

---

## ⚠️ Importante

- **Segurança**: Não compartilhe seu PHPSESSID publicamente
- **Expiração**: PHPSESSID pode expirar. Se parar de funcionar, obtenha um novo
- **Uso Responsável**: Use apenas para fins legítimos

---

## 🔄 Atualizar PHPSESSID

Se o PHPSESSID expirar:

1. Obtenha um novo PHPSESSID (mesmo processo acima)
2. Atualize a variável de ambiente no Coolify
3. Faça redeploy

---

## 📝 Nota Técnica

O código agora verifica automaticamente:
1. Variável de ambiente `PHPSESSID` (prioridade)
2. Configuração direta no código (fallback)

Isso permite flexibilidade entre diferentes ambientes de deploy.
