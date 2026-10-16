@extends('layouts.app')

@section('title', 'Contract Generator — Prazzu Tools')
@section('meta_description', 'Prepare a general U.S. business contract with guided inputs, review the text, and export it.')

@section('content')
    <div class="container py-5">
        <x-tools.page
            title="Contract Generator"
            description="Answer the questions, generate a draft, and review the text before export."
            icon="file-earmark-text"
            slug="contract-generator"
            badge="Beta"
            tone="green"
        >
            <div class="alert alert-warning mb-4" role="note" aria-label="Contract template notice">
                <strong>General template:</strong> review all content before use. Specific situations involving consumer, employment, real estate, guarantees, or other special rules may require additional clauses and professional review.
            </div>


            @if(session('history_message'))
                <div class="alert alert-success mb-4">{{ session('history_message') }}</div>
            @endif

            <div class="d-flex flex-wrap gap-2 align-items-center mb-3" aria-label="Generator steps">
                <span class="badge text-bg-primary">1. Template</span>
                <span class="badge text-bg-secondary">2. Questions</span>
                <span class="badge text-bg-secondary">3. Review</span>
                <span class="badge text-bg-secondary">4. Export</span>
            </div>

            <x-tools.form-panel
                data-testid="contract-type-panel"
                title="1. Choose a contract template"
                description="Essential templates remain free. The expanded library adds professional variations with Prazzu Plus."
                class="mb-4"
            >
                <div class="row g-3">
                    @foreach ($contractTemplates as $template)
                        @php($lockedTemplate = $template->isPlus() && ! $featureAccess['contract_library'])
                        <div class="col-12 col-md-6">
                            @if ($lockedTemplate)
                                <div class="card h-100 border-secondary" data-testid="contract-template-{{ $template->value }}-locked">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between gap-3">
                                            <div>
                                                <h3 class="h5 mb-1">{{ $template->label() }}</h3>
                                                <p class="text-body-secondary mb-0">{{ $template->description() }}</p>
                                            </div>
                                            <span class="badge text-bg-purple">Plus</span>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <a
                                    href="{{ route('tools.contract-generator.index', ['template' => $template->value]) }}"
                                    @if($template->value === 'service-standard') data-testid="contract-type-service" @elseif($template->value === 'sale-standard') data-testid="contract-type-sale" @endif
                                    class="card h-100 text-decoration-none {{ $selectedTemplate === $template ? 'border-primary' : '' }}"
                                    @if($selectedTemplate === $template) aria-current="true" @endif
                                >
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between gap-3">
                                            <div>
                                                <h3 class="h5 text-body mb-1">{{ $template->label() }}</h3>
                                                <p class="text-body-secondary mb-0">{{ $template->description() }}</p>
                                            </div>
                                            <div class="d-flex flex-column gap-1 align-items-end">
                                                @if($template->isPlus())<span class="badge text-bg-primary">Plus</span>@endif
                                                @if($selectedTemplate === $template)<span class="badge text-bg-success">Selected</span>@endif
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </x-tools.form-panel>

            @if ($selectedType !== null && $contractText === null)
                <form method="POST" action="{{ route('tools.contract-generator.build') }}" aria-label="Contract questionnaire" data-analytics-form="draft">
                    @csrf
                    <input type="hidden" name="contract_type" value="{{ $selectedType->value }}">
                    <input type="hidden" name="template_key" value="{{ $selectedTemplate?->value }}">

                    <x-tools.form-panel
                        data-testid="contract-parties-panel"
                        title="2. Identify the parties"
                        description="Enter the information identifying the parties to the agreement."
                        class="mb-4"
                    >
                        <div class="row g-3">
                            @if ($featureAccess['company_autofill'] && $companyProfiles->isNotEmpty())
                                <div class="col-12">
                                    <div class="border rounded p-3 bg-body-tertiary">
                                        <label class="form-label" for="company-autofill">Business profile autofill <span class="badge text-bg-primary">Plus</span></label>
                                        <div id="company-autofill" class="d-flex flex-wrap gap-2">
                                            @foreach($companyProfiles as $profile)
                                                <a class="btn btn-sm {{ request('company') == $profile->id ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('tools.contract-generator.index', ['template' => $selectedTemplate?->value, 'company' => $profile->id]) }}">
                                                    {{ $profile->legal_name ?: $profile->name }}
                                                </a>
                                            @endforeach
                                        </div>
                                        <div class="form-text">Fills business name and business ID from the shared profile. Review address and other details before use.</div>
                                    </div>
                                </div>
                            @endif
                            @foreach ([
                                ['prefix' => 'first_party', 'label' => $selectedType->firstPartyLabel()],
                                ['prefix' => 'second_party', 'label' => $selectedType->secondPartyLabel()],
                            ] as $party)
                                <div class="col-12">
                                    <h3 class="h5 mb-0">{{ $party['label'] }}</h3>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <x-tools.form.input
                                        :name="$party['prefix'].'_name'"
                                        :value="$party['prefix'] === 'first_party' ? ($companyAutofill['name'] ?? null) : null"
                                        label="Full name or legal business name"
                                        maxlength="180"
                                        required
                                    />
                                </div>
                                <div class="col-12 col-md-4 col-lg-2">
                                    <x-tools.form.select
                                        :name="$party['prefix'].'_document_type'"
                                        label="ID / Tax ID"
                                        :options="$documentTypes"
                                        :value="$party['prefix'] === 'first_party' ? ($companyAutofill['document_type'] ?? null) : null"
                                        placeholder="Select"
                                        required
                                    />
                                </div>
                                <div class="col-12 col-md-8 col-lg-4">
                                    <x-tools.form.input
                                        :name="$party['prefix'].'_document'"
                                        :value="$party['prefix'] === 'first_party' ? ($companyAutofill['document'] ?? null) : null"
                                        label="ID / Tax ID number"
                                        maxlength="18"
                                        required
                                    />
                                </div>
                                <div class="col-12 col-lg-6">
                                    <x-tools.form.input
                                        :name="$party['prefix'].'_address'"
                                        label="Full address"
                                        maxlength="240"
                                        required
                                    />
                                </div>
                                <div class="col-8 col-lg-4">
                                    <x-tools.form.input
                                        :name="$party['prefix'].'_city'"
                                        label="City"
                                        maxlength="120"
                                        required
                                    />
                                </div>
                                <div class="col-4 col-lg-2">
                                    <x-tools.form.input
                                        :name="$party['prefix'].'_state'"
                                        label="State"
                                        maxlength="2"
                                        required
                                    />
                                </div>
                                @if (! $loop->last)
                                    <div class="col-12"><hr class="my-2"></div>
                                @endif
                            @endforeach
                        </div>
                    </x-tools.form-panel>

                    <x-tools.form-panel
                        data-testid="contract-terms-panel"
                        title="3. Define scope and terms"
                        description="The answers below are used to draft the contract clauses."
                        class="mb-4"
                    >
                        <div class="row g-3">
                            @if ($selectedType->value === 'service-agreement')
                                <div class="col-12">
                                    <label for="service_description" class="form-label">What services will be provided?</label>
                                    <textarea
                                        id="service_description"
                                        name="service_description"
                                        rows="5"
                                        maxlength="4000"
                                        class="form-control @error('service_description') is-invalid @enderror"
                                        required
                                    >{{ old('service_description') }}</textarea>
                                    @error('service_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12 col-md-4">
                                    <x-tools.form.input name="start_date" label="Service start date" type="date" required />
                                </div>
                                <div class="col-12 col-md-4">
                                    <x-tools.form.input name="end_date" label="Expected end date" type="date" help="Optional for an open-ended agreement." />
                                </div>
                                <div class="col-12 col-md-4">
                                    <x-tools.form.input name="termination_notice_days" label="Termination notice" type="number" min="0" max="365" suffix="days" :value="old('termination_notice_days')" required />
                                </div>
                            @else
                                <div class="col-12">
                                    <label for="asset_description" class="form-label">What property or item will be sold?</label>
                                    <textarea
                                        id="asset_description"
                                        name="asset_description"
                                        rows="5"
                                        maxlength="4000"
                                        class="form-control @error('asset_description') is-invalid @enderror"
                                        required
                                    >{{ old('asset_description') }}</textarea>
                                    <div class="form-text">Include enough details to clearly identify the property or item.</div>
                                    @error('asset_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12 col-md-4">
                                    <x-tools.form.input name="delivery_date" label="Delivery date" type="date" required />
                                </div>
                                <div class="col-12 col-md-8">
                                    <x-tools.form.input name="delivery_location" label="Delivery location" maxlength="240" required />
                                </div>
                            @endif

                            <div class="col-12 col-md-4">
                                <x-tools.form.money name="amount" label="Contract amount" placeholder="1,000.00" required />
                            </div>
                            <div class="col-12 col-md-8">
                                <label for="payment_terms" class="form-label">How will payment be made?</label>
                                <textarea
                                    id="payment_terms"
                                    name="payment_terms"
                                    rows="3"
                                    maxlength="1200"
                                    class="form-control @error('payment_terms') is-invalid @enderror"
                                    required
                                >{{ old('payment_terms') }}</textarea>
                                @error('payment_terms')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </x-tools.form-panel>

                    <x-tools.form-panel
                        title="Optional clause library"
                        description="Add optional clauses to the agreement. Professional templates may preselect compatible clauses."
                        class="mb-4"
                    >
                        @if ($featureAccess['smart_clauses'])
                            <div class="row g-2">
                                @foreach($smartClauseOptions as $clause)
                                    @php($preset = in_array($clause, $selectedTemplate?->presetClauses() ?? [], true))
                                    <div class="col-12 col-md-6">
                                        <div class="form-check border rounded p-3 ps-5 h-100">
                                            <input class="form-check-input" type="checkbox" name="smart_clauses[]" value="{{ $clause->value }}" id="smart-{{ $clause->value }}" @checked($preset || in_array($clause->value, old('smart_clauses', []), true))>
                                            <label class="form-check-label" for="smart-{{ $clause->value }}"><strong>{{ $clause->label() }}</strong></label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-secondary mb-0">The optional clause library is part of Prazzu Plus when monetization is enabled.</div>
                        @endif
                    </x-tools.form-panel>

                    <x-tools.form-panel
                        data-testid="contract-closing-panel"
                        title="4. Finalize the agreement"
                        description="Choose the governing state, dispute venue, signing location/date, and any additional terms."
                        class="mb-4"
                    >
                        <div class="row g-3">
                            <div class="col-8 col-md-5">
                                <x-tools.form.input name="jurisdiction_city" label="Venue city" maxlength="120" required />
                            </div>
                            <div class="col-4 col-md-2">
                                <x-tools.form.input name="jurisdiction_state" label="Governing state" maxlength="2" required />
                            </div>
                            <div class="col-12 col-md-5">
                                <x-tools.form.input name="signing_city" label="Signing city" maxlength="120" required />
                            </div>
                            <div class="col-12 col-md-4">
                                <x-tools.form.input name="signing_date" label="Signing date" type="date" required />
                            </div>
                            <div class="col-12">
                                <label for="additional_terms" class="form-label">Additional terms</label>
                                <textarea
                                    id="additional_terms"
                                    name="additional_terms"
                                    rows="4"
                                    maxlength="4000"
                                    class="form-control @error('additional_terms') is-invalid @enderror"
                                >{{ old('additional_terms') }}</textarea>
                                <div class="form-text">Optional. Use only for specific terms not covered by the previous questions.</div>
                                @error('additional_terms')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-file-earmark-text me-1" aria-hidden="true"></i>
                                Generate full agreement
                            </button>
                            <a href="{{ route('tools.contract-generator.index') }}" class="btn btn-outline-secondary">Trocar modalidade</a>
                        </div>
                    </x-tools.form-panel>
                </form>
            @elseif ($selectedType === null)
                <div class="alert alert-info mb-4" role="status">
                    Choose a template above to start the questionnaire.
                </div>
            @endif

            @if ($draft !== null)
                <x-tools.result-panel
                    title="Information reviewed and agreement generated"
                    data-analytics-result="draft"
                    description="Your answers were normalized and used to assemble the contract text below."
                    class="mb-4"
                >
                    <div class="row g-4">
                        <div class="col-12 col-lg-6">
                            <h3 class="h6 text-uppercase text-body-secondary">{{ $draft['first_party_label'] }}</h3>
                            <p class="mb-1"><strong>{{ $draft['first_party']['name'] }}</strong></p>
                            <p class="mb-1">{{ strtoupper($draft['first_party']['document_type']) }} {{ $draft['first_party']['document'] }}</p>
                            <p class="mb-0">{{ $draft['first_party']['address'] }}, {{ $draft['first_party']['city'] }}/{{ $draft['first_party']['state'] }}</p>
                        </div>
                        <div class="col-12 col-lg-6">
                            <h3 class="h6 text-uppercase text-body-secondary">{{ $draft['second_party_label'] }}</h3>
                            <p class="mb-1"><strong>{{ $draft['second_party']['name'] }}</strong></p>
                            <p class="mb-1">{{ strtoupper($draft['second_party']['document_type']) }} {{ $draft['second_party']['document'] }}</p>
                            <p class="mb-0">{{ $draft['second_party']['address'] }}, {{ $draft['second_party']['city'] }}/{{ $draft['second_party']['state'] }}</p>
                        </div>
                        <div class="col-12"><hr class="my-0"></div>
                        <div class="col-12 col-md-4">
                            <span class="text-body-secondary d-block">Modalidade</span>
                            <strong>{{ $draft['type_label'] }}</strong>
                        </div>
                        <div class="col-12 col-md-4">
                            <span class="text-body-secondary d-block">Amount</span>
                            <strong>{{ $draft['amount_formatted'] }}</strong>
                        </div>
                        <div class="col-12 col-md-4">
                            <span class="text-body-secondary d-block">Venue</span>
                            <strong>{{ $draft['jurisdiction_city'] }}/{{ $draft['jurisdiction_state'] }}</strong>
                        </div>
                    </div>
                </x-tools.result-panel>
                @if($currentRunId)
                    <div class="alert alert-success mb-4">Agreement saved to Plus history. You can favorite it and compare this version with others.</div>
                @endif
            @endif

            @if ($contractText !== null)
                <x-tools.form-panel
                    data-testid="contract-editor-panel"
                    title="5. Review and edit the agreement"
                    description="The text below is fully editable. Adjust any clause before exporting."
                    class="mb-4"
                >
                    @if ($edited)
                        <div class="alert alert-success" role="status" aria-live="polite">
                            Preview updated with the edited text. No data was saved.
                        </div>
                    @endif

                    <form data-testid="contract-editor" method="POST" action="{{ route('tools.contract-generator.preview') }}" aria-label="Agreement editor and export" data-analytics-form="editor">
                        @csrf
                        <input type="hidden" name="contract_type" value="{{ $selectedType?->value }}">
                        <input type="hidden" name="source_run_id" value="{{ $currentRunId }}">
                        <label for="contract_text" class="form-label">Full agreement text</label>
                        <textarea
                            id="contract_text"
                            name="contract_text"
                            rows="32"
                            maxlength="60000"
                            class="form-control font-monospace @error('contract_text') is-invalid @enderror"
                            required
                        >{{ old('contract_text', $contractText['content']) }}</textarea>
                        <div class="form-text">Edits are temporary and apply only to this response. PDF and Word exports use the current editor text exactly.</div>
                        @error('contract_text')<div class="invalid-feedback">{{ $message }}</div>@enderror

                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-eye me-1" aria-hidden="true"></i>
                                Update preview
                            </button>
                            @if($featureAccess['history'] && auth()->check())
                                <button type="submit" class="btn btn-outline-dark" formaction="{{ route('tools.contract-generator.versions.store') }}">
                                    <i class="bi bi-clock-history me-1" aria-hidden="true"></i>
                                    Save new version
                                </button>
                            @endif
                            <button
                                type="submit"
                                class="btn btn-outline-primary"
                                data-testid="contract-export-pdf"
                                formaction="{{ route('tools.contract-generator.export.pdf') }}" data-analytics-action="export" data-analytics-form="editor" data-analytics-format="pdf"
                            >
                                <i class="bi bi-file-earmark-pdf me-1" aria-hidden="true"></i>
                                Export PDF
                            </button>
                            <button
                                type="submit"
                                class="btn btn-outline-success"
                                data-testid="contract-export-xlsx"
                                formaction="{{ route('tools.contract-generator.export.xlsx') }}" data-analytics-action="export" data-analytics-form="editor" data-analytics-format="xlsx"
                            >
                                <i class="bi bi-file-earmark-excel me-1" aria-hidden="true"></i>
                                Download Excel
                            </button>
                            <button
                                type="submit"
                                class="btn btn-outline-primary"
                                data-testid="contract-export-docx"
                                formaction="{{ route('tools.contract-generator.export.docx') }}" data-analytics-action="export" data-analytics-form="editor" data-analytics-format="docx"
                            >
                                <i class="bi bi-file-earmark-word me-1" aria-hidden="true"></i>
                                Download Word
                            </button>
                            <a href="{{ route('tools.contract-generator.index', ['tipo' => $selectedType?->value]) }}" class="btn btn-outline-secondary">Refazer perguntas</a>
                        </div>
                    </form>
                </x-tools.form-panel>

                <x-tools.result-panel
                    title="Agreement preview"
                    data-analytics-result="editor"
                    description="This preview uses the current editor content exactly."
                >
                    <pre data-testid="contract-preview" class="border rounded bg-body-tertiary p-3 p-md-4 mb-0 text-wrap overflow-auto" tabindex="0" aria-label="Text preview of the agreement">{{ $contractText['content'] }}</pre>
                </x-tools.result-panel>
            @endif

            @if($featureAccess['history'] && auth()->check())
                <x-tools.form-panel title="Contract history" description="Versions saved to your account and available for comparison." class="mt-4">
                    <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
                        <span class="text-body-secondary">{{ count($recentHistory) }} recent version(s)</span>
                        <a class="btn btn-outline-primary btn-sm" href="{{ route('tools.contract-generator.history.index') }}">Open full history</a>
                    </div>
                    @forelse($recentHistory as $run)
                        <div class="border rounded p-3 mb-2 d-flex justify-content-between gap-3 align-items-center">
                            <div><strong>{{ $run->result['contract_title'] ?? 'Agreement' }}</strong><div class="small text-body-secondary">{{ $run->createdAt->format('M j, Y g:i A') }}</div></div>
                            @if($run->favorite)<span class="badge text-bg-warning">Favorite</span>@endif
                        </div>
                    @empty
                        <p class="text-body-secondary mb-0">No saved versions yet.</p>
                    @endforelse
                </x-tools.form-panel>
            @endif
        </x-tools.page>
    </div>
@endsection

@if ($contractText !== null)
    @push('scripts')
        <script>
            (() => {
                const editor = document.querySelector('[data-testid="contract-editor"]');

                if (!(editor instanceof HTMLElement)) {
                    return;
                }

                requestAnimationFrame(() => {
                    editor.scrollIntoView({ block: 'start', inline: 'nearest', behavior: 'auto' });
                });
            })();
        </script>
    @endpush
@endif
