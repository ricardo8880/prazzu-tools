# Qualidade — Calculadora de Honorários Contábeis

## Perfil

- Natureza: cálculo financeiro gerencial.
- Dependência normativa: nenhuma; índices de reajuste são informados pelo usuário.
- Dados pessoais: comuns em rótulos/observações persistidos no histórico.
- Integração externa: opcional; o cálculo principal não depende dela.
- Persistência: histórico autenticado.
- Resultado: financeiro estimativo.

## Evidência verificada

- [x] Precificação principal possui regressão unitária com resultado monetário conhecido.
- [x] Regra de ao menos um sócio/titular possui teste de fronteira.
- [x] Reajuste cobre percentual positivo, negativo, entrada inválida e HalfUp em centavos.
- [x] Casos dourados são derivados dos testes executáveis existentes, sem referência placeholder.
- [x] O README explicita que honorários são estimativa gerencial e que índice oficial não é consultado automaticamente.
- [x] Dinheiro e percentuais usam value objects do Core, sem `float` no domínio.
- [x] Histórico declara os campos sensíveis e usa política compartilhada da plataforma.
- [x] Exportação e documentos atuais reutilizam capacidades compartilhadas, sem transformar o módulo em CRM.
- [x] Essencial resolve precificação e reajuste; Plus adiciona produtividade e continuidade.
