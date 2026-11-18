<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['care_task_id', 'shift_id', 'user_id', 'status', 'observation', 'performed_at'])]
class CareTaskRecord extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_DONE = 'done';

    public const STATUS_SKIPPED = 'skipped';

    /**
     * @return list<string>
     */
    public static function statuses(): array
    {
        return [self::STATUS_PENDING, self::STATUS_DONE, self::STATUS_SKIPPED];
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'performed_at' => 'datetime',
        ];
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(CareTask::class, 'care_task_id');
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
            'status' => $this->status,
            'observation' => $this->observation,
            'performed_at' => $this->performed_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'user' => $this->relationLoaded('user') && $this->user !== null
                ? ['id' => $this->user->id, 'name' => $this->user->name]
                : ['id' => $this->user_id],
        ];
    }
}
