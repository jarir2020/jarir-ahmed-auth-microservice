<?php

namespace JarirAhmed\AuthMicroservice\Controllers\Auth;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use JarirAhmed\AuthMicroservice\Services\SocialLoginService;
use Illuminate\Support\Facades\Auth;

class SocialLoginController extends Controller
{
    public function __construct(private SocialLoginService $socialLoginService) {}

    public function redirect(Request $request, string $provider)
    {
        $state = bin2hex(random_bytes(16));
        $request->session()->put("oauth_state_{$provider}", $state);
        $url = $this->socialLoginService->getRedirectUrl($provider, $state);
        return response()->json(['redirect_url' => $url]);
    }

    public function callback(Request $request, string $provider)
    {
        $expectedState = $request->session()->pull("oauth_state_{$provider}", '');

        try {
            $user = $this->socialLoginService->handleCallback(
                $provider,
                $request->query('code', ''),
                $request->query('state', ''),
                $expectedState
            );
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        Auth::login($user);
        return response()->json(['message' => 'Login successful.', 'user' => $user]);
    }
}
