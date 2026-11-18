<?php

namespace App\Features\Care\Application;

use App\Models\Shift;
use App\Models\User;

final class CreateHealthRecord
{
    public function __construct(private readonly CareScope $careScope) {}

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function __invoke(User $user, Shift $shift, array $data): array
    {
        $this->careScope->assertCanOperateShift($user, $shift);

        $record = $shift->healthRecords()->create([
            'patient_id' => $shift->patient_id,
            'user_id' => $user->id,
            'recorded_at' => $data['recorded_at'] ?? now(),
            'temperature' => $data['temperature'] ?? null,
            'heart_rate' => $data['heart_rate'] ?? null,
            'systolic' => $data['systolic'] ?? null,
            'diastolic' => $data['diastolic'] ?? null,
            'oxygen_saturation' => $data['oxygen_saturation'] ?? null,
            'respiratory_rate' => $data['respiratory_rate'] ?? null,
            'glucose' => $data['glucose'] ?? null,
            'symptoms' => $data['symptoms'] ?? null,
            'progress_notes' => $data['progress_notes'] ?? null,
        ]);

        $record->load('user:id,name');

        return $record->toCareArray();
    }
}
