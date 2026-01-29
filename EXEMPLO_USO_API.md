# 📚 Guia de Uso da API - Buscar Resultados (Incluindo Federal)

## 🎯 Endpoint Principal

```
GET /api_resultados.php?acao=buscar&loteria={CODIGO}&data={DATA}
```

**Parâmetros:**
- `acao`: sempre `buscar`
- `loteria`: código da loteria (`ln`, `fd`, `sp`, `bs`, `lce`, `lk`, `pb`, `m`)
- `data`: data no formato `YYYY-MM-DD` (ex: `2026-01-28`)

---

## ✅ Exemplos Práticos

### 1. **PHP (cURL)**

```php
<?php
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
    
    return ['erro' => 'Erro ao buscar resultados'];
}

// Exemplo: Buscar Federal de ontem
$resultado = buscarResultados('fd', '2026-01-28');

if (empty($resultado['erro'])) {
    foreach ($resultado['dados'] as $horario => $extracao) {
        echo "{$extracao['titulo']}\n";
        foreach ($extracao['premios'] as $premio) {
            echo "  - {$premio['numero']} ({$premio['animal']})\n";
        }
    }
} else {
    echo "Erro: {$resultado['erro']}\n";
}
?>
```

---

### 2. **JavaScript (Fetch API)**

```javascript
async function buscarResultados(loteria, data) {
    try {
        const url = `api_resultados.php?acao=buscar&loteria=${loteria}&data=${data}`;
        const response = await fetch(url);
        
        if (!response.ok) {
            throw new Error(`Erro HTTP: ${response.status}`);
        }
        
        const resultado = await response.json();
        
        if (resultado.erro) {
            console.error('Erro:', resultado.erro);
            return null;
        }
        
        return resultado.dados;
    } catch (error) {
        console.error('Erro ao buscar resultados:', error);
        return null;
    }
}

// Exemplo: Buscar Federal
buscarResultados('fd', '2026-01-28').then(dados => {
    if (dados) {
        Object.values(dados).forEach(extracao => {
            console.log(extracao.titulo);
            extracao.premios.forEach(premio => {
                console.log(`  ${premio.numero} - ${premio.animal}`);
            });
        });
    }
});
```

---

### 3. **Python (requests)**

```python
import requests
import json

def buscar_resultados(loteria, data):
    url = "http://seuservidor.com/api_resultados.php"
    params = {
        'acao': 'buscar',
        'loteria': loteria,
        'data': data
    }
    
    try:
        response = requests.get(url, params=params, timeout=30)
        response.raise_for_status()
        
        resultado = response.json()
        
        if resultado.get('erro'):
            print(f"Erro: {resultado['erro']}")
            return None
        
        return resultado.get('dados', {})
    except requests.RequestException as e:
        print(f"Erro ao buscar resultados: {e}")
        return None

# Exemplo: Buscar Federal
dados = buscar_resultados('fd', '2026-01-28')

if dados:
    for horario, extracao in dados.items():
        print(f"{extracao['titulo']}")
        for premio in extracao['premios']:
            print(f"  {premio['numero']} - {premio['animal']}")
```

---

### 4. **jQuery**

```javascript
function buscarResultados(loteria, data) {
    $.ajax({
        url: 'api_resultados.php',
        method: 'GET',
        data: {
            acao: 'buscar',
            loteria: loteria,
            data: data
        },
        dataType: 'json',
        success: function(resultado) {
            if (resultado.erro) {
                console.error('Erro:', resultado.erro);
                return;
            }
            
            // Processar resultados
            $.each(resultado.dados, function(horario, extracao) {
                console.log(extracao.titulo);
                $.each(extracao.premios, function(index, premio) {
                    console.log(premio.numero + ' - ' + premio.animal);
                });
            });
        },
        error: function(xhr, status, error) {
            console.error('Erro na requisição:', error);
        }
    });
}

// Exemplo: Buscar Federal
buscarResultados('fd', '2026-01-28');
```

---

### 5. **cURL (Linha de Comando)**

```bash
# Buscar Federal
curl "http://seuservidor.com/api_resultados.php?acao=buscar&loteria=fd&data=2026-01-28"

# Buscar Nacional
curl "http://seuservidor.com/api_resultados.php?acao=buscar&loteria=ln&data=2026-01-28"

# Buscar PT-SP
curl "http://seuservidor.com/api_resultados.php?acao=buscar&loteria=sp&data=2026-01-28"
```

---

## 📊 Formato da Resposta

