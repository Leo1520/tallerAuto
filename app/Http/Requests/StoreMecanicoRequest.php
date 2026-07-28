<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMecanicoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->hasPermission('mecanicos.crear');
    }

    public function rules(): array
    {
        return [
            'nombre'           => 'required|string|max:100',
            'telefono'         => 'nullable|string|max:20',
            'email'            => 'nullable|email|max:100|unique:persona,email',
            'cedula'           => 'required|string|max:20|unique:mecanicos,cedula',
            'sucursal_id'      => 'required|exists:sucursales,id',
            'especialidad_id'  => 'required|exists:especialidades,id',
            'fecha_ingreso'    => 'required|date',
            'salario'          => 'required|numeric|min:0',
            'crear_usuario'    => 'nullable|boolean',
            'user_email'       => 'required_if:crear_usuario,1|nullable|email|max:100|unique:users,email',
            'user_password'    => 'required_if:crear_usuario,1|nullable|string|min:8|confirmed',
            'rol_id'           => 'nullable|exists:roles,id',
        ];
    }

    public function messages(): array
    {
        return [
            'cedula.unique'        => 'Ya existe un mecánico con esta cédula.',
            'user_email.required_if' => 'El correo es obligatorio para crear cuenta de usuario.',
            'user_password.required_if' => 'La contraseña es obligatoria para crear cuenta de usuario.',
        ];
    }
}
