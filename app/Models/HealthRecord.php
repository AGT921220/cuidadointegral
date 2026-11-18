<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'patient_id',
    'shift_id',
    'user_id',
    'recorded_at',
    'temperature',
    'heart_rate',
    'systolic',
    'diastolic',
    'oxygen_saturation',
    'respiratory_rate',
    'glucose',
    'symptoms',
    'progress_notes',
])]
class HealthRecord extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'recorded_at' => 'datetime',
            'temperature' => 'float',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function toCareArray(): array
    {
        return [
            'id' => $this->id,
            'patient_id' => $this->patient_id,
            'shift_id' => $this->shift_id,
            'recorded_at' => $this->recorded_at?->toIso8601String(),
            'temperature' => $this->temperature,
            'heart_rate' => $this->heart_rate,
            'systolic' => $this->systolic,
            'diastolic' => $this->diastolic,
            'oxygen_saturation' => $this->oxygen_saturation,
            'respiratory_rate' => $this->respiratory_rate,
            'glucose' => $this->glucose,
            'symptoms' => $this->symptoms,
            'progress_notes' => $this->progress_notes,
            'author' => $this->relationLoaded('user') && $this->user !== null
                ? ['id' => $this->user->id, 'name' => $this->user->name]
                : ['id' => $this->user_id],
        ];
    }
}
