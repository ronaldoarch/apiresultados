# 🚀 Como Subir para o GitHub

## Passo a Passo

### 1. Inicializar o Repositório Git

```bash
cd integracao_web
git init
```

### 2. Adicionar Arquivos

```bash
# Adicionar todos os arquivos importantes
git add verificar_resultados.php
git add api_resultados.php
git add README.md
git add LICENSE
git add .gitignore
git add COMO_USAR_API.md
git add EXEMPLO_USO_API.md
git add OPCOES_INTEGRACAO.md
git add exemplo_integracao_simples.php
git add exemplo_frontend.html
git add GUIA_COMPLETO.md
```

### 3. Fazer Primeiro Commit

```bash
git commit -m "Initial commit: API de Resultados - Jogo do Bicho"
```

### 4. Conectar com o Repositório Remoto

```bash
git remote add origin https://github.com/ronaldoarch/apiresultados.git
```

### 5. Verificar Branch

```bash
git branch -M main
```

### 6. Fazer Push

```bash
git push -u origin main
```

---

## 📋 Arquivos Principais para Subir

### Obrigatórios:
- ✅ `verificar_resultados.php` - Classe principal
- ✅ `api_resultados.php` - API REST
- ✅ `README.md` - Documentação principal
- ✅ `LICENSE` - Licença MIT
- ✅ `.gitignore` - Arquivos ignorados

### Recomendados:
- ✅ `COMO_USAR_API.md` - Guia rápido
- ✅ `EXEMPLO_USO_API.md` - Exemplos detalhados
- ✅ `OPCOES_INTEGRACAO.md` - Opções de integração
- ✅ `exemplo_integracao_simples.php` - Exemplo prático
- ✅ `exemplo_frontend.html` - Interface web
- ✅ `GUIA_COMPLETO.md` - Documentação completa

### Opcionais:
- `automatico/` - Scripts de automação (se quiser incluir)
- `visualizar_resultados.html` - Interface de visualização
- Outros arquivos de documentação

---

## 🎯 Comando Completo (Copy & Paste)

```bash
# 1. Navegar para a pasta
cd integracao_web

# 2. Inicializar Git
git init

# 3. Adicionar arquivos principais
git add verificar_resultados.php api_resultados.php README.md LICENSE .gitignore
git add COMO_USAR_API.md EXEMPLO_USO_API.md OPCOES_INTEGRACAO.md
git add exemplo_integracao_simples.php exemplo_frontend.html GUIA_COMPLETO.md

# 4. Commit inicial
git commit -m "Initial commit: API de Resultados - Jogo do Bicho com suporte especial para Federal"

# 5. Conectar repositório remoto
git remote add origin https://github.com/ronaldoarch/apiresultados.git

# 6. Renomear branch para main
git branch -M main

# 7. Fazer push
git push -u origin main
```

---

## ⚠️ Se Der Erro de Autenticação

### Opção 1: Usar Token de Acesso Pessoal

1. Vá em GitHub → Settings → Developer settings → Personal access tokens
2. Crie um novo token com permissão `repo`
3. Use o token como senha:

```bash
git push -u origin main
# Username: seu-usuario
# Password: seu-token-aqui
```

### Opção 2: Usar SSH

```bash
# Gerar chave SSH (se não tiver)
ssh-keygen -t ed25519 -C "seu-email@exemplo.com"

# Adicionar chave ao GitHub
# Copie o conteúdo de ~/.ssh/id_ed25519.pub e adicione em GitHub → Settings → SSH keys

# Mudar URL para SSH
git remote set-url origin git@github.com:ronaldoarch/apiresultados.git

# Fazer push
git push -u origin main
```

---

## ✅ Verificar se Funcionou

Após o push, acesse:
```
https://github.com/ronaldoarch/apiresultados
```

Você deve ver:
- ✅ README.md exibido na página inicial
- ✅ Arquivos PHP listados
- ✅ Documentação disponível

---

## 📝 Próximos Passos

1. **Adicionar Badges** (opcional):
   - Adicione badges no README.md para mostrar status do projeto

2. **Criar Releases**:
   ```bash
   git tag -a v1.0.0 -m "Versão inicial"
   git push origin v1.0.0
   ```

3. **Adicionar Topics** no GitHub:
   - `php`
   - `api`
   - `lottery`
   - `jogo-do-bicho`
   - `federal`

4. **Configurar GitHub Pages** (opcional):
   - Para hospedar a documentação

---

## 🔄 Atualizações Futuras

Para fazer atualizações:

```bash
# 1. Adicionar mudanças
git add .

# 2. Commit
git commit -m "Descrição das mudanças"

# 3. Push
git push origin main
```

---

**Pronto! Seu repositório está no GitHub! 🎉**
