# Automação E2E — Lote 4 — Contrato visual e seletores estáveis

## Estado reconstruído

O lote foi iniciado a partir do ZIP original, com aplicação sequencial dos Lotes 1, 2 e 3. Foram relidos o README raiz, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, os relatórios E2E anteriores e os inventários executáveis.

## Escopo entregue

- normalizador compartilhado `TestId` no Core de Qualidade;
- contrato oficial documentado para seletores `data-testid`;
- raiz de ferramenta identificada por slug;
- painéis compartilhados de formulário e resultado;
- resumo de validação;
- inputs, selects e switches compartilhados;
- ações compartilhadas de PDF, XLSX, exportação e impressão;
- sobrescrita explícita de identificador para páginas com múltiplos painéis;
- piloto Playwright migrado para o contrato estável;
- testes unitário e arquitetural do contrato.

## Decisões

O atributo `data-testid` não substitui acessibilidade, labels, nomes de campo ou marcadores de Analytics. Ele existe exclusivamente para automação. Nenhum seletor é usado para CSS ou regra de domínio.

A adoção foi centralizada nos componentes compartilhados, conforme a regra do README que exige capacidades transversais no Core. Views legadas não foram reestruturadas em massa neste lote, evitando mudanças funcionais sem cenário concreto.

## Critérios de aceite

- o piloto não usa posição, classe Bootstrap ou primeiro formulário/botão;
- componentes compartilhados expõem identificadores previsíveis;
- nomes complexos de campo geram identificadores ASCII estáveis;
- páginas podem distinguir múltiplos painéis sem duplicar componente;
- nenhuma rota, slug, cálculo ou resultado foi alterado.

## Continuidade obrigatória para o Lote 5

1. Reconstruir o ZIP original e reaplicar os Lotes 1, 2, 3 e 4 em ordem.
2. Preservar os identificadores oficiais deste contrato.
3. Implementar descoberta automática das 32 ferramentas e smoke universal.
4. Classificar views legadas sem alterar regra de domínio.
5. Não implementar ainda o motor declarativo completo de cenários do Lote 6.
