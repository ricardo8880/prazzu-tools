# Remediação Prazzu Plus — Lote 1 — Fundação de certificação funcional

## Base reconstruída

O trabalho partiu do ZIP original e considerou os pacotes de correção já entregues nesta sequência. Foram relidos integralmente o README da raiz, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, o inventário executável e os oito relatórios anteriores de monetização Plus.

## Problema confirmado

O estado anterior diferenciava 61 contratos estruturais estritos e 76 contratos em `legacy_debt`. O contrato estrito comprovava implementação identificável por texto, autorização e teste Free × Plus, mas não comprovava necessariamente o comportamento prometido ao usuário.

As contagens também não impediam todas as substituições silenciosas: seria possível trocar uma chave por outra preservando os totais consolidados.

## Alterações

1. O catálogo completo de 137 contratos e a composição dos 76 legados receberam checksums ordenados. Alterações deliberadas continuam permitidas, mas exigem atualização explícita dos snapshots no mesmo lote.
2. Foi criada a dimensão `functional_contracts`, independente de `strict_contracts`.
3. Os 137 benefícios atuais começam como dívida funcional. Nenhum foi certificado retrospectivamente apenas com evidência estática.
4. A dívida funcional possui teto 137 e não pode crescer. Uma nova feature precisa nascer certificada ou o gate arquitetural falha.
5. O atributo `CoversPlusFeature` liga um método de teste comportamental a um contrato `slug:feature` concreto.
6. O `PlusFeatureReadinessInspector` exige esse marcador sempre que um contrato entra em `functional_contracts`.
7. O teste arquitetural global passou a validar checksums, duplicatas, contratos funcionais inexistentes/legados, teto da dívida e piso de certificações.

## Regra para os próximos lotes

Uma feature só pode entrar em `functional_contracts` depois de:

- existir comportamento de domínio ou produtividade verificável;
- possuir gate central correto;
- manter o Essencial completo;
- possuir teste Free × Plus;
- possuir teste comportamental marcado com `CoversPlusFeature`;
- sair de `legacy_debt`, quando ainda estiver nela;
- atualizar contagens, snapshots e documentação no mesmo lote.

## Compatibilidade

- Nenhum slug, rota pública, manifesto ou cálculo foi removido.
- O modo operacional continua `launch_free`.
- Nenhuma feature foi promovida artificialmente para o estado certificado.
- O inventário permanece com 43 ferramentas e 137 recursos Plus.

## Continuidade

Antes do Lote 2, reconstruir a base na ordem: ZIP original → todos os ajustes anteriores → Remediação Plus Lote 1. O próximo lote deve tratar as cinco ferramentas cujos recursos Plus permanecem integralmente na dívida estrutural, sem recriar esta fundação.
