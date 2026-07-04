<?php

namespace JarirAhmed\AuthMicroservice\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use JarirAhmed\AuthMicroservice\Events\PasswordChanged;
use JarirAhmed\AuthMicroservice\Events\AccountDeleted;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        return response()->json($request->user());
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name'  => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $request->user()->getKey(),
        ]);

        $request->user()->update($data);
        return response()->json(['message' => 'Profile updated.', 'user' => $request->user()->fresh()]);
    }

    public function changePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();
        if (!Hash::check($data['current_password'], $user->password)) {
            return response()->json(['message' => 'Current password is incorrect.'], 422);
        }

        $user->update(['password' => Hash::make($data['password'])]);
        event(new PasswordChanged($user));

        return response()->json(['message' => 'Password changed successfully.']);
    }

    public function closeAccount(Request $request)
    {
        $request->validate(['password' => 'required|string']);
        $user = $request->user();

        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Password is incorrect.'], 422);
        }

        event(new AccountDeleted($user));
        $user->delete();

        return response()->json(['message' => 'Account deleted.']);
    }

    public function updateNotificationPreferences(Request $request)
    {
        $data = $request->validate([
            'preferences' => 'required|array',
        ]);

        $request->user()->update(['notification_preferences' => $data['preferences']]);
        return response()->json(['message' => 'Preferences updated.']);
    }
}
