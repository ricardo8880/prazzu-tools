# Lote 2 — Núcleo definitivo das ferramentas

## Objetivo

Consolidar o contrato central necessário para suportar centenas de ferramentas,
sem implementar ferramentas reais e sem alterar o layout.

## Entregas

- `ToolManifest` tipado e imutável;
- enums de categoria, acesso e ciclo de vida;
- capacidades opcionais para rotas, views e migrations;
- `ToolRegistry` indexado por slug e com consultas explícitas;
- `ToolCatalog` baseado em manifestos;
- catálogo público composto somente por módulos reais;
- registro de módulos dividido por grupos;
- convenções de slug, rota e versão validadas;
- documentação e testes do novo contrato.

## Decisões preservadas

- Laravel continua como monólito modular;
- o catálogo continua sendo a fonte única das telas;
- somente manifestos registrados podem aparecer no catálogo;
- métricas demonstrativas e placeholders não são publicados;
- não há descoberta automática por diretório;
- o layout e seus assets não foram modificados.

## Próximo lote

Antes do Lote 3, este núcleo deve ser revisto em conjunto com os Lotes 4 a 8. O
Lote 3 definirá a estrutura interna padrão de um módulo e o gerador Artisan, sem
antecipar regras contábeis que pertencem ao Lote 4.
