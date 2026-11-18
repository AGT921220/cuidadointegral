<?php

namespace App\Features\Care\Application;

use App\Models\Patient;
use App\Models\User;

final class ShowPatientForCare
{
    public function __construct(private readonly CareScope $careScope) {}

    /**
     * @return array<string, mixed>
     */
    public function __invoke(User $user, Patient $patient): array
    {
        $this->careScope->assertCanViewPatient($user, (int) $patient->id);

        return $patient->toCareArray();
    }
}
