<?php

namespace App\Features\Care\Application;

use App\Models\Incident;
use App\Models\User;

final class ShowIncident
{
    public function __construct(private readonly CareScope $careScope) {}

    /**
     * @return array<string, mixed>
     */
    public function __invoke(User $user, Incident $incident): array
    {
        $this->careScope->assertCanViewPatient($user, (int) $incident->patient_id);
        $incident->load(['reporter:id,name', 'events.user:id,name']);

        return $incident->toCareArray();
    }
}
