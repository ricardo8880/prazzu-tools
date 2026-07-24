<section class="prazzu-tool-workspace text-start mt-4" aria-labelledby="product-import-title">
    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-3">
        <div>
            <span class="badge text-bg-success mb-2">Importação</span>
            <h2 id="product-import-title" class="mb-1">Importar produtos por CSV ou Excel</h2>
            <p class="text-body-secondary mb-0">Envie até 100 produtos, confira os dados e escolha qual coluna corresponde a cada campo.</p>
        </div>
        <a class="btn btn-outline-success align-self-lg-end" href="{{ route('tools.calculadora-margem-markup.import.template') }}">
            <i class="bi bi-download me-1"></i>Baixar modelo CSV
        </a>
    </div>

    <form method="post" action="{{ route('tools.calculadora-margem-markup.import.preview') }}" enctype="multipart/form-data" class="row g-3 align-items-end">
        @csrf
        <div class="col-12 col-lg-8">
            <label class="form-label" for="import_file">Arquivo de produtos</label>
            <input class="form-control @error('import_file') is-invalid @enderror" id="import_file" name="import_file" type="file" accept=".csv,.txt,.xlsx" required>
            <div class="form-text">Formatos aceitos: CSV e XLSX. Tamanho máximo: 5 MB.</div>
            @error('import_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-lg-4">
            <button class="btn btn-success w-100" type="submit"><i class="bi bi-eye me-1"></i>Pré-visualizar arquivo</button>
        </div>
    </form>

    @php
        $importResult = $productImportResult ?? session('product_import_result');
    @endphp
    @if($importResult)
        <div class="alert alert-success mt-3 mb-0" role="status">
            <strong>{{ $importResult['imported'] }} produto(s) importado(s).</strong>
            Os dados foram carregados na tabela de cálculo em lote abaixo.
            @if(count($importResult['rejected']))
                <div class="mt-2"><strong>{{ count($importResult['rejected']) }} linha(s) rejeitada(s):</strong></div>
                <ul class="mb-0">
                    @foreach($importResult['rejected'] as $rejected)
                        <li>Linha {{ $rejected['line'] }}: {{ $rejected['reason'] }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif

    @php
        $preview = $productImportPreview ?? session('product_import_preview');
    @endphp
    @if($preview)
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-body">
                <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-3">
                    <div>
                        <h3 class="h5 mb-1">Mapeamento das colunas</h3>
                        <p class="text-body-secondary mb-0">{{ $preview['file_name'] }} · {{ $preview['format'] }} · {{ $preview['total_rows'] }} linha(s)</p>
                    </div>
                    <span class="badge text-bg-primary align-self-start">Pré-visualização pronta</span>
                </div>

                <form method="post" action="{{ route('tools.calculadora-margem-markup.import.process') }}">
                    @csrf
                    <input type="hidden" name="import_token" value="{{ $preview['token'] }}">
                    @foreach($preview['headers'] as $header)
                        <input type="hidden" name="available_headers[]" value="{{ $header }}">
                    @endforeach

                    @php
                        $mappingFields = [
                            'name_column' => ['Produto', true],
                            'code_column' => ['Código / SKU', false],
                            'category_column' => ['Categoria', false],
                            'base_cost_column' => ['Custo base', true],
                            'additional_costs_column' => ['Outros custos', false],
                            'freight_cost_column' => ['Frete', false],
                            'packaging_cost_column' => ['Embalagem', false],
                            'fixed_expenses_column' => ['Despesas rateadas', false],
                            'desired_margin_column' => ['Margem desejada (%)', false],
                            'taxes_percentage_column' => ['Impostos (%)', false],
                            'commission_percentage_column' => ['Comissão (%)', false],
                            'card_fees_percentage_column' => ['Taxas de cartão (%)', false],
                            'marketplace_fees_percentage_column' => ['Taxas de marketplace (%)', false],
                        ];
                    @endphp

                    <div class="row g-3">
                        @foreach($mappingFields as $field => [$label, $required])
                            <div class="col-12 col-md-6 col-xl-4">
                                <label class="form-label" for="{{ $field }}">{{ $label }}</label>
                                <select class="form-select" id="{{ $field }}" name="{{ $field }}" {{ $required ? 'required' : '' }}>
                                    <option value="">{{ $required ? 'Selecione uma coluna' : 'Não importar' }}</option>
                                    @foreach($preview['headers'] as $header)
                                        <option value="{{ $header }}" @selected(($preview['suggested_mapping'][$field] ?? '') === $header)>{{ $header }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                    </div>

                    @error('import_token')<div class="alert alert-danger mt-3 mb-0">{{ $message }}</div>@enderror

                    <div class="table-responsive border rounded mt-4">
                        <table class="table table-sm table-striped align-middle mb-0">
                            <thead class="table-light"><tr>@foreach($preview['headers'] as $header)<th class="text-nowrap">{{ $header }}</th>@endforeach</tr></thead>
                            <tbody>
                                @forelse($preview['preview_rows'] as $row)
                                    <tr>@foreach($preview['headers'] as $header)<td class="text-nowrap">{{ $row[$header] ?? '—' }}</td>@endforeach</tr>
                                @empty
                                    <tr><td colspan="{{ count($preview['headers']) }}" class="text-center text-body-secondary py-4">Nenhuma linha encontrada.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="alert alert-info mt-3 mb-0">
                        Campos percentuais ausentes usarão os padrões: margem 30% e demais percentuais 0%. Custos opcionais ausentes usarão zero.
                    </div>

                    <div class="d-flex flex-wrap justify-content-end gap-2 mt-3">
                        <button class="btn btn-primary" type="submit"><i class="bi bi-box-arrow-in-down me-1"></i>Importar para a tabela</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</section>
