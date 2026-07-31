@extends('layouts.app')
@section('title', 'Nuevo Rol')
@section('page-title', 'Nuevo rol')

@section('content')
<div class="max-w-3xl mx-auto">

    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('roles.index') }}"
           class="p-2 text-gray-400 hover:text-gray-200 transition-colors">
            <i class="bi bi-arrow-left" style="font-size:18px;"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-100">Nuevo rol</h1>
            <p class="text-sm text-gray-500">Define el nombre y los módulos a los que tendrá acceso</p>
        </div>
    </div>

    <form method="POST" action="{{ route('roles.store') }}" class="space-y-5">
        @csrf
        @include('roles._form', ['permisosActivos' => old('permissions', [])])

        <div class="flex justify-end gap-3">
            <a href="{{ route('roles.index') }}"
               class="px-4 py-2 text-sm font-medium text-gray-300 hover:text-gray-100 hover:bg-gray-700 rounded-lg transition-colors">
                Cancelar
            </a>
            <button type="submit"
                    class="px-5 py-2 text-sm font-semibold text-white rounded-lg transition-colors btn-taller-red">
                Crear rol
            </button>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>.btn-taller-red{background:#D71920}.btn-taller-red:hover{background:#b81218}</style>
@endpush
