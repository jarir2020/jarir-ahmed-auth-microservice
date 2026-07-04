<?php

namespace JarirAhmed\AuthMicroservice\Controllers\Auth;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use JarirAhmed\AuthMicroservice\Services\MagicLinkService;
use JarirAhmed\AuthMicroservice\Services\NotificationService;
use Illuminate\Support\Facades\Auth;

class MagicLinkController extends Controller
{
    public function __construct(
        private MagicLinkService $magicLinkService,
        private NotificationService $notificationService
    ) {}

    public function send(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $userModel = config('auth-microservice.user_model');
        $user = $userModel::where('email', $request->email)->first();

        if ($user) {
            $token = $this->magicLinkService->generate($user);
            $this->notificationService->sendMagicLink($user, $token);
        }

        return response()->json(['message' => 'If that email exists, a sign-in link has been sent.']);
    }

    public function verify(Request $request)
    {
        $token = $request->query('token');
        $user  = $this->magicLinkService->verify($token ?? '');

        if (!$user) {
            return response()->json(['message' => 'Invalid or expired magic link.'], 422);
        }

        Auth::login($user);
        return response()->json(['message' => 'Login successful.', 'user' => $user]);
    }
}
