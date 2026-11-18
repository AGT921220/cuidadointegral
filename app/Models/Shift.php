<?php

namespace App\Models;

use Database\Factories\ShiftFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'patient_id',
    'caregiver_id',
    'status',
    'starts_at',
    'ends_at',
    'service_address',
    'latitude',
    'longitude',
    'instructions',
    'check_in_at',
    'check_in_latitude',
    'check_in_longitude',
    'check_in_accuracy',
    'check_out_at',
    'check_out_latitude',
    'check_out_longitude',
    'check_out_accuracy',
])]
class Shift extends Model
{
    public const STATUS_SCHEDULED = 'scheduled';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    /** @use HasFactory<ShiftFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'check_in_at' => 'datetime',
            'check_out_at' => 'datetime',
            'latitude' => 'float',
            'longitude' => 'float',
            'check_in_latitude' => 'float',
            'check_in_longitude' => 'float',
            'check_in_accuracy' => 'float',
            'check_out_latitude' => 'float',
            'check_out_longitude' => 'float',
            'check_out_accuracy' => 'float',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function caregiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'caregiver_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(ShiftEvent::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(ShiftLocation::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(CareTask::class);
    }

    public function taskRecords(): HasMany
    {
        return $this->hasMany(CareTaskRecord::class);
    }

    public function careLogs(): HasMany
    {
        return $this->hasMany(CareLog::class);
    }

    public function healthRecords(): HasMany
    {
        return $this->hasMany(HealthRecord::class);
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function toCareArray(): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'starts_at' => $this->starts_at?->toIso8601String(),
            'ends_at' => $this->ends_at?->toIso8601String(),
            'service_address' => $this->service_address ?? $this->patient?->address,
            'latitude' => $this->latitude ?? $this->patient?->latitude,
            'longitude' => $this->longitude ?? $this->patient?->longitude,
            'instructions' => $this->instructions,
            'check_in_at' => $this->check_in_at?->toIso8601String(),
            'check_out_at' => $this->check_out_at?->toIso8601String(),
            'check_in_location' => $this->locationPayload(
                $this->check_in_latitude,
                $this->check_in_longitude,
                $this->check_in_accuracy,
            ),
            'check_out_location' => $this->locationPayload(
                $this->check_out_latitude,
                $this->check_out_longitude,
                $this->check_out_accuracy,
            ),
            'patient' => $this->relationLoaded('patient') ? $this->patient?->toCareArray() : null,
            'caregiver' => $this->relationLoaded('caregiver') && $this->caregiver !== null
                ? ['id' => $this->caregiver->id, 'name' => $this->caregiver->name]
                : null,
        ];
    }

    /**
     * @return array{latitude: float, longitude: float, accuracy: float|null}|null
     */
    private function locationPayload(?float $latitude, ?float $longitude, ?float $accuracy): ?array
    {
        if ($latitude === null || $longitude === null) {
            return null;
        }

        return [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'accuracy' => $accuracy,
        ];
    }
}
