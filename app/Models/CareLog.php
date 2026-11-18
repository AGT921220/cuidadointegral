<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['shift_id', 'patient_id', 'user_id', 'kind', 'body'])]
class CareLog extends Model
{
    public const KIND_ACTIVITY = 'activity';

    public const KIND_OBSERVATION = 'observation';

    public const KIND_FOLLOW_UP = 'follow_up';

    /**
     * @return list<string>
     */
    public static function kinds(): array
    {
        return [self::KIND_ACTIVITY, self::KIND_OBSERVATION, self::KIND_FOLLOW_UP];
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
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
            'shift_id' => $this->shift_id,
            'patient_id' => $this->patient_id,
            'kind' => $this->kind,
            'body' => $this->body,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'author' => $this->relationLoaded('user') && $this->user !== null
                ? ['id' => $this->user->id, 'name' => $this->user->name]
                : ['id' => $this->user_id],
        ];
    }
}
