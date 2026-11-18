<?php

namespace App\Http\Controllers\Api;

use App\Features\Care\Application\CreateCareTask;
use App\Features\Care\Application\ListShiftTasks;
use App\Features\Care\Application\RecordCareTask;
use App\Features\Permissions\Constants\PermissionTypes;
use App\Http\Controllers\Controller;
use App\Models\CareTask;
use App\Models\CareTaskRecord;
use App\Models\Shift;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

final class CareTaskController extends Controller
{
    public function __construct(
        private readonly ListShiftTasks $listShiftTasks,
        private readonly RecordCareTask $recordCareTask,
        private readonly CreateCareTask $createCareTask,
    ) {}

    public function index(Request $request, Shift $shift): JsonResponse
    {
        $this->authorize(PermissionTypes::CARE_TASKS_VIEW);

        return response()->json([
            'data' => ($this->listShiftTasks)($request->user(), $shift),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize(PermissionTypes::CARE_TASKS_CREATE);

        $data = $request->validate([
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'shift_id' => ['nullable', 'integer', 'exists:shifts,id'],
            'title' => ['required', 'string', 'max:255'],
            'instructions' => ['nullable', 'string', 'max:5000'],
        ]);

        return response()->json([
            'data' => ($this->createCareTask)($data),
        ], 201);
    }

    public function record(Request $request, Shift $shift, CareTask $careTask): JsonResponse
    {
        $this->authorize(PermissionTypes::CARE_TASKS_RECORD);

        $data = $request->validate([
            'status' => ['required', 'string', Rule::in(CareTaskRecord::statuses())],
            'observation' => ['nullable', 'string', 'max:5000'],
            'performed_at' => ['nullable', 'date'],
        ]);

        return response()->json([
            'data' => ($this->recordCareTask)($request->user(), $shift, $careTask, $data),
        ], 201);
    }
}
