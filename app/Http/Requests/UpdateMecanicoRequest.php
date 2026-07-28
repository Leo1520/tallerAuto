<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMecanicoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->hasPermission('mecanicos.editar');
    }

    public function rules(): array
    {
        $mecanico = $this->route('mecanico');

        return [
            'nombre'          => 'required|string|max:100',
            'telefono'        => 'nullable|string|max:20',
            'email'           => 'nullable|email|max:100|unique:persona,email,' . $mecanico->persona_id,
            'cedula'          => 'required|string|max:20|unique:mecanicos,cedula,' . $mecanico->id,
            'sucursal_id'     => 'required|exists:sucursales,id',
            'especialidad_id' => 'required|exists:especialidades,id',
            'fecha_ingreso'   => 'required|date',
            'salario'         => 'required|numeric|min:0',
            'activo'          => 'nullable|boolean',
        ];
    }
}
