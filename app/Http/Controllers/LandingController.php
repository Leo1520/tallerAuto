<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Mecanico;
use App\Models\OrdenServicio;
use App\Models\Sucursal;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function index(): View
    {
        $sucursales = Sucursal::whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->orderBy('nombre')
            ->get();

        $stats = [
            'ordenes_activas' => OrdenServicio::whereIn('estado', ['pendiente', 'en_proceso'])->count(),
            'mecanicos'       => Mecanico::count(),
            'clientes'        => Cliente::count(),
        ];

        return view('public.landing', compact('sucursales', 'stats'));
    }
}
