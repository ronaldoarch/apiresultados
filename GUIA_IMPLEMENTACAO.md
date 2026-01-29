# 🚀 Guia de Implementação - Como Usar a API no Seu Sistema

## ✅ URL da Sua API (Já Funcionando)

```
https://rk48ccsoo8kcooc00wwwog04.agenciamidas.com/api_resultados.php
```

---

## 📋 Forma Mais Simples (Copiar e Colar)

### **PHP - Exemplo Básico**

```php
<?php
// 1. Configure a URL da sua API
$apiUrl = 'https://rk48ccsoo8kcooc00wwwog04.agenciamidas.com/api_resultados.php';

// 2. Buscar resultados da Federal
$url = $apiUrl . '?acao=buscar&loteria=fd&data=2026-01-28';
$response = file_get_contents($url);
$resultado = json_decode($response, true);

// 3. Verificar se funcionou
if (empty($resultado['erro'])) {
    // ✅ Sucesso! Processar dados
    foreach ($resultado['dados'] as $horario => $extracao) {
        echo "<h3>{$extracao['titulo']}</h3>";
        foreach ($extracao['premios'] as $index => $premio) {
            $posicao = $index + 1;
            echo "{$posicao}º: {$premio['numero']} - {$premio['animal']}<br>";
        }
    }
} else {
    // ❌ Erro
    echo "Erro: {$resultado['erro']}";
}
?>
```

---

### **JavaScript - Exemplo Básico**

```javascript
// Função para buscar resultados
async function buscarResultados(loteria, data) {
    const url = `https://rk48ccsoo8kcooc00wwwog04.agenciamidas.com/api_resultados.php?acao=buscar&loteria=${loteria}&data=${data}`;
    
    try {
        const response = await fetch(url);
        const resultado = await response.json();
        
        if (resultado.erro) {
            console.error('Erro:', resultado.erro);
            return null;
        }
        
        return resultado.dados;
    } catch (error) {
        console.error('Erro ao buscar:', error);
        return null;
    }
}

// Usar a função
buscarResultados('fd', '2026-01-28').then(dados => {
    if (dados) {
        Object.values(dados).forEach(extracao => {
            console.log(extracao.titulo);
            extracao.premios.forEach((premio, index) => {
                console.log(`${index + 1}º: ${premio.numero} - ${premio.animal}`);
            });
        });
    }
});
```

---

## 🔧 Implementação Completa (Pronta para Produção)

### **PHP - Classe Completa**

```php
<?php
class ApiResultados {
    private $apiUrl;
    
    public function __construct() {
        $this->apiUrl = 'https://rk48ccsoo8kcooc00wwwog04.agenciamidas.com/api_resultados.php';
    }
    
    /**
     * Busca resultados de uma loteria
     */
    public function buscar($loteria, $data) {
        $url = $this->apiUrl . '?acao=buscar&loteria=' . urlencode($loteria) . '&data=' . urlencode($data);
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($response === false || !empty($error)) {
            return [
                'sucesso' => false,
                'erro' => 'Erro de conexão: ' . $error,
                'dados' => []
            ];
        }
        
        if ($httpCode !== 200) {
            return [
                'sucesso' => false,
                'erro' => "Erro HTTP: {$httpCode}",
                'dados' => []
            ];
        }
        
        $resultado = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'sucesso' => false,
                'erro' => 'Resposta inválida da API',
                'dados' => []
            ];
        }
        
        if (!empty($resultado['erro'])) {
            return [
                'sucesso' => false,
                'erro' => $resultado['erro'],
                'dados' => []
            ];
        }
        
        return [
            'sucesso' => true,
            'dados' => $resultado['dados']
        ];
    }
    
    /**
     * Verifica se um número foi sorteado
     */
    public function verificarNumero($loteria, $data, $numero) {
        $resultado = $this->buscar($loteria, $data);
        
        if (!$resultado['sucesso']) {
            return [
                'encontrado' => false,
                'erro' => $resultado['erro']
            ];
        }
        
        $numeroNormalizado = ltrim($numero, '0');
        if (empty($numeroNormalizado)) {
            $numeroNormalizado = '0';
        }
        
        foreach ($resultado['dados'] as $horario => $extracao) {
            foreach ($extracao['premios'] as $index => $premio) {
                $premioNormalizado = ltrim($premio['numero'], '0');
                if (empty($premioNormalizado)) {
                    $premioNormalizado = '0';
                }
                
                if ($premioNormalizado === $numeroNormalizado) {
                    return [
                        'encontrado' => true,
                        'numero' => $premio['numero'],
                        'posicao' => ($index + 1) . 'º',
                        'animal' => $premio['animal'],
                        'grupo' => $premio['grupo'],
                        'horario' => $extracao['titulo']
                    ];
                }
            }
        }
        
        return [
            'encontrado' => false,
            'mensagem' => 'Número não encontrado'
        ];
    }
}

