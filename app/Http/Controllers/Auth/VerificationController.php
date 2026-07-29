<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerificationController extends Controller
{
    public function notice(Request $request): View|RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard'));
        }

        return view('auth.verify-email', [
            'email' => $request->user()->email,
        ]);
    }

    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard')->with('status', 'Tu correo ya fue verificado.');
        }

        $request->fulfill();

        $user      = $request->user();
        $roleNames = $user->roles->pluck('nombre')->toArray();
        $staffRoles = ['Admin','Administrador','Mecánico','Mecanico','Recepcionista','Cajero','Inventario','Supervisor'];

        foreach ($staffRoles as $role) {
            if (in_array($role, $roleNames)) {
                return redirect()->route('dashboard')
                    ->with('status', '¡Correo verificado! Bienvenido a Taller Pro.');
            }
        }

        if (in_array('Cliente', $roleNames)) {
            return redirect()->route('cliente.inicio')
                ->with('status', '¡Correo verificado! Bienvenido.');
        }

        return redirect()->route('pending');
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
