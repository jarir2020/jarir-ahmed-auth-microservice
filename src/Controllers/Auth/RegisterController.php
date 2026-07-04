<?php

namespace JarirAhmed\AuthMicroservice\Controllers\Auth;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use JarirAhmed\AuthMicroservice\Services\AuthService;

class RegisterController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|string|min:8|confirmed',
        ]);

        $user = $this->authService->register($data);

        return response()->json(['message' => 'Registration successful. Please verify your email.', 'user' => $user], 201);
    }

    public function verify(Request $request)
    {
        $token = $request->query('token');
        if (!$token || !$this->authService->verifyEmail($token)) {
            return response()->json(['message' => 'Invalid or expired verification token.'], 422);
        }
        return response()->json(['message' => 'Email verified successfully.']);
    }

    public function resend(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $userModel = config('auth-microservice.user_model');
        $user = $userModel::where('email', $request->email)->first();

        if ($user && !$user->isEmailVerified()) {
            $this->authService->resendVerification($user);
        }

        return response()->json(['message' => 'If that email exists and is unverified, a new link has been sent.']);
    }
}
