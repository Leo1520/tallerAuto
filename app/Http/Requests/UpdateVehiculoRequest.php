<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVehiculoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('vehiculo'));
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'placa' => strtoupper(trim($this->placa ?? '')),
            'vin'   => strtoupper(trim($this->vin ?? '')) ?: null,
            'color' => $this->color ? ucfirst(mb_strtolower(trim($this->color))) : null,
        ]);
    }

    public function rules(): array
    {
        $vehiculo = $this->route('vehiculo');

        return [
            'cliente_id'  => ['required', 'exists:clientes,id'],
            'modelo_id'   => ['required', 'exists:modelos,id'],
            'placa'       => ['required', 'string', 'max:10', Rule::unique('vehiculos', 'placa')->ignore($vehiculo->id)],
            'vin'         => ['nullable', 'string', 'size:17', Rule::unique('vehiculos', 'vin')->ignore($vehiculo->id)],
            'ano'         => ['required', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'color'       => ['nullable', 'string', 'max:30'],
            'combustible' => ['nullable', 'in:Gasolina,Diesel,Gas Natural,Eléctrico,Híbrido'],
            'kilometraje' => ['nullable', 'integer', 'min:0'],
            'activo'      => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'placa.unique' => 'Ya existe un vehículo con esa placa.',
            'vin.size'     => 'El VIN debe tener exactamente 17 caracteres.',
            'vin.unique'   => 'Ya existe un vehículo con ese VIN.',
        ];
    }
}
