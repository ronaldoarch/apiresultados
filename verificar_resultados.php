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
        // Verifica se cURL está disponível
        if (!function_exists('curl_init')) {
            return [
                'erro' => 'Extensão cURL não está disponível. Instale php-curl.',
                'dados' => []
            ];
        }
        
        $ch = curl_init();
        
        // Headers para simular navegador real e passar pelo Cloudflare
        $headers = [
            'Content-Type: application/x-www-form-urlencoded',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
            'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
            'Accept-Encoding: gzip, deflate, br',
            'Connection: keep-alive',
            'Upgrade-Insecure-Requests: 1',
            'Sec-Fetch-Dest: document',
            'Sec-Fetch-Mode: navigate',
            'Sec-Fetch-Site: none',
            'Sec-Fetch-User: ?1',
            'Cache-Control: max-age=0',
            'Referer: https://bichocerto.com/'
        ];
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->baseUrl,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'l' => $codigoLoteria,
                'd' => $data
            ]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_ENCODING => 'gzip, deflate, br',
            CURLOPT_HTTPHEADER => $headers
        ]);
        
        // Configura cookies se já tiver arquivo de cookies
        if (!$this->phpsessid) {
            $cookieFile = sys_get_temp_dir() . '/bichocerto_cookies_' . md5($this->baseUrl) . '.txt';
            if (file_exists($cookieFile)) {
                curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
                curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
            }
        }
        
        // Adiciona cookie PHPSESSID se fornecido
        if ($this->phpsessid) {
            curl_setopt($ch, CURLOPT_COOKIE, "PHPSESSID=" . $this->phpsessid);
        } else {
            // Tenta fazer uma requisição inicial para obter cookies do Cloudflare
            // Cria diretório temporário se não existir
            $cookieFile = sys_get_temp_dir() . '/bichocerto_cookies_' . md5($this->baseUrl) . '.txt';
            
            $chPreflight = curl_init();
            curl_setopt_array($chPreflight, [
                CURLOPT_URL => 'https://bichocerto.com',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 15,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS => 5,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_COOKIEJAR => $cookieFile,
                CURLOPT_COOKIEFILE => $cookieFile,
                CURLOPT_ENCODING => 'gzip, deflate, br',
                CURLOPT_HTTPHEADER => [
                    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
                    'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
                    'Accept-Encoding: gzip, deflate, br',
                    'Connection: keep-alive',
                    'Upgrade-Insecure-Requests: 1',
                    'Sec-Fetch-Dest: document',
                    'Sec-Fetch-Mode: navigate',
                    'Sec-Fetch-Site: none',
                    'Sec-Fetch-User: ?1'
                ]
            ]);
            
            $preflightResponse = curl_exec($chPreflight);
            $preflightCode = curl_getinfo($chPreflight, CURLINFO_HTTP_CODE);
            curl_close($chPreflight);
            
            // Se o preflight foi bloqueado, tenta continuar mesmo assim
            if ($preflightCode === 403 || strpos($preflightResponse, 'Cloudflare') !== false) {
                // Cloudflare bloqueou, mas vamos tentar mesmo assim com delay maior
                sleep(2);
            } else {
                // Pequeno delay para não parecer bot
                usleep(1000000); // 1 segundo
            }
            
            // Usa os cookies obtidos na requisição principal
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
        }
        
        $html = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        if ($html === false || !empty($curlError)) {
            return [
                'erro' => 'Erro de conexão: ' . ($curlError ?: 'Falha ao conectar com o servidor'),
                'dados' => []
            ];
        }
        
        if ($httpCode !== 200) {
            return [
                'erro' => "Erro HTTP {$httpCode}: " . ($html ?: 'Resposta vazia do servidor'),
                'dados' => []
            ];
        }
        
        if (empty($html)) {
            return [
                'erro' => 'Resposta vazia do servidor',
                'dados' => []
            ];
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
        
        // Verifica se DOM está disponível
        if (!class_exists('DOMDocument')) {
            return [
                'erro' => 'Extensão DOM não está disponível. Instale php-xml.',
                'dados' => []
            ];
        }
        
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
