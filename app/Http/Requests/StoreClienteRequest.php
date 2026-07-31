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
            'telefono'         => ['required', 'string', 'max:20'],
            'email'            => ['nullable', 'email', 'max:100', 'unique:persona,email'],
            'direccion'        => ['nullable', 'string', 'max:255'],
            'ciudad'           => ['nullable', 'string', 'max:100'],
            'tipo_documento'   => ['required', 'string', 'in:CI,NIT,Pasaporte,Otro'],
            'numero_documento' => ['required', 'string', 'max:50', 'unique:clientes,numero_documento'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'           => 'El nombre es obligatorio.',
            'telefono.required'         => 'El teléfono es obligatorio.',
            'email.email'               => 'Ingresa un correo válido.',
            'email.unique'              => 'Ya existe una persona registrada con ese correo.',
            'tipo_documento.required'   => 'Selecciona el tipo de documento.',
            'tipo_documento.in'         => 'Tipo de documento no válido.',
            'numero_documento.required' => 'El número de documento es obligatorio.',
            'numero_documento.unique'   => 'Ya existe un cliente con ese número de documento.',
        ];
    }
}
