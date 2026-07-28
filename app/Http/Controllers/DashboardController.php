<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\OrdenServicio;
use App\Models\Vehiculo;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'clientes'        => Cliente::count(),
            'vehiculos'       => Vehiculo::count(),
            'ordenes_activas' => OrdenServicio::whereNotIn('estado', ['Entregado', 'Cancelado'])->count(),
            'ordenes_hoy'     => OrdenServicio::whereDate('fecha_ingreso', today())->count(),
        ];

        $ordenes_recientes = OrdenServicio::with(['vehiculo.cliente.persona', 'mecanico.persona'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'ordenes_recientes'));
    }
}
