<?php

namespace App\Core\Organizations\Actions;

use App\Models\OrganizationMember;
use App\Models\OrganizationSeat;
use App\Models\OrganizationSubscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final readonly class AssignOrganizationSeat
{
    public function __construct(private ReleaseOrganizationSeat $releaseSeat) {}

    public function execute(OrganizationSubscription $subscription, OrganizationMember $member): OrganizationSeat
    {
        if (! $subscription->grantsPlusAccess()) {
            throw ValidationException::withMessages(['seat' => 'A assinatura empresarial precisa estar ativa para distribuir acessos Plus.']);
        }

        if ($member->organization_id !== $subscription->organization_id || ! $member->isActive()) {
            throw ValidationException::withMessages(['member' => 'A vaga só pode ser atribuída a um membro ativo desta empresa.']);
        }

        return DB::transaction(function () use ($subscription, $member): OrganizationSeat {
            $locked = OrganizationSubscription::query()->lockForUpdate()->findOrFail($subscription->getKey());

            $existingSeats = OrganizationSeat::query()
                ->where('organization_member_id', $member->getKey())
                ->whereNull('released_at')
                ->with('subscription')
                ->lockForUpdate()
                ->get();

            $currentSeat = $existingSeats->firstWhere('organization_subscription_id', $locked->getKey());

            if ($currentSeat !== null) {
                return $currentSeat;
            }

            $existingSeats->each(fn (OrganizationSeat $seat) => $this->releaseSeat->execute($seat));

            $occupied = $locked->seats()->whereNull('released_at')->count();
            if ($occupied >= $locked->seat_limit) {
                throw ValidationException::withMessages(['seat' => 'Todas as vagas contratadas já estão ocupadas.']);
            }

            return $locked->seats()->create([
                'organization_member_id' => $member->getKey(),
                'assigned_at' => now(),
            ]);
        });
    }
}
