# Essencial × Plus e retenção — Lote 7

## Base reconstruída

O lote foi executado sobre o estado acumulado reconstruído obrigatoriamente em:

1. ZIP original;
2. Lote 1 — maturidade;
3. Lote 2 — auditoria fiscal/trabalhista;
4. Lote 3 — testes e confiabilidade;
5. Lote 4 — confiança normativa;
6. Lote 5 — UX;
7. Lote 6 — catálogo e descoberta.

Antes das alterações foram relidos `README.md`, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, os relatórios dos Lotes 1–6, `config/product_tools.php` e a governança Prazzu Plus.

## Objetivo

Garantir que a divisão Essencial × Plus continue obedecendo à promessa da raiz — Essencial resolve e permanece transparente; Plus acrescenta produtividade — e fechar a lacuna de continuidade pós-resultado nas páginas legadas.

## Transparência que não pode ser paywall

A auditoria encontrou três capacidades declaradas como Plus que são necessárias para conferir o resultado básico:

- `calculadora-pis-cofins:memory` — memória de cálculo e fontes normativas;
- `calculadora-retencoes-nota-fiscal:memory` — memória de cálculo e premissas;
- `calculadora-retencoes-nota-fiscal:report` — a implementação atual controla a conferência por tributo e nota dentro da própria tela de resultado.

As três foram promovidas para `Essential`. As views deixaram de condicionar essas superfícies ao acesso Plus e agora possuem marcadores `data-essential-transparency` protegidos por teste arquitetural.

A mudança não libera produtividade paga indevida: múltiplas operações/notas, regras customizadas, cenários, exportações e histórico continuam classificados como Plus conforme seus contratos.

A governança Plus passa de 144 para 141 contratos declarados, estritos e funcionalmente certificados. O snapshot e os checksums foram atualizados em lote explícito; dívida legada continua zero.

## Retenção sem bloquear o primeiro valor

O CTA compartilhado de continuidade já aparecia nas ferramentas que usam `x-tools.page`, mas sete páginas legadas ficavam fora dele:

- Honorários Contábeis;
- Validador CNPJ/CPF/IE;
- DARF/GPS;
- Rescisão;
- Margem/Markup;
- Emissor de Recibos;
- Férias.

Essas páginas agora também renderizam `x-tools.plus-result-cta` depois da entrega de valor. O componente passou a resolver sozinho a rota de histórico quando a ferramenta suporta persistência, evitando wiring específico por página.

O comportamento continua fiel ao README:

- visitante calcula sem conta;
- CTA só é revelado depois que existe resultado significativo;
- cadastro é apresentado como continuidade opcional;
- usuário autenticado recebe acesso ao histórico real quando ele existe;
- não há CRM, workflow ou gestão de cliente.

## Governança

`EssentialPlusRetentionLot7Test` impede regressão da classificação de transparência e exige o CTA nas sete páginas legadas. `scripts/check-accounting-pains.php` deixou de congelar a quantidade histórica 144 e passa a validar strict/functional contra a contagem declarada, permitindo mudanças futuras apenas quando a configuração governada for atualizada explicitamente.

Nenhum slug, rota pública, ID, vertical, `release_order`, fórmula, regra normativa ou status de maturidade foi alterado.

## Estado após o lote

- 50 ferramentas oficiais;
- 49 Contabilidade + 1 RH;
- 13 `active`, 37 `beta`, 0 `draft`;
- 141 contratos Plus declarados;
- 141 contratos Plus estritos;
- 141 contratos Plus funcionalmente certificados;
- dívida Plus zero;
- `release_readiness = essential_plus_retention_lot_7_hardened`.

## Continuidade para o Lote 8

Antes do próximo lote, reconstruir obrigatoriamente **ZIP original → Lotes 1 → 7**, reler a documentação vigente e preservar a decisão deste lote: transparência necessária à conferência do cálculo é Essencial e não deve voltar a ser bloqueada por plano.
