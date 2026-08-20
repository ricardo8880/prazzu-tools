# Simulador da Reforma Tributária do Consumo

## Descrição

Simulador paramétrico da transição da Reforma Tributária do Consumo de 2026 a 2033.

## Funcionalidades

- comparação entre a carga atual informada e a etapa anual da transição;
- CBS e IBS com alíquotas de referência informadas pelo usuário quando ainda não determinadas pelo próprio ano da transição;
- aproximação de créditos a partir do percentual de base informado;
- legado federal e subnacional remanescente;
- memória de cálculo, snapshot normativo por ano simulado, fontes oficiais e alertas explícitos de escopo.

## Experiência Essencial

Simula qualquer etapa de 2026 a 2033 sem inventar alíquotas futuras, enquadramento, benefício ou regime específico. O resultado principal, as premissas e os alertas permanecem Essenciais.

## Prazzu Plus

`transition_diagnostics` detalha alíquotas, percentuais remanescentes e parâmetros usados na transição. O Plus não corrige nem completa a fórmula Essencial.

## Regras

A transição geral segue a EC 132/2023, a LC 214/2025 e alterações/regulamentações posteriores aplicáveis. Em 2026, CBS de 0,9% e IBS de 0,1% são alíquotas-teste: o simulador **não as soma como custo adicional automático** à carga federal legada. Quando há valor líquido simulado de CBS/IBS, ele é compensado contra a carga federal legada informada até o respectivo limite; eventual dispensa por cumprimento das obrigações acessórias é explicitada como premissa.

A ferramenta continua estimativa: não contempla regimes específicos, reduções, Imposto Seletivo, cashback, classificação jurídica da operação ou apuração documento a documento.

Detalhes e fontes: `docs/NORMATIVE_RULES.md`. O resultado também expõe o snapshot normativo versionado na superfície compartilhada de confiança, usando 1º de janeiro do ano simulado como referência da etapa anual de transição.

## Dependências

Nenhuma integração externa. Utiliza apenas `Money`, `Percentage`, memória de cálculo e contratos compartilhados da plataforma.

## Histórico de versões

- `1.0.0` — implementação inicial da transição geral.
- `1.1.0` — Lote 2: corrige o tratamento econômico do ano-teste de 2026 para não acumular automaticamente CBS/IBS sobre a carga federal legada e amplia a transparência normativa.
- regra normativa `2026.08.3` — Lote 4: formaliza a transição no contrato `App\Core\Normative`, registra fontes oficiais verificadas em 19/08/2026 e expõe snapshot por ano simulado.
