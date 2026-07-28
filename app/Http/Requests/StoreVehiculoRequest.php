<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehiculoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Vehiculo::class);
    }

    public function rules(): array
    {
        return [
            'cliente_id'  => ['required', 'exists:clientes,id'],
            'modelo_id'   => ['required', 'exists:modelos,id'],
            'placa'       => ['required', 'string', 'max:10', 'unique:vehiculos,placa'],
            'vin'         => ['required', 'string', 'size:17', 'unique:vehiculos,vin'],
            'ano'         => ['required', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'color'       => ['nullable', 'string', 'max:30'],
            'kilometraje' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'cliente_id.required'  => 'Selecciona un cliente.',
            'cliente_id.exists'    => 'El cliente seleccionado no existe.',
            'modelo_id.required'   => 'Selecciona un modelo.',
            'placa.required'       => 'La placa es obligatoria.',
            'placa.unique'         => 'Ya existe un vehículo con esa placa.',
            'vin.required'         => 'El VIN es obligatorio.',
            'vin.size'             => 'El VIN debe tener exactamente 17 caracteres.',
            'vin.unique'           => 'Ya existe un vehículo con ese VIN.',
            'ano.required'         => 'El año es obligatorio.',
            'ano.min'              => 'El año no puede ser anterior a 1900.',
        ];
    }
}
