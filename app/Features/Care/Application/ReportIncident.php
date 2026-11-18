<?php

namespace App\Features\Care\Application;

use App\Features\Care\Jobs\NotifyIncidentJob;
use App\Models\Incident;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class ReportIncident
{
    public function __construct(private readonly CareScope $careScope) {}

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function __invoke(User $user, Shift $shift, array $data): array
    {
        $this->careScope->assertCanOperateShift($user, $shift);

        $incident = DB::transaction(function () use ($user, $shift, $data): Incident {
            $incident = Incident::query()->create([
                'patient_id' => $shift->patient_id,
                'shift_id' => $shift->id,
                'user_id' => $user->id,
                'level' => $data['level'],
                'status' => Incident::STATUS_REPORTED,
                'description' => $data['description'],
                'occurred_at' => $data['occurred_at'] ?? now(),
            ]);

            $incident->events()->create([
                'user_id' => $user->id,
                'status' => Incident::STATUS_REPORTED,
                'note' => 'Incidencia reportada',
            ]);

            return $incident;
        });

        NotifyIncidentJob::dispatch($incident->id);

        $incident->load(['reporter:id,name', 'events.user:id,name']);

        return $incident->toCareArray();
    }
}
