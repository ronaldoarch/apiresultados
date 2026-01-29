<?php
/**
 * Classe para Integração com API de Resultados
 * 
 * Uso:
 *   $api = new ApiResultados();
 *   $resultado = $api->buscar('fd', '2026-01-28');
 */

class ApiResultados {
    private $apiUrl;
    
    public function __construct($apiUrl = null) {
        // URL padrão da sua API (pode ser alterada)
        $this->apiUrl = $apiUrl ?? 'https://rk48ccsoo8kcooc00wwwog04.agenciamidas.com/api_resultados.php';
    }
    
    /**
     * Busca resultados de uma loteria em uma data específica
     * 
     * @param string $loteria Código da loteria (fd, ln, sp, bs, lce, lk, pb, m)
     * @param string $data Data no formato YYYY-MM-DD
     * @return array ['sucesso' => bool, 'dados' => array, 'erro' => string|null]
     */
    public function buscar($loteria, $data) {
        $url = $this->apiUrl . '?acao=buscar&loteria=' . urlencode($loteria) . '&data=' . urlencode($data);
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json'
            ]
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($response === false || !empty($error)) {
            return [
                'sucesso' => false,
                'erro' => 'Erro de conexão: ' . ($error ?: 'Falha na conexão'),
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
                'erro' => 'Resposta inválida da API: ' . json_last_error_msg(),
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
            'dados' => $resultado['dados'] ?? [],
            'erro' => null
        ];
    }
    
    /**
     * Verifica se um número foi sorteado
     * 
     * @param string $loteria Código da loteria
     * @param string $data Data no formato YYYY-MM-DD
     * @param string $numero Número a verificar
     * @return array ['encontrado' => bool, 'numero' => string, 'posicao' => string, 'animal' => string, 'grupo' => string, 'horario' => string, 'erro' => string|null]
     */
    public function verificarNumero($loteria, $data, $numero) {
        $resultado = $this->buscar($loteria, $data);
        
        if (!$resultado['sucesso']) {
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
                        'horario' => $extracao['titulo'],
                        'erro' => null
                    ];
                }
            }
        }
        
        return [
            'encontrado' => false,
            'mensagem' => 'Número não encontrado nos resultados',
            'erro' => null
        ];
    }
    
    /**
     * Verifica múltiplos números de uma vez
     * 
     * @param string $loteria Código da loteria
     * @param string $data Data no formato YYYY-MM-DD
     * @param array $numeros Array de números para verificar
     * @return array ['sucesso' => bool, 'total_verificados' => int, 'total_acertos' => int, 'acertos' => array, 'erro' => string|null]
     */
    public function verificarNumeros($loteria, $data, $numeros) {
        $resultado = $this->buscar($loteria, $data);
        
        if (!$resultado['sucesso']) {
            return [
                'sucesso' => false,
                'total_verificados' => 0,
                'total_acertos' => 0,
                'acertos' => [],
                'erro' => $resultado['erro']
            ];
        }
        
        $acertos = [];
        foreach ($numeros as $numero) {
            $verificacao = $this->verificarNumero($loteria, $data, $numero);
            if ($verificacao['encontrado']) {
                $acertos[] = $verificacao;
            }
        }
        
        return [
            'sucesso' => true,
            'total_verificados' => count($numeros),
            'total_acertos' => count($acertos),
            'acertos' => $acertos,
            'erro' => null
        ];
    }
    
    /**
     * Obtém todos os números sorteados em formato simples
     * 
     * @param string $loteria Código da loteria
     * @param string $data Data no formato YYYY-MM-DD
     * @return array ['sucesso' => bool, 'numeros' => array, 'erro' => string|null]
     */
    public function obterNumerosSorteados($loteria, $data) {
        $resultado = $this->buscar($loteria, $data);
        
        if (!$resultado['sucesso']) {
            return [
                'sucesso' => false,
                'numeros' => [],
                'erro' => $resultado['erro']
            ];
        }
        
        $numeros = [];
        foreach ($resultado['dados'] as $horario => $extracao) {
            foreach ($extracao['premios'] as $premio) {
                $numeros[] = [
                    'numero' => $premio['numero'],
                    'animal' => $premio['animal'],
                    'grupo' => $premio['grupo'],
                    'horario' => $extracao['horario']
                ];
            }
        }
        
        return [
            'sucesso' => true,
            'numeros' => $numeros,
            'erro' => null
        ];
    }
}
