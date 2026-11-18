<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'client',
                'phone' => $validated['phone'] ?? null,
            ]);

            Account::create([
                'user_id' => $user->id,
                'balance' => 0.00,
                'tier' => 'standard',
                'status' => 'active',
            ]);

            return $user;
        }, 3);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Регистрация прошла успешно',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user->load('account'),
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Неверный логин или пароль.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Успешный вход в систему',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user->load('account'),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Токен успешно отозван (выход выполнен)',
        ]);
    }

    public function user(Request $request)
    {
        return response()->json([
            'user' => $request->user()->load('account'),
        ]);
    }
}
