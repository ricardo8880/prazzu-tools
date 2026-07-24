<section class="prazzu-tool-workspace text-start mt-4" aria-labelledby="batch-title">
    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-3">
        <div>
            <span class="badge text-bg-primary mb-2">Lote de produtos</span>
            <h2 id="batch-title" class="mb-1">Calcular vários produtos</h2>
            <p class="text-body-secondary mb-0">Adicione, duplique, filtre e compare até 100 produtos em um único cálculo.</p>
        </div>
        <div class="d-flex flex-wrap gap-2 align-self-lg-end">
            <button class="btn btn-outline-primary" type="button" id="batch-add"><i class="bi bi-plus-lg me-1"></i>Adicionar produto</button>
            <button class="btn btn-outline-secondary" type="button" id="batch-duplicate"><i class="bi bi-copy me-1"></i>Duplicar selecionados</button>
            <button class="btn btn-outline-danger" type="button" id="batch-remove"><i class="bi bi-trash me-1"></i>Remover selecionados</button>
        </div>
    </div>

    <div class="row g-2 mb-3">
        <div class="col-12 col-md-5">
            <label class="form-label" for="batch-search">Buscar produto</label>
            <div class="input-group"><span class="input-group-text"><i class="bi bi-search"></i></span><input class="form-control" id="batch-search" placeholder="Nome, código ou categoria"></div>
        </div>
        <div class="col-12 col-md-4">
            <label class="form-label" for="batch-category">Filtrar categoria</label>
            <select class="form-select" id="batch-category"><option value="">Todas as categorias</option></select>
        </div>
        <div class="col-12 col-md-3">
            <label class="form-label" for="batch_reference_date">Data de referência</label>
            <input class="form-control" id="batch_reference_date" form="batch-form" type="date" name="reference_date" value="{{ old('reference_date', date('Y-m-d')) }}" required>
        </div>
    </div>

    <form method="post" action="{{ route('tools.calculadora-margem-markup.batch.calculate') }}" id="batch-form">
        @csrf
        <div class="table-responsive border rounded">
            <table class="table table-hover table-sm align-middle mb-0" id="batch-table">
                <thead class="table-light text-nowrap">
                    <tr>
                        <th><input class="form-check-input" type="checkbox" id="batch-select-all" aria-label="Selecionar todos"></th>
                        <th>Produto</th><th>Código</th><th>Categoria</th><th>Custo base</th><th>Outros custos</th><th>Frete</th><th>Embalagem</th><th>Despesas</th><th>Margem %</th><th>Impostos %</th><th>Comissão %</th><th>Cartão %</th><th>Marketplace %</th>
                    </tr>
                </thead>
                <tbody id="batch-products">
                    @php
                        $products = old('products', [['name'=>'','code'=>'','category'=>'','base_cost'=>'','additional_costs'=>'0,00','freight_cost'=>'0,00','packaging_cost'=>'0,00','fixed_expenses'=>'0,00','desired_margin'=>'30','taxes_percentage'=>'0','commission_percentage'=>'0','card_fees_percentage'=>'0','marketplace_fees_percentage'=>'0']]);
                    @endphp
                    @foreach($products as $index => $product)
                        @include('tools-calculadora-margem-markup::partials.batch-row', ['index' => $index, 'product' => $product])
                    @endforeach
                </tbody>
            </table>
        </div>

        @error('products')<div class="alert alert-danger mt-3 mb-0">{{ $message }}</div>@enderror
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
            <small class="text-body-secondary"><span id="batch-visible-count">1</span> produto(s) visível(is)</small>
            <button class="btn btn-primary prazzu-btn-primary" type="submit"><i class="bi bi-calculator me-1"></i>Calcular produtos</button>
        </div>
    </form>
</section>

@php
    $batchResults = $batchCalculationResults ?? session('batch_calculation_results');
