<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\ConsultaRepuesto;
use App\Models\Repuesto;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConsultaController extends Controller
{
    public function repuestoForm(Repuesto $repuesto): View
    {
        $repuesto->load('inventarios');

        $cliente  = $this->clienteActual();
        $persona  = auth()->user()->persona;
        $stockTotal = $repuesto->inventarios->sum('stock');

        return view('cliente.consultas.repuesto', compact('repuesto', 'persona', 'stockTotal'));
    }

    public function repuestoStore(Request $request, Repuesto $repuesto): RedirectResponse
    {
        $request->validate([
            'cantidad' => 'required|integer|min:1|max:999',
            'telefono' => 'nullable|string|max:30',
            'notas'    => 'nullable|string|max:500',
        ]);

        $user    = auth()->user();
        $persona = $user->persona;
        $cliente = $this->clienteActual();

        ConsultaRepuesto::create([
            'repuesto_id' => $repuesto->id,
            'cliente_id'  => $cliente?->id,
            'nombre'      => $persona->nombre,
            'telefono'    => $request->telefono ?? $persona->telefono,
            'email'       => $persona->email,
            'cantidad'    => $request->cantidad,
            'notas'       => $request->notas,
            'estado'      => 'Pendiente',
        ]);

        // Notificar a administradores por correo
        User::whereHas('roles', fn($q) => $q->whereIn('nombre', ['Admin', 'Bodega']))
            ->get()
            ->each(fn($admin) => $admin->notify(
                new \App\Notifications\NuevaConsultaRepuestoNotification($repuesto, $persona->nombre, $request->cantidad)
            ));

        return redirect()
            ->route('cliente.consultas.enviada', ['repuesto' => $repuesto->id])
            ->with('success', 'Tu consulta fue enviada. Te contactaremos pronto.');
    }

    public function enviada(Request $request): View
    {
        $repuesto = Repuesto::find($request->query('repuesto'));
        return view('cliente.consultas.enviada', compact('repuesto'));
    }

    private function clienteActual(): ?\App\Models\Cliente
    {
        return \App\Models\Cliente::where('persona_id', auth()->user()->persona_id)->first();
    }
}
