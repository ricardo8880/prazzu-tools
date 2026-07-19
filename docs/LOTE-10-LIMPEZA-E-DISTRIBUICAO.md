# Lote 10 — Limpeza e distribuição

Este lote encerra a preparação arquitetural antes da primeira ferramenta contábil real. Ele não altera layout, CSS, componentes visuais ou comportamento de interface.

## Objetivos

- Padronizar a configuração inicial de desenvolvimento.
- Consolidar os comandos oficiais de instalação e verificação.
- Remover resíduos que não pertencem à aplicação.
- Impedir que segredos e dependências locais sejam enviados em pacotes.
- Produzir um ZIP de distribuição reproduzível.

## Limpeza do projeto local

Execute na raiz do projeto:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\cleanup-project.ps1
```

O script remove somente:

```text
.idea/
ARQUIVOS_REMOVIDOS.txt
ferramentas/
```

Ele preserva deliberadamente:

```text
.git/
.env
vendor/
database/database.sqlite
```

Esses itens podem ser necessários no ambiente local, embora não devam estar em um pacote público.

Para visualizar as remoções sem executá-las:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\cleanup-project.ps1 -WhatIf
```

## Pacote de distribuição

Execute:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\package-distribution.ps1
```

O pacote gerado exclui dados locais e reconstruíveis e executa uma validação bloqueante antes da compactação, incluindo:

- `.git`;
- `.idea`;
- `.env`;
- `vendor`;
- `node_modules`;
- cache do PHPUnit;
- banco SQLite local;
- logs locais.

Quem receber o pacote deve executar `composer setup`.

## Comandos oficiais

### Instalação

```bash
composer setup
```

### Correção de formatação

```bash
composer format
```

### Qualidade

```bash
composer quality
```

### Verificação completa

```bash
composer verify
```

O comando `verify` executa testes, arquitetura, formatação, caches e build, limpando os caches ao final.

## Segurança de configuração

O arquivo `.env` nunca deve ser distribuído. Somente `.env.example` faz parte do projeto compartilhável.

A configuração de exemplo usa arquivos locais para sessão e cache e fila síncrona. Isso permite instalar o projeto antes de depender das tabelas de infraestrutura. Ambientes de produção podem usar banco, Redis ou outro backend conforme sua implantação.


## Validação bloqueante

O script `scripts/verify-distribution.php` encerra com erro quando o diretório de distribuição contém segredos, banco local, dependências reconstruíveis, caches, logs ou arquivos temporários de editores e suítes de escritório.

Para validar manualmente um diretório já preparado:

```bash
php scripts/verify-distribution.php /caminho/do/pacote
```

O comando oficial de verificação para release é:

```bash
composer release:check
```
