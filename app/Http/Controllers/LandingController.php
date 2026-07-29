<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Mecanico;
use App\Models\OrdenServicio;
use App\Models\Repuesto;
use App\Models\Servicio;
use App\Models\Sucursal;
use App\Models\TipoServicio;
use Illuminate\Http\Request;
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

    public function tienda(Request $request): View
    {
        $tab   = $request->query('tab', 'servicios');
        $buscar = $request->query('q', '');

        $tiposServicio = TipoServicio::where('activo', true)->orderBy('nombre')->get();

        $servicios = Servicio::where('activo', true)
            ->with('tipoServicio')
            ->when($buscar, fn($q) => $q->where('nombre', 'like', "%{$buscar}%")
                ->orWhere('descripcion', 'like', "%{$buscar}%"))
            ->when($request->query('tipo'), fn($q) => $q->where('tipo_servicio_id', $request->query('tipo')))
            ->orderBy('nombre')
            ->get();

        $repuestos = Repuesto::where('activo', true)
            ->with(['inventarios'])
            ->when($buscar, fn($q) => $q->where('nombre', 'like', "%{$buscar}%")
                ->orWhere('codigo', 'like', "%{$buscar}%"))
            ->orderBy('nombre')
            ->get();

        return view('public.tienda', compact('tab', 'buscar', 'servicios', 'repuestos', 'tiposServicio'));
    }
}