// ============================================
// EXEMPLO DE USO
// ============================================

$api = new ApiResultados();

// Buscar Federal
$resultado = $api->buscar('fd', '2026-01-28');
if ($resultado['sucesso']) {
    foreach ($resultado['dados'] as $extracao) {
        echo "<h2>{$extracao['titulo']}</h2>";
        foreach ($extracao['premios'] as $index => $premio) {
            echo "{$premio['numero']} - {$premio['animal']}<br>";
        }
    }
}

// Verificar número
$verificacao = $api->verificarNumero('fd', '2026-01-28', '09593');
if ($verificacao['encontrado']) {
    echo "✅ Número {$verificacao['numero']} encontrado na posição {$verificacao['posicao']}";
}
?>
```

---

## 📊 Estrutura da Resposta JSON

### **Sucesso:**

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
        },
        {
          "numero": "1234",
          "animal": "Gato",
          "grupo": "12"
        }
      ]
    }
  }
}
```

### **Erro:**

```json
{
  "erro": "Sem resultados para esta data",
  "dados": []
}
```

---

## 🎯 Códigos das Loterias

| Código | Nome | Observação |
|--------|------|------------|
| `fd` | Loteria Federal | Sorteios: quartas e sábados às 18:50 |
| `ln` | Loteria Nacional | Múltiplos horários por dia |
| `sp` | PT-SP/Bandeirantes | - |
| `bs` | Boa Sorte Goiás | - |
| `lce` | Lotece | - |
| `lk` | Look Goiás | - |
| `pb` | PT Paraíba | - |
| `m` | Milhar | - |

---

## ⚠️ Tratamento de Erros Importante

```php
$resultado = $api->buscar('fd', '2026-01-28');

if (!$resultado['sucesso']) {
    // Tratar erros específicos
    switch ($resultado['erro']) {
        case 'Sem resultados para esta data':
            if ($loteria === 'fd') {
                echo "A Federal só sorteia às quartas e sábados";
            }
            break;
            
        case 'Data fora do intervalo permitido':
            echo "Data muito antiga";
            break;
            
        default:
            echo "Erro: " . $resultado['erro'];
    }
}
```

---

## 🔄 Exemplo: Integrar em Sistema Existente

```php
<?php
// No seu sistema, adicione:

require_once 'ApiResultados.php'; // Ou inclua a classe acima

class MeuSistema {
    private $api;
    
    public function __construct() {
        $this->api = new ApiResultados();
    }
    
    public function atualizarResultados($loteria, $data) {
        $resultado = $this->api->buscar($loteria, $data);
        
        if (!$resultado['sucesso']) {
            // Log do erro
            error_log("Erro ao buscar resultados: " . $resultado['erro']);
            return false;
        }
        
        // Salvar no seu banco de dados
        foreach ($resultado['dados'] as $horario => $extracao) {
            // Sua lógica de salvamento aqui
            $this->salvarNoBanco($loteria, $data, $horario, $extracao);
        }
        
        return true;
    }
    
    public function verificarAposta($loteria, $data, $numeroApostado) {
        $verificacao = $this->api->verificarNumero($loteria, $data, $numeroApostado);
        
        if ($verificacao['encontrado']) {
            // Atualizar status da aposta como ganhadora
            $this->marcarApostaComoGanhadora($numeroApostado, $verificacao);
            return true;
        }
        
        return false;
    }
    
    // ... seus métodos existentes ...
}
?>
```

---

## 🚀 Teste Rápido

### Via Navegador:
```
https://rk48ccsoo8kcooc00wwwog04.agenciamidas.com/api_resultados.php?acao=buscar&loteria=fd&data=2026-01-28
```

### Via cURL (Terminal):
```bash
curl "https://rk48ccsoo8kcooc00wwwog04.agenciamidas.com/api_resultados.php?acao=buscar&loteria=fd&data=2026-01-28"
```

---

## 📝 Observações Importantes

1. **Federal**: Sorteios apenas às quartas e sábados às 18:50
2. **Data**: Sempre use formato `YYYY-MM-DD` (ex: `2026-01-28`)
3. **Timeout**: Configure timeout de pelo menos 30 segundos
4. **Erros**: Sempre verifique se `erro` é `null` antes de processar
5. **Federal - Dígitos**: 
   - 1º ao 5º prêmio: 5 dígitos (ex: `09593`)
   - 6º prêmio: 4 dígitos (ex: `9593`)
   - 7º prêmio: 3 dígitos (ex: `593`)

---

## ✅ Pronto para Usar!

A API está funcionando e pronta para ser integrada no seu sistema. Use os exemplos acima como base e adapte conforme sua necessidade.

**URL Base:** `https://rk48ccsoo8kcooc00wwwog04.agenciamidas.com/api_resultados.php`

**Endpoint:** `?acao=buscar&loteria={CODIGO}&data={DATA}`
