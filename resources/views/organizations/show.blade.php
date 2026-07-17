@extends('layouts.app')

@section('title', $organization->name.' — Prazzu Tools')
@section('meta_description', 'Gerencie os acessos empresariais do Prazzu Tools.')
@section('meta_robots', 'noindex,nofollow')

@section('content')
    @php
        $canManage = $membership?->role?->canManageOrganization() ?? false;
        $isOwner = $membership?->role?->value === 'owner';
        $occupiedSeats = $subscription?->seats?->count() ?? 0;
        $seatLimit = $subscription?->seat_limit ?? 0;
        $availableSeats = max(0, $seatLimit - $occupiedSeats);
    @endphp

    <div class="prazzu-page">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start gap-3 mb-4">
            <div>
                <span class="badge text-bg-primary mb-2">Painel empresarial</span>
                <h1 class="h3 mb-1">{{ $organization->name }}</h1>
                <p class="text-body-secondary mb-0">Administração de cadastro, membros, convites e acessos Plus.</p>
            </div>
            <a class="btn btn-outline-secondary" href="{{ route('account.show') }}">
                <i class="bi bi-arrow-left me-2" aria-hidden="true"></i>Minha conta
            </a>
        </div>

        <div class="alert alert-info d-flex gap-3" role="note">
            <i class="bi bi-shield-lock fs-4" aria-hidden="true"></i>
            <div>
                <strong>Privacidade preservada</strong>
                <div>A empresa administra somente vínculos e licenças. Históricos, cálculos, favoritos e resultados permanecem privados em cada conta.</div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="small text-body-secondary mb-1">Membros ativos</div>
                        <div class="h3 mb-0">{{ $organization->members->filter(fn ($member) => $member->isActive())->count() }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="small text-body-secondary mb-1">Acessos contratados</div>
                        <div class="h3 mb-0">{{ $seatLimit }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="small text-body-secondary mb-1">Acessos em uso</div>
                        <div class="h3 mb-0">{{ $occupiedSeats }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="small text-body-secondary mb-1">Acessos disponíveis</div>
                        <div class="h3 mb-0">{{ $availableSeats }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12 col-xl-5">
                <div class="d-grid gap-4">
                    <section class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                <div>
                                    <h2 class="h5 mb-1">Perfil da empresa</h2>
                                    <p class="text-body-secondary mb-0">Dados administrativos do contrato empresarial.</p>
                                </div>
                                <span class="badge text-bg-light">Cadastro</span>
                            </div>

                            @if($canManage)
                                <form method="POST" action="{{ route('organizations.update', $organization) }}" novalidate>
                                    @csrf
                                    @method('PATCH')
                                    <div class="mb-3">
                                        <label class="form-label" for="name">Nome da empresa</label>
                                        <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text" value="{{ old('name', $organization->name) }}" maxlength="160" required>
                                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <button class="btn btn-primary" type="submit">Salvar alterações</button>
                                </form>
                            @else
                                <div class="fw-semibold">{{ $organization->name }}</div>
                            @endif
                        </div>
                    </section>

                    <section class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h2 class="h5 mb-3">Responsável pela empresa</h2>
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-body-tertiary border rounded-circle p-3 lh-1 flex-shrink-0">
                                    <i class="bi bi-person-badge fs-4" aria-hidden="true"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $organization->owner?->name ?? 'Não informado' }}</div>
                                    <div class="text-body-secondary">{{ $organization->owner?->email ?? 'E-mail não informado' }}</div>
                                </div>
                            </div>
                            <hr>
                            <p class="small text-body-secondary mb-0">O responsável mantém uma conta pessoal independente e responde apenas pela administração da empresa e das licenças.</p>
                        </div>
                    </section>

                    @if($canManage)
                        <section class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <h2 class="h5">Convidar colaborador</h2>
                                <p class="text-body-secondary">Gere um link individual, válido por 7 dias e para uma única pessoa.</p>

                                @if(session('invitation_link'))
                                    <div class="alert alert-success" role="status">
                                        <label class="form-label fw-semibold" for="generated-invitation-link">Link gerado</label>
                                        <div class="input-group">
                                            <input class="form-control" id="generated-invitation-link" type="text" value="{{ session('invitation_link') }}" readonly>
                                            <button class="btn btn-outline-primary" type="button" data-copy-target="generated-invitation-link">Copiar</button>
                                        </div>
                                        <div class="form-text">Envie este endereço somente para a pessoa que deve entrar na empresa.</div>
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('organizations.invitations.store', $organization) }}">
                                    @csrf
                                    <button class="btn btn-primary" type="submit">
                                        <i class="bi bi-link-45deg me-2" aria-hidden="true"></i>Gerar link de convite
                                    </button>
                                </form>
                            </div>
                        </section>
                    @endif
                </div>
            </div>

            <div class="col-12 col-xl-7">
                <div class="d-grid gap-4">
                    <section class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3">
                                <div>
                                    <h2 class="h5 mb-1">Assinatura empresarial</h2>
                                    <p class="text-body-secondary mb-0">A empresa paga e distribui os acessos contratados.</p>
                                </div>
                                @if($subscription)
                                    <span class="badge {{ $subscription->grantsPlusAccess() ? 'text-bg-success' : 'text-bg-warning' }}">
                                        {{ match($subscription->status->value) { 'active' => 'Ativa', 'pending' => 'Pendente', 'past_due' => 'Pagamento pendente', 'suspended' => 'Suspensa', 'canceled' => 'Cancelada' } }}
                                    </span>
                                @else
                                    <span class="badge text-bg-secondary">Não configurada</span>
                                @endif
                            </div>

                            @if($subscription)
                                <div class="progress mb-3" role="progressbar" aria-label="Ocupação das vagas" aria-valuenow="{{ $occupiedSeats }}" aria-valuemin="0" aria-valuemax="{{ max(1, $seatLimit) }}">
                                    <div class="progress-bar" style="width: {{ $seatLimit > 0 ? min(100, ($occupiedSeats / $seatLimit) * 100) : 0 }}%">{{ $occupiedSeats }} de {{ $seatLimit }}</div>
                                </div>
                                <div class="row g-3 small">
                                    <div class="col-sm-6"><span class="text-body-secondary">Início:</span> {{ $subscription->starts_at?->format('d/m/Y') ?? 'Não iniciado' }}</div>
                                    <div class="col-sm-6"><span class="text-body-secondary">Término:</span> {{ $subscription->ends_at?->format('d/m/Y') ?? 'Sem término definido' }}</div>
                                </div>
                            @else
                                <div class="alert alert-secondary mb-0" role="status">A contratação será configurada pelo fluxo comercial. Nenhuma vaga pode ser distribuída antes disso.</div>
                            @endif
                        </div>
                    </section>

                    <section class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h2 class="h5 mb-1">Membros e acessos</h2>
                                    <p class="text-body-secondary mb-0">Gerencie o vínculo e a licença Plus sem acessar dados pessoais de uso.</p>
                                </div>
                                <span class="badge text-bg-secondary">{{ $organization->members->count() }}</span>
                            </div>

                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead>
                                    <tr>
                                        <th>Pessoa</th>
                                        <th>Vínculo</th>
                                        <th>Acesso Plus</th>
                                        @if($canManage)<th class="text-end">Gestão</th>@endif
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($organization->members as $member)
                                        @php
                                            $activeSeat = $member->seats->first(fn ($seat) => $seat->isActive());
                                            $canEditMember = $member->role->value !== 'owner' && ($isOwner || $member->role->value === 'member');
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $member->user->name }}</div>
                                                <div class="small text-body-secondary">{{ $member->user->email }}</div>
                                            </td>
                                            <td>
                                                <span class="badge {{ $member->isActive() ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $member->isActive() ? 'Ativo' : 'Inativo' }}</span>
                                                <div class="small text-body-secondary mt-1">{{ match($member->role->value) { 'owner' => 'Responsável', 'administrator' => 'Administrador', default => 'Colaborador' } }}</div>
                                            </td>
                                            <td>
                                                @if($activeSeat)
                                                    <span class="badge text-bg-primary">Plus empresarial</span>
                                                @else
                                                    <span class="text-body-secondary">Sem licença</span>
                                                @endif
                                            </td>
                                            @if($canManage)
                                                <td class="text-end">
                                                    <div class="d-flex flex-column flex-sm-row justify-content-end gap-2">
                                                        @if($canEditMember)
                                                            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#member-{{ $member->id }}" aria-expanded="false" aria-controls="member-{{ $member->id }}">Editar</button>
                                                        @endif

                                                        @if($activeSeat)
                                                            <form method="POST" action="{{ route('organizations.seats.destroy', [$organization, $activeSeat]) }}">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button class="btn btn-sm btn-outline-danger" type="submit">Liberar vaga</button>
                                                            </form>
                                                        @elseif($member->isActive() && $subscription?->grantsPlusAccess() && $availableSeats > 0)
                                                            <form method="POST" action="{{ route('organizations.seats.store', [$organization, $member]) }}">
                                                                @csrf
                                                                <button class="btn btn-sm btn-outline-primary" type="submit">Atribuir Plus</button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            @endif
                                        </tr>
                                        @if($canManage && $canEditMember)
                                            <tr class="collapse" id="member-{{ $member->id }}">
                                                <td colspan="4" class="bg-body-tertiary">
                                                    <form class="row g-3 align-items-end" method="POST" action="{{ route('organizations.members.update', [$organization, $member]) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <div class="col-12 col-md-5">
                                                            <label class="form-label" for="role-{{ $member->id }}">Função</label>
                                                            <select class="form-select" id="role-{{ $member->id }}" name="role">
                                                                <option value="member" @selected($member->role->value === 'member')>Colaborador</option>
                                                                @if($isOwner)<option value="administrator" @selected($member->role->value === 'administrator')>Administrador</option>@endif
                                                            </select>
                                                        </div>
                                                        <div class="col-12 col-md-4">
                                                            <label class="form-label" for="status-{{ $member->id }}">Status</label>
                                                            <select class="form-select" id="status-{{ $member->id }}" name="status">
                                                                <option value="active" @selected($member->status->value === 'active')>Ativo</option>
                                                                <option value="inactive" @selected($member->status->value === 'inactive')>Inativo</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-12 col-md-3">
                                                            <button class="btn btn-primary w-100" type="submit">Salvar</button>
                                                        </div>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endif
                                    @empty
                                        <tr><td colspan="4" class="text-body-secondary">Nenhum membro cadastrado.</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>

                    @if($canManage)
                        <section class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h2 class="h5 mb-0">Convites recentes</h2>
                                    <span class="badge text-bg-secondary">{{ $organization->invitations->count() }}</span>
                                </div>
                                <div class="table-responsive">
                                    <table class="table align-middle mb-0">
                                        <thead><tr><th>Criado em</th><th>Status</th><th>Utilizado por</th><th>Validade</th><th class="text-end">Ação</th></tr></thead>
                                        <tbody>
                                        @forelse($organization->invitations as $invitation)
                                            <tr>
                                                <td>{{ $invitation->created_at->format('d/m/Y H:i') }}</td>
                                                <td><span class="badge text-bg-secondary">{{ match($invitation->status->value) { 'pending' => 'Pendente', 'accepted' => 'Utilizado', 'revoked' => 'Cancelado', default => 'Expirado' } }}</span></td>
                                                <td>{{ $invitation->acceptedBy?->email ?? '—' }}</td>
                                                <td>{{ $invitation->expires_at->format('d/m/Y H:i') }}</td>
                                                <td class="text-end">
                                                    @if($invitation->status->value === 'pending')
                                                        <div class="d-inline-flex flex-wrap justify-content-end gap-2">
                                                            <button class="btn btn-sm btn-outline-primary" type="button" data-copy-value="{{ route('organizations.invitations.show', $invitation->token) }}">Copiar</button>
                                                            <form method="POST" action="{{ route('organizations.invitations.destroy', [$organization, $invitation]) }}">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button class="btn btn-sm btn-outline-danger" type="submit">Cancelar</button>
                                                            </form>
                                                        </div>
                                                    @elseif($invitation->status->value === 'revoked')
                                                        <div class="d-inline-flex flex-wrap justify-content-end gap-2">
                                                            <form method="POST" action="{{ route('organizations.invitations.restore', [$organization, $invitation]) }}">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button class="btn btn-sm btn-outline-primary" type="submit">Restaurar</button>
                                                            </form>
                                                            <form method="POST" action="{{ route('organizations.invitations.purge', [$organization, $invitation]) }}" onsubmit="return confirm('Apagar este convite definitivamente? Esta ação não pode ser desfeita.')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button class="btn btn-sm btn-outline-danger" type="submit">Apagar</button>
                                                            </form>
                                                        </div>
                                                    @else
                                                        <span class="text-body-secondary">—</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="5" class="text-body-secondary">Nenhum link de convite gerado.</td></tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </section>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('click', async (event) => {
    const button = event.target.closest('[data-copy-target], [data-copy-value]');
    if (!button) return;

    const target = button.dataset.copyTarget ? document.getElementById(button.dataset.copyTarget) : null;
    const value = target?.value ?? button.dataset.copyValue;
    if (!value) return;

    try {
        await navigator.clipboard.writeText(value);
        const original = button.textContent;
        button.textContent = 'Copiado';
        setTimeout(() => { button.textContent = original; }, 1500);
    } catch {
        if (target) { target.select(); document.execCommand('copy'); }
    }
});
</script>
@endpush
