# Calculadora de Férias

## Descrição

Ferramenta trabalhista para estimar dias de direito, férias, terço constitucional, abono pecuniário, médias remuneratórias, descontos informados e prazos do período aquisitivo e concessivo.

## Funcionalidades

- remuneração-base formada por salário, médias e adicionais habituais;
- redução de dias pelas faixas de faltas injustificadas;
- cálculo dos dias de descanso e do abono pecuniário;
- terço constitucional discriminado;
- descontos manuais;
- memória detalhada de valores e prazos;
- alertas de domínio, SEO e evento de analytics.

## Experiência Essencial

O Lote 2 disponibiliza gratuitamente o formulário público, o cálculo completo do escopo atual, a memória dos valores, os períodos aquisitivo e concessivo e o prazo estimado para pagamento, sem exigir autenticação.

## Prazzu Plus

Planejado para o Lote 3:

- histórico e repetição de cálculos;
- múltiplos funcionários;
- calendário e planejamento;
- alertas operacionais;
- exportações profissionais.

## Regras

A ferramenta usa a versão normativa indicada pelo motor e as regras detalhadas em `docs/NORMATIVE_RULES.md`. Ela não apura automaticamente INSS e IRRF e não substitui a conferência do responsável trabalhista.

## Dependências

- Core de dinheiro e arredondamento;
- contrato compartilhado de cálculo;
- catálogo e manifesto de ferramentas;
- métricas compartilhadas de uso;
- componentes Blade oficiais da plataforma.

## Histórico de versões

- `0.1.0`: fundação de domínio, contratos, testes e golden cases do Lote 1.
- `0.2.0`: experiência Essencial pública, validações, resultado detalhado, SEO, analytics e testes de feature do Lote 2.

## Lote 3 — Plus e estabilização

A versão 1.0 adiciona histórico autenticado, recuperação de entradas, exclusão, exportações CSV/JSON/PDF e planejamento para até 50 colaboradores. O cálculo individual completo permanece Essencial; os recursos Plus servem exclusivamente para produtividade.
