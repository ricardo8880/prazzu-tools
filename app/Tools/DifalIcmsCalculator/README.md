# Calculadora DIFAL / ICMS Interestadual + FCP

## Descrição

Módulo fiscal independente para simular o diferencial de alíquotas em operação interestadual destinada a consumidor final, sem inferir dados estaduais ou de mercadoria que não possam ser determinados com segurança apenas pela UF.

## Funcionalidades

- DIFAL por base única;
- alíquota interestadual informada ou assistida em 7%, 12% e 4% quando a hipótese legal for confirmada;
- FCP parametrizado;
- cenário de base dupla/por dentro;
- indicação da responsabilidade conforme destinatário contribuinte ou não;
- memória normativa, histórico autenticado, CSV e impressão/PDF.

## Experiência Essencial

A experiência gratuita resolve o problema principal: dada a base tributável e as alíquotas aplicáveis confirmadas pelo profissional, calcula o DIFAL e apresenta memória de cálculo e fontes. Durante o lançamento, todos os recursos Plus também permanecem acessíveis sem autenticação, conforme README da raiz.

## Prazzu Plus

A camada Plus acrescenta produtividade e cenários sem alterar a correção do cálculo Essencial: assistência da alíquota interestadual, FCP parametrizado, base dupla/por dentro quando aplicável, histórico autenticado e exportações.

## Regras

- Origem e destino devem ser UFs diferentes.
- A regra assistida de 7%/12% segue a Resolução do Senado nº 22/1989.
- A regra assistida de 4% só deve ser marcada quando a operação estiver efetivamente abrangida pela Resolução do Senado nº 13/2012.
- Alíquota interna, FCP, NCM, benefício fiscal e método de base devem ser confirmados na legislação aplicável.
- O método de base dupla é disponibilizado como cenário parametrizado, não como decisão jurídica automática.
- O resultado é fiscal e estimativo e não substitui GNRE/DARE, Portal Nacional do DIFAL nem revisão da legislação estadual.

## Dependências

Somente infraestrutura transversal do Core: `Money`, `Percentage`, `IntegerRounding`, catálogo/snapshot normativo, memória de cálculo, histórico, exportação e contratos de ferramenta. O módulo não importa classes internas de outras ferramentas.

## Histórico de versões

- `1.0.0` — Expansão Lote 13: DIFAL, alíquota interestadual assistida, FCP, base simples/dupla, memória fiscal e exportações.
