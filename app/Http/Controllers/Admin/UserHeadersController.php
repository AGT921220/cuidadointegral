<?php

namespace App\Http\Controllers\Admin;

use App\Features\Permissions\Constants\PermissionTypes;
use App\Features\Shared\Query\QueryOptions;
use App\Features\Users\Application\SearchUserHeaders;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class UserHeadersController extends Controller
{
    /**
     * @var list<string>
     */
    private const ORDERABLE = [
        'users.id',
        'users.name',
        'users.nickname',
        'users.email',
    ];

    public function __construct(
        private readonly SearchUserHeaders $searchUserHeaders,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize(PermissionTypes::USERS_VIEW);

        $offset = max($request->integer('offset', 0), 0);
        $limit = $request->integer('limit', 20);

        if ($limit <= 0) {
            $limit = 20;
        }

        [$orderBy, $direction] = $this->order($request);

        return response()->json(
            ($this->searchUserHeaders)(
                modifiers: [
                    QueryOptions::offset($offset),
                    QueryOptions::limit($limit),
                    QueryOptions::orderBy($orderBy, $direction),
                ],
                draw: max($request->integer('draw', 1), 1),
            ),
        );
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function order(Request $request): array
    {
        $field = (string) $request->input('order_by', 'users.id');
        $direction = (string) $request->input('order_direction', 'desc');

        if (! in_array($field, self::ORDERABLE, true)) {
            $field = 'users.id';
        }

        if (! in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'desc';
        }

        return [$field, $direction];
    }
}
