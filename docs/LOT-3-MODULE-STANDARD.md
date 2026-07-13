# Lote 3 — Padrão interno dos módulos

## Objetivo

Garantir que novas ferramentas sejam criadas com a mesma organização, limites e
pontos de extensão, sem implementar regras contábeis e sem alterar o layout.

## Estrutura oficial

```text
app/Tools/NomeDaFerramenta/
├── Application/                 # casos de uso, actions e DTOs quando necessários
├── Domain/                      # regras, cálculos, value objects e resultados
├── Infrastructure/             # banco, arquivos e serviços externos
├── Presentation/
│   ├── Controllers/
│   └── Requests/
├── Resources/
│   ├── views/
│   ├── js/                      # somente quando houver comportamento próprio
│   └── css/                     # somente quando houver estilo próprio
├── Routes/
│   ├── web.php
│   └── api.php                  # opcional
├── Tests/
│   ├── Unit/
│   └── Feature/
├── Tool.php
└── README.md
```

As pastas `Application`, `Domain` e `Infrastructure` são criadas quando o módulo
realmente precisar delas. Pastas vazias não devem ser adicionadas apenas para
cumprir desenho arquitetural.

## Gerador

```bash
php artisan make:tool CalculadoraRescisao \
    --slug=calculadora-rescisao \
    --category=trabalhista \
    --access=free \
    --status=draft
```

O comando cria:

- manifesto e capacidades web/views;
- controller e Form Request;
- arquivo próprio de rotas;
- view inicial;
- testes unitário e funcional;
- README do módulo;
- registro no grupo correspondente em `config/tools/modules.php`.

O estado padrão é `draft`. Uma ferramenta não deve ser ativada enquanto possuir
testes incompletos ou metadados provisórios.

## Grupos de registro

Os grupos existem apenas para reduzir conflitos de edição do arquivo de
configuração:

- `general`;
- `fiscal`;
- `labor`;
- `corporate`;
- `documents`.

Eles não definem o namespace nem obrigam a ferramenta a ficar fisicamente dentro
de uma pasta de categoria.

## Regras obrigatórias

1. Cada módulo fica em `app/Tools/<Nome>/`.
2. A classe de entrada chama-se `Tool`.
3. A rota principal começa com `tools.<slug>.`.
4. Views usam o namespace `tools-<slug>`.
5. Rotas, views e migrations declaradas permanecem dentro do módulo.
6. Uma ferramenta não importa implementações internas de outra ferramenta.
7. Código só vai para o Core após reutilização real e contrato estável.
8. Métricas de uso não ficam no manifesto.
9. Regras contábeis, dinheiro, vigência e auditoria serão definidos nos Lotes 4 e 5.
10. O módulo inicia em `draft` e só muda de estado após revisão.

## Rotas de API

O arquivo `routes/tools-api.php` carrega somente módulos que implementam
`HasApiRoutes`. O Laravel aplica o prefixo e middleware de API configurados pelo
framework. Uma ferramenta sem API não implementa essa capacidade.

## Testes

O `phpunit.xml` inclui `app/Tools/**/Tests`, permitindo que os testes permaneçam
junto de cada módulo sem ficarem fora da suíte principal.
