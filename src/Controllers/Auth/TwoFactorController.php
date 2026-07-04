<?php

namespace JarirAhmed\AuthMicroservice\Controllers\Auth;

use JarirAhmed\AuthMicroservice\Http\Controller;
use JarirAhmed\AuthMicroservice\Http\Request;
use JarirAhmed\AuthMicroservice\Services\TwoFactorService;

class TwoFactorController extends Controller
{
    public function __construct(private TwoFactorService $twoFactorService) {}

    public function enable(Request $request)
    {
        $result = $this->twoFactorService->enable($request->user());
        return response()->json($result);
    }

    public function disable(Request $request)
    {
        $this->twoFactorService->disable($request->user());
        return response()->json(['message' => '2FA disabled.']);
    }

    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        $user = $request->user();

        $valid = $this->twoFactorService->verify($user, $request->code)
            || $this->twoFactorService->verifyBackupCode($user, $request->code);

        if (!$valid) {
            return response()->json(['message' => 'Invalid 2FA code.'], 422);
        }

        $request->session()->put('2fa_verified', true);
        return response()->json(['message' => '2FA verified.']);
    }
}
