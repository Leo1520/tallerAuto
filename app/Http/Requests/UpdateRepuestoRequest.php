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
            'precio_compra' => ['nullable', 'numeric', 'min:0'],
            'precio_venta'  => ['required', 'numeric', 'min:0'],
            'activo'        => ['boolean'],
        ];
    }
}
