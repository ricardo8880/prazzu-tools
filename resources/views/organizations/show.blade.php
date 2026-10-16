@extends('layouts.app')

@section('title', $organization->name.' — Prazzu Tools')
@section('meta_description', 'Manage business access in Prazzu Tools.')
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
                <span class="badge text-bg-primary mb-2">Business dashboard</span>
                <h1 class="h3 mb-1">{{ $organization->name }}</h1>
                <p class="text-body-secondary mb-0">Manage business profile, members, invitations, and Plus access.</p>
            </div>
            <a class="btn btn-outline-secondary" href="{{ route('account.show') }}">
                <i class="bi bi-arrow-left me-2" aria-hidden="true"></i>Meu Prazzu
            </a>
        </div>

        <div class="alert alert-info d-flex gap-3" role="note">
            <i class="bi bi-shield-lock fs-4" aria-hidden="true"></i>
            <div>
                <strong>Privacy protected</strong>
                <div>The business manages only membership and licenses. History, calculations, favorites, and results remain private in each account.</div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="small text-body-secondary mb-1">Active members</div>
                        <div class="h3 mb-0">{{ $organization->members->filter(fn ($member) => $member->isActive())->count() }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="small text-body-secondary mb-1">Purchased seats</div>
                        <div class="h3 mb-0">{{ $seatLimit }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="small text-body-secondary mb-1">Seats in use</div>
                        <div class="h3 mb-0">{{ $occupiedSeats }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="small text-body-secondary mb-1">Available seats</div>
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
                                    <h2 class="h5 mb-1">Business profile</h2>
                                    <p class="text-body-secondary mb-0">Administrative details for the business subscription.</p>
                                </div>
                                <span class="badge text-bg-light">Profile</span>
                            </div>

                            @if($canManage)
                                <form method="POST" action="{{ route('organizations.update', $organization) }}" novalidate>
                                    @csrf
                                    @method('PATCH')
                                    <div class="mb-3">
                                        <label class="form-label" for="name">Business name</label>
                                        <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text" value="{{ old('name', $organization->name) }}" maxlength="160" required>
                                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <button class="btn btn-primary" type="submit">Save changes</button>
                                </form>
                            @else
                                <div class="fw-semibold">{{ $organization->name }}</div>
                            @endif
                        </div>
                    </section>

                    <section class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h2 class="h5 mb-3">Business owner</h2>
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-body-tertiary border rounded-circle p-3 lh-1 flex-shrink-0">
                                    <i class="bi bi-person-badge fs-4" aria-hidden="true"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $organization->owner?->name ?? 'Not provided' }}</div>
                                    <div class="text-body-secondary">{{ $organization->owner?->email ?? 'Email not provided' }}</div>
                                </div>
                            </div>
                            <hr>
                            <p class="small text-body-secondary mb-0">The owner keeps an independent personal account and is responsible only for business and license administration.</p>
                        </div>
                    </section>

                    @if($canManage)
                        <section class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <h2 class="h5">Invite team member</h2>
                                <p class="text-body-secondary">Generate an individual link valid for 7 days and one person.</p>

                                @if(session('invitation_link'))
                                    <div class="alert alert-success" role="status">
                                        <label class="form-label fw-semibold" for="generated-invitation-link">Link gerado</label>
                                        <div class="input-group">
                                            <input class="form-control" id="generated-invitation-link" type="text" value="{{ session('invitation_link') }}" readonly>
                                            <button class="btn btn-outline-primary" type="button" data-copy-target="generated-invitation-link">Copy</button>
                                        </div>
                                        <div class="form-text">Send this address only to the person who should join the business.</div>
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('organizations.invitations.store', $organization) }}">
                                    @csrf
                                    <button class="btn btn-primary" type="submit">
                                        <i class="bi bi-link-45deg me-2" aria-hidden="true"></i>Generate invitation link
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
                                    <h2 class="h5 mb-1">Business subscription</h2>
                                    <p class="text-body-secondary mb-0">The business pays for and assigns the purchased seats.</p>
                                </div>
                                @if($subscription)
                                    <span class="badge {{ $subscription->grantsPlusAccess() ? 'text-bg-success' : 'text-bg-warning' }}">
                                        {{ match($subscription->status->value) { 'active' => 'Active', 'pending' => 'Pending', 'past_due' => 'Payment due', 'suspended' => 'Suspended', 'canceled' => 'Canceled' } }}
                                    </span>
                                @else
                                    <span class="badge text-bg-secondary">Not configured</span>
                                @endif
                            </div>

                            @if($subscription)
                                <div class="progress mb-3" role="progressbar" aria-label="Seat usage" aria-valuenow="{{ $occupiedSeats }}" aria-valuemin="0" aria-valuemax="{{ max(1, $seatLimit) }}">
                                    <div class="progress-bar" style="width: {{ $seatLimit > 0 ? min(100, ($occupiedSeats / $seatLimit) * 100) : 0 }}%">{{ $occupiedSeats }} de {{ $seatLimit }}</div>
                                </div>
                                <div class="row g-3 small">
                                    <div class="col-sm-6"><span class="text-body-secondary">Starts:</span> {{ $subscription->starts_at?->format('m/d/Y') ?? 'Not started' }}</div>
                                    <div class="col-sm-6"><span class="text-body-secondary">Ends:</span> {{ $subscription->ends_at?->format('m/d/Y') ?? 'No end date' }}</div>
                                </div>
                            @else
                                <div class="alert alert-secondary mb-0" role="status">The subscription will be configured through the sales process. No seats can be assigned before then.</div>
                            @endif
                        </div>
                    </section>

                    <section class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h2 class="h5 mb-1">Members and access</h2>
                                    <p class="text-body-secondary mb-0">Manage membership and Plus licenses without accessing personal usage data.</p>
                                </div>
                                <span class="badge text-bg-secondary">{{ $organization->members->count() }}</span>
                            </div>

                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead>
                                    <tr>
                                        <th>Pessoa</th>
                                        <th>Membership</th>
                                        <th>Plus access</th>
                                        @if($canManage)<th class="text-end">Management</th>@endif
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
                                                <span class="badge {{ $member->isActive() ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $member->isActive() ? 'Active' : 'Inactive' }}</span>
                                                <div class="small text-body-secondary mt-1">{{ match($member->role->value) { 'owner' => 'Owner', 'administrator' => 'Administrator', default => 'Member' } }}</div>
                                            </td>
                                            <td>
                                                @if($activeSeat)
                                                    <span class="badge text-bg-primary">Business Plus</span>
                                                @else
                                                    <span class="text-body-secondary">No license</span>
                                                @endif
                                            </td>
                                            @if($canManage)
                                                <td class="text-end">
                                                    <div class="d-flex flex-column flex-sm-row justify-content-end gap-2">
                                                        @if($canEditMember)
                                                            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#member-{{ $member->id }}" aria-expanded="false" aria-controls="member-{{ $member->id }}">Edit</button>
                                                        @endif

                                                        @if($activeSeat)
                                                            <form method="POST" action="{{ route('organizations.seats.destroy', [$organization, $activeSeat]) }}">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button class="btn btn-sm btn-outline-danger" type="submit">Release seat</button>
                                                            </form>
                                                        @elseif($member->isActive() && $subscription?->grantsPlusAccess() && $availableSeats > 0)
                                                            <form method="POST" action="{{ route('organizations.seats.store', [$organization, $member]) }}">
                                                                @csrf
                                                                <button class="btn btn-sm btn-outline-primary" type="submit">Assign Plus</button>
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
                                                            <label class="form-label" for="role-{{ $member->id }}">Role</label>
                                                            <select class="form-select" id="role-{{ $member->id }}" name="role">
                                                                <option value="member" @selected($member->role->value === 'member')>Member</option>
                                                                @if($isOwner)<option value="administrator" @selected($member->role->value === 'administrator')>Administrator</option>@endif
                                                            </select>
                                                        </div>
                                                        <div class="col-12 col-md-4">
                                                            <label class="form-label" for="status-{{ $member->id }}">Status</label>
                                                            <select class="form-select" id="status-{{ $member->id }}" name="status">
                                                                <option value="active" @selected($member->status->value === 'active')>Active</option>
                                                                <option value="inactive" @selected($member->status->value === 'inactive')>Inactive</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-12 col-md-3">
                                                            <button class="btn btn-primary w-100" type="submit">Save</button>
                                                        </div>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endif
                                    @empty
                                        <tr><td colspan="4" class="text-body-secondary">No members yet.</td></tr>
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
                                    <h2 class="h5 mb-0">Recent invitations</h2>
                                    <span class="badge text-bg-secondary">{{ $organization->invitations->count() }}</span>
                                </div>
                                <div class="table-responsive">
                                    <table class="table align-middle mb-0">
                                        <thead><tr><th>Created</th><th>Status</th><th>Used by</th><th>Expires</th><th class="text-end">Action</th></tr></thead>
                                        <tbody>
                                        @forelse($organization->invitations as $invitation)
                                            <tr>
                                                <td>{{ $invitation->created_at->format('m/d/Y h:i A') }}</td>
                                                <td><span class="badge text-bg-secondary">{{ match($invitation->status->value) { 'pending' => 'Pending', 'accepted' => 'Used', 'revoked' => 'Canceled', default => 'Expired' } }}</span></td>
                                                <td>{{ $invitation->acceptedBy?->email ?? '—' }}</td>
                                                <td>{{ $invitation->expires_at->format('m/d/Y h:i A') }}</td>
                                                <td class="text-end">
                                                    @if($invitation->status->value === 'pending')
                                                        <div class="d-inline-flex flex-wrap justify-content-end gap-2">
                                                            <button class="btn btn-sm btn-outline-primary" type="button" data-copy-value="{{ route('organizations.invitations.show', $invitation->token) }}">Copy</button>
                                                            <form method="POST" action="{{ route('organizations.invitations.destroy', [$organization, $invitation]) }}">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button class="btn btn-sm btn-outline-danger" type="submit">Cancel</button>
                                                            </form>
                                                        </div>
                                                    @elseif($invitation->status->value === 'revoked')
                                                        <div class="d-inline-flex flex-wrap justify-content-end gap-2">
                                                            <form method="POST" action="{{ route('organizations.invitations.restore', [$organization, $invitation]) }}">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button class="btn btn-sm btn-outline-primary" type="submit">Restore</button>
                                                            </form>
                                                            <form method="POST" action="{{ route('organizations.invitations.purge', [$organization, $invitation]) }}" data-confirm="Permanently delete this invitation? This action cannot be undone.">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                                            </form>
                                                        </div>
                                                    @else
                                                        <span class="text-body-secondary">—</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="5" class="text-body-secondary">No invitation links generated.</td></tr>
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
<script nonce="{{ $cspNonce ?? '' }}">
document.addEventListener('click', async (event) => {
    const button = event.target.closest('[data-copy-target], [data-copy-value]');
    if (!button) return;

    const target = button.dataset.copyTarget ? document.getElementById(button.dataset.copyTarget) : null;
    const value = target?.value ?? button.dataset.copyValue;
    if (!value) return;

    try {
        await navigator.clipboard.writeText(value);
        const original = button.textContent;
        button.textContent = 'Copied';
        setTimeout(() => { button.textContent = original; }, 1500);
    } catch {
        if (target) { target.select(); document.execCommand('copy'); }
    }
});
</script>
@endpush
