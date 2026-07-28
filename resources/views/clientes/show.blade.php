@extends('layouts.app')

@section('title', $cliente->persona->nombre)
@section('page-title', $cliente->persona->nombre)

@section('header-actions')
    <a href="{{ route('clientes.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 transition-colors">
        <i class="bi bi-arrow-left" style="font-size:14px;"></i> Volver
    </a>
    @can('update', $cliente)
    <a href="{{ route('clientes.edit', $cliente) }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 transition-colors">
        <i class="bi bi-pencil" style="font-size:13px;"></i> Editar
    </a>
    @endcan
    @can('create', App\Models\Vehiculo::class)
    <a href="{{ route('vehiculos.create', ['cliente_id' => $cliente->id]) }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-colors"
       style="background:#D71920;" onmouseover="this.style.background='#b81218'" onmouseout="this.style.background='#D71920'">
        <span class="relative inline-flex items-center" style="font-size:15px;">
            <i class="bi bi-car-front-fill"></i>
            <i class="bi bi-plus-lg" style="font-size:9px; font-weight:900; position:absolute; top:-4px; right:-5px;"></i>
        </span> Registrar vehículo
    </a>
    @endcan
@endsection

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Datos del cliente --}}
    <div class="lg:col-span-1 space-y-4">
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
            <div class="flex items-center gap-4 mb-5">
                <div class="w-14 h-14 rounded-full flex items-center justify-center text-xl font-bold text-white flex-shrink-0"
                     style="background:#D71920;">
                    {{ strtoupper(substr($cliente->persona->nombre, 0, 2)) }}
                </div>
                <div>
                    <h2 class="font-semibold text-gray-100 text-lg leading-tight">{{ $cliente->persona->nombre }}</h2>
                    <p class="text-sm text-gray-500">Cliente #{{ $cliente->id }}</p>
                </div>
            </div>

            <dl class="space-y-3 text-sm">
                <div class="flex justify-between gap-2">
                    <dt class="text-gray-500 shrink-0">Email</dt>
                    <dd class="text-gray-200 font-medium text-right truncate">{{ $cliente->persona->email ?? '—' }}</dd>
                </div>
                <div class="flex justify-between gap-2">
                    <dt class="text-gray-500 shrink-0">Teléfono</dt>
                    <dd class="text-gray-200 font-medium">{{ $cliente->persona->telefono ?? '—' }}</dd>
                </div>
                <div class="flex justify-between gap-2">
                    <dt class="text-gray-500 shrink-0">Ciudad</dt>
                    <dd class="text-gray-200 font-medium">{{ $cliente->ciudad ?? '—' }}</dd>
                </div>
                <div class="flex justify-between gap-2">
                    <dt class="text-gray-500 shrink-0">Dirección</dt>
                    <dd class="text-gray-200 font-medium text-right">{{ $cliente->direccion ?? '—' }}</dd>
                </div>
                @if ($cliente->numero_documento)
                <div class="flex justify-between gap-2">
                    <dt class="text-gray-500 shrink-0">{{ $cliente->tipo_documento }}</dt>
                    <dd class="text-gray-200 font-mono font-medium">{{ $cliente->numero_documento }}</dd>
                </div>
                @endif
                <div class="flex justify-between gap-2">
                    <dt class="text-gray-500 shrink-0">Registrado</dt>
                    <dd class="text-gray-200 font-medium">{{ $cliente->created_at->format('d/m/Y') }}</dd>
                </div>
            </dl>
        </div>
    </div>

    {{-- Vehículos del cliente --}}
    <div class="lg:col-span-2 space-y-4">
        <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-700 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-100">
                    Vehículos
                    <span class="text-gray-500 font-normal text-sm">({{ $cliente->vehiculos->count() }})</span>
                </h3>
            </div>

            @if ($cliente->vehiculos->isEmpty())
                <div class="py-12 text-center">
                    <i class="bi bi-car-front text-gray-600" style="font-size:40px;"></i>
                    <p class="mt-3 text-sm text-gray-500">Este cliente no tiene vehículos registrados.</p>
                    @can('create', App\Models\Vehiculo::class)
                    <a href="{{ route('vehiculos.create', ['cliente_id' => $cliente->id]) }}"
                       class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white"
                       style="background:#D71920;">
                        <i class="bi bi-plus-lg"></i> Registrar vehículo
                    </a>
                    @endcan
                </div>
            @else
                <div class="divide-y divide-gray-700/50">
                    @foreach ($cliente->vehiculos as $vehiculo)
                    <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-700/20 transition-colors">
                        <div class="flex items-center gap-4 min-w-0">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                                 style="background:rgba(215,25,32,.15);">
                                <i class="bi bi-car-front-fill" style="color:#D71920; font-size:16px;"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-100 truncate">
                                    {{ $vehiculo->modelo->marca->nombre }} {{ $vehiculo->modelo->nombre }}
                                    <span class="font-normal text-gray-500">({{ $vehiculo->ano }})</span>
                                </p>
                                <p class="text-xs text-gray-500">
                                    Placa: <span class="font-mono font-medium text-gray-400">{{ $vehiculo->placa }}</span>
                                    @if ($vehiculo->color) · {{ $vehiculo->color }} @endif
                                    · {{ number_format($vehiculo->kilometraje) }} km
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0 ml-3">
                            @if (! $vehiculo->activo)
                                <span class="px-2 py-0.5 text-xs rounded-full bg-red-900/40 text-red-400 border border-red-800">Inactivo</span>
                            @endif
                            <a href="{{ route('vehiculos.show', $vehiculo) }}"
                               class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors border border-gray-600">
                                <i class="bi bi-eye" style="font-size:11px;"></i> Ver detalle
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
