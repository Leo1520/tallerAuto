<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CambiarEstadoOrdenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('cambiarEstado', $this->route('orden'));
    }

    public function rules(): array
    {
        return [
            'estado'       => ['required', 'in:Recibido,En diagnóstico,En reparación,Esperando repuestos,Listo,Entregado,Cancelado'],
            'observaciones'=> ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'estado.required' => 'Selecciona el nuevo estado.',
            'estado.in'       => 'Estado no válido.',
        ];
    }
}
