<?php

namespace App\Http\Controllers\Api;

use App\Features\Care\Application\CreateCareLog;
use App\Features\Care\Application\ListCareLogs;
use App\Features\Care\Application\UpdateCareLog;
use App\Features\Permissions\Constants\PermissionTypes;
use App\Http\Controllers\Controller;
use App\Models\CareLog;
use App\Models\Shift;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

final class CareLogController extends Controller
{
    public function __construct(
        private readonly ListCareLogs $listCareLogs,
        private readonly CreateCareLog $createCareLog,
        private readonly UpdateCareLog $updateCareLog,
    ) {}

    public function index(Request $request, Shift $shift): JsonResponse
    {
        $this->authorize(PermissionTypes::CARE_LOGS_VIEW);

        return response()->json([
            'data' => ($this->listCareLogs)($request->user(), $shift),
        ]);
    }

    public function store(Request $request, Shift $shift): JsonResponse
    {
        $this->authorize(PermissionTypes::CARE_LOGS_CREATE);

        $data = $request->validate([
            'kind' => ['required', 'string', Rule::in(CareLog::kinds())],
            'body' => ['required', 'string', 'max:5000'],
        ]);

        return response()->json([
            'data' => ($this->createCareLog)($request->user(), $shift, $data),
        ], 201);
    }

    public function update(Request $request, CareLog $careLog): JsonResponse
    {
        $this->authorize(PermissionTypes::CARE_LOGS_CREATE);

        $data = $request->validate([
            'kind' => ['sometimes', 'string', Rule::in(CareLog::kinds())],
            'body' => ['sometimes', 'string', 'max:5000'],
        ]);

        return response()->json([
            'data' => ($this->updateCareLog)($request->user(), $careLog, $data),
        ]);
    }
}
