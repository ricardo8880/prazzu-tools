# Qualidade — Gerador DARF/GPS

## Classificação
Ferramenta tributária de risco crítico, com dependência normativa alta, processamento síncrono, histórico persistente e exportações CSV, JSON e PDF.

## Contrato normativo
- identificador: `federal_payment_guide.late_payment_charges`;
- versão: `2026.1.0`;
- resolução: pela data de vencimento usando `App\Core\Normative`;
- fontes: Lei nº 11.941/2009 e orientações oficiais da Receita Federal;
- multa de mora: 0,33% por dia, limitada a 20%;
- juros: percentual Selic acumulado informado pelo usuário;
- conferência obrigatória no sistema oficial antes do pagamento;
- calendário local limitado a sábados e domingos.

## Cobertura obrigatória
A suíte aprovada cobre caso típico, fronteira, entrada inválida, não aplicável, arredondamento, transição normativa e regressão.

## Privacidade
A ferramenta não solicita CPF ou CNPJ. O principal financeiro armazenado no histórico segue a política de proteção e retenção do Core.

## Restrições operacionais
A ferramenta não emite documento oficial, não transmite dados à Receita Federal e não substitui SicalcWeb ou sistema oficial aplicável.
