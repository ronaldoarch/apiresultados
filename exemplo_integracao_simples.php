<?php
/**
 * Exemplo Simples de Integração com a API de Resultados
 * 
 * Este arquivo mostra como buscar resultados de qualquer loteria,
 * incluindo a Federal, sem erros.
 */

// ============================================
// CONFIGURAÇÃO
// ============================================

// URL da sua API (ajuste conforme necessário)
define('API_URL', 'http://seuservidor.com/api_resultados.php');

// Ou se estiver no mesmo servidor:
// define('API_URL', '/api_resultados.php');

// ============================================
// FUNÇÃO PRINCIPAL: Buscar Resultados
// ============================================

/**
 * Busca resultados de uma loteria em uma data específica
 * 
 * @param string $loteria Código da loteria (ln, fd, sp, bs, lce, lk, pb, m)
 * @param string $data Data no formato YYYY-MM-DD
 * @return array Resultado da busca
 */
function buscarResultados($loteria, $data) {
    // Monta URL da requisição
    $url = API_URL . '?acao=buscar&loteria=' . urlencode($loteria) . '&data=' . urlencode($data);
    
    // Inicializa cURL
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false, // Ajuste conforme necessário
    ]);
    
    // Executa requisição
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    // Verifica erros de conexão
    if ($response === false || !empty($curlError)) {
        return [
            'erro' => 'Erro de conexão: ' . $curlError,
            'dados' => []
        ];
    }
    
    // Verifica código HTTP
    if ($httpCode !== 200) {
        return [
            'erro' => "Erro HTTP: {$httpCode}",
            'dados' => []
        ];
    }
    
    // Decodifica JSON
    $resultado = json_decode($response, true);
    
    // Verifica se JSON é válido
    if (json_last_error() !== JSON_ERROR_NONE) {
        return [
            'erro' => 'Resposta inválida da API',
            'dados' => []
        ];
    }
    
    return $resultado;
}

// ============================================
// EXEMPLOS DE USO
// ============================================

// Exemplo 1: Buscar Federal
echo "=== Exemplo 1: Buscar Federal ===\n";
$resultado = buscarResultados('fd', '2026-01-28');

if (empty($resultado['erro'])) {
    foreach ($resultado['dados'] as $horario => $extracao) {
        echo "\n{$extracao['titulo']}:\n";
        foreach ($extracao['premios'] as $index => $premio) {
            $posicao = $index + 1;
            echo "  {$posicao}º: {$premio['numero']} - {$premio['animal']} (Grupo {$premio['grupo']})\n";
        }
    }
} else {
    echo "Erro: {$resultado['erro']}\n";
}

echo "\n";

// Exemplo 2: Buscar Nacional
echo "=== Exemplo 2: Buscar Nacional ===\n";
$resultado = buscarResultados('ln', date('Y-m-d'));

if (empty($resultado['erro'])) {
    echo "Total de extrações: " . count($resultado['dados']) . "\n";
    foreach ($resultado['dados'] as $horario => $extracao) {
        echo "  - {$extracao['titulo']}: " . count($extracao['premios']) . " prêmios\n";
    }
} else {
    echo "Erro: {$resultado['erro']}\n";
}

echo "\n";

// Exemplo 3: Função com tratamento completo de erros
function buscarResultadosCompleto($loteria, $data) {
    $resultado = buscarResultados($loteria, $data);
    
    if (!empty($resultado['erro'])) {
        // Tratamento específico de erros
        switch ($resultado['erro']) {
            case 'Sem resultados para esta data':
                if ($loteria === 'fd') {
                    return [
                        'sucesso' => false,
                        'mensagem' => 'A Federal só sorteia às quartas e sábados às 18:50',
                        'dados' => []
                    ];
                }
                return [
                    'sucesso' => false,
                    'mensagem' => 'Não há resultados para esta data',
                    'dados' => []
                ];
                
            case 'Data fora do intervalo permitido':
                return [
                    'sucesso' => false,
                    'mensagem' => 'Data muito antiga. Configure autenticação para acessar dados históricos',
                    'dados' => []
                ];
                
            default:
                return [
                    'sucesso' => false,
                    'mensagem' => $resultado['erro'],
                    'dados' => []
                ];
        }
    }
    
    return [
        'sucesso' => true,
        'mensagem' => 'Resultados encontrados',
        'dados' => $resultado['dados']
    ];
}

