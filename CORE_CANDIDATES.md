# Candidatos a Componentes Compartilhados do Core Técnico

Este arquivo registra implementações que **podem** se tornar componentes compartilhados do Prazzu Tools no futuro.

Ele não representa dívida técnica nem uma lista automática de tarefas. Seu objetivo é impedir que oportunidades reais de reutilização sejam esquecidas, sem antecipar abstrações antes de existir repetição comprovada.

> Antes de criar uma nova ferramenta, adicionar uma capacidade transversal ou promover código para o Core técnico, leia este arquivo e atualize os candidatos afetados.

## Regra de promoção

Um candidato só deve ser promovido para o Core técnico quando:

1. duas ou mais ferramentas possuírem uma necessidade realmente equivalente;
2. a implementação puder ser compartilhada sem condicionais específicas de cada domínio;
3. a extração reduzir duplicação concreta ou padronizar um comportamento transversal;
4. a API compartilhada puder ser definida a partir de casos reais já implementados;
5. a mudança preservar a independência dos módulos e as regras do README da raiz.

A existência de apenas uma ferramenta usuária não justifica, por si só, uma extração antecipada.

## Status possíveis

- **Aguardando segunda ferramenta**: existe potencial claro, mas somente uma ferramenta utiliza a capacidade.
- **Em observação**: há semelhança entre implementações, porém ainda não existe evidência suficiente de uma abstração comum.
- **Pronto para extrair**: duas ou mais ferramentas possuem repetição estrutural concreta e compatível.
- **Extraído para o Core**: o componente já pertence à infraestrutura compartilhada.
- **Manter no domínio**: a implementação continua específica de uma ferramenta e não deve ser generalizada.

## Candidatos atuais

| Candidato | Implementação atual | Ferramentas que utilizam | Status | Gatilho para reavaliação |
|---|---|---|---|---|
| Conversão de valores monetários por extenso | Domínio do Emissor de Recibos | Emissor de Recibos | **Aguardando segunda ferramenta** | Reavaliar quando contratos, declarações ou outro gerador também precisar converter valores monetários por extenso. |
| Construção de entrada tipada a partir de formulário validado | `BuildCalculationInput` dentro do Emissor de Recibos | Emissor de Recibos | **Manter no domínio** | Reavaliar somente se outras ferramentas repetirem a mesma estrutura de transformação, e não apenas o conceito genérico de Request → validação → DTO. |
| Geração e download de modelos CSV | Modelo específico da importação em lote do Emissor de Recibos; leitura já usa o Core compartilhado | Emissor de Recibos | **Aguardando segunda ferramenta** | Reavaliar quando outra ferramenta também precisar disponibilizar um arquivo-modelo CSV com resposta, cabeçalhos e regras de download equivalentes. |

## Componentes já compartilhados relacionados

| Componente | Situação |
|---|---|
| Leitor de CSV | Já pertence ao Core técnico e deve ser reutilizado por importações em lote. |
| Exportação por impressão/PDF | Já utiliza a infraestrutura compartilhada do Core técnico. |
| Persistência e histórico | Devem utilizar os mecanismos compartilhados da plataforma, mantendo no módulo somente os dados e regras do domínio. |

## Procedimento obrigatório para assistentes de IA

Ao iniciar qualquer lote ou tarefa:

1. leia o README da raiz;
2. leia este `CORE_CANDIDATES.md`;
3. verifique se a tarefa atual ativa o gatilho de algum candidato;
4. procure repetição concreta no projeto antes de criar uma nova abstração;
5. se um candidato estiver pronto para extração, implemente a promoção somente quando ela fizer parte do escopo ou for necessária para evitar duplicação no trabalho atual;
6. atualize este arquivo sempre que um candidato surgir, mudar de status, for descartado ou for extraído para o Core técnico.

Quando uma nova oportunidade ainda não justificar extração, registre-a aqui em vez de criar antecipadamente um componente genérico.
