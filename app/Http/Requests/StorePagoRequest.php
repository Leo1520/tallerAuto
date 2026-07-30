<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePagoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('pagos.crear') || $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'orden_id'        => ['required', 'exists:ordenes_servicio,id'],
            'metodo_pago_id'  => ['required', 'exists:metodos_pago,id'],
            'monto'           => ['required', 'numeric', 'min:0.01'],
            'referencia'      => ['nullable', 'string', 'max:100'],
            'observaciones'   => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'orden_id.required'       => 'Selecciona la orden de servicio.',
            'metodo_pago_id.required' => 'Selecciona el método de pago.',
            'monto.min'               => 'El monto debe ser mayor a cero.',
        ];
    }
}
