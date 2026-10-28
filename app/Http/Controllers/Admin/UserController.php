<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $page = User::orderByDesc('is_admin')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $page->setCollection($page->getCollection()->map(fn ($u) => [
            'id' => $u->id,
            'name' => $u->name,
            'email' => $u->email,
            'is_admin' => (bool) $u->is_admin,
            'created_at' => $u->created_at?->format('M d, Y') ?? 'N/A',
        ]));

        return response()->json([
            'data' => $page,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150|unique:users,email',
            'password' => 'required|string|min:8|max:100',
            'is_admin' => 'nullable|boolean',
        ]);

        $validated['is_admin'] = $request->boolean('is_admin', true);

        $user = User::create($validated);

        return response()->json([
            'message' => 'User created successfully.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_admin' => (bool) $user->is_admin,
                'created_at' => $user->created_at?->format('M d, Y') ?? 'N/A',
            ],
        ], 201);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'nullable|string|min:8|max:100',
            'is_admin' => 'nullable|boolean',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        if (array_key_exists('is_admin', $validated)) {
            $isAdmin = $request->boolean('is_admin');
            if ($user->id === $request->user()->id && ! $isAdmin) {
                return response()->json([
                    'message' => 'You cannot revoke your own administrator privileges.',
                ], 422);
            }
            $validated['is_admin'] = $isAdmin;
        }

        $user->update($validated);

        return response()->json([
            'message' => 'User updated successfully.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_admin' => (bool) $user->is_admin,
                'created_at' => $user->created_at?->format('M d, Y') ?? 'N/A',
            ],
        ]);
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        if ($user->id === $request->user()->id) {
            return response()->json([
                'message' => 'You cannot delete your own logged-in account.',
            ], 422);
        }

        if ($user->is_admin && User::where('is_admin', true)->count() <= 1) {
            return response()->json([
                'message' => 'Cannot delete the only remaining administrator account.',
            ], 422);
        }

        $user->delete();

        return response()->json([], 204);
    }
}

