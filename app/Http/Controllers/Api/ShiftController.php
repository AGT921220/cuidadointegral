<?php

namespace App\Http\Controllers\Api;

use App\Features\Care\Application\CheckInShift;
use App\Features\Care\Application\CheckOutShift;
use App\Features\Care\Application\CreateShift;
use App\Features\Care\Application\ListShifts;
use App\Features\Care\Application\ShowShift;
use App\Features\Permissions\Constants\PermissionTypes;
use App\Http\Controllers\Controller;
use App\Models\Shift;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ShiftController extends Controller
{
    public function __construct(
        private readonly ListShifts $listShifts,
        private readonly ShowShift $showShift,
        private readonly CheckInShift $checkInShift,
        private readonly CheckOutShift $checkOutShift,
        private readonly CreateShift $createShift,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize(PermissionTypes::SHIFTS_VIEW);

        $filters = $request->validate([
            'status' => ['sometimes'],
            'status.*' => ['string'],
            'from' => ['sometimes', 'date'],
            'to' => ['sometimes', 'date'],
            'limit' => ['sometimes', 'integer', 'min:1', 'max:50'],
            'offset' => ['sometimes', 'integer', 'min:0'],
        ]);

        return response()->json(($this->listShifts)($request->user(), $filters));
    }

    public function show(Request $request, Shift $shift): JsonResponse
    {
        $this->authorize(PermissionTypes::SHIFTS_VIEW);

        return response()->json([
            'data' => ($this->showShift)($request->user(), $shift),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize(PermissionTypes::SHIFTS_CREATE);

        $data = $request->validate([
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'caregiver_id' => ['required', 'integer', 'exists:users,id'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'service_address' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'required_with:longitude', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'required_with:latitude', 'numeric', 'between:-180,180'],
            'instructions' => ['nullable', 'string', 'max:5000'],
        ]);

        return response()->json([
            'data' => ($this->createShift)($data),
        ], 201);
    }

    public function checkIn(Request $request, Shift $shift): JsonResponse
    {
        $this->authorize(PermissionTypes::SHIFTS_CHECK_IN);

        $data = $request->validate($this->locationRules());

        return response()->json([
            'data' => ($this->checkInShift)($request->user(), $shift, $data),
        ]);
    }

    public function checkOut(Request $request, Shift $shift): JsonResponse
    {
        $this->authorize(PermissionTypes::SHIFTS_CHECK_OUT);

        $data = $request->validate($this->locationRules());

        return response()->json([
            'data' => ($this->checkOutShift)($request->user(), $shift, $data),
        ]);
    }

    /**
     * @return array<string, list<string>>
     */
    private function locationRules(): array
    {
        return [
            'latitude' => ['nullable', 'required_with:longitude', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'required_with:latitude', 'numeric', 'between:-180,180'],
            'accuracy' => ['nullable', 'numeric', 'min:0', 'max:10000'],
        ];
    }
}
