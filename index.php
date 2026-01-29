<?php
/**
 * Página inicial da API
 * Redireciona para a documentação ou API
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API de Resultados - Jogo do Bicho</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 15px;
            padding: 40px;
            max-width: 800px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        h1 {
            color: #667eea;
            margin-bottom: 20px;
            font-size: 2.5em;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 1.1em;
        }
        .endpoints {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .endpoint {
            margin: 15px 0;
            padding: 15px;
            background: white;
            border-left: 4px solid #667eea;
            border-radius: 5px;
        }
        .method {
            display: inline-block;
            padding: 5px 10px;
            background: #667eea;
            color: white;
            border-radius: 5px;
            font-weight: bold;
            margin-right: 10px;
        }
        .url {
            font-family: 'Courier New', monospace;
            color: #333;
            word-break: break-all;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            margin: 10px 10px 10px 0;
            transition: transform 0.3s;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .status {
            display: inline-block;
            padding: 5px 15px;
            background: #28a745;
            color: white;
            border-radius: 20px;
            font-size: 0.9em;
            margin-left: 10px;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎯 API de Resultados</h1>
        <p class="subtitle">API completa para buscar e verificar resultados das principais loterias do Jogo do Bicho</p>
        
        <div class="status">✅ Online</div>
        
        <div class="endpoints">
            <h2>📡 Endpoints Disponíveis</h2>
            
            <div class="endpoint">
                <span class="method">GET</span>
                <span class="url">/api_resultados.php?acao=buscar&loteria={codigo}&data={data}</span>
                <p style="margin-top: 10px; color: #666;">
                    Busca resultados de uma loteria. Exemplo: <code>?acao=buscar&loteria=fd&data=2026-01-28</code>
                </p>
            </div>
            
            <div class="endpoint">
                <span class="method">POST</span>
                <span class="url">/api_resultados.php?acao=verificar</span>
                <p style="margin-top: 10px; color: #666;">
                    Verifica se números apostados foram sorteados. Envie JSON no body.
                </p>
            </div>
        </div>
        
        <div style="margin-top: 30px;">
            <h3>📚 Documentação</h3>
            <a href="README.md" class="btn">📖 README</a>
            <a href="exemplo_frontend.html" class="btn">🌐 Exemplo Frontend</a>
            <a href="api_resultados.php" class="btn">🔌 Testar API</a>
        </div>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
            <h3>🎲 Loterias Suportadas</h3>
            <p style="color: #666; margin-top: 10px;">
                <code>ln</code> (Nacional), <code>fd</code> (Federal), <code>sp</code> (PT-SP), 
                <code>bs</code> (Boa Sorte), <code>lce</code> (Lotece), <code>lk</code> (Look), 
                <code>pb</code> (PT Paraíba), <code>m</code> (Milhar)
            </p>
        </div>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; text-align: center; color: #999;">
            <p>Desenvolvido com ❤️ | <a href="https://github.com/ronaldoarch/apiresultados" style="color: #667eea;">GitHub</a></p>
        </div>
    </div>
</body>
</html>
