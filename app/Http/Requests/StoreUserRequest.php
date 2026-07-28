<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'nombre'            => 'required|string|max:100',
            'telefono'          => 'nullable|string|max:20',
            'email'             => 'required|email|max:100|unique:persona,email|unique:users,email',
            'password'          => 'required|string|min:8|confirmed',
            'rol_id'            => 'nullable|exists:roles,id',
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique'    => 'Este correo ya está registrado.',
            'password.min'    => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ];
    }
}
