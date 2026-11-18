<?php

namespace App\Features\Care\Application;

use App\Models\Shift;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class ListShifts
{
    public function __construct(private readonly CareScope $careScope) {}

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function __invoke(User $user, array $filters): array
    {
        $limit = min(max((int) ($filters['limit'] ?? 20), 1), 50);
        $offset = max((int) ($filters['offset'] ?? 0), 0);

        $query = Shift::query()->with(['patient', 'caregiver:id,name']);
        $this->careScope->constrainShifts($query, $user);
        $this->applyFilters($query, $filters);

        $total = (clone $query)->count();
        $shifts = $query->orderBy('starts_at')->offset($offset)->limit($limit)->get();

        return [
            'data' => $shifts->map(fn (Shift $shift): array => $shift->toCareArray())->values(),
            'meta' => [
                'total' => $total,
                'limit' => $limit,
                'offset' => $offset,
            ],
        ];
    }

    /**
     * @param  Builder<Shift>  $query
     * @param  array<string, mixed>  $filters
     */
    private function applyFilters(Builder $query, array $filters): void
    {
        $requested = $filters['status'] ?? [Shift::STATUS_SCHEDULED, Shift::STATUS_IN_PROGRESS];

        if (is_string($requested)) {
            $requested = [$requested];
        }

        $allowed = array_values(array_intersect((array) $requested, [
            Shift::STATUS_SCHEDULED,
            Shift::STATUS_IN_PROGRESS,
            Shift::STATUS_COMPLETED,
            Shift::STATUS_CANCELLED,
        ]));

        $query->whereIn('status', $allowed === [] ? ['__none__'] : $allowed);

        if (! empty($filters['from'])) {
            $query->where('starts_at', '>=', $filters['from']);
        }

        if (! empty($filters['to'])) {
            $query->where('starts_at', '<=', $filters['to']);
        }
    }
}
