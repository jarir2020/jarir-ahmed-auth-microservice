<?php

namespace JarirAhmed\AuthMicroservice\Controllers;

use JarirAhmed\AuthMicroservice\Http\Controller;
use JarirAhmed\AuthMicroservice\Http\Request;
use JarirAhmed\AuthMicroservice\Services\TokenService;

class TokenController extends Controller
{
    public function __construct(private TokenService $tokenService) {}

    public function index(Request $request)
    {
        return response()->json($request->user()->personalAccessTokens()->whereNull('revoked_at')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'scopes'      => 'array',
            'expiry_days' => 'integer|min:1',
        ]);

        $result = $this->tokenService->issue(
            $request->user(),
            $data['name'],
            $data['scopes'] ?? [],
            $data['expiry_days'] ?? null
        );

        return response()->json(['token' => $result['token'], 'model' => $result['model']], 201);
    }

    public function destroy(Request $request, int $id)
    {
        $token = $request->user()->personalAccessTokens()->findOrFail($id);
        $this->tokenService->revoke($token->token);
        return response()->json(['message' => 'Token revoked.']);
    }
}
