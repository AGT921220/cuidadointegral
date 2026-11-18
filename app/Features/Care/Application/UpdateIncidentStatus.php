<?php

namespace App\Features\Care\Application;

use App\Models\Incident;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class UpdateIncidentStatus
{
    public function __construct(private readonly CareScope $careScope) {}

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function __invoke(User $user, Incident $incident, array $data): array
    {
        $this->careScope->assertCanAttendIncident($user, (int) $incident->patient_id);

        if ($incident->status === Incident::STATUS_CLOSED) {
            throw ValidationException::withMessages([
                'status' => 'La incidencia ya está cerrada.',
            ]);
        }

        if ($incident->status === $data['status']) {
            throw ValidationException::withMessages([
                'status' => 'La incidencia ya tiene ese estado.',
            ]);
        }

        DB::transaction(function () use ($user, $incident, $data): void {
            $incident->forceFill(['status' => $data['status']])->save();
            $incident->events()->create([
                'user_id' => $user->id,
                'status' => $data['status'],
                'note' => $data['note'] ?? null,
            ]);
        });

        $incident->refresh()->load(['reporter:id,name', 'events.user:id,name']);

        return $incident->toCareArray();
    }
}
