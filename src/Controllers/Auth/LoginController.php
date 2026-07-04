<?php

namespace JarirAhmed\AuthMicroservice\Controllers\Auth;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use JarirAhmed\AuthMicroservice\Services\AuthService;
use JarirAhmed\AuthMicroservice\Exceptions\AccountLockedException;
use JarirAhmed\AuthMicroservice\Exceptions\EmailNotVerifiedException;

class LoginController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function login(Request $request)
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
                $request->userAgent() ?? '',
                $data['remember_me'] ?? false
            );
        } catch (AccountLockedException $e) {
            return response()->json(['message' => $e->getMessage()], 423);
        } catch (EmailNotVerifiedException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }

        if (!$user) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        if ($user->two_factor_enabled) {
            return response()->json(['message' => '2FA required.', 'requires_2fa' => true, 'user_id' => $user->getKey()]);
        }

        return response()->json(['message' => 'Login successful.', 'user' => $user]);
    }
}
