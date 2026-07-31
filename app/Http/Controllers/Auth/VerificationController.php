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
        // SECURITY: Validar firma ANTES de cualquier lookup en DB.
        // Sin esto, un atacante puede enumerar emails enviando hashes falsos
        // y observando si el redirect incluye el email real del usuario.
        if (! $request->hasValidSignature()) {
            return redirect()->route('login')
                ->with('status', 'El enlace de activación no es válido o expiró. Solicita uno nuevo al iniciar sesión.');
        }

        $user = User::find($id);

        // Respuesta idéntica para: usuario inexistente, hash de email incorrecto.
        // No revelar si el ID existe en la base de datos.
        if (! $user || ! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return redirect()->route('login')
                ->with('status', 'El enlace de activación no es válido o expiró. Solicita uno nuevo al iniciar sesión.');
        }

        // Ya verificado — no incluir el email en la URL de redirect.
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')
                ->with('status', 'Tu cuenta ya está activa. Ingresa tu contraseña para continuar.');
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

        // No incluir el email en la URL — el usuario ya lo conoce.
        return redirect()->route('login')
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