### Sucesso:

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
          "numero": "83636",
          "animal": "Cobra",
          "grupo": "09"
        }
      ]
    }
  }
}
```

### Erro:

```json
{
  "erro": "Sem resultados para esta data",
  "dados": []
}
```

---

## 🎯 Códigos das Loterias

| Código | Nome | Observações |
|--------|------|-------------|
| `ln` | Loteria Nacional | Múltiplos horários |
| `fd` | Loteria Federal | Sorteios: Quartas e Sábados às 18:50 |
| `sp` | PT-SP/Bandeirantes | Múltiplos horários |
| `bs` | Boa Sorte Goiás | Múltiplos horários |
| `lce` | Lotece | Múltiplos horários |
| `lk` | Look Goiás | Múltiplos horários |
| `pb` | PT Paraíba/Lotep | Múltiplos horários |
| `m` | Milhar | Múltiplos horários |

---

## ⚠️ Tratamento de Erros

### Erros Comuns:

1. **"Sem resultados para esta data"**
   - Federal: Data não é quarta ou sábado
   - Outras: Data muito antiga ou sem sorteios

2. **"Data fora do intervalo permitido"**
   - Visitante: Mais de 10 dias atrás
   - Solução: Configure `PHPSESSID` para acessar dados históricos

3. **Erro HTTP 500**
   - Problema no servidor
   - Verifique logs do servidor

### Exemplo com Tratamento de Erros:

```php
<?php
$resultado = buscarResultados('fd', '2026-01-28');

if ($resultado['erro']) {
    switch ($resultado['erro']) {
        case 'Sem resultados para esta data':
            echo "⚠️ Não há sorteio da Federal nesta data.";
            break;
        case 'Data fora do intervalo permitido':
            echo "⚠️ Data muito antiga. Configure autenticação.";
            break;
        default:
            echo "❌ Erro: {$resultado['erro']}";
    }
} else {
    // Processar resultados normalmente
    foreach ($resultado['dados'] as $extracao) {
        // ...
    }
}
?>
```

---

## 🔒 Autenticação (Dados Históricos)

Para acessar resultados de mais de 10 dias, configure o `PHPSESSID`:

```php
<?php
// Edite api_resultados.php e configure:
$config = [
    'phpsessid' => 'SEU_PHPSESSID_AQUI'
];
?>
```

**Como obter PHPSESSID:**
1. Faça login em `https://bichocerto.com`
2. Abra DevTools (F12) → Application → Cookies
3. Copie o valor de `PHPSESSID`

---

## 📝 Exemplo Completo: Sistema de Verificação

```php
<?php
require_once 'verificar_resultados.php';

class SistemaResultados {
    private $verificador;
    
    public function __construct() {
        $this->verificador = new VerificadorResultados();
    }
    
    public function verificarApostasUsuario($userId, $loteria, $data) {
        // 1. Buscar apostas do usuário do banco
        $apostas = $this->buscarApostasDoBanco($userId, $loteria, $data);
        
        if (empty($apostas)) {
            return ['erro' => 'Nenhuma aposta encontrada'];
        }
        
        // 2. Buscar resultados
        $resultados = $this->verificador->buscarResultados($loteria, $data);
        
        if (!empty($resultados['erro'])) {
            return $resultados;
        }
        
        // 3. Verificar cada aposta
        $acertos = [];
        foreach ($apostas as $aposta) {
            $verificacao = $this->verificador->verificarAposta(
                $loteria,
                $data,
                json_decode($aposta['numeros'])
            );
            
            if ($verificacao['total_acertos'] > 0) {
                $acertos[] = [
                    'aposta_id' => $aposta['id'],
                    'acertos' => $verificacao['acertos']
                ];
            }
        }
        
        return [
            'sucesso' => true,
            'total_apostas' => count($apostas),
            'total_com_acertos' => count($acertos),
            'detalhes' => $acertos
        ];
    }
    
    private function buscarApostasDoBanco($userId, $loteria, $data) {
        // Implementar busca no seu banco de dados
        // Exemplo:
        // $pdo = new PDO(...);
        // $stmt = $pdo->prepare("SELECT * FROM apostas WHERE user_id = ? AND loteria = ? AND data_aposta = ?");
        // return $stmt->fetchAll();
        return [];
    }
}

// Uso
$sistema = new SistemaResultados();
$resultado = $sistema->verificarApostasUsuario(123, 'fd', '2026-01-28');
print_r($resultado);
?>
```

---

## 🚀 Teste Rápido

Teste se a API está funcionando:

```bash
# Teste 1: Federal (deve funcionar se for quarta ou sábado)
curl "http://localhost/api_resultados.php?acao=buscar&loteria=fd&data=2026-01-28"

# Teste 2: Nacional (sempre funciona)
curl "http://localhost/api_resultados.php?acao=buscar&loteria=ln&data=2026-01-28"

# Teste 3: Verificar formato JSON
curl "http://localhost/api_resultados.php?acao=buscar&loteria=ln&data=2026-01-28" | python -m json.tool
```

---

## ✅ Checklist de Integração

- [ ] API está acessível (`api_resultados.php`)
- [ ] Testei com Federal (`loteria=fd`)
- [ ] Testei com outras loterias (`ln`, `sp`, etc.)
- [ ] Tratamento de erros implementado
- [ ] Validação de data implementada
- [ ] Logs de erro configurados
- [ ] Timeout configurado (30 segundos recomendado)

---

## 📞 Suporte

Se encontrar problemas:

1. Verifique se `verificar_resultados.php` está no mesmo diretório
2. Verifique permissões de arquivo
3. Verifique logs do PHP
4. Teste com `curl` diretamente
5. Verifique se a data está no formato correto (`YYYY-MM-DD`)

---

**Última atualização:** 29/01/2026
