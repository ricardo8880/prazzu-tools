@extends('layouts.app')

@section('title', 'Receipt Generator — Prazzu Tools')
@section('meta_description', 'Enter payment details and generate a clear receipt.')

@section('content')
    @php($result = $result ?? session('receipt_result'))
    @php($receipt = is_array($result) ? ($result['details']['receipt'] ?? null) : null)
    @if ($receipt)
        <span data-analytics-result="main" hidden></span>
    @endif

    <x-tools.trust-seo slug="receipt-generator" :show-content="false" />

    <div class="container py-5 tool-page" data-tool="receipt-generator" data-testid="tool-page-receipt-generator">
        <div class="row justify-content-center">
            <div class="col-xl-10">
                <x-tools.intro icon="receipt" tone="green" title="Receipt Generator" description="Enter, review, and generate a complete receipt." badge="Available" />

                @if(session('profile_message'))
                    <div class="alert alert-success">{{ session('profile_message') }}</div>
                @endif

                @if(session('history_message'))
                    <div class="alert alert-success">{{ session('history_message') }}</div>
                @endif

                @if($historySaved ?? session('history_saved', false))
                    <div class="alert alert-success">Receipt saved to your history.</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert" data-testid="validation-summary" tabindex="-1">
                        <strong>Review the information entered.</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                <div class="card shadow-sm mb-4" data-testid="tool-form-panel">
                    <div class="card-body p-4">
                        <h2 class="h4 mb-3">Receipt details</h2>
                        <form method="POST" action="{{ route('tools.receipt-generator.issue') }}" class="row g-3">
                            @csrf


                            @auth
                                <div class="col-12">
                                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 rounded border p-3">
                                        <div>
                                            <strong>Saved profiles</strong>
                                            <div class="small text-body-secondary">Fill payer or payee details from a saved profile.</div>
                                        </div>
                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('tools.receipt-generator.profiles.index') }}">Manage profiles</a>
                                    </div>
                                </div>
                                @if($partyProfiles->isNotEmpty())
                                    <div class="col-12 d-flex flex-wrap gap-2">
                                        @foreach($partyProfiles as $profile)
                                            <form method="POST" action="{{ route('tools.receipt-generator.profiles.use', $profile->id) }}">
                                                @csrf
                                                <button class="btn btn-sm btn-outline-secondary" type="submit">
                                                    {{ $profile->party_type === 'payer' ? 'Payer' : 'Payee' }}: {{ $profile->label }}
                                                </button>
                                            </form>
                                        @endforeach
                                    </div>
                                @endif
                            @endauth

                            <div class="col-md-4">
                                <label for="number" class="form-label">Receipt number</label>
                                <input id="number" name="number" value="{{ old('number', now()->format('Ymd').'-001') }}" class="form-control" maxlength="40" required>
                            </div>
                            <div class="col-md-4">
                                <label for="amount" class="form-label">Amount received</label>
                                <input id="amount" name="amount" value="{{ old('amount') }}" class="form-control" placeholder="1,000.00" inputmode="decimal" required>
                            </div>
                            <div class="col-md-4">
                                <label for="issued_at" class="form-label">Issue date</label>
                                <input id="issued_at" type="date" name="issued_at" value="{{ old('issued_at', now()->format('Y-m-d')) }}" class="form-control" required>
                            </div>

                            <div class="col-12"><hr><h3 class="h5">Payer</h3></div>
                            <div class="col-md-6">
                                <label for="payer_name" class="form-label">Name or business name</label>
                                <input id="payer_name" name="payer_name" value="{{ old('payer_name') }}" class="form-control" maxlength="160" required>
                            </div>
                            <div class="col-md-2">
                                <label for="payer_document_type" class="form-label">ID type</label>
                                <select id="payer_document_type" name="payer_document_type" class="form-select">
                                    <option value="">No ID</option>
                                    <option value="ein" @selected(old('payer_document_type') === 'ein')>EIN</option>
                                    <option value="id" @selected(old('payer_document_type') === 'id')>Other ID</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="payer_document" class="form-label">ID number</label>
                                <input id="payer_document" name="payer_document" value="{{ old('payer_document') }}" class="form-control" maxlength="18" inputmode="numeric">
                            </div>

                            <div class="col-12"><hr><h3 class="h5">Payee</h3></div>
                            <div class="col-md-6">
                                <label for="payee_name" class="form-label">Name or business name</label>
                                <input id="payee_name" name="payee_name" value="{{ old('payee_name') }}" class="form-control" maxlength="160" required>
                            </div>
                            <div class="col-md-2">
                                <label for="payee_document_type" class="form-label">ID type</label>
                                <select id="payee_document_type" name="payee_document_type" class="form-select">
                                    <option value="">No ID</option>
                                    <option value="ein" @selected(old('payee_document_type') === 'ein')>EIN</option>
                                    <option value="id" @selected(old('payee_document_type') === 'id')>Other ID</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="payee_document" class="form-label">ID number</label>
                                <input id="payee_document" name="payee_document" value="{{ old('payee_document') }}" class="form-control" maxlength="18" inputmode="numeric">
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label">Payment for</label>
                                <textarea id="description" name="description" class="form-control" rows="3" maxlength="1000" required>{{ old('description') }}</textarea>
                                <div class="form-text">Briefly describe the payment.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="city" class="form-label">City <span class="text-muted">(optional)</span></label>
                                <input id="city" name="city" value="{{ old('city') }}" class="form-control" maxlength="120">
                            </div>
                            <div class="col-12"><hr><h3 class="h5">Business branding <span class="badge text-bg-primary">Prazzu Plus</span></h3><p class="small text-body-secondary mb-2">Optional. Customize the receipt header and footer.</p></div>
                            <div class="col-md-5"><label for="brand_name" class="form-label">Business name</label><input id="brand_name" name="brand_name" value="{{ old('brand_name') }}" class="form-control" maxlength="120"></div>
                            <div class="col-md-3"><label for="brand_document" class="form-label">Business ID / registration</label><input id="brand_document" name="brand_document" value="{{ old('brand_document') }}" class="form-control" maxlength="40"></div>
                            <div class="col-md-4"><label for="brand_footer" class="form-label">Custom footer</label><input id="brand_footer" name="brand_footer" value="{{ old('brand_footer') }}" class="form-control" maxlength="160"></div>

                            <div class="col-12 d-flex gap-2">
                                <button class="btn btn-primary" type="submit">Generate and review receipt</button>
                                <a class="btn btn-outline-secondary" href="{{ route('tools.receipt-generator.index') }}">Clear</a>
                            </div>
                        </form>
                    </div>
                </div>

                @if (is_array($receipt))
                    <section aria-labelledby="receipt-generated" class="mb-4 prazzu-tool-result-surface" data-testid="tool-result" data-tool-result-panel>
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                            <h2 id="receipt-generated" class="h3 mb-0">Receipt review</h2>
                            <div class="d-flex flex-wrap gap-2">
                                <form method="POST" action="{{ route('tools.receipt-generator.export', 'pdf') }}">
                                    @csrf
                                    @foreach (['number', 'payer_name', 'payer_document_type', 'payer_document', 'payee_name', 'payee_document_type', 'payee_document', 'amount', 'description', 'issued_at', 'city', 'brand_name', 'brand_document', 'brand_footer'] as $field)
                                        <input type="hidden" name="{{ $field }}" value="{{ old($field) }}">
                                    @endforeach
                                    <button class="btn btn-success" type="submit">
                                        <i class="bi bi-file-earmark-pdf me-1" aria-hidden="true"></i>Export PDF
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('tools.receipt-generator.export', 'xlsx') }}">
                                    @csrf
                                    @foreach (['number', 'payer_name', 'payer_document_type', 'payer_document', 'payee_name', 'payee_document_type', 'payee_document', 'amount', 'description', 'issued_at', 'city', 'brand_name', 'brand_document', 'brand_footer'] as $field)
                                        <input type="hidden" name="{{ $field }}" value="{{ old($field) }}">
                                    @endforeach
                                    <button class="btn btn-outline-success" type="submit"><i class="bi bi-file-earmark-excel me-1" aria-hidden="true"></i>Export Excel (.xlsx)</button>
                                </form>
                            </div>
                        </div>

                        <article class="card shadow-sm" id="receipt-preview">
                            <div class="card-body p-4 p-md-5">
                                @if(!empty($receipt['branding']['name']) || !empty($receipt['branding']['document']))
                                    <div class="mb-3" data-plus-feature="custom_branding"><strong>{{ $receipt['branding']['name'] ?? '' }}</strong>@if(!empty($receipt['branding']['document']))<div class="small text-muted">{{ $receipt['branding']['document'] }}</div>@endif</div>
                                @endif
                                <div class="d-flex justify-content-between gap-3 border-bottom pb-3 mb-4">
                                    <div>
                                        <div class="text-uppercase text-muted small">Receipt</div>
                                        <h3 class="h4 mb-0">No. {{ $receipt['number'] }}</h3>
                                    </div>
                                    <div class="text-end">
                                        <div class="text-muted small">Amount</div>
                                        <div class="h3 mb-0">{{ $receipt['amount'] }}</div>
                                    </div>
                                </div>

                                <p class="fs-5 lh-lg">
                                    Received from <strong>{{ $receipt['payer']['name'] }}</strong>
                                    @if($receipt['payer']['document'])<span>({{ strtoupper($receipt['payer']['document_type']) }} {{ $receipt['payer']['document'] }})</span>@endif,
                                    the amount of <strong>{{ $receipt['amount_in_words'] }}</strong>, for
                                    <strong>{{ $receipt['description'] }}</strong>.
                                </p>

                                <p class="mb-5">This receipt acknowledges the payment described above.</p>

                                <div class="row g-4 align-items-end">
                                    <div class="col-md-6">
                                        {{ $receipt['city'] ? $receipt['city'].', ' : '' }}{{ \Carbon\CarbonImmutable::parse($receipt['issued_at'])->format('F j, Y') }}.
                                    </div>
                                    <div class="col-md-6 text-center">
                                        <div class="border-top pt-2 mt-5">
                                            <strong>{{ $receipt['payee']['name'] }}</strong><br>
                                            @if($receipt['payee']['document'])<span>{{ strtoupper($receipt['payee']['document_type']) }} {{ $receipt['payee']['document'] }}</span>@endif
                                        </div>
                                    </div>
                                </div>

                                <div class="text-muted small mt-5">Identifier: {{ $receipt['identifier'] }}</div>
                                @if(!empty($receipt['branding']['footer']))<div class="text-center small mt-3" data-plus-feature="custom_branding">{{ $receipt['branding']['footer'] }}</div>@endif
                            </div>
                        </article>
                        <div class="alert alert-info mt-3 mb-0">Review names, IDs, amount, description, and date before printing or saving the PDF.</div>
                    </section>
                @endif

                @auth
                    <section class="mt-5" aria-labelledby="recent-history">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                            <div>
                                <h2 id="recent-history" class="h4 mb-1">Recent receipts</h2>
                                <p class="text-body-secondary mb-0">Quickly access receipts saved to your account.</p>
                            </div>
                            <a class="btn btn-outline-primary" href="{{ route('tools.receipt-generator.batch.index') }}"><i class="bi bi-files me-1"></i> Generate batch</a>
                <a class="btn btn-outline-primary btn-sm" href="{{ route('tools.receipt-generator.history.index') }}">View full history</a>
                        </div>
                        <div class="row g-3">
                            @forelse($recentHistory as $run)
                                @php($historyReceipt = $run->result['details']['receipt'] ?? [])
                                <div class="col-md-4">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <strong>Receipt nº {{ $historyReceipt['number'] ?? ($run->input['number'] ?? '—') }}</strong>
                                            <div class="small text-body-secondary mt-1">{{ $historyReceipt['amount'] ?? 'Amount unavailable' }}</div>
                                            <div class="small mt-2">{{ $historyReceipt['payer']['name'] ?? ($run->input['payer_name'] ?? 'Payer') }}</div>
                                            <form method="POST" action="{{ route('tools.receipt-generator.history.repeat', $run->id) }}" class="mt-3">
                                                @csrf
                                                <button class="btn btn-sm btn-outline-primary" type="submit">Reuse data</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12"><div class="alert alert-light border mb-0">Your saved receipts will appear here.</div></div>
                            @endforelse
                        </div>
                    </section>
                @endauth

                <section class="mt-5">
                    <h2 class="h4">How it works</h2>
                    <p>Prazzu validates the main fields, writes the U.S. dollar amount in words, and builds the receipt. Signed-in users can save and reuse receipts from history.</p>
                </section>
                <x-tool-feature-tiers slug="receipt-generator" />
    <x-tools.plus-result-cta slug="receipt-generator" />

            </div>
        </div>
    </div>
@endsection
