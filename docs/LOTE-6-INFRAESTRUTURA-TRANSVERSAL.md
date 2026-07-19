# Lote 6 — acesso e infraestrutura transversal

> **Registro histórico, não normativo.** Este documento descreve o estado do
> lote em que foi produzido. Para as regras atuais, prevalecem o `README.md` da
> raiz, `app/Tools/README.md` e `docs/ARCHITECTURE.md`. Nomes antigos de planos
> ou modelos de acesso não representam o contrato atual.

Este lote prepara a aplicação para ferramentas públicas, autenticadas, premium e internas sem implementar telas de autenticação, cobrança ou modificar o layout.

## Princípios

- A ferramenta declara seu tipo de acesso no `ToolManifest`.
- Controllers não decidem acesso com condicionais próprias.
- Feature flags podem desabilitar uma ferramenta sem remover código.
- Limites de uso são aplicados por uma chave de sujeito opaca.
- Métricas não armazenam payloads contábeis.
- Integrações externas dependem de contrato e possuem timeout e retry.
- Processamentos demorados devem implementar jobs de fila.

## Acesso

`DefaultToolAccessGate` considera:

1. status da ferramenta;
2. feature flag `tools.<slug>.enabled`;
3. autenticação;
4. plano;
5. papel interno.

Os motivos de recusa são códigos estáveis, não mensagens destinadas à interface.

## Organizações

As tabelas `organizations` e `organization_user` permitem associar usuários a escritórios ou empresas. O papel global do usuário não substitui o papel dentro de uma organização.

## Planos

O lote cria apenas os planos técnicos `free` e `premium`. Cobrança, gateway, renovação e webhooks deverão ser construídos quando o modelo comercial estiver definido.

## Limites

O limitador comercial por execução descrito neste lote histórico foi removido.
A experiência Essencial atual é completa e ilimitada; proteção operacional
contra abuso deve usar throttling técnico separado da política Essencial/Plus.

## Métricas

A tabela `tool_usage_events` registra somente:

- slug;
- evento;
- usuário e organização opcionais;
- duração opcional;
- horário.

Entradas e resultados pertencem ao histórico controlado do Lote 5, não às métricas.

## Integrações

Ferramentas devem depender de `ExternalServiceClient` ou de um contrato mais específico criado dentro de `Infrastructure`. O cliente padrão possui timeout e tentativas controladas.

## Filas

Jobs demorados devem implementar `AsynchronousToolJob`, declarar o slug e usar os contratos normais do Laravel (`ShouldQueue`). O marcador permite testes arquiteturais e observabilidade por ferramenta.

## O que não foi antecipado

- telas de login e cadastro;
- cobrança real;
- gateway de pagamento;
- feature flags por porcentagem;
- painel administrativo;
- permissões específicas ainda desconhecidas.
