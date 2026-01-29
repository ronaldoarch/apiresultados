<?php
/**
 * Script para testar se PHPSESSID está sendo lido corretamente
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Teste PHPSESSID</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .test { background: white; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #007bff; }
        .success { border-left-color: #28a745; }
        .error { border-left-color: #dc3545; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>🔍 Teste de PHPSESSID</h1>
    
    <?php
    // Teste 1: Variável de ambiente
    $phpsessid_env = $_ENV['PHPSESSID'] ?? getenv('PHPSESSID') ?? null;
    
    echo '<div class="test ' . ($phpsessid_env ? 'success' : 'error') . '">';
    echo '<strong>1. Variável de Ambiente PHPSESSID:</strong><br>';
    if ($phpsessid_env) {
        echo '✅ Encontrado: ' . substr($phpsessid_env, 0, 20) . '...<br>';
        echo 'Tamanho: ' . strlen($phpsessid_env) . ' caracteres';
    } else {
        echo '❌ NÃO ENCONTRADO<br>';
        echo 'Configure a variável PHPSESSID no Coolify';
    }
    echo '</div>';
    
    // Teste 2: Verificar se está sendo usado
    require_once 'verificar_resultados.php';
    $verificador = new VerificadorResultados($phpsessid_env);
    
    // Usar reflection para verificar propriedade privada
    $reflection = new ReflectionClass($verificador);
    $property = $reflection->getProperty('phpsessid');
    $property->setAccessible(true);
    $phpsessid_used = $property->getValue($verificador);
    
    echo '<div class="test ' . ($phpsessid_used ? 'success' : 'error') . '">';
    echo '<strong>2. PHPSESSID na Classe:</strong><br>';
    if ($phpsessid_used) {
        echo '✅ Configurado: ' . substr($phpsessid_used, 0, 20) . '...';
    } else {
        echo '❌ NÃO CONFIGURADO';
    }
    echo '</div>';
    
    // Teste 3: Todas as variáveis de ambiente
    echo '<div class="test">';
    echo '<strong>3. Todas as Variáveis de Ambiente:</strong><br>';
    echo '<pre>';
    $env_vars = [];
    foreach ($_ENV as $key => $value) {
        if (stripos($key, 'PHP') !== false || stripos($key, 'SESSION') !== false || stripos($key, 'COOKIE') !== false) {
            $env_vars[$key] = $value;
        }
    }
    if (empty($env_vars)) {
        echo 'Nenhuma variável PHP/SESSION encontrada';
    } else {
        foreach ($env_vars as $key => $value) {
            echo $key . ' = ' . (strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value) . "\n";
        }
    }
    echo '</pre>';
    echo '</div>';
    
    // Teste 4: Testar requisição real
    if ($phpsessid_env) {
        echo '<div class="test">';
        echo '<strong>4. Teste de Requisição:</strong><br>';
        echo '<p>Testando busca de resultados...</p>';
        
        $resultado = $verificador->buscarResultados('ln', date('Y-m-d'));
        
        if (empty($resultado['erro'])) {
            echo '<p style="color: green;">✅ Sucesso! Resultados encontrados.</p>';
            echo '<pre>' . json_encode($resultado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';
        } else {
            echo '<p style="color: red;">❌ Erro: ' . htmlspecialchars($resultado['erro']) . '</p>';
            
            // Mostra informações sobre proxy se disponível
            $proxyUrl = $_ENV['PROXY_URL'] ?? getenv('PROXY_URL') ?? null;
            if (!$proxyUrl) {
                echo '<div style="background: #fff3cd; padding: 15px; margin-top: 15px; border-radius: 5px; border-left: 4px solid #ffc107;">';
                echo '<strong>💡 Solução:</strong> Configure PROXY_URL no Coolify para usar outro servidor como proxy.';
                echo '</div>';
            } else {
                echo '<div style="background: #d1ecf1; padding: 15px; margin-top: 15px; border-radius: 5px; border-left: 4px solid #0c5460;">';
                echo '<strong>ℹ️ Proxy configurado:</strong> ' . htmlspecialchars($proxyUrl);
                echo '</div>';
            }
        }
        echo '</div>';
    }
    ?>
    
    <div class="test">
        <h3>📝 Como Configurar no Coolify:</h3>
        <ol>
            <li>Vá em <strong>Environment Variables</strong></li>
            <li>Adicione: <code>PHPSESSID=45c16270330406d85326a05c4058334b</code></li>
            <li>Salve e faça <strong>Redeploy</strong></li>
            <li>Atualize esta página</li>
        </ol>
    </div>
</body>
</html>
