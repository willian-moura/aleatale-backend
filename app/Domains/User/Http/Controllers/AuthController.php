<?php

namespace App\Domains\User\Http\Controllers;

use App\Domains\User\Services\AuthService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    /**
     * Login user and create token.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $result = $this->authService->login(
            $request->email,
            $request->password
        );

        return response()->json($result);
    }

    /**
     * Logout user and revoke current token.
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user()->currentAccessToken());

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }
}
