<?php

namespace JarirAhmed\AuthMicroservice\Controllers\Auth;

use JarirAhmed\AuthMicroservice\Http\Request;
use JarirAhmed\AuthMicroservice\Http\Response;
use JarirAhmed\AuthMicroservice\Services\AuthService;

class LogoutController
{
    public function __construct(private AuthService $authService) {}

    public function logout(Request $request): Response
    {
        $this->authService->logout($request->user());
        return Response::json(['message' => 'Logged out successfully.']);
    }
}
