# Qualidade da Calculadora de Férias

O README da raiz é a autoridade máxima.

## Perfil de risco

- Natureza: cálculo
- Dependência normativa: alta
- Dados pessoais: nenhum no cálculo individual atual
- Integração externa: nenhuma
- Persistência planejada: histórico
- Processamento: síncrono
- Risco: trabalhista
- Atualização: anual ou sempre que houver mudança normativa
- Exportações Plus previstas: CSV, JSON e PDF

## Lote 1 concluído

- [x] Perfil de risco executável.
- [x] Manifesto com separação Essencial/Plus.
- [x] Dinheiro calculado sem `float`.
- [x] Faixas de faltas testadas nas fronteiras.
- [x] Abono pecuniário testado.
- [x] Datas aquisitiva, concessiva e de pagamento testadas.
- [x] Casos dourados sem placeholders.
- [x] Nenhuma dependência interna de outra ferramenta.

## Pendente para ativação

- [ ] Interface pública completa e acessível.
- [ ] Validação monetária específica no Form Request.
- [ ] Incidências automáticas definidas e testadas, caso entrem no escopo.
- [ ] Histórico e exportações Plus integrados ao Core.
- [ ] Testes Feature e Browser do fluxo final.
- [ ] Revisão normativa responsável antes da mudança para `beta`.
- [ ] `composer release:check` integralmente verde.
