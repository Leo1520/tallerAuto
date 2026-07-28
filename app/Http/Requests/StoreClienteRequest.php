<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Cliente::class);
    }

    public function rules(): array
    {
        return [
            'nombre'           => ['required', 'string', 'max:100'],
            'telefono'         => ['nullable', 'string', 'max:20'],
            'email'            => ['nullable', 'email', 'max:100', 'unique:persona,email'],
            'direccion'        => ['nullable', 'string', 'max:255'],
            'ciudad'           => ['nullable', 'string', 'max:50'],
            'tipo_documento'   => ['nullable', 'string', 'max:20'],
            'numero_documento' => ['nullable', 'string', 'max:50', 'unique:clientes,numero_documento'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'         => 'El nombre es obligatorio.',
            'email.email'             => 'Ingresa un correo válido.',
            'email.unique'            => 'Ya existe una persona registrada con ese correo.',
            'numero_documento.unique' => 'Ya existe un cliente con ese número de documento.',
        ];
    }
}
