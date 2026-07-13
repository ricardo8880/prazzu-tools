# Lote 1 — Fundação e padronização

## Objetivo

Eliminar ambiguidades da base antes da reformulação do núcleo modular, sem alterar o layout ou a aparência da aplicação.

## Decisões consolidadas

### 1. Layout preservado

A estrutura HTML, classes visuais, Bootstrap, Bootstrap Icons e o CSS já aprovado foram mantidos. Este lote não redesenha componentes nem altera identidade visual.

### 2. Vite como pipeline oficial

Os assets próprios passam a ter uma única origem:

```text
resources/css/app.css
resources/js/app.js
```

O arquivo Blade principal os carrega por `@vite`. Os arquivos antigos em `public/assets` foram mantidos apenas para evitar uma remoção destrutiva neste pacote incremental, mas não são mais carregados pelo layout.

### 3. Navegação móvel alimentada pelo catálogo

As categorias do menu móvel agora usam a mesma coleção fornecida ao menu lateral desktop. Assim, novas categorias não precisam ser cadastradas manualmente em dois lugares.

### 4. Rotas nomeadas

Os links do menu móvel utilizam rotas nomeadas. Isso reduz dependência de URLs escritas diretamente nas views.

### 5. Documentação do projeto

O README padrão do Laravel foi substituído por instruções específicas do Prazzu Tools, incluindo instalação, assets, testes e estado atual.

## Limites deste lote

Este lote não modifica:

- contrato dos módulos;
- metadados das ferramentas;
- versionamento de regras;
- persistência;
- autenticação;
- aparência da plataforma.

Esses assuntos pertencem aos lotes seguintes e devem considerar as decisões consolidadas aqui.

## Critérios para iniciar o Lote 2

Antes de reformular o núcleo das ferramentas, confirmar que:

1. o build Vite conclui sem erros;
2. as views continuam usando as mesmas classes visuais;
3. as categorias móveis e desktop vêm da mesma fonte;
4. as rotas e configurações podem ser armazenadas em cache;
5. os testes existentes continuam válidos.
