<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerificationController extends Controller
{
    // Página "revisa tu correo" — pública, sin login
    public function activacion(Request $request): View
    {
        return view('auth.activacion', [
            'email' => $request->query('email', ''),
        ]);
    }

    // Pantalla de reenvío (requiere auth, para quien ya inició sesión sin verificar)
    public function notice(Request $request): View|RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard'));
        }

        return view('auth.verify-email', [
            'email' => $request->user()->email,
        ]);
    }

    // Activar cuenta — ruta pública, firma validada manualmente
    public function verify(Request $request, string $id, string $hash): RedirectResponse
    {
        $user = User::find($id);

        // Si el usuario no existe, redirigir al login con mensaje genérico
        if (! $user) {
            return redirect()->route('login')
                ->with('status', 'El enlace de activación no es válido. Intenta registrarte de nuevo.');
        }

        // Si ya está verificado, redirigir directo al login — sin error
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login', ['email' => $user->email])
                ->with('status', 'Tu cuenta ya estaba activa. Ingresa tu contraseña.');
        }

        // Validar firma de la URL
        if (! $request->hasValidSignature()) {
            return redirect()->route('login')
                ->with('error', 'El enlace de activación expiró o no es válido. Solicita uno nuevo al iniciar sesión.');
        }

        // Validar hash del email
        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return redirect()->route('login')
                ->with('error', 'El enlace de activación no es válido.');
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        // Garantizar rol Cliente si aún no tiene ninguno
        if ($user->roles()->count() === 0) {
            $rolCliente = Role::where('nombre', 'Cliente')->first();
            if ($rolCliente) {
                $user->roles()->attach($rolCliente->id);
            }
        }

        return redirect()->route('login', ['email' => $user->email])
            ->with('status', '¡Cuenta activada! Ingresa tu contraseña para continuar.');
    }

    public function resend(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('resent', true);
    }
}
