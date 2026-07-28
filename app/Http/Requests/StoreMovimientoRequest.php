<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMovimientoRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->can('registrarMovimiento', \App\Policies\InventarioPolicy::class); }

    public function rules(): array
    {
        return [
            'repuesto_id' => ['required', 'exists:repuestos,id'],
            'sucursal_id' => ['required', 'exists:sucursales,id'],
            'tipo'        => ['required', 'in:Entrada,Salida,Ajuste'],
            'cantidad'    => ['required', 'integer', 'min:1'],
            'motivo'      => ['nullable', 'string', 'max:100'],
            'referencia'  => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'repuesto_id.required' => 'Selecciona un repuesto.',
            'sucursal_id.required' => 'Selecciona una sucursal.',
            'tipo.required'        => 'Selecciona el tipo de movimiento.',
            'cantidad.min'         => 'La cantidad debe ser al menos 1.',
        ];
    }
}
