# 🔍 Como Verificar se Está Usando a API Corretamente

## ✅ Teste 1: Verificar o que a API Retorna

### Via Navegador:
```
https://rk48ccsoo8kcooc00wwwog04.agenciamidas.com/api_resultados.php?acao=buscar&loteria=fd&data=2026-01-28
```

### Via Terminal:
```bash
curl "https://rk48ccsoo8kcooc00wwwog04.agenciamidas.com/api_resultados.php?acao=buscar&loteria=fd&data=2026-01-28"
```

**O que você deve ver:**
- Números de **5 dígitos** para 1º ao 5º prêmio (ex: `"09593"`, `"83636"`)
- Números de **4 dígitos** para 6º prêmio (ex: `"7396"`)
- Números de **3 dígitos** para 7º prêmio (ex: `"320"`)

---

## ⚠️ Problema Comum: Mostrando Números do "Bicho" ao Invés da Federal

Se seu sistema está mostrando números de **4 dígitos** (ex: `3824`, `0160`) ao invés de **5 dígitos**, significa que:

### Possível Causa 1: Não está usando a API
Você pode estar fazendo requisição direta ao `bichocerto.com` ao invés de usar a API.

**Solução:** Use a API:
```php
$url = 'https://rk48ccsoo8kcooc00wwwog04.agenciamidas.com/api_resultados.php?acao=buscar&loteria=fd&data=2026-01-28';
```

### Possível Causa 2: Processando campo errado
Você pode estar pegando o número do campo "animal" ou "grupo" ao invés do campo "numero".

**Verifique:**
```php
// ✅ CORRETO
$premio['numero']  // Ex: "09593"

// ❌ ERRADO
$premio['animal']  // Ex: "Veado"
$premio['grupo']   // Ex: "24"
```

### Possível Causa 3: Cache ou dados antigos
Seu sistema pode estar usando dados em cache ou de uma versão antiga da API.

**Solução:** Limpe o cache e faça uma nova requisição.

---

## 🔧 Exemplo Correto de Implementação

### PHP:
```php
<?php
$apiUrl = 'https://rk48ccsoo8kcooc00wwwog04.agenciamidas.com/api_resultados.php';
$url = $apiUrl . '?acao=buscar&loteria=fd&data=2026-01-28';

$response = file_get_contents($url);
$resultado = json_decode($response, true);

if (empty($resultado['erro'])) {
    foreach ($resultado['dados'] as $horario => $extracao) {
        echo "<h3>{$extracao['titulo']}</h3>";
        
        foreach ($extracao['premios'] as $index => $premio) {
            $posicao = $index + 1;
            
            // ✅ Use o campo 'numero' - ele já vem com os dígitos corretos!
            $numero = $premio['numero'];  // Ex: "09593" (5 dígitos)
            
            echo "{$posicao}º: {$numero} - {$premio['animal']}<br>";
        }
    }
}
?>
```

### JavaScript:
```javascript
async function buscarFederal() {
    const url = 'https://rk48ccsoo8kcooc00wwwog04.agenciamidas.com/api_resultados.php?acao=buscar&loteria=fd&data=2026-01-28';
    const response = await fetch(url);
    const resultado = await response.json();
    
    if (!resultado.erro) {
        Object.values(resultado.dados).forEach(extracao => {
            console.log(extracao.titulo);
            
            extracao.premios.forEach((premio, index) => {
                const posicao = index + 1;
                
                // ✅ Use premio.numero - ele já vem com os dígitos corretos!
                const numero = premio.numero;  // Ex: "09593" (5 dígitos)
                
                console.log(`${posicao}º: ${numero} - ${premio.animal}`);
            });
        });
    }
}
```

---

## 📊 Estrutura Correta da Resposta

```json
{
  "erro": null,
  "dados": {
    "20": {
      "titulo": "Resultado Loteria Federal",
      "horario": "20",
      "premios": [
        {
          "numero": "09593",    ← 5 dígitos (1º prêmio)
          "animal": "Veado",
          "grupo": "24"
        },
        {
          "numero": "83636",    ← 5 dígitos (2º prêmio)
          "animal": "Gato",
          "grupo": "15"
        },
        {
          "numero": "11969",    ← 5 dígitos (3º prêmio)
          "animal": "Cavalo",
          "grupo": "12"
        },
        {
          "numero": "89318",    ← 5 dígitos (4º prêmio)
          "animal": "Leão",
          "grupo": "20"
        },
        {
          "numero": "32880",    ← 5 dígitos (5º prêmio)
          "animal": "Cachorro",
          "grupo": "06"
        },
        {
          "numero": "7396",     ← 4 dígitos (6º prêmio)
          "animal": "Galo",
          "grupo": "22"
        },
        {
          "numero": "320",      ← 3 dígitos (7º prêmio)
          "animal": "Touro",
          "grupo": "14"
        }
      ]
    }
  }
}
```

---

## 🐛 Debug: Verificar o que Está Sendo Recebido

Adicione este código temporário no seu sistema para ver o que está chegando:

```php
<?php
$apiUrl = 'https://rk48ccsoo8kcooc00wwwog04.agenciamidas.com/api_resultados.php';
$url = $apiUrl . '?acao=buscar&loteria=fd&data=2026-01-28';

$response = file_get_contents($url);
$resultado = json_decode($response, true);

// DEBUG: Mostra tudo que está vindo
echo "<pre>";
echo "RESPOSTA COMPLETA:\n";
print_r($resultado);
echo "\n\nPRIMEIRO PRÊMIO:\n";
if (!empty($resultado['dados'])) {
    $primeiraExtracao = reset($resultado['dados']);
    if (!empty($primeiraExtracao['premios'][0])) {
        $primeiroPremio = $primeiraExtracao['premios'][0];
        echo "Número: " . $primeiroPremio['numero'] . "\n";
        echo "Tamanho: " . strlen($primeiroPremio['numero']) . " dígitos\n";
        echo "Animal: " . $primeiroPremio['animal'] . "\n";
        echo "Grupo: " . $primeiroPremio['grupo'] . "\n";
    }
}
echo "</pre>";
?>
```

**O que verificar:**
- O campo `numero` deve ter **5 dígitos** para os primeiros 5 prêmios
- O campo `numero` deve ter **4 dígitos** para o 6º prêmio
- O campo `numero` deve ter **3 dígitos** para o 7º prêmio

---

## ✅ Checklist

- [ ] Está usando a URL correta da API?
- [ ] Está usando o campo `premio['numero']` e não `premio['animal']` ou `premio['grupo']`?
- [ ] Não está fazendo cache de dados antigos?
- [ ] Está fazendo requisição para `loteria=fd` (Federal)?
- [ ] Está usando a data correta no formato `YYYY-MM-DD`?

---

## 🆘 Se Ainda Não Funcionar

1. **Teste a API diretamente** no navegador e veja o JSON retornado
2. **Compare** o JSON retornado com o que seu sistema está processando
3. **Verifique** se não há código que está modificando os números antes de exibir
4. **Confirme** que está usando a versão mais recente da API (com as correções)

---

**URL da API:** `https://rk48ccsoo8kcooc00wwwog04.agenciamidas.com/api_resultados.php`
