# Lote 3 — Padrão interno dos módulos

## Objetivo

Garantir que novas ferramentas sejam criadas com a mesma arquitetura, os mesmos
limites e os mesmos pontos de extensão. O `README.md` da raiz é a autoridade
máxima deste padrão.

## Estrutura oficial obrigatória

```text
app/Tools/NomeDaFerramenta/
├── Application/                 # Actions, casos de uso e DTOs de aplicação
├── Domain/                      # regras, cálculos, value objects e resultados
├── Infrastructure/             # banco, arquivos, repositórios e APIs
├── Presentation/
│   ├── Controllers/
│   └── Requests/
├── Resources/
│   ├── views/
│   ├── js/                      # quando houver comportamento específico
│   └── css/                     # quando Bootstrap/Core não forem suficientes
├── Routes/
│   ├── web.php
│   └── api.php                  # quando a ferramenta expuser API
├── Tests/
│   ├── Unit/
│   ├── Feature/
│   └── Browser/                 # obrigatório para interfaces complexas
├── Tool.php
└── README.md
```

`Application`, `Domain`, `Infrastructure`, `Presentation`, `Resources`,
`Routes`, `Tests`, `README.md` e `Tool.php` são obrigatórios desde a criação do
módulo. Não é permitido omitir uma camada nem deslocar sua responsabilidade por
ela ainda não possuir uma implementação concreta.

## Limites das camadas

### Presentation

Controllers recebem Request, chamam Actions e retornam Response. Eles não
calculam, consultam banco ou APIs, geram arquivos, registram histórico ou
implementam regras comerciais.

### Application

Actions orquestram casos de uso, chamam o Domain e coordenam contratos do Core
ou da Infrastructure. Não contêm regras de negócio.

### Domain

O Domain contém todas as regras, cálculos e validações de negócio. Ele é
determinístico, não depende do Laravel, não consulta configuração ou tempo
global e não usa `float` para dinheiro ou percentuais.

### Infrastructure

Implementa persistência, repositórios, armazenamento, APIs e integrações
específicas da ferramenta. Migrations exclusivas do módulo permanecem nesta
camada e são declaradas ao carregador por `HasMigrations`.

## Gerador

```bash
php artisan make:tool CalculadoraRescisao \
    --slug=calculadora-rescisao \
    --category=trabalhista \
    --access=free \
    --status=draft
```

O comando deve criar:

- as sete pastas obrigatórias;
- manifesto e capacidades web/views;
- Action inicial, controller e Form Request;
- arquivo próprio de rotas;
- view inicial;
- testes Unit e Feature;
- README com as seções obrigatórias;
- registro no grupo correspondente em `config/tools/modules.php`.

O estado padrão é `draft`. Uma ferramenta não pode ser ativada enquanto possuir
testes incompletos, metadados provisórios ou qualquer item pendente na definição
de ferramenta pronta do README raiz.

## Grupos de registro

Os grupos existem apenas para reduzir conflitos de edição do arquivo de
configuração:

- `general`;
- `fiscal`;
- `labor`;
- `corporate`;
- `documents`.

Eles não definem namespace nem alteram a estrutura física do módulo.

## Regras de reutilização

Antes de implementar uma funcionalidade, deve ser respondido: outra ferramenta
poderá utilizá-la? Se sim, ela pertence ao Core. Não é necessário aguardar uma
segunda cópia surgir para então centralizá-la.

Pertencem ao Core, entre outros:

- exportação, impressão, PDF, CSV e XLSX;
- histórico, favoritos e compartilhamento;
- autenticação, autorização, planos e limites;
- analytics, auditoria e notificações;
- componentes Blade, máscaras, helpers e traits reutilizáveis;
- objetos de dinheiro, percentuais, datas, CPF e CNPJ.

Uma ferramenta nunca importa a implementação interna de outra ferramenta. Ela
conhece somente o próprio domínio e os contratos disponibilizados pelo Core.

## Assets

Views, JavaScript e CSS específicos ficam dentro de `Resources` do módulo. É
proibido espalhar scripts da ferramenta por `resources/js/tools` ou estilos
específicos pelo CSS global.

Todo script deve limitar sua atuação ao elemento
`[data-tool="<slug-da-ferramenta>"]`. Bootstrap e componentes compartilhados
têm prioridade; CSS próprio só é criado quando eles não resolvem a necessidade.
O pipeline Vite compartilhado pode registrar as entradas, mas o código-fonte
continua dentro do módulo.

## Rotas e views

1. A rota principal começa com `tools.<slug>.`.
2. Endpoints apontam para controllers; handlers em closures são proibidos.
3. Cada ferramenta possui seu arquivo de rotas.
4. Views usam o namespace `tools-<slug>`.
5. Uma ferramenta não utiliza views de outra ferramenta.

O arquivo `routes/tools-api.php` carrega somente módulos que implementam
`HasApiRoutes`. Uma ferramenta sem API não implementa essa capacidade, mas
mantém a pasta obrigatória `Routes`.

## Acesso e persistência

Ferramentas declaram capacidades Essenciais e Plus, mas não decidem cobrança,
plano, gratuidade ou limites. Durante a fase gratuita de lançamento, visitantes
utilizam a capacidade máxima sem autenticação. Login é exigido somente para
persistência e continuidade, como salvar, consultar histórico e favoritos.

## README do módulo

O README de cada ferramenta deve conter explicitamente:

- Descrição;
- Funcionalidades;
- Regras;
- Dependências;
- Histórico de versões.

## Testes

O `phpunit.xml` inclui `app/Tools/**/Tests`, permitindo que os testes permaneçam
junto de cada módulo. Toda ferramenta possui Unit e Feature; interfaces
complexas também possuem Browser Tests.
