<?php

namespace App\Http\Controllers\Api;

use App\Features\Care\Application\SearchCareReports;
use App\Features\Permissions\Constants\PermissionTypes;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CareReportController extends Controller
{
    public function __construct(private readonly SearchCareReports $searchCareReports) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize(PermissionTypes::REPORTS_VIEW);

        $filters = $request->validate([
            'patient_id' => ['sometimes', 'integer'],
            'caregiver_id' => ['sometimes', 'integer'],
            'shift_id' => ['sometimes', 'integer'],
            'from' => ['sometimes', 'date'],
            'to' => ['sometimes', 'date'],
            'limit' => ['sometimes', 'integer', 'min:1', 'max:20'],
            'offset' => ['sometimes', 'integer', 'min:0'],
        ]);

        return response()->json(($this->searchCareReports)($request->user(), $filters));
    }
}
