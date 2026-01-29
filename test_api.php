<?php
/**
 * Script de teste para verificar se a API está funcionando corretamente
 * Acesse: /test_api.php
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste da API</title>
    <style>
        body {
            font-family: monospace;
            padding: 20px;
            background: #f5f5f5;
        }
        .test {
            background: white;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            border-left: 4px solid #007bff;
        }
        .success { border-left-color: #28a745; }
        .error { border-left-color: #dc3545; }
        .warning { border-left-color: #ffc107; }
        h1 { color: #333; }
        pre {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 3px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <h1>🔍 Teste da API - Diagnóstico</h1>
    
    <?php
    $tests = [];
    
    // Teste 1: Verificar PHP
    $tests[] = [
        'name' => 'Versão do PHP',
        'status' => version_compare(PHP_VERSION, '7.4.0', '>=') ? 'success' : 'error',
        'message' => PHP_VERSION . (version_compare(PHP_VERSION, '7.4.0', '>=') ? ' ✅' : ' ❌ (Requer PHP 7.4+)')
    ];
    
    // Teste 2: Verificar cURL
    $tests[] = [
        'name' => 'Extensão cURL',
        'status' => function_exists('curl_init') ? 'success' : 'error',
        'message' => function_exists('curl_init') ? 'Disponível ✅' : 'NÃO DISPONÍVEL ❌'
    ];
    
    // Teste 3: Verificar DOM
    $tests[] = [
        'name' => 'Extensão DOM',
        'status' => class_exists('DOMDocument') ? 'success' : 'error',
        'message' => class_exists('DOMDocument') ? 'Disponível ✅' : 'NÃO DISPONÍVEL ❌'
    ];
    
    // Teste 4: Verificar arquivos
    $tests[] = [
        'name' => 'Arquivo verificar_resultados.php',
        'status' => file_exists(__DIR__ . '/verificar_resultados.php') ? 'success' : 'error',
        'message' => file_exists(__DIR__ . '/verificar_resultados.php') ? 'Encontrado ✅' : 'NÃO ENCONTRADO ❌'
    ];
    
    // Teste 5: Testar conexão com bichocerto.com
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://bichocerto.com',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_FOLLOWLOCATION => true
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        $tests[] = [
            'name' => 'Conexão com bichocerto.com',
            'status' => ($httpCode === 200 && !empty($response)) ? 'success' : 'error',
            'message' => ($httpCode === 200 && !empty($response)) 
                ? "Conectado ✅ (HTTP {$httpCode})" 
                : "Falhou ❌ (HTTP {$httpCode})" . ($curlError ? " - {$curlError}" : "")
        ];
    } else {
        $tests[] = [
            'name' => 'Conexão com bichocerto.com',
            'status' => 'warning',
            'message' => 'Não testado (cURL não disponível)'
        ];
    }
    
    // Teste 6: Testar classe VerificadorResultados
    if (file_exists(__DIR__ . '/verificar_resultados.php')) {
        require_once __DIR__ . '/verificar_resultados.php';
        
        try {
            $verificador = new VerificadorResultados();
            $resultado = $verificador->buscarResultados('ln', date('Y-m-d'));
            
            $tests[] = [
                'name' => 'Teste de busca (Loteria Nacional)',
                'status' => empty($resultado['erro']) ? 'success' : 'error',
                'message' => empty($resultado['erro']) 
                    ? 'Funcionando ✅' 
                    : 'Erro: ' . $resultado['erro']
            ];
        } catch (Exception $e) {
            $tests[] = [
                'name' => 'Teste de busca (Loteria Nacional)',
                'status' => 'error',
                'message' => 'Exceção: ' . $e->getMessage()
            ];
        }
    }
    
    // Exibir resultados
    foreach ($tests as $test) {
        echo '<div class="test ' . $test['status'] . '">';
        echo '<strong>' . $test['name'] . ':</strong> ' . $test['message'];
        echo '</div>';
    }
    ?>
    
    <div class="test">
        <h3>📋 Informações do Sistema</h3>
        <pre><?php
        echo "PHP Version: " . PHP_VERSION . "\n";
        echo "Server API: " . php_sapi_name() . "\n";
        echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
        echo "Script Filename: " . __FILE__ . "\n";
        echo "\nExtensões carregadas:\n";
        $extensions = ['curl', 'dom', 'xml', 'libxml', 'simplexml'];
        foreach ($extensions as $ext) {
            echo "  - $ext: " . (extension_loaded($ext) ? '✅' : '❌') . "\n";
        }
        ?></pre>
    </div>
    
    <div class="test">
        <h3>🔗 Links Úteis</h3>
        <p><a href="api_resultados.php">API Resultados</a></p>
        <p><a href="api_resultados.php?acao=buscar&loteria=ln&data=<?php echo date('Y-m-d'); ?>">Testar API (GET)</a></p>
        <p><a href="index.php">Página Inicial</a></p>
    </div>
</body>
</html>
