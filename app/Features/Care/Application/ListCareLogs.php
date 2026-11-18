<?php

namespace App\Features\Care\Application;

use App\Models\CareLog;
use App\Models\Shift;
use App\Models\User;

final class ListCareLogs
{
    public function __construct(private readonly CareScope $careScope) {}

    /**
     * @return list<array<string, mixed>>
     */
    public function __invoke(User $user, Shift $shift): array
    {
        $this->careScope->assertCanViewShift($user, $shift);

        return CareLog::query()
            ->where('shift_id', $shift->id)
            ->with('user:id,name')
            ->orderBy('id')
            ->get()
            ->map(fn (CareLog $log): array => $log->toCareArray())
            ->values()
            ->all();
    }
}
