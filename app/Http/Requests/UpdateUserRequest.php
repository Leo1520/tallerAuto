<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        $usuario = $this->route('usuario');

        return [
            'nombre'   => 'required|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'email'    => [
                'required', 'email', 'max:100',
                'unique:persona,email,' . $usuario->persona_id,
                'unique:users,email,' . $usuario->id,
            ],
            'rol_id'   => 'nullable|exists:roles,id',
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Este correo ya está en uso por otro usuario.',
        ];
    }
}
