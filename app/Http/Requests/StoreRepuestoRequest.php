<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRepuestoRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->can('gestionarRepuestos', \App\Policies\InventarioPolicy::class); }

    public function rules(): array
    {
        return [
            'proveedor_id'  => ['nullable', 'exists:proveedores,id'],
            'nombre'        => ['required', 'string', 'max:100'],
            'codigo'        => ['required', 'string', 'max:50', 'unique:repuestos,codigo'],
            'descripcion'   => ['nullable', 'string', 'max:1000'],
            'imagen'        => ['nullable', 'image', 'max:2048'],
            'precio_compra' => ['nullable', 'numeric', 'min:0'],
            'precio_venta'  => ['required', 'numeric', 'min:0'],
            // Stock inicial por sucursal
            'stock_inicial'         => ['nullable', 'integer', 'min:0'],
            'sucursal_id_inicial'   => ['nullable', 'exists:sucursales,id'],
            'stock_minimo'          => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'        => 'El nombre es obligatorio.',
            'codigo.required'        => 'El código es obligatorio.',
            'codigo.unique'          => 'Ya existe un repuesto con ese código.',
            'precio_venta.required'  => 'El precio de venta es obligatorio.',
        ];
    }
}
