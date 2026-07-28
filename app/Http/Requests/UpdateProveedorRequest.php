<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProveedorRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->can('gestionarRepuestos', \App\Policies\InventarioPolicy::class); }

    public function rules(): array
    {
        $proveedor = $this->route('proveedor');
        return [
            'nombre'    => ['required', 'string', 'max:100', Rule::unique('proveedores', 'nombre')->ignore($proveedor->id)],
            'telefono'  => ['nullable', 'string', 'max:20'],
            'email'     => ['nullable', 'email', 'max:100'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'ciudad'    => ['nullable', 'string', 'max:50'],
            'nit'       => ['nullable', 'string', 'max:30', Rule::unique('proveedores', 'nit')->ignore($proveedor->id)],
            'activo'    => ['boolean'],
        ];
    }
}
