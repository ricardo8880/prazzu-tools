# Módulos de ferramentas

Cada ferramenta vive em `app/Tools/<NomeDaFerramenta>` e implementa
`App\Core\Tools\Contracts\ToolModule`.

O contrato mínimo expõe um `ToolManifest`. Recursos opcionais são declarados por
capacidades específicas:

- `HasWebRoutes`;
- `HasApiRoutes`;
- `HasViews`;
- `HasMigrations`.

## Criando um módulo

```bash
php artisan make:tool CalculadoraRescisao \
    --slug=calculadora-rescisao \
    --category=trabalhista
```

O gerador cria apenas a estrutura inicial. Camadas como `Domain`, `Application`
e `Infrastructure` devem ser adicionadas quando houver responsabilidade real
para elas.

## Regras obrigatórias

1. O slug é imutável e único.
2. Rotas usam o prefixo `tools.<slug>.`.
3. Views usam o namespace `tools-<slug>`.
4. A ferramenta depende do Core, nunca da implementação interna de outra.
5. Métricas de uso não pertencem ao manifesto.
6. O manifesto declara versão semântica, acesso e ciclo de vida.
7. Uma capacidade só é implementada quando o módulo realmente a utiliza.
8. Rotas, views e migrations declaradas permanecem dentro do próprio módulo.
9. Testes ficam em `Tests/Unit` e `Tests/Feature` dentro do módulo.
10. Toda ferramenta inicia como `draft` até concluir sua validação.
11. Dinheiro, percentuais, datas, vigência, CPF e CNPJ usam os value objects do Core.
12. Regras de domínio recebem a data de referência; não consultam `now()` internamente.

Consulte `docs/LOT-3-MODULE-STANDARD.md` para a convenção do módulo e
`docs/LOT-4-ACCOUNTING-FOUNDATIONS.md` para os fundamentos contábeis.
