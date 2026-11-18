<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['patient_id', 'shift_id', 'user_id', 'level', 'status', 'description', 'occurred_at'])]
class Incident extends Model
{
    public const LEVEL_LOW = 'low';

    public const LEVEL_MEDIUM = 'medium';

    public const LEVEL_HIGH = 'high';

    public const STATUS_REPORTED = 'reported';

    public const STATUS_IN_ATTENTION = 'in_attention';

    public const STATUS_CLOSED = 'closed';

    /**
     * @return list<string>
     */
    public static function levels(): array
    {
        return [self::LEVEL_LOW, self::LEVEL_MEDIUM, self::LEVEL_HIGH];
    }

    /**
     * @return list<string>
     */
    public static function statuses(): array
    {
        return [self::STATUS_REPORTED, self::STATUS_IN_ATTENTION, self::STATUS_CLOSED];
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',
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

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(IncidentEvent::class);
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
            'level' => $this->level,
            'status' => $this->status,
            'description' => $this->description,
            'occurred_at' => $this->occurred_at?->toIso8601String(),
            'reporter' => $this->relationLoaded('reporter') && $this->reporter !== null
                ? ['id' => $this->reporter->id, 'name' => $this->reporter->name]
                : ['id' => $this->user_id],
            'events' => $this->relationLoaded('events')
                ? $this->events->map(fn (IncidentEvent $event): array => $event->toCareArray())->values()->all()
                : [],
        ];
    }
}
