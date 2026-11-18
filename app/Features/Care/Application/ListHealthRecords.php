<?php

namespace App\Features\Care\Application;

use App\Models\HealthRecord;
use App\Models\Shift;
use App\Models\User;

final class ListHealthRecords
{
    public function __construct(private readonly CareScope $careScope) {}

    /**
     * @return list<array<string, mixed>>
     */
    public function __invoke(User $user, Shift $shift): array
    {
        $this->careScope->assertCanViewShift($user, $shift);

        return HealthRecord::query()
            ->where('shift_id', $shift->id)
            ->with('user:id,name')
            ->orderByDesc('recorded_at')
            ->get()
            ->map(fn (HealthRecord $record): array => $record->toCareArray())
            ->values()
            ->all();
    }
}
