<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => User::with('account')->latest()->paginate(15),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['nullable', Rule::in(['client', 'admin', 'owner'])],
        ]);

        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = Hash::make($validated['password']);
        $user->phone = $validated['phone'] ?? null;
        $user->role = $validated['role'] ?? 'client';
        $user->save();

        return response()->json([
            'message' => 'Пользователь создан.',
            'data' => $user->load('account'),
        ], 201);
    }

    public function show(User $user)
    {
        return response()->json([
            'data' => $user->load('account', 'bookings'),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['sometimes', 'required', Rule::in(['client', 'admin', 'owner'])],
        ]);

        if (
            $user->id === auth()->id()
            && isset($validated['role'])
            && $validated['role'] !== $user->role
        ) {
            return response()->json([
                'message' => 'Нельзя изменить собственную роль.',
            ], 422);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Пользователь обновлён.',
            'data' => $user->fresh()->load('account'),
        ]);
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json([
                'message' => 'Нельзя удалить собственный аккаунт.',
            ], 422);
        }

        $user->delete();

        return response()->json([
            'message' => 'Пользователь удалён.',
        ]);
    }
}
