<?php

namespace JarirAhmed\AuthMicroservice\Controllers\Auth;

use JarirAhmed\AuthMicroservice\Http\Request;
use JarirAhmed\AuthMicroservice\Http\Response;
use JarirAhmed\AuthMicroservice\Services\AuthService;

class RegisterController
{
    public function __construct(private AuthService $authService) {}

    public function register(Request $request): Response
    {
        $data = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email',
            'password'              => 'required|string|min:8|confirmed',
        ]);

        $user = $this->authService->register($data);

        return Response::json(['message' => 'Registration successful. Please verify your email.', 'user' => $user->toArray()], 201);
    }

    public function verify(Request $request): Response
    {
        $token = $request->query('token');
        if (!$token || !$this->authService->verifyEmail($token)) {
            return Response::json(['message' => 'Invalid or expired verification token.'], 422);
        }
        return Response::json(['message' => 'Email verified successfully.']);
    }

    public function resend(Request $request): Response
    {
        $data = $request->validate(['email' => 'required|email']);
        $userModel = \JarirAhmed\AuthMicroservice\Config::get('auth-microservice.user_model');
        $user = $userModel::where('email', $data['email'])->first();

        if ($user && !$user->isEmailVerified()) {
            $this->authService->resendVerification($user);
        }

        return Response::json(['message' => 'If that email exists and is unverified, a new link has been sent.']);
    }
}
