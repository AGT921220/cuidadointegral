<?php

namespace App\Features\Care\Application;

use App\Models\CareTask;
use App\Models\Shift;
use App\Models\User;

final class ListShiftTasks
{
    public function __construct(private readonly CareScope $careScope) {}

    /**
     * @return list<array<string, mixed>>
     */
    public function __invoke(User $user, Shift $shift): array
    {
        $this->careScope->assertCanViewShift($user, $shift);

        return CareTask::query()
            ->where('patient_id', $shift->patient_id)
            ->where(function ($query) use ($shift): void {
                $query->whereNull('shift_id')->orWhere('shift_id', $shift->id);
            })
            ->with(['records' => function ($query) use ($shift): void {
                $query->where('shift_id', $shift->id)->with('user:id,name')->latest('id');
            }])
            ->orderBy('id')
            ->get()
            ->map(fn (CareTask $task): array => $task->toCareArray())
            ->values()
            ->all();
    }
}
