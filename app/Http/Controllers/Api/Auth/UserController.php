<?php

namespace App\Http\Controllers\Api\Auth;

use App\Features\Auth\Application\AuthenticatedUserPayload;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        private readonly AuthenticatedUserPayload $authenticatedUserPayload,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return response()->json(
            ($this->authenticatedUserPayload)($user),
        );
    }
}
