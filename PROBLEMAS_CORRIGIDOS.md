# Problemas Encontrados e Corrigidos na API de Resultados

## 1. Cookie duplicado (verificar_resultados.php)

**Problema:** `CURLOPT_COOKIE` era definido duas vezes (linhas 83-84). A segunda chamada sobrescrevia a primeira. A segunda incluía `__cf_bm=; __cfruid=` (vazios), o que poderia causar comportamento inesperado.

**Correção:** Mantida apenas uma definição de cookie com `PHPSESSID`.

---

## 2. Código redundante na extração Federal (verificar_resultados.php)

**Problema:** Bloco `if/else` duplicado para 1º a 5º prêmio da Federal – o `else` repetia exatamente o mesmo `preg_match` do `if`.

**Correção:** Removido o bloco `else` redundante.

---

## 3. Comparação de números na verificação de apostas (verificar_resultados.php)

**Problema:** A normalização usava `str_pad(ltrim($numApostado, '0'), strlen($numApostado), '0', STR_PAD_LEFT)`, que podia falhar quando:
- O número apostado tinha formato diferente do sorteado (ex.: "02047" vs "2047")
- Federal usa 5 dígitos (1º-5º), 4 dígitos (6º) e 3 dígitos (7º)

**Correção:** Comparação feita removendo zeros à esquerda e caracteres não numéricos em ambos os lados, garantindo que "2047", "02047" e "2047" sejam tratados como iguais.

---

## 4. Exemplo executando ao incluir o arquivo (verificar_resultados.php)

**Problema:** O código de exemplo no final do arquivo rodava sempre que `verificar_resultados.php` era incluído (por `api_resultados.php` ou `test_api.php`), gerando saída indesejada e quebrando o JSON/HTML.

**Correção:** O exemplo só é executado quando o script é rodado via CLI (`php verificar_resultados.php`).

---

## Resumo

| Arquivo | Problema | Severidade |
|---------|----------|------------|
| verificar_resultados.php | Cookie duplicado | Média |
| verificar_resultados.php | Código redundante Federal | Baixa |
| verificar_resultados.php | Comparação de números incorreta | Alta |
| verificar_resultados.php | Exemplo rodando ao incluir | Alta |
