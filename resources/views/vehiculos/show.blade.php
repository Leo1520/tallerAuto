@extends('layouts.app')

@section('title', $vehiculo->placa)
@section('page-title', $vehiculo->modelo->marca->nombre . ' ' . $vehiculo->modelo->nombre . ' — ' . $vehiculo->placa)

@section('header-actions')
    <a href="{{ route('vehiculos.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 transition-colors">
        <i class="bi bi-arrow-left" style="font-size:14px;"></i> Volver
    </a>
    @can('update', $vehiculo)
    <a href="{{ route('vehiculos.edit', $vehiculo) }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 transition-colors">
        <i class="bi bi-pencil" style="font-size:13px;"></i> Editar
    </a>
    @endcan
@endsection

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Ficha del vehículo --}}
    <div class="space-y-4">
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background:rgba(215,25,32,.15);">
                    <i class="bi bi-car-front-fill" style="color:#D71920; font-size:22px;"></i>
                </div>
                <div>
                    <h2 class="font-bold text-gray-100 text-lg leading-tight">{{ $vehiculo->placa }}</h2>
                    <span class="text-xs px-2 py-0.5 rounded-full
                        {{ $vehiculo->activo
                            ? 'bg-green-900/40 text-green-400 border border-green-800'
                            : 'bg-red-900/40 text-red-400 border border-red-800' }}">
                        {{ $vehiculo->activo ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>
            </div>

            <dl class="space-y-3 text-sm">
                <div class="flex justify-between gap-2">
                    <dt class="text-gray-500">Marca</dt>
                    <dd class="font-medium text-gray-200">{{ $vehiculo->modelo->marca->nombre }}</dd>
                </div>
                <div class="flex justify-between gap-2">
                    <dt class="text-gray-500">Modelo</dt>
                    <dd class="font-medium text-gray-200">{{ $vehiculo->modelo->nombre }}</dd>
                </div>
                <div class="flex justify-between gap-2">
                    <dt class="text-gray-500">Año</dt>
                    <dd class="font-medium text-gray-200">{{ $vehiculo->ano }}</dd>
                </div>
                <div class="flex justify-between gap-2">
                    <dt class="text-gray-500">Color</dt>
                    <dd class="font-medium text-gray-200">{{ $vehiculo->color ?? '—' }}</dd>
                </div>
                <div class="flex justify-between gap-2">
                    <dt class="text-gray-500">Kilometraje</dt>
                    <dd class="font-medium text-gray-200">{{ number_format($vehiculo->kilometraje) }} km</dd>
                </div>
                <div class="flex justify-between gap-2">
                    <dt class="text-gray-500">VIN</dt>
                    <dd class="font-mono text-xs text-gray-400">{{ $vehiculo->vin }}</dd>
                </div>
            </dl>
        </div>

        {{-- Propietario --}}
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Propietario</p>
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold text-white flex-shrink-0"
                     style="background:#D71920;">
                    {{ strtoupper(substr($vehiculo->cliente->persona->nombre, 0, 2)) }}
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-100">{{ $vehiculo->cliente->persona->nombre }}</p>
                    <p class="text-xs text-gray-500">{{ $vehiculo->cliente->persona->telefono ?? $vehiculo->cliente->persona->email ?? '—' }}</p>
                </div>
            </div>
            <a href="{{ route('clientes.show', $vehiculo->cliente) }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors border border-gray-600">
                <i class="bi bi-person" style="font-size:11px;"></i> Ver perfil del cliente
            </a>
        </div>
    </div>

    {{-- Historial --}}
    <div class="lg:col-span-2 space-y-4">

        {{-- Mantenimientos preventivos --}}
        @if ($vehiculo->mantenimientos->isNotEmpty())
        <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-700">
                <h3 class="text-base font-semibold text-gray-100">Mantenimientos preventivos</h3>
            </div>
            <div class="divide-y divide-gray-700/50">
                @foreach ($vehiculo->mantenimientos as $mant)
                @php
                    $mantClass = match($mant->estado) {
                        'Vencido' => 'bg-red-900/40 text-red-400 border border-red-800',
                        'Proximo' => 'bg-yellow-900/40 text-yellow-400 border border-yellow-800',
                        default   => 'bg-green-900/40 text-green-400 border border-green-800',
                    };
                @endphp
                <div class="px-6 py-3 flex items-center justify-between text-sm">
                    <div>
                        <p class="font-medium text-gray-200">{{ $mant->tipo }}</p>
                        <p class="text-xs text-gray-500">
                            Próximo: {{ $mant->proxima_fecha?->format('d/m/Y') ?? '—' }}
                            @if ($mant->kilometraje_proximo) · {{ number_format($mant->kilometraje_proximo) }} km @endif
                        </p>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $mantClass }}">
                        {{ $mant->estado }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Órdenes de servicio --}}
        <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-700">
                <h3 class="text-base font-semibold text-gray-100">
                    Historial de servicio
                    <span class="text-gray-500 font-normal text-sm">(últimas 10)</span>
                </h3>
            </div>
            @if ($vehiculo->ordenes->isEmpty())
                <div class="py-12 text-center">
                    <i class="bi bi-clipboard2-x text-gray-600" style="font-size:40px;"></i>
                    <p class="mt-3 text-sm text-gray-500">Sin órdenes de servicio registradas.</p>
                </div>
            @else
                @php
                $colores = [
                    'Recibido'            => 'bg-blue-900/40 text-blue-400 border border-blue-800',
                    'En diagnóstico'      => 'bg-yellow-900/40 text-yellow-400 border border-yellow-800',
                    'En reparación'       => 'bg-orange-900/40 text-orange-400 border border-orange-800',
                    'Esperando repuestos' => 'bg-purple-900/40 text-purple-400 border border-purple-800',
                    'Listo'               => 'bg-green-900/40 text-green-400 border border-green-800',
                    'Entregado'           => 'bg-gray-700 text-gray-400 border border-gray-600',
                    'Cancelado'           => 'bg-red-900/40 text-red-400 border border-red-800',
                ];
                @endphp
                <div class="divide-y divide-gray-700/50">
                    @foreach ($vehiculo->ordenes as $orden)
                    <div class="px-6 py-4 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-mono font-semibold text-gray-100">{{ $orden->numero }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $orden->fecha_ingreso->format('d/m/Y') }}
                                @if ($orden->mecanico) · {{ $orden->mecanico->persona->nombre }} @endif
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $colores[$orden->estado] ?? 'bg-gray-700 text-gray-400' }}">
                                {{ $orden->estado }}
                            </span>
                            <span class="text-sm font-semibold text-gray-100">Bs {{ number_format($orden->total, 2) }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>

@endsection
