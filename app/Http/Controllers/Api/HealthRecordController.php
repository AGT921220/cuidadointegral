<?php

namespace App\Http\Controllers\Api;

use App\Features\Care\Application\CreateHealthRecord;
use App\Features\Care\Application\ListHealthRecords;
use App\Features\Permissions\Constants\PermissionTypes;
use App\Http\Controllers\Controller;
use App\Models\Shift;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

final class HealthRecordController extends Controller
{
    public function __construct(
        private readonly ListHealthRecords $listHealthRecords,
        private readonly CreateHealthRecord $createHealthRecord,
    ) {}

    public function index(Request $request, Shift $shift): JsonResponse
    {
        $this->authorize(PermissionTypes::HEALTH_RECORDS_VIEW);

        return response()->json([
            'data' => ($this->listHealthRecords)($request->user(), $shift),
        ]);
    }

    public function store(Request $request, Shift $shift): JsonResponse
    {
        $this->authorize(PermissionTypes::HEALTH_RECORDS_CREATE);

        $data = $request->validate([
            'recorded_at' => ['nullable', 'date'],
            'temperature' => ['nullable', 'numeric', 'between:30,45'],
            'heart_rate' => ['nullable', 'integer', 'between:20,250'],
            'systolic' => ['nullable', 'integer', 'between:50,250'],
            'diastolic' => ['nullable', 'integer', 'between:30,150', Rule::when($request->filled('systolic'), 'lt:systolic')],
            'oxygen_saturation' => ['nullable', 'integer', 'between:50,100'],
            'respiratory_rate' => ['nullable', 'integer', 'between:5,60'],
            'glucose' => ['nullable', 'integer', 'between:20,600'],
            'symptoms' => ['nullable', 'string', 'max:5000', 'required_without_all:temperature,heart_rate,systolic,diastolic,oxygen_saturation,respiratory_rate,glucose,progress_notes'],
            'progress_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        return response()->json([
            'data' => ($this->createHealthRecord)($request->user(), $shift, $data),
        ], 201);
    }
}
