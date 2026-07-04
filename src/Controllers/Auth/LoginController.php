<?php

namespace JarirAhmed\AuthMicroservice\Controllers\Auth;

use JarirAhmed\AuthMicroservice\Http\Request;
use JarirAhmed\AuthMicroservice\Http\Response;
use JarirAhmed\AuthMicroservice\Services\AuthService;
use JarirAhmed\AuthMicroservice\Exceptions\AccountLockedException;
use JarirAhmed\AuthMicroservice\Exceptions\EmailNotVerifiedException;

class LoginController
{
    public function __construct(private AuthService $authService) {}

    public function login(Request $request): Response
    {
        $data = $request->validate([
            'email'       => 'required|email',
            'password'    => 'required|string',
            'remember_me' => 'boolean',
        ]);

        try {
            $user = $this->authService->login(
                $data['email'],
                $data['password'],
                $request->ip(),
                $request->userAgent(),
                $data['remember_me'] ?? false
            );
        } catch (AccountLockedException $e) {
            return Response::json(['message' => $e->getMessage()], 423);
        } catch (EmailNotVerifiedException $e) {
            return Response::json(['message' => $e->getMessage()], 403);
        }

        if (!$user) {
            return Response::json(['message' => 'Invalid credentials.'], 401);
        }

        if ($user->two_factor_enabled) {
            return Response::json(['message' => '2FA required.', 'requires_2fa' => true, 'user_id' => $user->getKey()]);
        }

        return Response::json(['message' => 'Login successful.', 'user' => $user->toArray()]);
    }
}
