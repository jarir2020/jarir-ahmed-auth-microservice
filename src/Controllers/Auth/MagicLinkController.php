<?php

namespace JarirAhmed\AuthMicroservice\Controllers\Auth;

use JarirAhmed\AuthMicroservice\Http\Request;
use JarirAhmed\AuthMicroservice\Http\Response;
use JarirAhmed\AuthMicroservice\Services\MagicLinkService;
use JarirAhmed\AuthMicroservice\Services\NotificationService;
use JarirAhmed\AuthMicroservice\Auth\SessionAuth;
use JarirAhmed\AuthMicroservice\Config;

class MagicLinkController
{
    public function __construct(
        private MagicLinkService $magicLinkService,
        private NotificationService $notificationService
    ) {}

    public function send(Request $request): Response
    {
        $data = $request->validate(['email' => 'required|email']);
        $userModel = Config::get('auth-microservice.user_model');
        $user = $userModel::where('email', $data['email'])->first();

        if ($user) {
            $token = $this->magicLinkService->generate($user);
            $this->notificationService->sendMagicLink($user, $token);
        }

        return Response::json(['message' => 'If that email exists, a sign-in link has been sent.']);
    }

    public function verify(Request $request): Response
    {
        $token = $request->query('token');
        $user  = $this->magicLinkService->verify($token ?? '');

        if (!$user) {
            return Response::json(['message' => 'Invalid or expired magic link.'], 422);
        }

        SessionAuth::login($user);
        return Response::json(['message' => 'Login successful.', 'user' => $user->toArray()]);
    }
}
