<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total' => User::count(),
            'clients' => User::where('role', 'user')->count(),
            'admins' => User::whereIn('role', ['admin', 'owner'])->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', Rule::in(['user', 'admin', 'owner'])],
        ]);

        if ($user->id === Auth::id() && $validated['role'] !== $user->role) {
            return back()
                ->withInput()
                ->withErrors(['role' => 'Нельзя изменить собственную роль.']);
        }

        $user->update($validated);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Данные пользователя успешно обновлены.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Нельзя удалить собственную учётную запись.');
        }

        if ($user->bookings()->whereIn('status', ['pending', 'confirmed', 'active'])->exists()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Нельзя удалить пользователя с активными бронированиями.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Пользователь успешно удалён.');
    }
}
