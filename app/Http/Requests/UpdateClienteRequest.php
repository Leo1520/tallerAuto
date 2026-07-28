<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('cliente'));
    }

    public function rules(): array
    {
        $cliente = $this->route('cliente');

        return [
            'nombre'           => ['required', 'string', 'max:100'],
            'telefono'         => ['nullable', 'string', 'max:20'],
            'email'            => ['nullable', 'email', 'max:100', Rule::unique('persona', 'email')->ignore($cliente->persona_id)],
            'direccion'        => ['nullable', 'string', 'max:255'],
            'ciudad'           => ['nullable', 'string', 'max:50'],
            'tipo_documento'   => ['nullable', 'string', 'max:20'],
            'numero_documento' => ['nullable', 'string', 'max:50', Rule::unique('clientes', 'numero_documento')->ignore($cliente->id)],
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
