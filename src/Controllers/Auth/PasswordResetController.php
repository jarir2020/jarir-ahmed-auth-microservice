<?php

namespace JarirAhmed\AuthMicroservice\Controllers\Auth;

use JarirAhmed\AuthMicroservice\Http\Request;
use JarirAhmed\AuthMicroservice\Http\Response;
use JarirAhmed\AuthMicroservice\Services\PasswordResetService;
use JarirAhmed\AuthMicroservice\Services\NotificationService;

class PasswordResetController
{
    public function __construct(
        private PasswordResetService $passwordResetService,
        private NotificationService $notificationService
    ) {}

    public function sendLink(Request $request): Response
    {
        $data = $request->validate(['email' => 'required|email']);
        $user = $this->passwordResetService->findUserByEmail($data['email']);

        if ($user) {
            $token = $this->passwordResetService->createToken($user);
            $this->notificationService->sendPasswordReset($user, $token);
        }

        return Response::json(['message' => 'If that email exists, a reset link has been sent.']);
    }

    public function reset(Request $request): Response
    {
        $data = $request->validate([
            'token'                 => 'required|string',
            'password'              => 'required|string|min:8|confirmed',
        ]);

        if (!$this->passwordResetService->reset($data['token'], $data['password'])) {
            return Response::json(['message' => 'Invalid or expired reset token.'], 422);
        }

        return Response::json(['message' => 'Password reset successfully.']);
    }
}
