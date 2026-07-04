<?php

namespace JarirAhmed\AuthMicroservice\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use JarirAhmed\AuthMicroservice\Services\LockoutService;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function __construct(private LockoutService $lockoutService) {}

    public function index(Request $request)
    {
        $userModel = config('auth-microservice.user_model');
        return response()->json($userModel::latest()->paginate(20));
    }

    public function ban(Request $request, int $id)
    {
        $request->validate(['reason' => 'nullable|string|max:500']);
        $userModel = config('auth-microservice.user_model');
        $user = $userModel::findOrFail($id);
        $user->update(['is_banned' => true, 'ban_reason' => $request->reason]);
        return response()->json(['message' => 'User banned.']);
    }

    public function unban(Request $request, int $id)
    {
        $userModel = config('auth-microservice.user_model');
        $user = $userModel::findOrFail($id);
        $user->update(['is_banned' => false, 'ban_reason' => null]);
        return response()->json(['message' => 'User unbanned.']);
    }

    public function unlock(Request $request, int $id)
    {
        $userModel = config('auth-microservice.user_model');
        $user = $userModel::findOrFail($id);
        $this->lockoutService->adminUnlock($user, $request->user());
        return response()->json(['message' => 'Account unlocked.']);
    }

    public function impersonate(Request $request, int $id)
    {
        $userModel = config('auth-microservice.user_model');
        $user = $userModel::findOrFail($id);
        $request->session()->put('impersonating', $request->user()->getKey());
        Auth::login($user);
        return response()->json(['message' => "Now impersonating user {$id}."]);
    }

    public function stopImpersonating(Request $request)
    {
        $adminId = $request->session()->pull('impersonating');
        if (!$adminId) {
            return response()->json(['message' => 'Not impersonating anyone.'], 400);
        }
        $userModel = config('auth-microservice.user_model');
        Auth::login($userModel::findOrFail($adminId));
        return response()->json(['message' => 'Stopped impersonating.']);
    }
}
