# 🎯 API de Resultados - Jogo do Bicho

API PHP completa para buscar e verificar resultados das principais loterias do Jogo do Bicho, incluindo suporte especial para **Loteria Federal** com números de 5 dígitos.

[![PHP](https://img.shields.io/badge/PHP-7.4%2B-blue.svg)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

---

## ✨ Características

- ✅ **8 Loterias Suportadas**: Nacional, Federal, PT-SP, Boa Sorte, Lotece, Look Goiás, PT Paraíba, Milhar
- ✅ **Suporte Especial Federal**: Captura corretamente números de 5 dígitos (1º a 5º), 4 dígitos (6º) e 3 dígitos (7º)
- ✅ **API REST Completa**: Endpoints para buscar resultados e verificar apostas
- ✅ **Fácil Integração**: Use diretamente a classe PHP ou via API HTTP
- ✅ **Tratamento de Erros**: Respostas padronizadas em JSON
- ✅ **Documentação Completa**: Guias detalhados e exemplos práticos

---

## 🚀 Início Rápido

### Instalação

1. Clone o repositório:
```bash
git clone https://github.com/ronaldoarch/apiresultados.git
cd apiresultados
```

2. Copie os arquivos para seu servidor:
```bash
cp verificar_resultados.php /seu/servidor/
cp api_resultados.php /seu/servidor/
```

### Uso Básico

```php
<?php
require_once 'verificar_resultados.php';

$verificador = new VerificadorResultados();

// Buscar resultados da Federal
$resultado = $verificador->buscarResultados('fd', '2026-01-28');

if (empty($resultado['erro'])) {
    foreach ($resultado['dados'] as $extracao) {
        echo $extracao['titulo'] . "\n";
        foreach ($extracao['premios'] as $premio) {
            echo "  {$premio['numero']} - {$premio['animal']}\n";
        }
    }
}
?>
```

---

## 📚 Documentação

### Loterias Suportadas

| Código | Nome | Observações |
|--------|------|-------------|
| `ln` | Loteria Nacional | Múltiplos horários |
| `fd` | **Loteria Federal** | Sorteios: Quartas e Sábados às 18:50 |
| `sp` | PT-SP/Bandeirantes | Múltiplos horários |
| `bs` | Boa Sorte Goiás | Múltiplos horários |
| `lce` | Lotece | Múltiplos horários |
| `lk` | Look Goiás | Múltiplos horários |
| `pb` | PT Paraíba/Lotep | Múltiplos horários |
| `m` | Milhar | Múltiplos horários |

### API Endpoints

#### Buscar Resultados

```
GET /api_resultados.php?acao=buscar&loteria={CODIGO}&data={DATA}
```

**Exemplo:**
```bash
curl "http://seuservidor.com/api_resultados.php?acao=buscar&loteria=fd&data=2026-01-28"
```

**Resposta:**
```json
{
  "erro": null,
  "dados": {
    "20": {
      "titulo": "Resultado Loteria Federal",
      "horario": "20",
      "premios": [
        {
          "numero": "09593",
          "animal": "Veado",
          "grupo": "24"
        }
      ]
    }
  }
}
```

#### Verificar Apostas

```
POST /api_resultados.php?acao=verificar
Content-Type: application/json

{
  "loteria": "fd",
  "data": "2026-01-28",
  "numeros": ["09593", "83636"]
}
```

---

## 💡 Exemplos de Uso

### Exemplo 1: Buscar Federal

```php
<?php
require_once 'verificar_resultados.php';

$verificador = new VerificadorResultados();
$resultado = $verificador->buscarResultados('fd', '2026-01-28');

if (empty($resultado['erro'])) {
    $extracao = $resultado['dados']['20'];
    echo "{$extracao['titulo']}\n";
    
    foreach ($extracao['premios'] as $index => $premio) {
        $posicao = $index + 1;
        echo "{$posicao}º: {$premio['numero']} - {$premio['animal']}\n";
    }
}
?>
```

### Exemplo 2: Verificar Apostas

```php
<?php
require_once 'verificar_resultados.php';

$verificador = new VerificadorResultados();
$apostas = ['09593', '83636', '11969'];

$verificacao = $verificador->verificarAposta('fd', '2026-01-28', $apostas);

if ($verificacao['sucesso']) {
    echo "Total de acertos: {$verificacao['total_acertos']}\n";
    foreach ($verificacao['acertos'] as $acerto) {
        echo "✅ {$acerto['numero']} - {$acerto['posicao']} lugar\n";
    }
}
?>
```

### Exemplo 3: Via API HTTP (JavaScript)

```javascript
async function buscarFederal(data) {
    const url = `api_resultados.php?acao=buscar&loteria=fd&data=${data}`;
    const response = await fetch(url);
    const resultado = await response.json();
    
    if (!resultado.erro) {
        Object.values(resultado.dados).forEach(extracao => {
            console.log(extracao.titulo);
            extracao.premios.forEach(premio => {
                console.log(`${premio.numero} - ${premio.animal}`);
            });
        });
    }
}

buscarFederal('2026-01-28');
```

---

## 🔧 Integração em Outro Sistema

### Opção 1: Usar Classe Diretamente (Recomendado para PHP)

Copie apenas `verificar_resultados.php` para seu sistema e use diretamente:

```php
require_once 'verificar_resultados.php';
$verificador = new VerificadorResultados();
```

### Opção 2: Usar API HTTP

Suba `verificar_resultados.php` e `api_resultados.php` em um servidor e faça requisições HTTP do seu sistema.

Veja [OPCOES_INTEGRACAO.md](OPCOES_INTEGRACAO.md) para mais detalhes.

---

## 📖 Documentação Completa

- **[COMO_USAR_API.md](COMO_USAR_API.md)** - Guia rápido de uso
- **[EXEMPLO_USO_API.md](EXEMPLO_USO_API.md)** - Exemplos em múltiplas linguagens
- **[OPCOES_INTEGRACAO.md](OPCOES_INTEGRACAO.md)** - Opções de integração
- **[GUIA_COMPLETO.md](GUIA_COMPLETO.md)** - Documentação completa
- **[exemplo_integracao_simples.php](exemplo_integracao_simples.php)** - Exemplo prático completo

---

## ⚠️ Observações Importantes

### Loteria Federal

- **Sorteios**: Apenas quartas e sábados às 18:50
- **Formato dos Números**:
  - 1º a 5º: 5 dígitos (ex: `09593`)
  - 6º: 4 dígitos (ex: `7396`)
  - 7º: 3 dígitos (ex: `320`)

### Limitações

- **Visitantes**: Apenas últimos 10 dias
- **Autenticados**: Até 1 mês atrás (configure `PHPSESSID`)
- **Formato de Data**: Sempre `YYYY-MM-DD` (ex: `2026-01-28`)

---

## 🛠️ Requisitos

- PHP 7.4 ou superior
- Extensão cURL habilitada
- Extensão DOM habilitada
- Acesso à internet (para buscar do bichocerto.com)

---

## 📝 Estrutura do Projeto

```
apiresultados/
├── verificar_resultados.php      # Classe principal
├── api_resultados.php            # API REST
├── README.md                     # Este arquivo
├── COMO_USAR_API.md             # Guia rápido
├── EXEMPLO_USO_API.md           # Exemplos detalhados
├── OPCOES_INTEGRACAO.md         # Opções de integração
├── exemplo_integracao_simples.php # Exemplo prático
├── exemplo_frontend.html         # Interface web
└── automatico/                   # Scripts de automação
    ├── verificar_automatico.php
    └── ...
```

---

## 🤝 Contribuindo

Contribuições são bem-vindas! Sinta-se à vontade para:

1. Fazer fork do projeto
2. Criar uma branch para sua feature (`git checkout -b feature/MinhaFeature`)
3. Commit suas mudanças (`git commit -m 'Adiciona MinhaFeature'`)
4. Push para a branch (`git push origin feature/MinhaFeature`)
5. Abrir um Pull Request

---

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

---

## 🙏 Agradecimentos

- Dados fornecidos por [bichocerto.com](https://bichocerto.com)

---

## 📞 Suporte

- 📧 Issues: [GitHub Issues](https://github.com/ronaldoarch/apiresultados/issues)
- 📚 Documentação: Veja os arquivos `.md` na raiz do projeto

---

**Desenvolvido com ❤️ para a comunidade**
