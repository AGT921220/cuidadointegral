<?php

namespace App\Features\Care\Application;

use App\Models\CareTask;
use App\Models\Shift;
use Illuminate\Validation\ValidationException;

final class CreateCareTask
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function __invoke(array $data): array
    {
        if (! empty($data['shift_id'])) {
            $shift = Shift::query()->findOrFail($data['shift_id']);

            if ((int) $shift->patient_id !== (int) $data['patient_id']) {
                throw ValidationException::withMessages([
                    'shift_id' => 'El turno no corresponde al paciente.',
                ]);
            }
        }

        $task = CareTask::query()->create([
            'patient_id' => $data['patient_id'],
            'shift_id' => $data['shift_id'] ?? null,
            'title' => $data['title'],
            'instructions' => $data['instructions'] ?? null,
        ]);

        return $task->toCareArray();
    }
}
