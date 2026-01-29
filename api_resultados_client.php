<?php
/**
 * API Proxy - Faz requisições do lado do cliente para evitar bloqueio Cloudflare
 * 
 * Este arquivo retorna JavaScript que faz a requisição diretamente do navegador,
 * evitando o bloqueio do Cloudflare no servidor.
 */

header('Content-Type: application/javascript; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$acao = $_GET['acao'] ?? 'buscar';
$loteria = $_GET['loteria'] ?? 'ln';
$data = $_GET['data'] ?? date('Y-m-d');

?>
// API Client-Side para evitar bloqueio Cloudflare
(function() {
    'use strict';
    
    const acao = <?php echo json_encode($acao); ?>;
    const loteria = <?php echo json_encode($loteria); ?>;
    const data = <?php echo json_encode($data); ?>;
    
    // Função para fazer requisição do lado do cliente
    async function buscarResultadosClient(loteria, data) {
        const formData = new URLSearchParams();
        formData.append('l', loteria);
        formData.append('d', data);
        
        try {
            const response = await fetch('https://bichocerto.com/resultados/base/resultado/', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: formData,
                credentials: 'include' // Inclui cookies
            });
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            
            const html = await response.text();
            
            // Parse HTML e retorna JSON
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            const resultados = {};
            
            // Encontra todas as divs de resultado
            const divs = doc.querySelectorAll('[id^="div_display_"]');
            
            divs.forEach(div => {
                const divId = div.id;
                const match = divId.match(/div_display_(\d+)/);
                if (!match) return;
                
                const horarioId = match[1];
                const tableId = `table_${horarioId}`;
                const tabela = div.querySelector(`#${tableId}`);
                
                if (!tabela) return;
                
                // Extrai título
                const tituloNode = div.querySelector('h5.card-title');
                const titulo = tituloNode ? tituloNode.textContent.trim() : `Extração ${horarioId}`;
                
                // Extrai prêmios
                const premios = [];
                const linhas = tabela.querySelectorAll('tr');
                
                linhas.forEach((linha, index) => {
                    const colunas = linha.querySelectorAll('td');
                    if (colunas.length < 4) return;
                    
                    // Extrai posição
                    const posicaoNode = linha.querySelector('.bg-dark');
                    let posicao = null;
                    if (posicaoNode) {
                        const posicaoMatch = posicaoNode.textContent.match(/(\d+)/);
                        if (posicaoMatch) {
                            posicao = parseInt(posicaoMatch[1]);
                        }
                    }
                    
                    // Extrai número
                    const numNode = colunas[2].querySelector('a, h5');
                    if (!numNode) return;
                    
                    let numero = null;
                    
                    // Para Federal: tenta extrair do parâmetro m= ou c= da URL
                    if (loteria === 'fd' && numNode.tagName === 'A') {
                        const href = numNode.getAttribute('href');
                        if (href) {
                            if (posicao >= 1 && posicao <= 5) {
                                const match = href.match(/[?&]m=(\d{5})/);
                                if (match) numero = match[1];
                            } else if (posicao === 6) {
                                const match = href.match(/[?&]m=(\d{4})/);
                                if (match) numero = match[1];
                            } else if (posicao === 7) {
                                const match = href.match(/[?&]c=(\d{3})/);
                                if (match) numero = match[1];
                            }
                        }
                    }
                    
                    // Se não encontrou na URL, tenta do texto
                    if (!numero) {
                        const numeroTexto = numNode.textContent.trim().replace(/[.\s,]/g, '');
                        const match = numeroTexto.match(/(\d{3,5})/);
                        if (match) numero = match[1];
                    }
                    
                    if (numero) {
                        premios.push({
                            numero: numero,
                            animal: colunas.length > 4 ? colunas[4].textContent.trim() : '',
                            grupo: colunas.length > 3 ? colunas[3].textContent.trim() : ''
                        });
                    }
                });
                
                if (premios.length > 0) {
                    resultados[horarioId] = {
                        titulo: titulo,
                        horario: horarioId,
                        premios: premios
                    };
                }
            });
            
            return {
                erro: null,
                dados: resultados
            };
            
        } catch (error) {
            return {
                erro: 'Erro ao buscar resultados: ' + error.message,
                dados: []
            };
        }
    }
    
    // Executa e retorna resultado
    if (acao === 'buscar') {
        buscarResultadosClient(loteria, data).then(resultado => {
            // Retorna como JSONP se callback especificado
            const callback = new URLSearchParams(window.location.search).get('callback');
            if (callback && typeof window[callback] === 'function') {
                window[callback](resultado);
            } else {
                // Retorna como JSON
                document.body.innerHTML = '<pre>' + JSON.stringify(resultado, null, 2) + '</pre>';
            }
        });
    }
})();
