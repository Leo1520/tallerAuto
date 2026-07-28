<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrdenServicioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('orden'));
    }

    public function rules(): array
    {
        return [
            'sucursal_id'            => ['nullable', 'exists:sucursales,id'],
            'mecanico_id'            => ['nullable', 'exists:mecanicos,id'],
            'prioridad'              => ['required', 'in:Baja,Media,Alta,Urgente'],
            'fecha_entrega_estimada' => ['nullable', 'date'],
            'descuento'              => ['nullable', 'numeric', 'min:0'],
            'observaciones'          => ['nullable', 'string', 'max:1000'],
        ];
    }
}
