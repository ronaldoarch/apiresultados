<?php
/**
 * Exemplo Prático de Uso da Classe ApiResultados
 * 
 * Copie este arquivo e adapte para seu sistema!
 */

require_once 'ApiResultados.php';

// ============================================
// EXEMPLO 1: Buscar Resultados da Federal
// ============================================

$api = new ApiResultados();

echo "<h2>Exemplo 1: Buscar Federal</h2>";

$resultado = $api->buscar('fd', '2026-01-28');

if ($resultado['sucesso']) {
    echo "<p>✅ Resultados encontrados!</p>";
    
    foreach ($resultado['dados'] as $horario => $extracao) {
        echo "<h3>{$extracao['titulo']}</h3>";
        echo "<ul>";
        foreach ($extracao['premios'] as $index => $premio) {
            $posicao = $index + 1;
            echo "<li><strong>{$posicao}º:</strong> {$premio['numero']} - {$premio['animal']} (Grupo {$premio['grupo']})</li>";
        }
        echo "</ul>";
    }
} else {
    echo "<p>❌ Erro: {$resultado['erro']}</p>";
}

echo "<hr>";

// ============================================
// EXEMPLO 2: Verificar se Número Foi Sorteado
// ============================================

echo "<h2>Exemplo 2: Verificar Número</h2>";

$verificacao = $api->verificarNumero('fd', '2026-01-28', '09593');

if ($verificacao['encontrado']) {
    echo "<p>✅ Número <strong>{$verificacao['numero']}</strong> encontrado!</p>";
    echo "<ul>";
    echo "<li>Posição: {$verificacao['posicao']}</li>";
    echo "<li>Animal: {$verificacao['animal']}</li>";
    echo "<li>Grupo: {$verificacao['grupo']}</li>";
    echo "<li>Horário: {$verificacao['horario']}</li>";
    echo "</ul>";
} else {
    if (isset($verificacao['erro'])) {
        echo "<p>❌ Erro: {$verificacao['erro']}</p>";
    } else {
        echo "<p>❌ Número não encontrado nos resultados</p>";
    }
}

echo "<hr>";

// ============================================
// EXEMPLO 3: Verificar Múltiplos Números
// ============================================

echo "<h2>Exemplo 3: Verificar Múltiplos Números</h2>";

$numerosParaVerificar = ['09593', '1234', '5678', '9999'];
$resultadoVerificacao = $api->verificarNumeros('fd', '2026-01-28', $numerosParaVerificar);

if ($resultadoVerificacao['sucesso']) {
    echo "<p>Total verificados: {$resultadoVerificacao['total_verificados']}</p>";
    echo "<p>Total de acertos: <strong>{$resultadoVerificacao['total_acertos']}</strong></p>";
    
    if (!empty($resultadoVerificacao['acertos'])) {
        echo "<h4>Números que ganharam:</h4>";
        echo "<ul>";
        foreach ($resultadoVerificacao['acertos'] as $acerto) {
            echo "<li>{$acerto['numero']} - {$acerto['posicao']} ({$acerto['animal']})</li>";
        }
        echo "</ul>";
    }
} else {
    echo "<p>❌ Erro: {$resultadoVerificacao['erro']}</p>";
}

echo "<hr>";

// ============================================
// EXEMPLO 4: Obter Todos os Números Sorteados
// ============================================

echo "<h2>Exemplo 4: Todos os Números Sorteados</h2>";

$numerosSorteados = $api->obterNumerosSorteados('fd', '2026-01-28');

if ($numerosSorteados['sucesso']) {
    echo "<p>Total de números sorteados: " . count($numerosSorteados['numeros']) . "</p>";
    echo "<ul>";
    foreach ($numerosSorteados['numeros'] as $item) {
        echo "<li>{$item['numero']} - {$item['animal']} (Horário: {$item['horario']}h)</li>";
    }
    echo "</ul>";
} else {
    echo "<p>❌ Erro: {$numerosSorteados['erro']}</p>";
}

echo "<hr>";

// ============================================
// EXEMPLO 5: Integração com Banco de Dados
// ============================================

echo "<h2>Exemplo 5: Salvar no Banco de Dados</h2>";

function salvarResultadosNoBanco($api, $loteria, $data) {
    $resultado = $api->buscar($loteria, $data);
    
    if (!$resultado['sucesso']) {
        return false;
    }
    
    // Exemplo de código para salvar (adaptar para seu banco)
    /*
    $pdo = new PDO('mysql:host=localhost;dbname=seu_banco', 'usuario', 'senha');
    
    foreach ($resultado['dados'] as $horario => $extracao) {
        foreach ($extracao['premios'] as $index => $premio) {
            $stmt = $pdo->prepare("
                INSERT INTO resultados (loteria, data, horario, posicao, numero, animal, grupo)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $loteria,
                $data,
                $horario,
                $index + 1,
                $premio['numero'],
                $premio['animal'],
                $premio['grupo']
            ]);
        }
    }
    */
    
    echo "<p>✅ Resultados prontos para salvar no banco!</p>";
    echo "<p>(Código de exemplo comentado - descomente e adapte para seu banco)</p>";
    
    return true;
}

salvarResultadosNoBanco($api, 'fd', '2026-01-28');

?>
