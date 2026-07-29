<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Cliente;
use App\Models\OrdenServicio;
use App\Models\Servicio;
use App\Models\Sucursal;
use App\Models\Vehiculo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PortalController extends Controller
{
    private function clienteActual(): ?Cliente
    {
        $user = Auth::user();
        return Cliente::where('persona_id', $user->persona_id)->first();
    }

    // ─── Inicio / Dashboard ──────────────────────────────────────

    public function inicio(): View
    {
        $cliente = $this->clienteActual();

        $proximaCita = null;
        $ordenesActivas = collect();
        $vehiculos = collect();
        $totalCitas = 0;
        $totalOrdenes = 0;

        if ($cliente) {
            $proximaCita = Cita::where('cliente_id', $cliente->id)
                ->where('fecha', '>=', today())
                ->where('estado', '!=', 'cancelada')
                ->with(['sucursal', 'servicio', 'vehiculo.modelo.marca'])
                ->orderBy('fecha')->orderBy('hora')
                ->first();

            $ordenesActivas = OrdenServicio::whereHas('vehiculo', fn($q) => $q->where('cliente_id', $cliente->id))
                ->whereIn('estado', ['Recibido', 'Diagnóstico', 'En proceso', 'Esperando repuestos'])
                ->with(['vehiculo.modelo.marca', 'sucursal'])
                ->orderByDesc('fecha_ingreso')
                ->take(3)->get();

            $vehiculos = Vehiculo::where('cliente_id', $cliente->id)
                ->with('modelo.marca')
                ->where('activo', true)
                ->take(3)->get();

            $totalCitas   = Cita::where('cliente_id', $cliente->id)->count();
            $totalOrdenes = OrdenServicio::whereHas('vehiculo', fn($q) => $q->where('cliente_id', $cliente->id))->count();
        }

        return view('cliente.inicio', compact('cliente', 'proximaCita', 'ordenesActivas', 'vehiculos', 'totalCitas', 'totalOrdenes'));
    }

    // ─── Citas ───────────────────────────────────────────────────

    public function citasIndex(): View
    {
        $cliente = $this->clienteActual();
        $citas = $cliente
            ? Cita::where('cliente_id', $cliente->id)
                ->with(['sucursal', 'servicio', 'vehiculo.modelo.marca'])
                ->orderByDesc('fecha')->orderByDesc('hora')
                ->paginate(10)
            : collect();

        return view('cliente.citas.index', compact('cliente', 'citas'));
    }

    public function citasCreate(Request $request): View
    {
        $cliente    = $this->clienteActual();
        $sucursales = Sucursal::orderBy('nombre')->get();
        $servicios  = Servicio::where('activo', true)->orderBy('nombre')->get();
        $vehiculos  = $cliente
            ? Vehiculo::where('cliente_id', $cliente->id)->where('activo', true)->with('modelo.marca')->get()
            : collect();
        $servicioId = $request->query('servicio_id');

        return view('cliente.citas.create', compact('cliente', 'sucursales', 'servicios', 'vehiculos', 'servicioId'));
    }

    public function citasStore(Request $request): RedirectResponse
    {
        $cliente = $this->clienteActual();

        if (! $cliente) {
            return back()->with('error', 'No tienes un perfil de cliente activo.');
        }

        $data = $request->validate([
            'sucursal_id' => 'required|exists:sucursales,id',
            'vehiculo_id' => 'nullable|exists:vehiculos,id',
            'servicio_id' => 'nullable|exists:servicios,id',
            'fecha'       => 'required|date|after_or_equal:today',
            'hora'        => 'required',
            'notas'       => 'nullable|string|max:500',
        ], [
            'fecha.after_or_equal' => 'La fecha debe ser hoy o en el futuro.',
        ]);

        $data['cliente_id'] = $cliente->id;
        $data['estado']     = 'pendiente';

        Cita::create($data);

        return redirect()->route('cliente.citas.index')
            ->with('success', 'Cita agendada correctamente. Te confirmaremos por correo.');
    }

    public function citasCancel(Cita $cita): RedirectResponse
    {
        $cliente = $this->clienteActual();

        if (! $cliente || $cita->cliente_id !== $cliente->id) {
            abort(403);
        }

        if (! in_array($cita->estado, ['pendiente', 'confirmada'])) {
            return back()->with('error', 'Esta cita no puede cancelarse.');
        }

        $cita->update(['estado' => 'cancelada']);

        return back()->with('success', 'Cita cancelada.');
    }

    // ─── Vehículos ───────────────────────────────────────────────

    public function vehiculosIndex(): View
    {
        $cliente  = $this->clienteActual();
        $vehiculos = $cliente
            ? Vehiculo::where('cliente_id', $cliente->id)
                ->with(['modelo.marca', 'ordenes' => fn($q) => $q->latest()->take(1)])
                ->get()
            : collect();

        return view('cliente.vehiculos.index', compact('cliente', 'vehiculos'));
    }

    // ─── Órdenes ─────────────────────────────────────────────────

    public function ordenesIndex(): View
    {
        $cliente = $this->clienteActual();
        $ordenes = $cliente
            ? OrdenServicio::whereHas('vehiculo', fn($q) => $q->where('cliente_id', $cliente->id))
                ->with(['vehiculo.modelo.marca', 'sucursal', 'mecanico.persona'])
                ->orderByDesc('fecha_ingreso')
                ->paginate(10)
            : collect();

        return view('cliente.ordenes.index', compact('cliente', 'ordenes'));
    }

    public function ordenShow(OrdenServicio $orden): View
    {
        $cliente = $this->clienteActual();

        if (! $cliente || $orden->vehiculo->cliente_id !== $cliente->id) {
            abort(403);
        }

        $orden->load(['vehiculo.modelo.marca', 'sucursal', 'mecanico.persona', 'detallesServicios.servicio', 'detallesRepuestos.repuesto']);

        return view('cliente.ordenes.show', compact('orden', 'cliente'));
    }
}
