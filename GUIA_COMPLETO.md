# 📚 Guia Completo de Integração - Sistema de Verificação de Resultados

**Sistema completo para integrar verificação de resultados do Jogo do Bicho no seu site.**

---

## 📋 Índice

1. [Visão Geral](#visão-geral)
2. [Início Rápido](#início-rápido-5-minutos)
3. [Integração Básica](#integração-básica)
4. [API e Endpoints](#api-e-endpoints)
5. [Automação Completa](#automação-completa)
6. [Segurança e Deploy](#segurança-e-deploy)
7. [Troubleshooting](#troubleshooting)
8. [Referências](#referências)

---

## 🎯 Visão Geral

### O que este sistema faz?

- ✅ Busca resultados de loterias do bichocerto.com
- ✅ Verifica se números apostados foram sorteados
- ✅ Fornece API REST completa
- ✅ Interface web pronta para usar
- ✅ Automação de verificação de apostas
- ✅ Notificações automáticas de acertos

### Loterias Suportadas

| Código | Nome |
|--------|------|
| `ln` | Loteria Nacional |
| `sp` | PT-SP/Bandeirantes |
| `ba` | PT Bahia |
| `rj` | PT Rio de Janeiro |
| `pb` | PT Paraíba/Lotep |
| `bs` | Boa Sorte Goiás |
| `lce` | Lotece |
| `lk` | Look Goiás |
| `fd` | Loteria Federal |

### Estrutura de Arquivos

```
integracao_web/
├── verificar_resultados.php        ← Classe PHP principal
├── api_resultados.php              ← API REST
├── exemplo_frontend.html           ← Interface web pronta
├── exemplo_jquery.html             ← Exemplo jQuery
├── api_flask.py                    ← API Python (alternativa)
├── visualizar_resultados.html      ← Interface de visualização
│
└── automatico/                     ← Automação
    ├── verificar_automatico.php    ← Script principal
    ├── cron_setup.sh               ← Configurador cron
    ├── worker_background.js        ← Worker Node.js
    ├── exemplo_completo_bd.php     ← Exemplo completo
    └── sql_exemplo.sql             ← Estrutura BD
```

---

## ⚡ Início Rápido (5 Minutos)

### Opção 1: PHP (Mais Comum) ⭐

```php
<?php
require_once 'verificar_resultados.php';

$verificador = new VerificadorResultados();

// Verificar apostas
$apostas = ['2047', '2881', '2289'];
$resultado = $verificador->verificarAposta('ln', '2026-01-17', $apostas);

if ($resultado['total_acertos'] > 0) {
    echo "🎉 Você acertou {$resultado['total_acertos']} número(s)!";
    foreach ($resultado['acertos'] as $acerto) {
        echo "✅ {$acerto['numero']} - {$acerto['posicao']} lugar\n";
    }
}
?>
```

**Pronto!** ✅

### Opção 2: HTML/JavaScript (Frontend)

1. Copie `exemplo_frontend.html` para seu servidor
2. Ajuste a URL da API (se necessário)
3. Abra no navegador!

### Opção 3: Python/Flask (API)

```bash
pip install flask flask-cors
python api_flask.py
```

Depois use no frontend:
```javascript
fetch('http://localhost:5000/api/verificar', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({
        loteria: 'ln',
        data: '2026-01-17',
        numeros: ['2047', '2881']
    })
})
```

---

## 🔧 Integração Básica

### 1. PHP (Backend Completo)

#### Arquivos Necessários:
- `verificar_resultados.php` - Classe principal
- `api_resultados.php` - API REST

#### Uso Básico:

```php
<?php
require_once 'verificar_resultados.php';

$verificador = new VerificadorResultados();

// Buscar resultados
$resultados = $verificador->buscarResultados('ln', '2026-01-17');

if (empty($resultados['erro'])) {
    foreach ($resultados['dados'] as $horario => $extracao) {
        echo "{$extracao['titulo']}: " . count($extracao['premios']) . " prêmios\n";
    }
}

// Verificar apostas
$apostas = ['2047', '2881', '2289'];
$verificacao = $verificador->verificarAposta('ln', '2026-01-17', $apostas);

if ($verificacao['sucesso']) {
    echo "Total de acertos: {$verificacao['total_acertos']}\n";
    foreach ($verificacao['acertos'] as $acerto) {
        echo "✅ {$acerto['numero']} - {$acerto['posicao']} lugar\n";
    }
}
?>
```

#### Com Autenticação (Dados Históricos):

```php
// Para acessar resultados de mais de 10 dias
$verificador = new VerificadorResultados('SEU_PHPSESSID_AQUI');
```

**Como obter PHPSESSID:**
1. Faça login em `https://bichocerto.com`
2. Abra DevTools (F12) → Application → Cookies
3. Copie o valor de `PHPSESSID`

---

### 2. JavaScript (Frontend)

#### Com Fetch API:

```javascript
async function verificarApostas(loteria, data, numeros) {
    const response = await fetch('/api_resultados.php?acao=verificar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            loteria: loteria,
            data: data,
            numeros: numeros
        })
    });
    
    const resultado = await response.json();
    
    if (resultado.sucesso) {
        console.log(`Acertos: ${resultado.total_acertos}`);
        resultado.acertos.forEach(acerto => {
            console.log(`✅ ${acerto.numero} - ${acerto.posicao} lugar`);
        });
    }
}

// Uso
verificarApostas('ln', '2026-01-17', ['2047', '2881', '2289']);
```

#### Com jQuery:

```javascript
$.ajax({
    url: 'api_resultados.php?acao=verificar',
    method: 'POST',
    contentType: 'application/json',
    data: JSON.stringify({
        loteria: 'ln',
        data: '2026-01-17',
        numeros: ['2047', '2881']
    }),
    success: function(resultado) {
        if (resultado.sucesso) {
            $('#resultado').html(`Acertos: ${resultado.total_acertos}`);
        }
    }
});
```

---

### 3. Python/Flask (API Backend)

#### Instalação:

```bash
pip install flask flask-cors
```

#### Executar:

```bash
python api_flask.py
```

#### Endpoints:

```
GET  http://localhost:5000/api/resultados?loteria=ln&data=2026-01-17
POST http://localhost:5000/api/verificar
```

#### Exemplo de Uso:

```python
import requests

# Buscar resultados
response = requests.get('http://localhost:5000/api/resultados', params={
    'loteria': 'ln',
    'data': '2026-01-17'
})
print(response.json())

# Verificar apostas
response = requests.post('http://localhost:5000/api/verificar', json={
    'loteria': 'ln',
    'data': '2026-01-17',
    'numeros': ['2047', '2881']
})
print(response.json())
```

---

### 4. Exemplo Completo: Página de Verificação

Veja o arquivo `exemplo_frontend.html` - interface completa e pronta para usar.

**Recursos:**
- ✅ Interface moderna e responsiva
- ✅ Seleção de loteria
- ✅ Input de números
- ✅ Exibição de acertos
- ✅ Tratamento de erros

---

## 🔌 API e Endpoints

### PHP REST API (`api_resultados.php`)

#### Buscar Resultados:

```bash
GET /api_resultados.php?acao=buscar&loteria=ln&data=2026-01-17
```

**Resposta:**
```json
{
  "erro": null,
  "dados": {
    "23": {
      "titulo": "Resultado Nacional 23h",
      "horario": "23",
      "premios": [
        {
          "numero": "2047",
          "grupo": "12",
          "animal": "Elefante"
        }
      ]
    }
  }
}
```

#### Verificar Apostas:

```bash
POST /api_resultados.php?acao=verificar
Content-Type: application/json

{
    "loteria": "ln",
    "data": "2026-01-17",
    "numeros": ["2047", "2881", "2289"]
}
```

**Resposta:**
```json
{
  "sucesso": true,
  "data": "2026-01-17",
  "loteria": "ln",
  "total_apostado": 3,
  "total_acertos": 2,
  "acertos": [
    {
      "numero": "2047",
      "horario": "Resultado Nacional 23h",
      "posicao": "1º",
      "animal": "Elefante",
      "grupo": "12"
    }
  ]
}
```

---

## 🤖 Automação Completa

### O que é?

Sistema para verificar automaticamente todas as apostas dos seus usuários em intervalos regulares, sem intervenção manual.

### ✅ Vantagens:

- ⚡ Verificação automática 24/7
- 📧 Notificações automáticas de acertos
- 💾 Atualização automática no banco de dados
- 📊 Logs detalhados de todas as verificações

---

### 🚀 Início Rápido (3 Passos)

#### 1️⃣ Configurar Banco de Dados

Edite `automatico/verificar_automatico.php`:

```php
// Configure sua conexão com banco
$pdo = new PDO('mysql:host=localhost;dbname=SEU_BANCO', 'USUARIO', 'SENHA');
```

#### 2️⃣ Testar Manualmente

```bash
cd automatico
php verificar_automatico.php
```

#### 3️⃣ Configurar Automação

**Opção A: Cron Job (Recomendado)**

```bash
bash cron_setup.sh
```

O script pergunta o intervalo e configura automaticamente.

**Opção B: Manual**

```bash
crontab -e
```

Adicione (ajuste o caminho):

```bash
# Verificar a cada 15 minutos
*/15 * * * * /usr/bin/php /caminho/verificar_automatico.php
```

---

### 📁 Arquivos Disponíveis

Na pasta `automatico/`:

| Arquivo | Descrição |
|---------|-----------|
| `verificar_automatico.php` | Script PHP principal - **Comece aqui** |
| `cron_setup.sh` | Configurador automático de cron |
| `exemplo_completo_bd.php` | Exemplo completo com banco de dados |
| `sql_exemplo.sql` | Estrutura de tabelas SQL |
| `worker_background.js` | Worker Node.js (alternativa) |
| `INICIO_RAPIDO.md` | Guia rápido (3 passos) |
| `README_AUTOMATICO.md` | Documentação completa |

---

### 🔧 Como Funciona

1. **Busca Apostas Pendentes** do banco de dados
2. **Verifica Cada Aposta** nos resultados das loterias
3. **Atualiza Banco** com resultados e acertos
4. **Cria Notificações** para usuários que acertaram
5. **Registra Logs** de todas as operações

---

### ⏰ Intervalos Recomendados

```bash
*/5 * * * *   # A cada 5 minutos (durante horários de sorteio: 18h-23h)
*/15 * * * *  # A cada 15 minutos (padrão)
0 * * * *     # A cada hora
0 */6 * * *   # A cada 6 horas
0 0 * * *     # Uma vez por dia (meia-noite)
```

**Recomendação**: A cada 15 minutos durante horários de sorteio (18h-23h).

---

### 📊 Estrutura de Banco de Dados

Veja `automatico/sql_exemplo.sql` para criar as tabelas:

```sql
-- Tabela de apostas
CREATE TABLE apostas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    loteria VARCHAR(10),
    data_aposta DATE,
    numeros JSON,
    verificada BOOLEAN DEFAULT FALSE,
    acertos INT DEFAULT 0,
    resultado JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de notificações
CREATE TABLE notificacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    mensagem TEXT,
    dados JSON,
    lida BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

**Campos importantes em `apostas`:**
- `verificada` (0/1) - Se já foi verificada
- `total_acertos` - Quantidade de acertos
- `resultado_json` - Resultado completo da verificação

---

### 🔧 Adaptar ao Seu Sistema

#### 1. Buscar Apostas do Banco

**Exemplo PDO:**
```php
function buscarApostasPendentes() {
    $pdo = new PDO('mysql:host=localhost;dbname=seu_banco', 'user', 'pass');
    $stmt = $pdo->prepare("
        SELECT id, user_id, loteria, data_aposta, numeros 
        FROM apostas 
        WHERE verificada = 0 
        AND data_aposta <= CURDATE()
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
```

#### 2. Atualizar no Banco

```php
function atualizarAposta($apostaId, $resultado) {
    $pdo = new PDO('mysql:host=localhost;dbname=seu_banco', 'user', 'pass');
    $stmt = $pdo->prepare("
        UPDATE apostas 
        SET verificada = 1, 
            acertos = :acertos,
            resultado = :resultado
        WHERE id = :id
    ");
    return $stmt->execute([
        ':id' => $apostaId,
        ':acertos' => $resultado['total_acertos'],
        ':resultado' => json_encode($resultado)
    ]);
}
```

#### 3. Notificar Usuário

```php
function notificarUsuario($userId, $resultado) {
    // Opção 1: Salvar no banco
    $pdo = new PDO('mysql:host=localhost;dbname=seu_banco', 'user', 'pass');
    $stmt = $pdo->prepare("
        INSERT INTO notificacoes (user_id, mensagem, dados)
        VALUES (:user_id, :mensagem, :dados)
    ");
    $stmt->execute([
        ':user_id' => $userId,
        ':mensagem' => "Você acertou {$resultado['total_acertos']} número(s)!",
        ':dados' => json_encode($resultado)
    ]);
    
    // Opção 2: Enviar email
    $email = buscarEmailUsuario($userId);
    mail($email, "🎉 Você acertou!", $mensagem);
    
    // Opção 3: Webhook/API
    file_get_contents("https://seu-servidor.com/api/notificar?user={$userId}");
}
```

---

### 🔍 Monitoramento

#### Ver Logs:

```bash
# PHP
tail -f automatico/logs/verificacao_*.log

# Node.js (PM2)
pm2 logs verificador-apostas
```

#### Verificar Cron:

```bash
# Ver crontab configurado
crontab -l

# Testar manualmente
cd automatico
php verificar_automatico.php
```

---

### 🎯 Exemplo de Integração

```php
<?php
// No seu sistema quando usuário faz aposta

// 1. Salvar aposta no banco
$stmt = $pdo->prepare("
    INSERT INTO apostas (user_id, loteria, data_aposta, numeros)
    VALUES (:user_id, :loteria, :data, :numeros)
");
$stmt->execute([
    ':user_id' => $userId,
    ':loteria' => 'ln',
    ':data' => date('Y-m-d'),
    ':numeros' => json_encode(['2047', '2881', '2289'])
]);

// 2. O script automático verifica depois automaticamente!
// 3. Notificação aparece para o usuário quando houver acerto
?>
```

---

## 🔐 Segurança e Deploy

### 1. Rate Limiting

```php
// Limitar requisições por IP
session_start();

if (!isset($_SESSION['req_count'])) {
    $_SESSION['req_count'] = 0;
    $_SESSION['req_time'] = time();
}

if (time() - $_SESSION['req_time'] > 60) {
    $_SESSION['req_count'] = 0;
    $_SESSION['req_time'] = time();
}

if ($_SESSION['req_count'] > 60) { // 60 req/min
    http_response_code(429);
    die(json_encode(['erro' => 'Muitas requisições']));
}

$_SESSION['req_count']++;
```

### 2. Autenticação

```php
// Verificar se usuário está autenticado
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die(json_encode(['erro' => 'Não autenticado']));
}
```

### 3. Validação de Dados

```php
// Validar dados recebidos
$loteria = filter_var($_GET['loteria'], FILTER_SANITIZE_STRING);
$data = filter_var($_GET['data'], FILTER_VALIDATE_REGEXP, [
    'options' => ['regexp' => '/^\d{4}-\d{2}-\d{2}$/']
]);

if (!$data || !in_array($loteria, ['ln', 'sp', 'ba', 'pb', 'bs', 'lce', 'lk', 'fd'])) {
    http_response_code(400);
    die(json_encode(['erro' => 'Dados inválidos']));
}
```

### 4. Proteger Scripts Automáticos

```php
// Em verificar_automatico.php
define('TOKEN_SEGURO', 'seu_token_secreto');
define('ENABLE_WEB_ACCESS', false); // Desabilitar acesso web
```

### 5. Deploy PHP (Apache/Nginx)

1. Copie os arquivos PHP para seu servidor
2. Configure permissões
3. Teste os endpoints

### 6. Deploy Flask

```bash
# Produção com Gunicorn
pip install gunicorn
gunicorn -w 4 -b 0.0.0.0:5000 api_flask:app
```

---

## 🚨 Troubleshooting

### Erro: "ERR_CONNECTION_REFUSED"

**Problema**: Servidor web não está rodando.

**Solução**:
```bash
cd integracao_web
php -S localhost:8000
```

Depois acesse: `http://localhost:8000/visualizar_resultados.html`

Veja `INICIAR_SERVIDOR.md` para mais opções.

---

### Cron não executa

**Problema**: Script não roda automaticamente.

**Soluções**:
```bash
# Usar caminho completo do PHP
which php
# Use o caminho completo no crontab:
/usr/bin/php /caminho/verificar_automatico.php

# Verificar permissões
chmod +x verificar_automatico.php

# Testar manualmente primeiro
php verificar_automatico.php
```

---

### Erros de conexão

**Problema**: Não consegue conectar ao banco ou API.

**Soluções**:
- Verifique usuário/senha do banco
- Teste conexão manualmente
- Verifique firewall
- Teste com `curl` manualmente

---

### Worker para de funcionar

**Problema**: Worker Node.js para de executar.

**Soluções**:
```bash
# Verificar PM2
pm2 status
pm2 logs verificador-apostas

# Reiniciar
pm2 restart verificador-apostas

# Verificar memória
pm2 monit
```

---

### PHP: command not found

**Problema**: PHP não está instalado ou não está no PATH.

**Soluções**:
- Instale o PHP: `brew install php` (macOS) ou `apt install php` (Linux)
- Use outro método (Python/Node.js)
- Use caminho completo: `/usr/bin/php`

---

### Porta 8000 já está em uso

**Problema**: Outro processo está usando a porta.

**Solução**: Use outra porta:
```bash
php -S localhost:8080
```

Depois acesse: `http://localhost:8080/visualizar_resultados.html`

---

## 📊 Exemplos de Integração

### Cenário 1: Usuário tem conta no seu site

```php
<?php
// No seu sistema de apostas

// Quando o usuário faz login
session_start();

// Buscar apostas do usuário do banco de dados
$apostasUsuario = buscarApostasDoUsuario($_SESSION['user_id']);

// Verificar cada aposta
foreach ($apostasUsuario as $aposta) {
    $verificador = new VerificadorResultados();
    $resultado = $verificador->verificarAposta(
        $aposta['loteria'],
        $aposta['data_aposta'],
        json_decode($aposta['numeros'])
    );
    
    if ($resultado['total_acertos'] > 0) {
        // Marcar aposta como verificada
        marcarApostaVerificada($aposta['id'], $resultado);
        
        // Notificar usuário
        enviarNotificacao($aposta['user_id'], $resultado);
    }
}
?>
```

### Cenário 2: Verificação em tempo real

```javascript
// Verifica automaticamente a cada 5 minutos
setInterval(async () => {
    const apostas = await buscarApostasPendentes();
    
    for (const aposta of apostas) {
        const resultado = await verificarAposta(aposta);
        
        if (resultado.total_acertos > 0) {
            // Atualizar no banco
            await atualizarAposta(aposta.id, resultado);
            
            // Notificar usuário (WebSocket, email, etc)
            notificarUsuario(aposta.user_id, resultado);
        }
    }
}, 5 * 60 * 1000); // 5 minutos
```

### Cenário 3: Widget de Resultados

```html
<!-- Widget para mostrar resultados ao vivo -->
<div id="widget-resultados">
    <h3>Resultados de Hoje</h3>
    <div id="lista-resultados"></div>
</div>

<script>
async function carregarResultados(loteria) {
    const hoje = new Date().toISOString().split('T')[0];
    const response = await fetch(`api_resultados.php?acao=buscar&loteria=${loteria}&data=${hoje}`);
    const dados = await response.json();
    
    if (!dados.erro) {
        let html = '';
        Object.values(dados.dados).forEach(extracao => {
            html += `<div class="extracao">
                <h4>${extracao.titulo}</h4>
                ${extracao.premios.slice(0, 5).map(p => 
                    `<span>${p.numero}</span>`
                ).join('')}
            </div>`;
        });
        document.getElementById('lista-resultados').innerHTML = html;
    }
}

// Carrega a cada 1 minuto
setInterval(() => carregarResultados('ln'), 60000);
carregarResultados('ln');
</script>
```

---

## 📈 Otimizações

### 1. Verificar apenas quando necessário

```php
// Verificar apenas em horários de sorteio
$hora = (int)date('H');
$horariosSorteio = [18, 19, 20, 21, 22, 23];

if (!in_array($hora, $horariosSorteio)) {
    logMessage("⏸️  Fora do horário de sorteio");
    exit(0);
}
```

### 2. Cache de resultados

```php
// Verificar cache antes de buscar
$cacheFile = "cache/{$loteria}_{$data}.json";
if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < 3600) {
    return json_decode(file_get_contents($cacheFile), true);
}
```

### 3. Limitar quantidade por execução

```php
// Verificar apenas últimas 100 apostas por vez
$stmt = $pdo->prepare("... LIMIT 100");
```

---

## 📝 Notas Importantes

1. **Respeite os termos de uso** do site bichocerto.com
2. **Use rate limiting** para não sobrecarregar o servidor
3. **Cache resultados** quando possível
4. **Monitore logs** regularmente
5. **Teste sempre** antes de colocar em produção
6. **Dados históricos** (mais de 10 dias) requerem autenticação com `PHPSESSID`

---

## 🔗 Referências

### Documentação Adicional:

- **`endpoints-loterias.md`** - Documentação completa dos endpoints
- **`ESTRUTURA_HTML_ANALISADA.md`** - Análise da estrutura HTML
- **`EXPLICACAO_HTML_RETORNADO.md`** - Explicação do HTML retornado
- **`INICIAR_SERVIDOR.md`** - Como iniciar servidor local
- **`README_VERIFICACAO.md`** - Guia de uso completo (Python)

### Arquivos de Exemplo:

- **`exemplo_frontend.html`** - Interface web completa
- **`exemplo_jquery.html`** - Exemplo com jQuery
- **`visualizar_resultados.html`** - Interface de visualização
- **`automatico/exemplo_completo_bd.php`** - Exemplo completo com BD

---

## 🎉 Pronto para Começar?

1. ✅ Escolha sua tecnologia (PHP, JS, Python)
2. ✅ Siga o início rápido
3. ✅ Teste em ambiente de desenvolvimento
4. ✅ Configure automação (se necessário)
5. ✅ Deploy em produção!

**Boa sorte! 🚀**

---

## 📞 Suporte

### Por Tarefa:

| Tarefa | Documento |
|--------|-----------|
| Começar agora | Seção "Início Rápido" |
| Entender tudo | Este guia completo |
| Automação | Seção "Automação Completa" |
| Exemplos código | Arquivos `exemplo_*.php`, `exemplo_*.html` |

### Por Tecnologia:

- **PHP**: `verificar_resultados.php` + Seção "Integração Básica"
- **JavaScript**: `exemplo_frontend.html` + Seção "Integração Básica"
- **Python**: `api_flask.py` + Seção "Integração Básica"
- **Automação**: `automatico/` (toda a pasta) + Seção "Automação Completa"

---

**Última atualização**: Janeiro 2026
