<?php

namespace App\Core\Organizations\Actions;

use App\Core\Organizations\Contracts\OrganizationSeatCounter;
use App\Core\Organizations\Enums\OrganizationSubscriptionStatus;
use App\Models\Organization;
use App\Models\OrganizationSubscription;
use Illuminate\Validation\ValidationException;

final readonly class ConfigureOrganizationSubscription
{
    public function __construct(private OrganizationSeatCounter $seatCounter) {}

    public function execute(
        Organization $organization,
        int $seatLimit,
        OrganizationSubscriptionStatus $status,
        ?string $billingProvider = null,
        ?string $billingReference = null,
    ): OrganizationSubscription {
        if ($seatLimit < 1) {
            throw ValidationException::withMessages(['seat_limit' => 'A assinatura deve possuir ao menos uma vaga.']);
        }

        if ($seatLimit < $this->seatCounter->occupiedSeats($organization->getKey())) {
            throw ValidationException::withMessages(['seat_limit' => 'Libere vagas antes de reduzir a quantidade contratada.']);
        }

        return $organization->subscriptions()->updateOrCreate(
            ['billing_reference' => $billingReference],
            [
                'status' => $status,
                'seat_limit' => $seatLimit,
                'billing_provider' => $billingProvider,
                'starts_at' => $status === OrganizationSubscriptionStatus::Active ? now() : null,
                'ends_at' => null,
            ],
        );
    }
}
