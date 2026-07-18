# Scripts das ferramentas

Este diretório não deve receber código específico de ferramentas.

O código-fonte de cada ferramenta permanece integralmente dentro do módulo,
conforme a regra de isolamento do projeto:

```text
app/Tools/<NomeDaFerramenta>/Resources/js/index.js
```

O pipeline Vite compartilhado descobre automaticamente `Resources/js/index.js`
e `Resources/css/index.css` em cada módulo. A view da ferramenta carrega a
entrada necessária com `@vite`, sem copiar arquivos para `resources/js/tools`.

Todo script específico deve buscar somente o escopo da própria ferramenta:

```js
const root = document.querySelector('[data-tool="<slug-da-ferramenta>"]');

if (root) {
    // comportamento exclusivo da ferramenta
}
```

Regras reutilizáveis por duas ou mais ferramentas pertencem ao Core ou aos
assets compartilhados da plataforma. Uma ferramenta nunca importa scripts nem
implementações internas de outra.
