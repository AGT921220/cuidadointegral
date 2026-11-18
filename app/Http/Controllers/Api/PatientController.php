<?php

namespace App\Http\Controllers\Api;

use App\Features\Care\Application\CreatePatient;
use App\Features\Care\Application\LinkPatientUser;
use App\Features\Care\Application\ShowPatientForCare;
use App\Features\Permissions\Constants\PermissionTypes;
use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\PatientLink;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

final class PatientController extends Controller
{
    public function __construct(
        private readonly ShowPatientForCare $showPatientForCare,
        private readonly CreatePatient $createPatient,
        private readonly LinkPatientUser $linkPatientUser,
    ) {}

    public function show(Request $request, Patient $patient): JsonResponse
    {
        $this->authorize(PermissionTypes::PATIENTS_VIEW);

        return response()->json([
            'data' => ($this->showPatientForCare)($request->user(), $patient),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize(PermissionTypes::PATIENTS_CREATE);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'phone' => ['nullable', 'string', 'max:32'],
            'address' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'required_with:longitude', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'required_with:latitude', 'numeric', 'between:-180,180'],
            'care_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        return response()->json([
            'data' => ($this->createPatient)($data),
        ], 201);
    }

    public function link(Request $request, Patient $patient): JsonResponse
    {
        $this->authorize(PermissionTypes::PATIENT_LINKS_CREATE);

        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'link_type' => ['required', 'string', Rule::in(PatientLink::types())],
        ]);

        return response()->json([
            'data' => ($this->linkPatientUser)($patient, $data),
        ], 201);
    }
}
