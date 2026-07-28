@extends('layouts.app')

@section('title', $vehiculo->placa)
@section('page-title', $vehiculo->modelo->marca->nombre . ' ' . $vehiculo->modelo->nombre . ' — ' . $vehiculo->placa)

@section('header-actions')
    @can('update', $vehiculo)
        <a href="{{ route('vehiculos.edit', $vehiculo) }}"
           class="inline-flex items-center gap-2 bg-gray-800 hover:bg-gray-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
            Editar
        </a>
    @endcan
@endsection

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Ficha del vehículo --}}
    <div class="space-y-4">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="p-3 bg-gray-100 rounded-xl">
                    <svg class="w-7 h-7 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-gray-900 text-lg leading-tight">{{ $vehiculo->placa }}</h2>
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $vehiculo->activo ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $vehiculo->activo ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>
            </div>

            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Marca</dt>
                    <dd class="font-medium text-gray-900">{{ $vehiculo->modelo->marca->nombre }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Modelo</dt>
                    <dd class="font-medium text-gray-900">{{ $vehiculo->modelo->nombre }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Año</dt>
                    <dd class="font-medium text-gray-900">{{ $vehiculo->ano }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Color</dt>
                    <dd class="font-medium text-gray-900">{{ $vehiculo->color ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Kilometraje</dt>
                    <dd class="font-medium text-gray-900">{{ number_format($vehiculo->kilometraje) }} km</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">VIN</dt>
                    <dd class="font-mono text-xs text-gray-700">{{ $vehiculo->vin }}</dd>
                </div>
            </dl>
        </div>

        {{-- Propietario --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Propietario</p>
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center text-sm font-bold">
                    {{ strtoupper(substr($vehiculo->cliente->persona->nombre, 0, 2)) }}
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900">{{ $vehiculo->cliente->persona->nombre }}</p>
                    <p class="text-xs text-gray-500">{{ $vehiculo->cliente->persona->telefono ?? $vehiculo->cliente->persona->email ?? '—' }}</p>
                </div>
            </div>
            <a href="{{ route('clientes.show', $vehiculo->cliente) }}"
               class="block mt-3 text-xs text-blue-600 hover:underline">Ver perfil del cliente →</a>
        </div>
    </div>

    {{-- Historial --}}
    <div class="lg:col-span-2 space-y-4">

        {{-- Mantenimientos preventivos --}}
        @if ($vehiculo->mantenimientos->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-semibold text-gray-800">Mantenimientos preventivos</h3>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach ($vehiculo->mantenimientos as $mant)
                <div class="px-6 py-3 flex items-center justify-between text-sm">
                    <div>
                        <p class="font-medium text-gray-800">{{ $mant->tipo }}</p>
                        <p class="text-xs text-gray-500">Próximo: {{ $mant->proxima_fecha?->format('d/m/Y') ?? '—' }}
                            @if ($mant->kilometraje_proximo) · {{ number_format($mant->kilometraje_proximo) }} km @endif
                        </p>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                        {{ $mant->estado === 'Vencido' ? 'bg-red-100 text-red-700' : ($mant->estado === 'Proximo' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700') }}">
                        {{ $mant->estado }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Órdenes de servicio --}}
        <div class="bg-white rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-semibold text-gray-800">
                    Historial de servicio <span class="text-gray-400 font-normal text-sm">(últimas 10)</span>
                </h3>
            </div>
            @if ($vehiculo->ordenes->isEmpty())
                <div class="py-10 text-center text-sm text-gray-400">Sin órdenes de servicio registradas.</div>
            @else
                @php
                $colores = [
                    'Recibido' => 'bg-blue-100 text-blue-800', 'En diagnóstico' => 'bg-yellow-100 text-yellow-800',
                    'En reparación' => 'bg-orange-100 text-orange-800', 'Esperando repuestos' => 'bg-purple-100 text-purple-800',
                    'Listo' => 'bg-green-100 text-green-800', 'Entregado' => 'bg-gray-100 text-gray-600',
                    'Cancelado' => 'bg-red-100 text-red-700',
                ];
                @endphp
                <div class="divide-y divide-gray-50">
                    @foreach ($vehiculo->ordenes as $orden)
                    <div class="px-6 py-4 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-mono font-semibold text-gray-900">{{ $orden->numero }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $orden->fecha_ingreso->format('d/m/Y') }}
                                @if ($orden->mecanico) · {{ $orden->mecanico->persona->nombre }} @endif
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $colores[$orden->estado] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $orden->estado }}
                            </span>
                            <span class="text-sm font-semibold text-gray-900">Bs {{ number_format($orden->total, 2) }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>

@endsection
