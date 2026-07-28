<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrdenServicioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\OrdenServicio::class);
    }

    public function rules(): array
    {
        return [
            'vehiculo_id'            => ['required', 'exists:vehiculos,id'],
            'sucursal_id'            => ['nullable', 'exists:sucursales,id'],
            'mecanico_id'            => ['nullable', 'exists:mecanicos,id'],
            'prioridad'              => ['required', 'in:Baja,Media,Alta,Urgente'],
            'fecha_entrega_estimada' => ['nullable', 'date', 'after:now'],
            'descuento'              => ['nullable', 'numeric', 'min:0'],
            'observaciones'          => ['nullable', 'string', 'max:1000'],
            'servicios'              => ['required', 'array', 'min:1'],
            'servicios.*.servicio_id'     => ['required', 'exists:servicios,id'],
            'servicios.*.cantidad'        => ['required', 'integer', 'min:1'],
            'servicios.*.precio_unitario' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'vehiculo_id.required'    => 'Selecciona un vehículo.',
            'servicios.required'      => 'Agrega al menos un servicio.',
            'servicios.min'           => 'Agrega al menos un servicio.',
            'servicios.*.servicio_id.required' => 'Selecciona un servicio válido.',
        ];
    }
}
