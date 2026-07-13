# Lote 9 — Estabilização técnica

Este lote estabiliza a ferramenta-piloto sem alterar o layout ou a aparência da plataforma.

## Correções

- cálculo e exportação usam o mesmo Form Request;
- exportação passa pela autorização e pelo limite de uso;
- motivos de bloqueio são convertidos em respostas HTTP específicas;
- erros esperados do domínio retornam como erros de validação;
- histórico persistente é criado somente para usuários autenticados;
- métricas anônimas continuam sem armazenar os dados do formulário;
- métodos do controller possuem tipos de retorno;
- arquivos alterados usam `strict_types`;
- exportação CSV valida a abertura do stream;
- testes cobrem cálculo, exportação, validação e custo total igual a zero.

## Validação local recomendada

```bash
composer dump-autoload
php artisan optimize:clear
php artisan migrate:fresh
composer format
composer quality
npm ci
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

O comando `composer format` continua sendo a forma oficial de corrigir automaticamente arquivos antigos que ainda estejam fora do padrão do Pint.
