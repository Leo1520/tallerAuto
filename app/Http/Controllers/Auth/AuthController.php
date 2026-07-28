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

        // Sin rol → pantalla de espera
        if ($user->roles->isEmpty()) {
            return redirect()->route('pending');
        }

        return redirect()->intended(route('dashboard'));
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

        DB::transaction(function () use ($request) {
            $persona = Persona::create([
                'nombre'   => $request->nombre,
                'email'    => $request->email,
                'activo'   => true,
            ]);

            User::create([
                'persona_id' => $persona->id,
                'email'      => $request->email,
                'password'   => $request->password,
            ]);
        });

        // Notificar a todos los admins
        User::whereHas('roles', fn($q) => $q->where('nombre', 'Administrador'))
            ->get()
            ->each(fn($admin) => $admin->notify(
                new NuevaCuentaNotification($request->nombre, $request->email)
            ));

        return redirect()->route('login')
            ->with('status', 'Cuenta creada. Un administrador asignara tu rol para que puedas ingresar.');
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
