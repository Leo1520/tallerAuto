<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmitirFacturaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('facturas.emitir') || $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'orden_id'     => ['required', 'exists:ordenes_servicio,id'],
            'observaciones'=> ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'orden_id.required' => 'Debes indicar la orden de servicio.',
            'orden_id.exists'   => 'La orden de servicio no existe.',
        ];
    }
}
