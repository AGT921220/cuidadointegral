<?php

namespace App\Http\Controllers\Admin;

use App\Features\Permissions\Constants\PermissionTypes;
use App\Features\Roles\Application\SearchRoleHeaders;
use App\Features\Shared\Query\QueryOptions;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class RoleHeadersController extends Controller
{
    /**
     * @var list<string>
     */
    private const ORDERABLE = [
        'roles.id',
        'roles.name',
    ];

    public function __construct(
        private readonly SearchRoleHeaders $searchRoleHeaders,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize(PermissionTypes::ROLES_VIEW);

        $offset = max($request->integer('offset', 0), 0);
        $limit = $request->integer('limit', 20);

        if ($limit <= 0) {
            $limit = 20;
        }

        $field = (string) $request->input('order_by', 'roles.id');
        $direction = (string) $request->input('order_direction', 'desc');

        if (! in_array($field, self::ORDERABLE, true)) {
            $field = 'roles.id';
        }

        if (! in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'desc';
        }

        return response()->json(
            ($this->searchRoleHeaders)(
                modifiers: [
                    QueryOptions::offset($offset),
                    QueryOptions::limit($limit),
                    QueryOptions::orderBy($field, $direction),
                ],
                draw: max($request->integer('draw', 1), 1),
            ),
        );
    }
}
