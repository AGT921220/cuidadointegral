<?php

namespace App\Features\Care\Application;

use App\Models\CareTask;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Validation\ValidationException;

final class RecordCareTask
{
    public function __construct(private readonly CareScope $careScope) {}

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function __invoke(User $user, Shift $shift, CareTask $task, array $data): array
    {
        $this->careScope->assertCanOperateShift($user, $shift);

        if ((int) $task->patient_id !== (int) $shift->patient_id) {
            throw ValidationException::withMessages([
                'care_task' => 'La tarea no corresponde al paciente de este turno.',
            ]);
        }

        if ($task->shift_id !== null && (int) $task->shift_id !== (int) $shift->id) {
            throw ValidationException::withMessages([
                'care_task' => 'La tarea no corresponde a este turno.',
            ]);
        }

        $record = $task->records()->create([
            'shift_id' => $shift->id,
            'user_id' => $user->id,
            'status' => $data['status'],
            'observation' => $data['observation'] ?? null,
            'performed_at' => $data['performed_at'] ?? now(),
        ]);

        $record->load('user:id,name');

        return $record->toCareArray();
    }
}
