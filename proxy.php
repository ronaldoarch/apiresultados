<?php
/**
 * Proxy Simples para Contornar Bloqueio Cloudflare
 * 
 * Coloque este arquivo em OUTRO servidor (com IP diferente)
 * e configure a URL como PROXY_URL no Coolify
 */

header('Content-Type: text/html; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');

// Obter parâmetros
$loteria = $_GET['loteria'] ?? $_POST['loteria'] ?? 'ln';
$data = $_GET['data'] ?? $_POST['data'] ?? date('Y-m-d');

// URL do bichocerto.com
$url = 'https://bichocerto.com/resultados/base/resultado/';
$postData = http_build_query([
    'l' => $loteria,
    'd' => $data
]);

// Headers realistas
$headers = [
    'Content-Type: application/x-www-form-urlencoded',
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Language: pt-BR,pt;q=0.9',
    'Referer: https://bichocerto.com/'
];

// Fazer requisição
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $postData,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_HTTPHEADER => $headers
]);

$html = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// Verifica erros
if ($html === false || !empty($curlError)) {
    header('Content-Type: application/json');
    echo json_encode([
        'erro' => 'Erro de conexão: ' . ($curlError ?: 'Falha ao conectar'),
        'dados' => []
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($httpCode !== 200) {
    header('Content-Type: application/json');
    echo json_encode([
        'erro' => "Erro HTTP {$httpCode}",
        'dados' => []
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Retorna HTML para o servidor principal fazer parse
echo $html;
