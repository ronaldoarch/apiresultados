<?php
/**
 * API REST para verificar resultados
 * Endpoints:
 * GET /api_resultados.php?loteria=ln&data=2026-01-17
 * POST /api_resultados.php/verificar
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *'); // Ajuste conforme necessário
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'verificar_resultados.php';

// Configuração
// Tenta obter PHPSESSID de variável de ambiente (para Coolify/Docker) ou configuração direta
$phpsessid = $_ENV['PHPSESSID'] ?? getenv('PHPSESSID') ?? null;

// Tenta obter PROXY_URL de variável de ambiente (para contornar bloqueio Cloudflare)
$proxyUrl = $_ENV['PROXY_URL'] ?? getenv('PROXY_URL') ?? null;

// Se não encontrou em variável de ambiente, pode configurar diretamente aqui:
// $phpsessid = '45c16270330406d85326a05c4058334b';
// $proxyUrl = 'https://outro-servidor.com'; // URL do servidor proxy (sem /proxy.php)

$verificador = new VerificadorResultados($phpsessid, $proxyUrl);
$metodo = $_SERVER['REQUEST_METHOD'];
$acao = $_GET['acao'] ?? '';

// Rotas
if ($metodo === 'OPTIONS') {
    http_response_code(200);
    exit;
}

switch ($acao) {
    case 'buscar':
        // GET /api_resultados.php?acao=buscar&loteria=ln&data=2026-01-17
        $loteria = $_GET['loteria'] ?? 'ln';
        $data = $_GET['data'] ?? date('Y-m-d');
        
        $resultado = $verificador->buscarResultados($loteria, $data);
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        break;
        
    case 'verificar':
        // POST /api_resultados.php?acao=verificar
        $dados = json_decode(file_get_contents('php://input'), true);
        
        $loteria = $dados['loteria'] ?? 'ln';
        $data = $dados['data'] ?? date('Y-m-d');
        $numeros = $dados['numeros'] ?? [];
        
        if (empty($numeros)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Números não fornecidos'], JSON_UNESCAPED_UNICODE);
            break;
        }
        
        $resultado = $verificador->verificarAposta($loteria, $data, $numeros);
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        break;
        
    default:
        // Lista de endpoints disponíveis
        echo json_encode([
            'endpoints' => [
                'GET /api_resultados.php?acao=buscar&loteria=ln&data=2026-01-17' => 'Busca resultados',
                'POST /api_resultados.php?acao=verificar' => 'Verifica apostas',
            ],
            'exemplo_buscar' => [
                'url' => '/api_resultados.php?acao=buscar&loteria=ln&data=2026-01-17',
                'method' => 'GET'
            ],
            'exemplo_verificar' => [
                'url' => '/api_resultados.php?acao=verificar',
                'method' => 'POST',
                'body' => [
                    'loteria' => 'ln',
                    'data' => '2026-01-17',
                    'numeros' => ['2047', '2881']
                ]
            ]
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}

?>
