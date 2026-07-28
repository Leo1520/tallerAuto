<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProveedorRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->can('gestionarRepuestos', \App\Policies\InventarioPolicy::class); }

    public function rules(): array
    {
        return [
            'nombre'    => ['required', 'string', 'max:100', 'unique:proveedores,nombre'],
            'telefono'  => ['nullable', 'string', 'max:20'],
            'email'     => ['nullable', 'email', 'max:100'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'ciudad'    => ['nullable', 'string', 'max:50'],
            'nit'       => ['nullable', 'string', 'max:30', 'unique:proveedores,nit'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del proveedor es obligatorio.',
            'nombre.unique'   => 'Ya existe un proveedor con ese nombre.',
            'nit.unique'      => 'Ya existe un proveedor con ese NIT.',
        ];
    }
}
