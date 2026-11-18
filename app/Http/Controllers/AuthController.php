<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Показывает страницу регистрации.
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Регистрирует нового пользователя.
     */
    public function register(Request $request)
    {
        // Проверяем введённые пользователем данные.
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            // Телефон обязателен.
            // Разрешены цифры, пробелы и один знак + в начале.
            // Буквы и остальные символы запрещены.
            'phone' => [
                'required',
                'string',
                'regex:/^\+?[0-9 ]{10,16}$/',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        // Создаём пользователя.
        // Роль всегда client: посетитель не может самостоятельно
        // зарегистрироваться как администратор или владелец.
        $user = new User();

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];
        $user->password = Hash::make($validated['password']);
        $user->role = 'client';

        $user->save();

        // Автоматически авторизуем нового пользователя.
        Auth::login($user);

        // Обновляем сессию для безопасности.
        $request->session()->regenerate();

        return redirect()->route('home')
            ->with('success', 'Регистрация прошла успешно!');
    }

    /**
     * Показывает страницу входа.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Выполняет вход пользователя.
     */
    public function login(Request $request)
    {
        // Проверяем введённые данные.
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Проверяем логин и пароль.
        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Неверный email или пароль.',
            ]);
        }

        // Обновляем сессию после входа.
        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }

    /**
     * Выполняет выход из аккаунта.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        // Уничтожаем старую сессию.
        $request->session()->invalidate();

        // Создаём новый токен сессии.
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'Вы вышли из аккаунта.');
    }
}
