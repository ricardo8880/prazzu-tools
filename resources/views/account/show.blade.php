@extends('layouts.app')

@section('title', 'Meu Prazzu — Prazzu Tools')
@section('meta_description', 'Retome ferramentas, resultados salvos e favoritos vinculados à sua conta Prazzu Tools.')
@section('meta_robots', 'noindex,nofollow')

@section('content')
    <div class="prazzu-page">
        <div class="row justify-content-center">
            <div class="col-12 col-xxl-10">
                <section class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4 p-lg-5">
                        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start gap-4">
                            <div>
                                <span class="badge text-bg-success mb-3">Conta ativa</span>
                                <h1 class="h2 mb-2">Meu Prazzu</h1>
                                <p class="text-body-secondary mb-2">
                                    Olá, {{ auth()->user()->name }}. Este é seu ponto de retorno para continuar o que você já salvou nas ferramentas.
                                </p>
                                <p class="small text-body-secondary mb-0">{{ auth()->user()->email }}</p>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="bi bi-box-arrow-right me-2" aria-hidden="true"></i>Sair
                                </button>
                            </form>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-12 col-sm-4">
                                <div class="border rounded-3 p-3 h-100">
                                    <div class="small text-body-secondary mb-1">Resultados salvos</div>
                                    <div class="fs-3 fw-semibold">{{ number_format($historyCount, 0, ',', '.') }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4">
                                <div class="border rounded-3 p-3 h-100">
                                    <div class="small text-body-secondary mb-1">Ferramentas favoritas</div>
                                    <div class="fs-3 fw-semibold">{{ number_format($toolFavoriteCount, 0, ',', '.') }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4">
                                <div class="border rounded-3 p-3 h-100">
                                    <div class="small text-body-secondary mb-1">Ferramentas com histórico</div>
                                    <div class="fs-3 fw-semibold">{{ number_format($usedToolCount, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                @if($continueRuns->isNotEmpty())
                    <section class="mb-4" aria-labelledby="continue-title">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-end gap-2 mb-3">
                            <div>
                                <h2 class="h4 mb-1" id="continue-title">Continue de onde parou</h2>
                                <p class="text-body-secondary mb-0">As ferramentas mais recentes com resultados vinculados à sua conta.</p>
                            </div>
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('tools.index') }}">Ver todas as ferramentas</a>
                        </div>

                        <div class="row g-3">
                            @foreach($continueRuns as $run)
                                <div class="col-12 col-md-6">
                                    <article class="card border h-100">
                                        <div class="card-body d-flex flex-column gap-3">
                                            <div class="d-flex align-items-start gap-3">
                                                <div class="fs-4" aria-hidden="true"><i class="bi {{ $run['tool_icon'] }}"></i></div>
                                                <div class="flex-grow-1">
                                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                                        <h3 class="h6 mb-0">{{ $run['tool_name'] }}</h3>
                                                        @if($run['favorite'])
                                                            <span class="badge text-bg-warning"><i class="bi bi-star-fill me-1" aria-hidden="true"></i>Favorito</span>
                                                        @endif
                                                    </div>
                                                    <div class="small text-body-secondary mt-1">
                                                        Último resultado em {{ $run['finished_at']->format('d/m/Y \à\s H:i') }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="d-flex flex-wrap gap-2 mt-auto">
                                                @if($run['repeat_url'])
                                                    <form method="POST" action="{{ url()->query($run['repeat_url'], ['source' => 'account_continuity']) }}">
                                                        @csrf
                                                        <button class="btn btn-primary btn-sm" type="submit">
                                                            <i class="bi bi-arrow-repeat me-1" aria-hidden="true"></i>Refazer cálculo
                                                        </button>
                                                    </form>
                                                @else
                                                    <a class="btn btn-primary btn-sm" href="{{ url()->query($run['tool_url'], ['source' => 'account_continuity']) }}">
                                                        <i class="bi bi-arrow-right-circle me-1" aria-hidden="true"></i>Usar novamente
                                                    </a>
                                                @endif

                                                @if($run['history_url'])
                                                    <a class="btn btn-outline-secondary btn-sm" href="{{ url()->query($run['history_url'], ['source' => 'account_continuity']) }}">
                                                        <i class="bi bi-clock-history me-1" aria-hidden="true"></i>Histórico
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </article>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @else
                    <section class="card border mb-4" aria-labelledby="continue-title">
                        <div class="card-body p-4">
                            <h2 class="h4 mb-2" id="continue-title">Continue de onde parou</h2>
                            <p class="text-body-secondary mb-3">
                                Ainda não há resultados salvos nesta conta. Quando você usar a persistência de uma ferramenta, seus resultados aparecerão aqui para facilitar o retorno.
                            </p>
                            <a class="btn btn-primary" href="{{ route('tools.index') }}">Explorar ferramentas</a>
                        </div>
                    </section>
                @endif

                <section class="mb-4" aria-labelledby="favorite-tools-title">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-end gap-2 mb-3">
                        <div>
                            <h2 class="h4 mb-1" id="favorite-tools-title">Ferramentas favoritas</h2>
                            <p class="text-body-secondary mb-0">Marque as ferramentas que você usa mais para voltar a elas sem procurar no catálogo.</p>
                        </div>
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('tools.index') }}">Explorar ferramentas</a>
                    </div>

                    @if($favoriteTools->isNotEmpty())
                        <div class="row g-3">
                            @foreach($favoriteTools as $tool)
                                <div class="col-12 col-md-6 col-xl-4">
                                    <article class="card border h-100">
                                        <div class="card-body d-flex flex-column gap-3">
                                            <div class="d-flex align-items-start gap-3">
                                                <i class="bi {{ $tool['icon'] }} fs-5" aria-hidden="true"></i>
                                                <div>
                                                    <h3 class="h6 mb-1">{{ $tool['name'] }}</h3>
                                                    <p class="small text-body-secondary mb-0">{{ \Illuminate\Support\Str::limit($tool['description'], 110) }}</p>
                                                </div>
                                            </div>
                                            <div class="d-flex flex-wrap gap-2 mt-auto">
                                                <a class="btn btn-sm btn-primary" href="{{ url()->query($tool['url'], ['source' => 'account_tool_favorite']) }}">Abrir ferramenta</a>
                                                <form method="POST" action="{{ route('account.tools.favorite', ['tool' => $tool['slug']]) }}">
                                                    @csrf
                                                    <button class="btn btn-sm btn-outline-secondary" type="submit">
                                                        <i class="bi bi-star-fill me-1" aria-hidden="true"></i>Desfavoritar
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="card border">
                            <div class="card-body p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                                <div>
                                    <h3 class="h5 mb-1">Nenhuma ferramenta favorita ainda</h3>
                                    <p class="text-body-secondary mb-0">Ao abrir uma ferramenta, use o botão <strong>Favoritar</strong> no topo dela. Ela aparecerá aqui.</p>
                                </div>
                                <i class="bi bi-star fs-3 text-body-secondary flex-shrink-0" aria-hidden="true"></i>
                            </div>
                        </div>
                    @endif
                </section>

                @if($historyTools->isNotEmpty())
                    <section class="card border mb-4" aria-labelledby="history-title">
                        <div class="card-body p-4">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-2 mb-3">
                                <div>
                                    <h2 class="h4 mb-1" id="history-title">Seu histórico por ferramenta</h2>
                                    <p class="text-body-secondary mb-0">Acesse rapidamente os históricos que já existem na sua conta.</p>
                                </div>
                            </div>

                            <div class="list-group list-group-flush">
                                @foreach($historyTools as $tool)
                                    <div class="list-group-item px-0 py-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                                        <div class="d-flex align-items-start gap-3">
                                            <i class="bi {{ $tool['tool_icon'] }} fs-5" aria-hidden="true"></i>
                                            <div>
                                                <h3 class="h6 mb-1">{{ $tool['tool_name'] }}</h3>
                                                <div class="small text-body-secondary">
                                                    {{ $tool['runs_count'] }} {{ $tool['runs_count'] === 1 ? 'resultado salvo' : 'resultados salvos' }}
                                                    @if($tool['last_used_at'])
                                                        · último em {{ \Illuminate\Support\Carbon::parse($tool['last_used_at'])->format('d/m/Y') }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-wrap gap-2">
                                            @if($tool['history_url'])
                                                <a class="btn btn-sm btn-outline-primary" href="{{ url()->query($tool['history_url'], ['source' => 'account_history']) }}">Ver histórico</a>
                                            @endif
                                            <a class="btn btn-sm btn-outline-secondary" href="{{ url()->query($tool['tool_url'], ['source' => 'account_history']) }}">Abrir ferramenta</a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                @endif

                <section class="card border mb-4" aria-labelledby="account-settings-title">
                    <div class="card-body p-4">
                        <h2 class="h4 mb-3" id="account-settings-title">Conta e segurança</h2>

                        @if (auth()->user()->hasVerifiedEmail())
                            <div class="alert alert-success d-flex align-items-center gap-2" role="status">
                                <i class="bi bi-patch-check-fill" aria-hidden="true"></i>
                                <span>Seu e-mail está confirmado.</span>
                            </div>
                        @else
                            <div class="alert alert-warning d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3" role="alert">
                                <span>Confirme seu e-mail para proteger a recuperação dos dados salvos.</span>
                                <a class="btn btn-sm btn-warning" href="{{ route('verification.notice') }}">Confirmar e-mail</a>
                            </div>
                        @endif

                        <div class="row g-4">
                            <div class="col-12 col-lg-7">
                                <div class="card h-100 border">
                                    <div class="card-body">
                                        <h3 class="h5">Alterar senha</h3>
                                        <form method="POST" action="{{ route('password.update') }}" novalidate>
                                            @csrf
                                            @method('PUT')
                                            <div class="mb-3">
                                                <label class="form-label" for="current_password">Senha atual</label>
                                                <input class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" id="current_password" name="current_password" type="password" autocomplete="current-password" required>
                                                @error('current_password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label" for="password">Nova senha</label>
                                                <input class="form-control @error('password', 'updatePassword') is-invalid @enderror" id="password" name="password" type="password" autocomplete="new-password" required>
                                                @error('password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label" for="password_confirmation">Confirmar nova senha</label>
                                                <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
                                            </div>
                                            <button class="btn btn-primary" type="submit">Atualizar senha</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="card border mb-4" aria-labelledby="business-access-title">
                    <div class="card-body p-4">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                            <div>
                                <h2 class="h4 mb-1" id="business-access-title">Acessos empresariais</h2>
                                <p class="text-body-secondary mb-0">Cadastre uma empresa ou acesse empresas às quais sua conta pertence.</p>
                            </div>
                            <a class="btn btn-outline-primary" href="{{ route('organizations.create') }}">Cadastrar empresa</a>
                        </div>

                        @if($memberships->isNotEmpty())
                            <div class="list-group list-group-flush mt-3">
                                @foreach($memberships as $membership)
                                    <a class="list-group-item list-group-item-action px-0 d-flex justify-content-between align-items-center" href="{{ route('organizations.show', $membership->organization) }}">
                                        <span>{{ $membership->organization->name }}</span>
                                        <i class="bi bi-chevron-right" aria-hidden="true"></i>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </section>

                <div class="alert alert-info mb-0" role="note">
                    <strong>Conta Prazzu unificada:</strong>
                    sua conta é local neste momento. A estrutura já está preparada para receber futuramente o identificador da conta única Prazzu sem usar seu e-mail como chave de integração.
                </div>
            </div>
        </div>
    </div>
@endsection
