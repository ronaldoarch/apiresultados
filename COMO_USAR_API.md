# 🚀 Como Usar a API - Guia Rápido

## ✅ Forma Mais Simples (PHP)

```php
<?php
// 1. Configure a URL da API
$apiUrl = 'http://seuservidor.com/api_resultados.php';

// 2. Faça a requisição
$url = $apiUrl . '?acao=buscar&loteria=fd&data=2026-01-28';
$response = file_get_contents($url);
$resultado = json_decode($response, true);

// 3. Use os dados
if (empty($resultado['erro'])) {
    foreach ($resultado['dados'] as $extracao) {
        echo $extracao['titulo'] . "\n";
        foreach ($extracao['premios'] as $premio) {
            echo "  {$premio['numero']} - {$premio['animal']}\n";
        }
    }
} else {
    echo "Erro: {$resultado['erro']}\n";
}
?>
```

---

## ✅ Forma Mais Simples (JavaScript)

```javascript
// 1. Faça a requisição
const url = 'api_resultados.php?acao=buscar&loteria=fd&data=2026-01-28';
fetch(url)
    .then(response => response.json())
    .then(resultado => {
        if (resultado.erro) {
            console.error('Erro:', resultado.erro);
            return;
        }
        
        // Use os dados
        Object.values(resultado.dados).forEach(extracao => {
            console.log(extracao.titulo);
            extracao.premios.forEach(premio => {
                console.log(premio.numero, premio.animal);
            });
        });
    })
    .catch(error => console.error('Erro:', error));
```

---

## 📋 Endpoint

```
GET /api_resultados.php?acao=buscar&loteria={CODIGO}&data={DATA}
```

**Parâmetros:**
- `acao`: sempre `buscar`
- `loteria`: `fd` (Federal), `ln` (Nacional), `sp`, `bs`, `lce`, `lk`, `pb`, `m`
- `data`: formato `YYYY-MM-DD` (ex: `2026-01-28`)

---

## 📊 Resposta de Sucesso

```json
{
  "erro": null,
  "dados": {
    "20": {
      "titulo": "Resultado Loteria Federal",
      "horario": "20",
      "premios": [
        {
          "numero": "09593",
          "animal": "Veado",
          "grupo": "24"
        }
      ]
    }
  }
}
```

---

## ⚠️ Resposta de Erro

```json
{
  "erro": "Sem resultados para esta data",
  "dados": []
}
```

---

## 🎯 Códigos das Loterias

| Código | Nome |
|--------|------|
| `fd` | Loteria Federal |
| `ln` | Loteria Nacional |
| `sp` | PT-SP/Bandeirantes |
| `bs` | Boa Sorte Goiás |
| `lce` | Lotece |
| `lk` | Look Goiás |
| `pb` | PT Paraíba |
| `m` | Milhar |

---

## ✅ Tratamento de Erros

```php
$resultado = buscarResultados('fd', '2026-01-28');

if ($resultado['erro']) {
    // Trata erros específicos
    if ($resultado['erro'] === 'Sem resultados para esta data') {
        echo "Federal só sorteia quartas e sábados";
    } else {
        echo "Erro: {$resultado['erro']}";
    }
} else {
    // Processa resultados
    foreach ($resultado['dados'] as $extracao) {
        // ...
    }
}
```

---

## 🔧 Exemplo Completo com cURL

```php
function buscarResultados($loteria, $data) {
    $url = "http://seuservidor.com/api_resultados.php?acao=buscar&loteria={$loteria}&data={$data}";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        return json_decode($response, true);
    }
    
    return ['erro' => "Erro HTTP: {$httpCode}", 'dados' => []];
}

// Uso
$resultado = buscarResultados('fd', '2026-01-28');
```

---

## 📝 Observações Importantes

1. **Federal**: Sorteios apenas às quartas e sábados às 18:50
2. **Data**: Sempre use formato `YYYY-MM-DD`
3. **Timeout**: Configure timeout de pelo menos 30 segundos
4. **Erros**: Sempre verifique se `erro` é `null` antes de processar

---

## 🚀 Teste Rápido

```bash
# Teste via linha de comando
curl "http://seuservidor.com/api_resultados.php?acao=buscar&loteria=fd&data=2026-01-28"
```

---

**Arquivos de exemplo completos:**
- `exemplo_integracao_simples.php` - Exemplo completo em PHP
- `EXEMPLO_USO_API.md` - Guia detalhado com múltiplas linguagens
