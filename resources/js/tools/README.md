# Scripts das ferramentas

Cada ferramenta que precisar de JavaScript deve ter um arquivo próprio nesta pasta.

Exemplo: `resources/js/tools/validador-cnpj.js`.

O script deve buscar somente o seu escopo:

```js
const root = document.querySelector('[data-tool="validador-cnpj"]');

if (root) {
    // comportamento exclusivo desta ferramenta
}
```

Nunca importe diretamente o script ou a implementação interna de outra ferramenta.
