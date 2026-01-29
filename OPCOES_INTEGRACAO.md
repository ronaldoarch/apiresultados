# 🔧 Opções de Integração - Como Usar em Outro Sistema

## ❌ Problema: API Precisa Estar Online

Se você **não subir** este projeto online, outro sistema **não conseguirá** acessar a API via HTTP.

**Por quê?**
- A API (`api_resultados.php`) precisa estar rodando em um servidor web
- Requisições HTTP precisam de um servidor para responder
- Sem servidor = sem acesso

---

## ✅ Soluções Possíveis

### **Opção 1: Usar Diretamente a Classe PHP (Recomendado)** ⭐

**Melhor opção se:** Seu outro sistema também é PHP

**Como fazer:**
1. Copie apenas o arquivo `verificar_resultados.php` para seu outro sistema
2. Use a classe diretamente, sem precisar de API HTTP

**Exemplo:**

```php
<?php
// No seu outro sistema online
require_once 'verificar_resultados.php';

// Use diretamente, sem API HTTP
$verificador = new VerificadorResultados();

// Buscar Federal
$resultado = $verificador->buscarResultados('fd', '2026-01-28');

if (empty($resultado['erro'])) {
    foreach ($resultado['dados'] as $extracao) {
        echo $extracao['titulo'] . "\n";
        foreach ($extracao['premios'] as $premio) {
            echo "  {$premio['numero']} - {$premio['animal']}\n";
        }
    }
}
?>
```

**Vantagens:**
- ✅ Não precisa de servidor separado
- ✅ Mais rápido (sem requisição HTTP)
- ✅ Mais seguro (sem expor API pública)
- ✅ Funciona offline (só precisa internet para buscar do bichocerto.com)

**Desvantagens:**
- ❌ Só funciona se o outro sistema for PHP

---

### **Opção 2: Subir Apenas os Arquivos Necessários**

**Melhor opção se:** Você quer usar API HTTP e tem um servidor disponível

**Como fazer:**
1. Suba apenas estes arquivos para um servidor:
   - `verificar_resultados.php`
   - `api_resultados.php`
2. Configure a URL no seu outro sistema

**Estrutura mínima no servidor:**
```
/seu-servidor/
├── verificar_resultados.php
└── api_resultados.php
```

**No seu outro sistema:**
```php
// Aponte para o servidor onde você subiu a API
$apiUrl = 'https://seuservidor.com/api_resultados.php';
```

**Vantagens:**
- ✅ Funciona com qualquer linguagem (PHP, Python, JavaScript, etc.)
- ✅ Centralizado em um servidor
- ✅ Pode ser usado por múltiplos sistemas

**Desvantagens:**
- ❌ Precisa de servidor web
- ❌ Requer requisições HTTP (mais lento)

---

### **Opção 3: Integrar Diretamente no Outro Sistema (Qualquer Linguagem)**

**Melhor opção se:** Seu outro sistema não é PHP

**Como fazer:**
1. Reimplemente a lógica em outra linguagem (Python, Node.js, etc.)
2. Ou use a API se você subir ela em um servidor

**Exemplo Python:**

```python
import requests
from bs4 import BeautifulSoup
import re

class VerificadorResultados:
    def __init__(self):
        self.base_url = "https://bichocerto.com/resultados/base/resultado/"
    
    def buscar_resultados(self, codigo_loteria, data):
        response = requests.post(
            self.base_url,
            data={'l': codigo_loteria, 'd': data},
            timeout=30
        )
        
        if response.status_code != 200:
            return {'erro': 'Erro ao buscar resultados', 'dados': []}
        
        # Parse HTML e extrai resultados (mesma lógica do PHP)
        # ... código de parsing ...
        
        return {'erro': None, 'dados': resultados}

# Uso
verificador = VerificadorResultados()
resultado = verificador.buscar_resultados('fd', '2026-01-28')
```

