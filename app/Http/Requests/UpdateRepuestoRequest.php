<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRepuestoRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->can('gestionarRepuestos', \App\Policies\InventarioPolicy::class); }

    public function rules(): array
    {
        $repuesto = $this->route('repuesto');
        return [
            'proveedor_id'  => ['nullable', 'exists:proveedores,id'],
            'nombre'        => ['required', 'string', 'max:100'],
            'codigo'        => ['required', 'string', 'max:50', Rule::unique('repuestos', 'codigo')->ignore($repuesto->id)],
            'descripcion'   => ['nullable', 'string', 'max:1000'],
            'imagen'        => ['nullable', 'image', 'max:2048'],
            'precio_compra' => ['required', 'numeric', 'min:0'],
            'precio_venta'  => ['required', 'numeric', 'min:0', 'gte:precio_compra'],
            'activo'        => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'precio_compra.required' => 'El precio de compra es obligatorio.',
            'precio_venta.gte'       => 'El precio de venta debe ser mayor o igual al precio de compra.',
        ];
    }
}
