# Módulos de ferramentas

Cada ferramenta vive em uma pasta própria dentro de `app/Tools` e implementa
`App\Core\Tools\Contracts\ToolModule`.

Estrutura mínima recomendada:

```text
app/Tools/NomeDaFerramenta/
├── NomeDaFerramentaTool.php
├── Http/
├── Services/
└── routes.php
```

Crie apenas as pastas necessárias. Uma ferramenta não pode depender da
implementação interna de outra. Código realmente compartilhável deve ser movido
para `app/Core`.
