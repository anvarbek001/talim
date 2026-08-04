<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller implements HasMiddleware
{
    /**
     * Roles assignable from the admin panel.
     *
     * @var list<string>
     */
    public const ASSIGNABLE_ROLES = ['admin', 'teacher', 'inspector', 'student'];

    public static function middleware(): array
    {
        return ['auth', 'admin'];
    }

    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $role = $request->query('role');

        $users = User::with('roles')
            ->when($q !== '', fn ($query) => $query->where(
                fn ($sub) => $sub->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%")
            ))
            ->when($role, fn ($query) => $query->whereHas('roles', fn ($r) => $r->where('name', $role)))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $roles = Role::whereIn('name', self::ASSIGNABLE_ROLES)->pluck('name');

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'string', 'in:'.implode(',', self::ASSIGNABLE_ROLES)],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'email_verified_at' => now(),
        ]);

        $user->assignRole($data['role']);

        return redirect()->route('admin.users.index')->with('success', 'Foydalanuvchi muvaffaqiyatli qo\'shildi');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', 'string', 'in:'.implode(',', self::ASSIGNABLE_ROLES)],
        ]);

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => filled($data['password'] ?? null) ? Hash::make($data['password']) : $user->password,
        ]);

        $user->syncRoles([$data['role']]);

        return redirect()->route('admin.users.index')->with('success', 'Foydalanuvchi ma\'lumotlari yangilandi');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_if($user->id === auth()->id(), 403, "O'zingizni o'chira olmaysiz");

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', "Foydalanuvchi o'chirildi");
    }
}
