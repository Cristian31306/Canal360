<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Password;

class UserController extends Controller
{
    /**
     * Check if the authenticated user is an admin.
     */
    protected function authorizeAdmin()
    {
        if (!auth()->user()->is_admin) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }
    }

    /**
     * Display a listing of the users.
     */
    public function index()
    {
        $this->authorizeAdmin();

        return Inertia::render('Admin/Users', [
            'users' => User::select('id', 'name', 'email', 'is_admin', 'is_active', 'created_at')
                ->latest()
                ->get(),
        ]);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'is_admin' => 'boolean',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => $request->is_admin ?? false,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $this->authorizeAdmin();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email,'.$user->id,
            'is_admin' => 'boolean',
            'is_active' => 'boolean',
        ];

        if ($request->filled('password') && $request->password !== '') {
            $rules['password'] = ['required', 'confirmed', Rules\Password::defaults()];
        }

        $request->validate($rules);

        $user->name = $request->name;
        $user->email = $request->email;
        
        // Only update password if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Security: Don't allow an admin to demote themselves or suspend themselves
        if (auth()->id() !== $user->id) {
            $user->is_admin = $request->is_admin;
            $user->is_active = $request->is_active;
        }

        $user->save();

        return redirect()->back()->with('success', 'Usuario actualizado correctamente.');
    /**
     * Send a password reset link to the given user.
     */
    public function sendPasswordResetLink(User $user)
    {
        $this->authorizeAdmin();

        $status = Password::broker()->sendResetLink(['email' => $user->email]);

        if ($status === Password::RESET_LINK_SENT) {
            return redirect()->back()->with('success', 'Enlace de recuperación enviado correctamente.');
        }

        return redirect()->back()->with('error', 'No se pudo enviar el enlace de recuperación. Verifica la configuración de correo.');
    }
}
