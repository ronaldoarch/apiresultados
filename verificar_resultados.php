<?php
/**
 * Script PHP para verificar resultados das loterias
 * Integre este arquivo no seu site de Jogo do Bicho
 */

class VerificadorResultados {
    private $baseUrl = "https://bichocerto.com/resultados/base/resultado/";
    private $phpsessid = null;
    
    public function __construct($phpsessid = null) {
        $this->phpsessid = $phpsessid;
    }
    
    /**
     * Busca resultados de uma loteria
     */
    public function buscarResultados($codigoLoteria, $data) {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->baseUrl,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'l' => $codigoLoteria,
                'd' => $data
            ]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
                'User-Agent: Mozilla/5.0 (compatible; JogoBicho/1.0)'
            ]
        ]);
        
        // Adiciona cookie PHPSESSID se fornecido
        if ($this->phpsessid) {
            curl_setopt($ch, CURLOPT_COOKIE, "PHPSESSID=" . $this->phpsessid);
        }
        
        $html = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200 || !$html) {
            return ['erro' => 'Erro ao buscar resultados', 'dados' => []];
        }
        
        // Verifica erros
        if (strpos($html, 'Sem resultados para esta data') !== false) {
            return ['erro' => 'Sem resultados para esta data', 'dados' => []];
        }
        
        if (strpos($html, 'Só é possível visualizar resultados dos últimos') !== false) {
            return ['erro' => 'Data fora do intervalo permitido', 'dados' => []];
        }
        
        return $this->extrairResultados($html, $codigoLoteria);
    }
    
    /**
     * Extrai resultados do HTML
     */
    private function extrairResultados($html, $codigoLoteria) {
        $resultados = [];
        
        // Usa DOMDocument para parsear HTML
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML('<?xml encoding="UTF-8">' . $html);
        $xpath = new DOMXPath($dom);
        
        // Encontra todas as divs de resultado
        $divs = $xpath->query("//div[starts-with(@id, 'div_display_')]");
        
        foreach ($divs as $div) {
            $divId = $div->getAttribute('id');
            preg_match('/div_display_(\d+)/', $divId, $matches);
            
            if (empty($matches[1])) continue;
            
            $horarioId = $matches[1];
            $tableId = "table_{$horarioId}";
            
            // Busca a tabela
            $tabelas = $xpath->query(".//table[@id='{$tableId}']", $div);
            if ($tabelas->length === 0) continue;
            
            $tabela = $tabelas->item(0);
            
            // Extrai título
            $tituloNodes = $xpath->query(".//h5[contains(@class, 'card-title')]", $div);
            $titulo = $tituloNodes->length > 0 ? trim($tituloNodes->item(0)->textContent) : "Extração {$horarioId}";
            
            // Extrai prêmios
            $premios = [];
            $linhas = $xpath->query('.//tr', $tabela);
            
            foreach ($linhas as $linha) {
                $colunas = $xpath->query('.//td', $linha);
                if ($colunas->length < 4) continue;
                
                // Extrai posição (1º, 2º, 3º, etc.)
                $posicaoNode = $xpath->query('.//div[contains(@class, "bg-dark")]', $colunas->item(0));
                $posicao = null;
                if ($posicaoNode->length > 0) {
                    $posicaoTexto = trim($posicaoNode->item(0)->textContent);
                    preg_match('/(\d+)/', $posicaoTexto, $posMatch);
                    if (!empty($posMatch[1])) {
                        $posicao = (int)$posMatch[1];
                    }
                }
                
                // Extrai número
                $numNode = $xpath->query('.//a | .//h5', $colunas->item(2))->item(0);
                if (!$numNode) continue;
                
                $numero = null;
                
                // Para Federal: extrai baseado na posição
                if ($codigoLoteria === 'fd' && $numNode->nodeName === 'a') {
                    $href = $numNode->getAttribute('href');
                    
                    if ($posicao >= 1 && $posicao <= 5) {
                        // 1º a 5º: números de 5 dígitos (parâmetro m=)
                        if (preg_match('/[?&]m=(\d{5})/', $href, $urlMatch)) {
                            $numero = $urlMatch[1];
                        }
                    } elseif ($posicao == 6) {
                        // 6º: números de 4 dígitos (parâmetro m=)
                        if (preg_match('/[?&]m=(\d{4})/', $href, $urlMatch)) {
                            $numero = $urlMatch[1];
                        }
                    } elseif ($posicao == 7) {
                        // 7º: números de 3 dígitos (parâmetro c= para centena)
                        if (preg_match('/[?&]c=(\d{3})/', $href, $urlMatch)) {
                            $numero = $urlMatch[1];
                        }
                    }
                }
                
                // Se não encontrou na URL ou não é Federal, tenta do texto
                if (!$numero) {
                    $numeroTexto = trim($numNode->textContent);
                    // Remove pontos e espaços, depois busca números de 3 a 5 dígitos
                    $numeroTexto = str_replace(['.', ' ', ','], '', $numeroTexto);
                    preg_match('/(\d{3,5})/', $numeroTexto, $numMatch);
                    if (!empty($numMatch[1])) {
                        $numero = $numMatch[1];
                    }
                }
                
                if ($numero) {
                    $premios[] = [
                        'numero' => $numero,
                        'animal' => $colunas->length > 4 ? trim($colunas->item(4)->textContent) : '',
                        'grupo' => $colunas->length > 3 ? trim($colunas->item(3)->textContent) : ''
                    ];
                }
            }
            
            if (!empty($premios)) {
                $resultados[$horarioId] = [
                    'titulo' => $titulo,
                    'horario' => $horarioId,
                    'premios' => $premios
                ];
            }
        }
        
        return ['erro' => null, 'dados' => $resultados];
    }
    
    /**
     * Verifica se números apostados foram sorteados
     */
    public function verificarAposta($codigoLoteria, $data, $numerosApostados) {
        $resultados = $this->buscarResultados($codigoLoteria, $data);
        
        if (!empty($resultados['erro'])) {
            return [
                'sucesso' => false,
                'erro' => $resultados['erro'],
                'acertos' => []
            ];
        }
        
        $acertos = [];
        $dados = $resultados['dados'];
        
        foreach ($dados as $horarioId => $extracao) {
            $premios = $extracao['premios'];
            $numerosSorteados = array_column($premios, 'numero');
            
            foreach ($numerosApostados as $numApostado) {
                $numNormalizado = str_pad(ltrim($numApostado, '0'), strlen($numApostado), '0', STR_PAD_LEFT);
                
                $posicao = array_search($numNormalizado, $numerosSorteados);
                if ($posicao !== false) {
                    $premio = $premios[$posicao];
                    $acertos[] = [
                        'numero' => $numApostado,
                        'horario' => $extracao['titulo'],
                        'posicao' => ($posicao + 1) . 'º',
                        'animal' => $premio['animal'],
                        'grupo' => $premio['grupo']
                    ];
                }
            }
        }
        
        return [
            'sucesso' => true,
            'data' => $data,
            'loteria' => $codigoLoteria,
            'total_apostado' => count($numerosApostados),
            'total_acertos' => count($acertos),
            'acertos' => $acertos
        ];
    }
}

// ============================================
// EXEMPLOS DE USO
// ============================================

// Exemplo 1: Buscar resultados
$verificador = new VerificadorResultados();

$hoje = date('Y-m-d');
$resultados = $verificador->buscarResultados('ln', $hoje);

if (empty($resultados['erro'])) {
    echo "Resultados encontrados:\n";
    foreach ($resultados['dados'] as $horario => $extracao) {
        echo "{$extracao['titulo']}: " . count($extracao['premios']) . " prêmios\n";
    }
}

// Exemplo 2: Verificar apostas
$apostas = ['2047', '2881', '2289'];
$verificacao = $verificador->verificarAposta('ln', $hoje, $apostas);

if ($verificacao['sucesso']) {
    echo "\nAcertos: {$verificacao['total_acertos']}\n";
    foreach ($verificacao['acertos'] as $acerto) {
        echo "✅ {$acerto['numero']} - {$acerto['posicao']} lugar\n";
    }
}

?>
