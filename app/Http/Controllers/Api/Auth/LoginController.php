<?php

namespace App\Http\Controllers\Api\Auth;

use App\Features\Auth\Application\AuthenticateMobileUser;
use App\Features\Auth\Application\AuthenticatedUserPayload;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function __construct(
        private readonly AuthenticateMobileUser $authenticateMobileUser,
        private readonly AuthenticatedUserPayload $authenticatedUserPayload,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'nickname' => ['required', 'string'],
            'password' => ['required', 'string'],
            'device_name' => ['required', 'string', 'max:255'],
        ]);

        $user = ($this->authenticateMobileUser)(
            $credentials['nickname'],
            $credentials['password'],
        );

        $token = $user->createToken($credentials['device_name'])->plainTextToken;

        return response()->json([
            'token_type' => 'Bearer',
            'token' => $token,
            'user' => ($this->authenticatedUserPayload)($user),
        ]);
    }
}
