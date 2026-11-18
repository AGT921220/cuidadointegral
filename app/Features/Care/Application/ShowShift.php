<?php

namespace App\Features\Care\Application;

use App\Models\Shift;
use App\Models\User;

final class ShowShift
{
    public function __construct(private readonly CareScope $careScope) {}

    /**
     * @return array<string, mixed>
     */
    public function __invoke(User $user, Shift $shift): array
    {
        $this->careScope->assertCanViewShift($user, $shift);
        $shift->load(['patient', 'caregiver:id,name']);

        return $shift->toCareArray();
    }
}
