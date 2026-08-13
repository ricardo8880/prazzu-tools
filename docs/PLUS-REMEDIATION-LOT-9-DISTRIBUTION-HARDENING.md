# Remediação Prazzu Plus — Lote 9 — Higiene de distribuição

## Origem

A auditoria do projeto consolidado confirmou os 137 contratos Plus, mas identificou resíduos no ZIP completo: arquivo `.env`, metadados Git, `vendor`, `node_modules` e uma segunda raiz Laravel desatualizada em `prazzu-tools/prazzu-tools`.

## Ajustes

- o empacotador oficial ignora raízes Laravel aninhadas;
- o validador inspeciona resíduos proibidos em qualquer profundidade;
- arquivos `.env` são rejeitados, preservando somente os exemplos oficiais;
- o saneamento local remove a cópia duplicada e executa `git rm --cached --ignore-unmatch .env`, sem apagar o ambiente local;
- o CI constrói o ZIP oficial e exige sua aprovação após todos os gates de release.

## Aplicação

Depois de aplicar este lote, execute uma vez:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\cleanup-project.ps1
```

Para gerar entregas futuras:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\package-distribution.ps1
```

Não compacte diretamente a pasta de desenvolvimento.

## Compatibilidade

O lote não altera comportamento de produto. Permanecem 43 ferramentas, 137 contratos Plus estritos e funcionais, e dívidas estrutural e funcional zeradas.