**Vantagens:**
- ✅ Funciona em qualquer linguagem
- ✅ Não depende de servidor externo

**Desvantagens:**
- ❌ Precisa reimplementar a lógica
- ❌ Mais trabalho

---

## 📊 Comparação das Opções

| Opção | Precisa Servidor? | Funciona Offline? | Linguagem | Complexidade |
|-------|------------------|-------------------|-----------|--------------|
| **1. Classe PHP Direta** | ❌ Não | ✅ Sim* | PHP | ⭐ Fácil |
| **2. API HTTP** | ✅ Sim | ❌ Não | Qualquer | ⭐⭐ Média |
| **3. Reimplementar** | ❌ Não | ✅ Sim* | Qualquer | ⭐⭐⭐ Difícil |

*Precisa internet apenas para buscar do bichocerto.com

---

## 🎯 Recomendação

### Se seu outro sistema é PHP:
→ **Use Opção 1** (classe direta)
- Copie `verificar_resultados.php`
- Use diretamente no código
- Mais simples e rápido

### Se seu outro sistema não é PHP:
→ **Use Opção 2** (subir API)
- Suba `verificar_resultados.php` + `api_resultados.php` em um servidor
- Faça requisições HTTP do seu sistema
- Mais prático que reimplementar

---

## 📝 Exemplo Prático: Integração Direta (Opção 1)

### No seu sistema online (PHP):

```php
<?php
// 1. Copie verificar_resultados.php para seu sistema
// 2. Inclua no seu código

require_once '/caminho/para/verificar_resultados.php';

class MeuSistema {
    private $verificador;
    
    public function __construct() {
        $this->verificador = new VerificadorResultados();
    }
    
    public function verificarApostasUsuario($userId, $loteria, $data) {
        // Busca apostas do banco
        $apostas = $this->buscarApostasDoBanco($userId, $loteria, $data);
        
        // Busca resultados
        $resultados = $this->verificador->buscarResultados($loteria, $data);
        
        if (!empty($resultados['erro'])) {
            return ['erro' => $resultados['erro']];
        }
        
        // Verifica cada aposta
        $acertos = [];
        foreach ($apostas as $aposta) {
            $verificacao = $this->verificador->verificarAposta(
                $loteria,
                $data,
                json_decode($aposta['numeros'])
            );
            
            if ($verificacao['total_acertos'] > 0) {
                $acertos[] = $verificacao;
            }
        }
        
        return ['sucesso' => true, 'acertos' => $acertos];
    }
    
    private function buscarApostasDoBanco($userId, $loteria, $data) {
        // Sua lógica de banco de dados
        return [];
    }
}

// Uso
$sistema = new MeuSistema();
$resultado = $sistema->verificarApostasUsuario(123, 'fd', '2026-01-28');
?>
```

---

## ✅ Checklist de Integração

### Opção 1 (Classe Direta):
- [ ] Copiar `verificar_resultados.php` para seu sistema
- [ ] Testar se funciona no seu servidor
- [ ] Integrar no seu código
- [ ] Testar com Federal (`fd`)

### Opção 2 (API HTTP):
- [ ] Subir `verificar_resultados.php` em um servidor
- [ ] Subir `api_resultados.php` no mesmo servidor
- [ ] Testar API: `curl "http://servidor/api_resultados.php?acao=buscar&loteria=fd&data=2026-01-28"`
- [ ] Configurar URL no seu outro sistema
- [ ] Testar integração

---

## 🚀 Resumo Final

**Pergunta:** Posso usar em outro sistema sem subir este projeto?

**Resposta:** 
- ❌ **Não**, se você quiser usar via API HTTP
- ✅ **Sim**, se você copiar `verificar_resultados.php` e usar diretamente (sistema PHP)
- ✅ **Sim**, se você subir apenas os arquivos necessários em um servidor

**Recomendação:** Se seu outro sistema é PHP, use a classe diretamente (Opção 1). É mais simples e não precisa de servidor separado!
