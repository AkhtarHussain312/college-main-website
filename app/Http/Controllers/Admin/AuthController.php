<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function login(Request $r): JsonResponse
    {
        $data = $r->validate(['email' => ['required', 'email'], 'password' => ['required', 'string'], 'remember' => ['sometimes', 'boolean']]);
        if (! Auth::attempt(['email' => $data['email'], 'password' => $data['password'], 'is_admin' => true], $data['remember'] ?? false)) {
            return response()->json(['message' => 'The provided credentials are incorrect.'], 422);
        }
        $r->session()->regenerate();

        return response()->json(['data' => $r->user()->only('id', 'name', 'email')]);
    }

    public function me(Request $r): JsonResponse
    {
        return response()->json(['data' => $r->user()->only('id', 'name', 'email')]);
    }

    public function updateProfile(Request $r): JsonResponse
    {
        $user = $r->user();

        $data = $r->validate([
            'name' => 'required|string|max:150',
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'current_password' => 'nullable|string',
            'password' => 'nullable|string|min:8|max:100|confirmed',
        ]);

        if (! empty($data['password'])) {
            if (empty($data['current_password']) || ! Hash::check($data['current_password'], $user->password)) {
                return response()->json([
                    'message' => 'The provided current password does not match our records.',
                    'errors' => ['current_password' => ['The current password is incorrect.']],
                ], 422);
            }
            $user->password = $data['password'];
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->save();

        return response()->json([
            'message' => 'Login credentials updated successfully.',
            'data' => $user->only('id', 'name', 'email'),
        ]);
    }

    public function logout(Request $r): JsonResponse
    {
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();

        return response()->json(['message' => 'Logged out.']);
    }
}
