@extends('layouts.app')

@section('title', $cliente->persona->nombre)
@section('page-title', $cliente->persona->nombre)

@section('header-actions')
    @can('update', $cliente)
        <a href="{{ route('clientes.edit', $cliente) }}"
           class="inline-flex items-center gap-2 bg-gray-800 hover:bg-gray-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
            Editar
        </a>
    @endcan
    @can('create', App\Models\Vehiculo::class)
        <a href="{{ route('vehiculos.create', ['cliente_id' => $cliente->id]) }}"
           class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Registrar vehículo
        </a>
    @endcan
@endsection

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Datos del cliente --}}
    <div class="lg:col-span-1 space-y-4">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center gap-4 mb-5">
                <div class="w-14 h-14 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center text-xl font-bold">
                    {{ strtoupper(substr($cliente->persona->nombre, 0, 2)) }}
                </div>
                <div>
                    <h2 class="font-semibold text-gray-900 text-lg leading-tight">{{ $cliente->persona->nombre }}</h2>
                    <p class="text-sm text-gray-500">Cliente #{{ $cliente->id }}</p>
                </div>
            </div>

            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Email</dt>
                    <dd class="text-gray-900 font-medium">{{ $cliente->persona->email ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Teléfono</dt>
                    <dd class="text-gray-900 font-medium">{{ $cliente->persona->telefono ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Ciudad</dt>
                    <dd class="text-gray-900 font-medium">{{ $cliente->ciudad ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Dirección</dt>
                    <dd class="text-gray-900 font-medium">{{ $cliente->direccion ?? '—' }}</dd>
                </div>
                @if ($cliente->numero_documento)
                <div class="flex justify-between">
                    <dt class="text-gray-500">{{ $cliente->tipo_documento }}</dt>
                    <dd class="text-gray-900 font-mono font-medium">{{ $cliente->numero_documento }}</dd>
                </div>
                @endif
                <div class="flex justify-between">
                    <dt class="text-gray-500">Registrado</dt>
                    <dd class="text-gray-900 font-medium">{{ $cliente->created_at->format('d/m/Y') }}</dd>
                </div>
            </dl>
        </div>
    </div>

    {{-- Vehículos del cliente --}}
    <div class="lg:col-span-2 space-y-4">
        <div class="bg-white rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-800">
                    Vehículos <span class="text-gray-400 font-normal text-sm">({{ $cliente->vehiculos->count() }})</span>
                </h3>
            </div>

            @if ($cliente->vehiculos->isEmpty())
                <div class="py-10 text-center text-gray-400 text-sm">
                    Este cliente no tiene vehículos registrados.
                </div>
            @else
                <div class="divide-y divide-gray-50">
                    @foreach ($cliente->vehiculos as $vehiculo)
                    <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2.5 bg-gray-100 rounded-lg">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">
                                    {{ $vehiculo->modelo->marca->nombre }} {{ $vehiculo->modelo->nombre }}
                                    <span class="font-normal text-gray-500">({{ $vehiculo->ano }})</span>
                                </p>
                                <p class="text-xs text-gray-500">
                                    Placa: <span class="font-mono font-medium">{{ $vehiculo->placa }}</span>
                                    @if ($vehiculo->color) · {{ $vehiculo->color }} @endif
                                    · {{ number_format($vehiculo->kilometraje) }} km
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            @if (! $vehiculo->activo)
                                <span class="px-2 py-0.5 text-xs bg-red-100 text-red-700 rounded-full">Inactivo</span>
                            @endif
                            <a href="{{ route('vehiculos.show', $vehiculo) }}"
                               class="text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors">
                                Ver detalle
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>

@endsection
