<?php

namespace App\Http\Controllers\Api;

use App\Features\Care\Application\ReportIncident;
use App\Features\Care\Application\ShowIncident;
use App\Features\Care\Application\UpdateIncidentStatus;
use App\Features\Permissions\Constants\PermissionTypes;
use App\Http\Controllers\Controller;
use App\Models\Incident;
use App\Models\Shift;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

final class IncidentController extends Controller
{
    public function __construct(
        private readonly ReportIncident $reportIncident,
        private readonly ShowIncident $showIncident,
        private readonly UpdateIncidentStatus $updateIncidentStatus,
    ) {}

    public function store(Request $request, Shift $shift): JsonResponse
    {
        $this->authorize(PermissionTypes::INCIDENTS_CREATE);

        $data = $request->validate([
            'level' => ['required', 'string', Rule::in(Incident::levels())],
            'description' => ['required', 'string', 'max:5000'],
            'occurred_at' => ['nullable', 'date'],
        ]);

        return response()->json([
            'data' => ($this->reportIncident)($request->user(), $shift, $data),
        ], 201);
    }

    public function show(Request $request, Incident $incident): JsonResponse
    {
        $this->authorize(PermissionTypes::INCIDENTS_VIEW);

        return response()->json([
            'data' => ($this->showIncident)($request->user(), $incident),
        ]);
    }

    public function attend(Request $request, Incident $incident): JsonResponse
    {
        $this->authorize(PermissionTypes::INCIDENTS_UPDATE);

        $data = $request->validate([
            'status' => ['required', 'string', Rule::in(Incident::statuses())],
            'note' => ['nullable', 'string', 'max:5000'],
        ]);

        return response()->json([
            'data' => ($this->updateIncidentStatus)($request->user(), $incident, $data),
        ]);
    }
}
