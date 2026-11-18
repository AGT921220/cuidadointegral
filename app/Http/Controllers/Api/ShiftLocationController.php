<?php

namespace App\Http\Controllers\Api;

use App\Features\Care\Application\StoreShiftLocation;
use App\Features\Permissions\Constants\PermissionTypes;
use App\Http\Controllers\Controller;
use App\Models\Shift;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ShiftLocationController extends Controller
{
    public function __construct(private readonly StoreShiftLocation $storeShiftLocation) {}

    public function store(Request $request, Shift $shift): JsonResponse
    {
        $this->authorize(PermissionTypes::SHIFT_LOCATIONS_CREATE);

        $data = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'accuracy' => ['nullable', 'numeric', 'min:0', 'max:10000'],
            'recorded_at' => ['nullable', 'date'],
        ]);

        return response()->json([
            'data' => ($this->storeShiftLocation)($request->user(), $shift, $data),
        ], 201);
    }
}