// Exemplo 4: Verificar se número foi sorteado
function verificarNumeroSorteado($loteria, $data, $numero) {
    $resultado = buscarResultados($loteria, $data);
    
    if (!empty($resultado['erro'])) {
        return [
            'encontrado' => false,
            'erro' => $resultado['erro']
        ];
    }
    
    // Normaliza número (remove zeros à esquerda para comparação)
    $numeroNormalizado = ltrim($numero, '0');
    if (empty($numeroNormalizado)) {
        $numeroNormalizado = '0';
    }
    
    // Busca em todas as extrações
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
        'mensagem' => 'Número não encontrado nos resultados'
    ];
}

// Teste: Verificar se número foi sorteado
echo "=== Exemplo 4: Verificar Número ===\n";
$verificacao = verificarNumeroSorteado('fd', '2026-01-28', '09593');
if ($verificacao['encontrado']) {
    echo "✅ Número {$verificacao['numero']} encontrado!\n";
    echo "   Posição: {$verificacao['posicao']}\n";
    echo "   Animal: {$verificacao['animal']}\n";
    echo "   Grupo: {$verificacao['grupo']}\n";
    echo "   Horário: {$verificacao['horario']}\n";
} else {
    echo "❌ Número não encontrado\n";
    if (isset($verificacao['erro'])) {
        echo "   Erro: {$verificacao['erro']}\n";
    }
}

echo "\n";

// ============================================
// EXEMPLO PARA USAR EM SEU SISTEMA
// ============================================

/**
 * Exemplo de como integrar em seu sistema existente
 */
class IntegracaoResultados {
    private $apiUrl;
    
    public function __construct($apiUrl) {
        $this->apiUrl = $apiUrl;
    }
    
    /**
     * Busca resultados e retorna em formato padronizado
     */
    public function obterResultados($loteria, $data) {
        $resultado = buscarResultados($loteria, $data);
        
        if (!empty($resultado['erro'])) {
            return [
                'sucesso' => false,
                'erro' => $resultado['erro']
            ];
        }
        
        // Formata resultado para seu formato
        $formatado = [];
        foreach ($resultado['dados'] as $horario => $extracao) {
            $formatado[] = [
                'titulo' => $extracao['titulo'],
                'horario' => $extracao['horario'],
                'total_premios' => count($extracao['premios']),
                'premios' => $extracao['premios']
            ];
        }
        
        return [
            'sucesso' => true,
            'loteria' => $loteria,
            'data' => $data,
            'extracoes' => $formatado
        ];
    }
    
    /**
     * Verifica múltiplos números de uma vez
     */
    public function verificarNumeros($loteria, $data, $numeros) {
        $resultado = buscarResultados($loteria, $data);
        
        if (!empty($resultado['erro'])) {
            return [
                'sucesso' => false,
                'erro' => $resultado['erro']
            ];
        }
        
        $acertos = [];
        foreach ($numeros as $numero) {
            $verificacao = verificarNumeroSorteado($loteria, $data, $numero);
            if ($verificacao['encontrado']) {
                $acertos[] = $verificacao;
            }
        }
        
        return [
            'sucesso' => true,
            'total_verificados' => count($numeros),
            'total_acertos' => count($acertos),
            'acertos' => $acertos
        ];
    }
}

// Uso da classe
echo "=== Exemplo com Classe ===\n";
$integracao = new IntegracaoResultados(API_URL);
$resultado = $integracao->obterResultados('fd', '2026-01-28');

if ($resultado['sucesso']) {
    echo "✅ Resultados obtidos com sucesso!\n";
    echo "Total de extrações: " . count($resultado['extracoes']) . "\n";
} else {
    echo "❌ Erro: {$resultado['erro']}\n";
}

?>
