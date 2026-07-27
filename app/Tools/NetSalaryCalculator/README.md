# Calculadora de Salário Líquido

## Descrição

Calcula o salário líquido mensal de empregado CLT em 2026 com INSS progressivo, IRRF mensal, dependentes, pensão alimentícia dedutível e descontos informados pelo usuário.

## Funcionalidades

- cálculo progressivo do INSS do empregado;
- IRRF mensal com comparação entre deduções legais e desconto simplificado;
- redução mensal do IRRF vigente em 2026;
- proventos tributáveis e não tributáveis adicionais;
- descontos personalizados;
- memória de cálculo estruturada e snapshot normativo;
- histórico para usuário autenticado;
- exportação CSV e relatório imprimível/PDF pela infraestrutura compartilhada.

## Experiência Essencial

O visitante resolve integralmente o caso mensal regular sem autenticação. Basta informar competência, salário-base e dependentes quando houver. O resultado Essencial mostra INSS, IRRF, descontos e salário líquido, além da memória e das regras utilizadas.

## Prazzu Plus

Durante a fase inicial, todos os recursos Plus permanecem gratuitos, conforme o README da raiz. O Plus adiciona proventos e descontos personalizados, histórico autenticado e exportações. Nenhuma capacidade Plus corrige, completa ou desbloqueia uma fórmula necessária ao cálculo Essencial.

## Regras

A versão `2026.1.0` cobre competências de janeiro a dezembro de 2026. O cálculo considera um único vínculo CLT no mês. Proventos marcados como tributáveis integram as bases de INSS e IRRF; proventos informados como não tributáveis ficam fora dessas bases. A ferramenta não classifica juridicamente rubricas e não calcula férias, 13º salário, rescisão, múltiplos vínculos ou regimes previdenciários especiais.

## Integração entre ferramentas

O módulo não publica nem aceita contratos de integração neste lote. Ele funciona isoladamente e não importa classes internas de outras ferramentas.

## Dependências

Utiliza somente capacidades transversais do Core técnico: `Money`, `Percentage`, regras normativas versionadas, memória de cálculo, histórico, exportação e componentes Blade compartilhados. O IRRF mensal foi promovido ao Core porque passou a ser utilizado por mais de uma ferramenta com regra equivalente.

## Histórico de versões

- `1.0.0`: implementação inicial com INSS progressivo do empregado, IRRF 2026, memória normativa, Essencial/Plus, histórico e exportação.

## Qualidade

O módulo é publicado como `beta`, com testes unitários, feature, arquitetura e golden cases. As fontes oficiais e premissas ficam documentadas em `docs/NORMATIVE_RULES.md`.
