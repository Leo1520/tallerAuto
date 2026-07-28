@extends('layouts.app')
@section('title', $mecanico->persona->nombre)
@section('page-title', $mecanico->persona->nombre)

@section('header-actions')
    <a href="{{ route('mecanicos.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 transition-colors">
        <i class="bi bi-arrow-left" style="font-size:14px;"></i> Volver
    </a>
    @if(auth()->user()->isAdmin() || auth()->user()->hasPermission('mecanicos.editar'))
    <a href="{{ route('mecanicos.edit', $mecanico) }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 transition-colors">
        <i class="bi bi-pencil" style="font-size:13px;"></i> Editar
    </a>
    @endif
@endsection

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Columna izquierda --}}
    <div class="space-y-4">

        {{-- Tarjeta de perfil --}}
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
            <div class="flex flex-col items-center text-center mb-6">
                <div class="w-20 h-20 rounded-full flex items-center justify-center mb-3 text-2xl font-bold text-white"
                     style="background:#D71920;">
                    {{ strtoupper(substr($mecanico->persona->nombre, 0, 2)) }}
                </div>
                <h2 class="text-lg font-bold text-gray-100">{{ $mecanico->persona->nombre }}</h2>
                <p class="text-sm text-gray-500">{{ $mecanico->especialidad->nombre }}</p>
                <span class="mt-2 px-2.5 py-0.5 rounded-full text-xs font-semibold
                    {{ $mecanico->activo
                        ? 'bg-green-900/40 text-green-400 border border-green-800'
                        : 'bg-red-900/40 text-red-400 border border-red-800' }}">
                    {{ $mecanico->activo ? 'Activo' : 'Inactivo' }}
                </span>
            </div>

            <div class="space-y-2.5 text-sm">
                <div class="flex items-center gap-3 text-gray-400">
                    <i class="bi bi-person-badge flex-shrink-0" style="font-size:14px;"></i>
                    <span>CI: <strong class="text-gray-200">{{ $mecanico->cedula }}</strong></span>
                </div>
                @if($mecanico->persona->telefono)
                <div class="flex items-center gap-3 text-gray-400">
                    <i class="bi bi-telephone flex-shrink-0" style="font-size:14px;"></i>
                    <span>{{ $mecanico->persona->telefono }}</span>
                </div>
                @endif
                @if($mecanico->persona->email)
                <div class="flex items-center gap-3 text-gray-400">
                    <i class="bi bi-envelope flex-shrink-0" style="font-size:14px;"></i>
                    <span class="truncate">{{ $mecanico->persona->email }}</span>
                </div>
                @endif
                <div class="flex items-center gap-3 text-gray-400">
                    <i class="bi bi-building flex-shrink-0" style="font-size:14px;"></i>
                    <span>{{ $mecanico->sucursal->nombre }}</span>
                </div>
                <div class="flex items-center gap-3 text-gray-400">
                    <i class="bi bi-calendar3 flex-shrink-0" style="font-size:14px;"></i>
                    <span>Ingresó el {{ $mecanico->fecha_ingreso?->format('d/m/Y') ?? '—' }}</span>
                </div>
            </div>
        </div>

        {{-- Estadísticas --}}
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Estadísticas</p>
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-gray-900/50 rounded-lg p-3 text-center">
                    <p class="text-xl font-bold text-gray-100">{{ $stats->total ?? 0 }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">Total órdenes</p>
                </div>
                <div class="bg-green-900/20 rounded-lg p-3 text-center border border-green-900/40">
                    <p class="text-xl font-bold text-green-400">{{ $stats->entregadas ?? 0 }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">Entregadas</p>
                </div>
                <div class="bg-blue-900/20 rounded-lg p-3 text-center border border-blue-900/40">
                    <p class="text-xl font-bold text-blue-400">{{ $stats->activas ?? 0 }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">Activas</p>
                </div>
                <div class="bg-gray-900/50 rounded-lg p-3 text-center">
                    <p class="text-base font-bold text-gray-100">Bs {{ number_format($stats->facturacion_total ?? 0, 0) }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">Facturado</p>
                </div>
            </div>
        </div>

        {{-- Cuenta del sistema --}}
        @if($mecanico->persona->user)
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Cuenta del sistema</p>
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0"
                     style="background:rgba(215,25,32,.15);">
                    <i class="bi bi-person" style="color:#D71920; font-size:16px;"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-200">{{ $mecanico->persona->user->email }}</p>
                    @foreach($mecanico->persona->user->roles as $rol)
                        <span class="text-xs text-blue-400">{{ $rol->nombre }}</span>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Historial de órdenes --}}
    <div class="lg:col-span-2">
        <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-700">
                <h3 class="text-sm font-semibold text-gray-100">Historial de órdenes</h3>
            </div>

            @if($ordenes->isEmpty())
                <div class="py-16 text-center">
                    <i class="bi bi-clipboard2-x text-gray-600" style="font-size:40px;"></i>
                    <p class="mt-3 text-sm text-gray-500">Sin órdenes asignadas aún.</p>
                </div>
            @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-700">
                    <thead class="bg-gray-900/50">
                        <tr class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            <th class="px-5 py-3 text-left">Orden</th>
                            <th class="px-5 py-3 text-left">Vehículo / Cliente</th>
                            <th class="px-5 py-3 text-center">Estado</th>
                            <th class="px-5 py-3 text-right">Total</th>
                            <th class="px-5 py-3 text-left">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/50">
                        @foreach($ordenes as $orden)
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
                        <tr class="hover:bg-gray-700/30 transition-colors">
                            <td class="px-5 py-3.5">
                                <a href="{{ route('ordenes.show', $orden) }}"
                                   class="font-mono text-xs font-semibold hover:underline" style="color:#D71920;">
                                    {{ $orden->numero }}
                                </a>
                            </td>
                            <td class="px-5 py-3.5">
                                <p class="text-sm font-semibold text-gray-100">{{ $orden->vehiculo->placa }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $orden->vehiculo->modelo->marca->nombre }}
                                    {{ $orden->vehiculo->modelo->nombre }}
                                    · {{ $orden->vehiculo->cliente->persona->nombre }}
                                </p>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $colores[$orden->estado] ?? 'bg-gray-700 text-gray-400' }}">
                                    {{ $orden->estado }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right text-sm font-semibold text-gray-100">
                                Bs {{ number_format($orden->total, 2) }}
                            </td>
                            <td class="px-5 py-3.5 text-sm text-gray-400">
                                {{ $orden->fecha_ingreso?->format('d/m/Y') ?? '—' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            @if($ordenes->hasPages())
            <div class="px-6 py-4 border-t border-gray-700">
                {{ $ordenes->links() }}
            </div>
            @endif
        </div>
    </div>

</div>

@endsection
