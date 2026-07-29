<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Persona;
use App\Models\User;
use App\Notifications\NuevaCuentaNotification;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    // ─── Login ──────────────────────────────────────────────────

    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Credenciales incorrectas. Verifica tu correo y contraseña.']);
        }

        $request->session()->regenerate();

        $user = Auth::user();
        $user->update(['ultimo_acceso' => now()]);

        // Sin verificar → pantalla de verificación
        if (! $user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        return redirect()->intended($this->redirectAfterLogin($user));
    }

    private function redirectAfterLogin(\App\Models\User $user): string
    {
        $roleNames = $user->roles->pluck('nombre')->toArray();

        // Roles de staff — van al panel admin
        $staffRoles = ['Admin', 'Administrador', 'Mecánico', 'Mecanico', 'Recepcionista', 'Cajero', 'Inventario', 'Supervisor'];

        foreach ($staffRoles as $role) {
            if (in_array($role, $roleNames)) {
                return route('dashboard');
            }
        }

        // Solo rol Cliente → portal cliente
        if (in_array('Cliente', $roleNames)) {
            return route('cliente.inicio');
        }

        // Sin rol → pendiente
        return route('pending');
    }

    public function pending(): RedirectResponse|\Illuminate\View\View
    {
        // Si ya tiene rol, mandarlo al dashboard
        if (Auth::user()->roles->isNotEmpty()) {
            return redirect()->route('dashboard');
        }

        return view('auth.pending');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    // ─── Registro ───────────────────────────────────────────────

    public function showRegister(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'nombre'            => 'required|string|max:100',
            'email'             => 'required|email|max:100|unique:users,email|unique:persona,email',
            'password'          => 'required|string|min:8|confirmed',
        ], [
            'email.unique'       => 'Este correo ya está registrado.',
            'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $user = null;

        DB::transaction(function () use ($request, &$user) {
            $persona = Persona::create([
                'nombre'   => $request->nombre,
                'email'    => $request->email,
                'activo'   => true,
            ]);

            $user = User::create([
                'persona_id' => $persona->id,
                'email'      => $request->email,
                'password'   => $request->password,
            ]);

            // Crear registro de cliente asociado
            \App\Models\Cliente::create(['persona_id' => $persona->id]);

            // Asignar rol Cliente automáticamente
            $rolCliente = \App\Models\Role::where('nombre', 'Cliente')->first();
            if ($rolCliente) {
                $user->roles()->attach($rolCliente->id);
            }
        });

        // Enviar email de verificación
        $user->sendEmailVerificationNotification();

        // Notificar a todos los admins
        User::whereHas('roles', fn($q) => $q->where('nombre', 'Administrador'))
            ->get()
            ->each(fn($admin) => $admin->notify(
                new NuevaCuentaNotification($request->nombre, $request->email)
            ));

        // Iniciar sesión automáticamente y redirigir a verificar email
        Auth::login($user);

        return redirect()->route('verification.notice');
    }

    // ─── Olvidé mi contraseña ────────────────────────────────────

    public function showForgotPassword(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', 'Te enviamos un enlace de recuperación a tu correo.')
            : back()->withErrors(['email' => 'No encontramos una cuenta con ese correo.']);
    }

    // ─── Restablecer contraseña ──────────────────────────────────

    public function showResetPassword(Request $request, string $token): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:8|confirmed',
        ], [
            'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])
                     ->setRememberToken(Str::random(60));
                $user->save();
                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', 'Contraseña restablecida. Ya puedes iniciar sesión.')
            : back()->withErrors(['email' => __($status)]);
    }
}
