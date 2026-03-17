<?php
/**
 * Router para servidor PHP embutido (php -S)
 * Uso: php -S 0.0.0.0:8000 router.php
 */
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// API de resultados
if (strpos($uri, '/api_resultados') === 0 || $uri === '/api_resultados.php') {
    require __DIR__ . '/api_resultados.php';
    return true;
}

// Outros .php na raiz
if (preg_match('#^/(\w+\.php)#', $uri, $m) && file_exists(__DIR__ . '/' . $m[1])) {
    require __DIR__ . '/' . $m[1];
    return true;
}

// index para /
if ($uri === '/' || $uri === '') {
    require __DIR__ . '/index.php';
    return true;
}

// Arquivos estáticos (deixar o servidor servir)
return false;