@endphp
@if($batchResults)
    <section class="mt-4" aria-labelledby="batch-results-title">
        <div class="d-flex justify-content-between align-items-end gap-3 mb-3">
            <div><h2 id="batch-results-title" class="prazzu-section-title mb-1">Comparação dos produtos</h2><p class="text-body-secondary mb-0">Ordene a tabela para identificar preços, lucros e margens.</p></div>
            <span class="badge text-bg-success">{{ count($batchResults) }} calculado(s)</span>
        </div>
        <div class="table-responsive border rounded">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead class="table-light text-nowrap"><tr><th>Produto</th><th>Código</th><th>Categoria</th><th class="text-end">Custo total</th><th class="text-end">Preço sugerido</th><th class="text-end">Lucro líquido</th><th class="text-end">Margem</th><th class="text-end">Markup</th><th class="text-end">Índice</th></tr></thead>
                <tbody>
                    @foreach($batchResults as $result)
                        <tr><th scope="row">{{ $result['name'] }}</th><td>{{ $result['code'] ?: '—' }}</td><td>{{ $result['category'] ?: '—' }}</td><td class="text-end text-nowrap">{{ $result['total_cost'] }}</td><td class="text-end text-nowrap fw-semibold">{{ $result['sale_price'] }}</td><td class="text-end text-nowrap">{{ $result['net_profit'] }}</td><td class="text-end">{{ $result['margin'] }}</td><td class="text-end">{{ $result['markup'] }}</td><td class="text-end">{{ $result['markup_multiplier'] }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endif

<template id="batch-row-template">@include('tools-calculadora-margem-markup::partials.batch-row', ['index' => '__INDEX__', 'product' => []])</template>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const body = document.getElementById('batch-products');
    const template = document.getElementById('batch-row-template');
    const search = document.getElementById('batch-search');
    const category = document.getElementById('batch-category');
    const visibleCount = document.getElementById('batch-visible-count');
    let nextIndex = body.querySelectorAll('tr').length;

    const rows = () => [...body.querySelectorAll('tr')];
    const reindex = () => rows().forEach((row, index) => row.querySelectorAll('[name]').forEach(input => input.name = input.name.replace(/products\[[^\]]+\]/, `products[${index}]`)));
    const refreshCategories = () => {
        const selected = category.value;
        const values = [...new Set(rows().map(row => row.querySelector('[data-field="category"]').value.trim()).filter(Boolean))].sort();
        category.innerHTML = '<option value="">Todas as categorias</option>' + values.map(value => `<option value="${value.replaceAll('"', '&quot;')}">${value}</option>`).join('');
        category.value = values.includes(selected) ? selected : '';
    };
    const filter = () => {
        const term = search.value.trim().toLowerCase();
        const selectedCategory = category.value.toLowerCase();
        let count = 0;
        rows().forEach(row => {
            const text = row.innerText.toLowerCase() + ' ' + [...row.querySelectorAll('input')].map(i => i.value.toLowerCase()).join(' ');
            const rowCategory = row.querySelector('[data-field="category"]').value.trim().toLowerCase();
            const visible = (!term || text.includes(term)) && (!selectedCategory || rowCategory === selectedCategory);
            row.classList.toggle('d-none', !visible); if (visible) count++;
        });
        visibleCount.textContent = count;
    };
    const addRow = (source = null) => {
        const wrapper = document.createElement('tbody');
        wrapper.innerHTML = template.innerHTML.replaceAll('__INDEX__', nextIndex++).trim();
        const row = wrapper.firstElementChild;
        if (source) row.querySelectorAll('input[data-field]').forEach(input => input.value = source.querySelector(`[data-field="${input.dataset.field}"]`)?.value ?? input.value);
        body.appendChild(row); refreshCategories(); filter();
    };

    document.getElementById('batch-add').addEventListener('click', () => addRow());
    document.getElementById('batch-duplicate').addEventListener('click', () => rows().filter(r => r.querySelector('[data-select]').checked).forEach(r => addRow(r)));
    document.getElementById('batch-remove').addEventListener('click', () => { rows().filter(r => r.querySelector('[data-select]').checked).forEach(r => r.remove()); if (!rows().length) addRow(); reindex(); refreshCategories(); filter(); });
    document.getElementById('batch-select-all').addEventListener('change', event => rows().filter(r => !r.classList.contains('d-none')).forEach(r => r.querySelector('[data-select]').checked = event.target.checked));
    body.addEventListener('input', event => { if (event.target.dataset.field === 'category') refreshCategories(); filter(); });
    search.addEventListener('input', filter); category.addEventListener('change', filter);
    document.getElementById('batch-form').addEventListener('submit', reindex);
    refreshCategories(); filter();
});
</script>
@endpush
