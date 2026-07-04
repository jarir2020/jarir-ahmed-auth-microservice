<?php

namespace JarirAhmed\AuthMicroservice\Controllers\Auth;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use JarirAhmed\AuthMicroservice\Services\PasswordResetService;
use JarirAhmed\AuthMicroservice\Services\NotificationService;

class PasswordResetController extends Controller
{
    public function __construct(
        private PasswordResetService $passwordResetService,
        private NotificationService $notificationService
    ) {}

    public function sendLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = $this->passwordResetService->findUserByEmail($request->email);

        if ($user) {
            $token = $this->passwordResetService->createToken($user);
            $this->notificationService->sendPasswordReset($user, $token);
        }

        return response()->json(['message' => 'If that email exists, a reset link has been sent.']);
    }

    public function reset(Request $request)
    {
        $data = $request->validate([
            'token'                 => 'required|string',
            'password'              => 'required|string|min:8|confirmed',
        ]);

        if (!$this->passwordResetService->reset($data['token'], $data['password'])) {
            return response()->json(['message' => 'Invalid or expired reset token.'], 422);
        }

        return response()->json(['message' => 'Password reset successfully.']);
    }
}
