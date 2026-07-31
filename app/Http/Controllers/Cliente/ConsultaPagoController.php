<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\ConsultaRepuesto;
use App\Models\User;
use App\Notifications\NuevaConsultaRepuestoNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class ConsultaPagoController extends Controller
{
    public function show(string $token): View
    {
        $consulta = ConsultaRepuesto::where('token', $token)
            ->with(['repuesto', 'cliente.persona'])
            ->firstOrFail();

        abort_if($consulta->estado !== 'Atendida', 404);

        return view('cliente.consultas.pagar', compact('consulta'));
    }

    public function store(Request $request, string $token): RedirectResponse
    {
        $consulta = ConsultaRepuesto::where('token', $token)->firstOrFail();

        abort_if($consulta->estado !== 'Atendida', 404);
        abort_if(in_array($consulta->pago_estado, ['En revisión', 'Confirmado']), 422,
            'Ya enviaste un comprobante. Espera la revisión del equipo.');

        $request->validate([
            'comprobante' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ], [
            'comprobante.required' => 'Debes adjuntar el comprobante de pago.',
            'comprobante.mimes'    => 'Solo se aceptan imágenes (JPG, PNG) o PDF.',
            'comprobante.max'      => 'El archivo no puede superar 5 MB.',
        ]);

        $ruta = $request->file('comprobante')
            ->store("comprobantes_consulta/{$consulta->id}", 'public');

        $consulta->update([
            'comprobante_path' => $ruta,
            'pago_estado'      => 'En revisión',
        ]);

        // Notificar a admins
        $consulta->load('repuesto');
        User::whereHas('roles', fn($q) => $q->whereIn('nombre', ['Admin', 'Bodega']))
            ->get()
            ->each(fn($admin) => $admin->notify(
                new \App\Notifications\ConsultaComprobanteNotification($consulta)
            ));

        return redirect()->route('cliente.consultas.pago.enviado', $token);
    }

    public function enviado(string $token): View
    {
        $consulta = ConsultaRepuesto::where('token', $token)
            ->with('repuesto')
            ->firstOrFail();

        return view('cliente.consultas.pago_enviado', compact('consulta'));
    }
}
