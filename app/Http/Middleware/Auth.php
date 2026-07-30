<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as AuthFacade;
use Symfony\Component\HttpFoundation\Response;

class Auth
{
    /**
     * Verifica si el usuario está autenticado.
     * Si no lo está, redirige a /login.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! AuthFacade::check()) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
