@extends('layouts.app')
@section('title', 'Perfil Mecánico')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('mecanicos.index') }}"
               class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Perfil del mecánico</h1>
        </div>
        @if(auth()->user()->isAdmin() || auth()->user()->hasPermission('mecanicos.editar'))
        <a href="{{ route('mecanicos.edit', $mecanico) }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Editar
        </a>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Tarjeta de perfil --}}
        <div class="space-y-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex flex-col items-center text-center mb-6">
                    <div class="w-20 h-20 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center mb-3">
                        <span class="text-2xl font-bold text-orange-700 dark:text-orange-400">
                            {{ strtoupper(substr($mecanico->persona->nombre, 0, 2)) }}
                        </span>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ $mecanico->persona->nombre }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $mecanico->especialidad->nombre }}</p>
                    <span class="mt-2 inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ $mecanico->activo ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-600' }}">
                        {{ $mecanico->activo ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>

                <div class="space-y-3 text-sm">
                    <div class="flex items-center gap-3 text-gray-600 dark:text-gray-400">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/>
                        </svg>
                        <span>CI: <strong class="text-gray-900 dark:text-white">{{ $mecanico->cedula }}</strong></span>
                    </div>
                    @if($mecanico->persona->telefono)
                    <div class="flex items-center gap-3 text-gray-600 dark:text-gray-400">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        {{ $mecanico->persona->telefono }}
                    </div>
                    @endif
                    @if($mecanico->persona->email)
                    <div class="flex items-center gap-3 text-gray-600 dark:text-gray-400">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        {{ $mecanico->persona->email }}
                    </div>
                    @endif
                    <div class="flex items-center gap-3 text-gray-600 dark:text-gray-400">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                        </svg>
                        {{ $mecanico->sucursal->nombre }}
                    </div>
                    <div class="flex items-center gap-3 text-gray-600 dark:text-gray-400">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Ingresó el {{ $mecanico->fecha_ingreso->format('d/m/Y') }}
                    </div>
                </div>
            </div>

            {{-- Estadísticas --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 space-y-3">
                <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Estadísticas</h3>
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 text-center">
                        <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $stats->total ?? 0 }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">Total órdenes</p>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-3 text-center">
                        <p class="text-xl font-bold text-green-600 dark:text-green-400">{{ $stats->entregadas ?? 0 }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">Entregadas</p>
                    </div>
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3 text-center">
                        <p class="text-xl font-bold text-blue-600 dark:text-blue-400">{{ $stats->activas ?? 0 }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">Activas</p>
                    </div>
                    <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-3 text-center">
                        <p class="text-lg font-bold text-orange-600 dark:text-orange-400">Bs {{ number_format($stats->facturacion_total ?? 0, 0) }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">Facturado</p>
                    </div>
                </div>
            </div>

            {{-- Cuenta del sistema --}}
            @if($mecanico->persona->user)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">Cuenta del sistema</h3>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $mecanico->persona->user->email }}</p>
                        @foreach($mecanico->persona->user->roles as $rol)
                            <span class="text-xs text-indigo-600 dark:text-indigo-400">{{ $rol->nombre }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Historial de órdenes --}}
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Historial de órdenes</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-3 text-left">Orden</th>
                                <th class="px-4 py-3 text-left">Vehículo</th>
                                <th class="px-4 py-3 text-left">Cliente</th>
                                <th class="px-4 py-3 text-center">Estado</th>
                                <th class="px-4 py-3 text-right">Total</th>
                                <th class="px-4 py-3 text-center">Fecha</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($ordenes as $orden)
                            @php
                                $colores = [
                                    'Recibido'             => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                                    'En diagnóstico'       => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                                    'En reparación'        => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                    'Esperando repuestos'  => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
                                    'Listo'                => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
                                    'Entregado'            => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                    'Cancelado'            => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                ];
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-4 py-3">
                                    <a href="{{ route('ordenes.show', $orden) }}"
                                       class="font-mono text-xs text-blue-600 dark:text-blue-400 hover:underline">
                                        {{ $orden->numero_orden }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                    {{ $orden->vehiculo->marca->nombre }} {{ $orden->vehiculo->modelo->nombre }}
                                    <span class="text-xs text-gray-400 block">{{ $orden->vehiculo->placa }}</span>
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs">
                                    {{ $orden->vehiculo->cliente->persona->nombre }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $colores[$orden->estado] ?? '' }}">
                                        {{ $orden->estado }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white">
                                    Bs {{ number_format($orden->total, 2) }}
                                </td>
                                <td class="px-4 py-3 text-center text-xs text-gray-400">
                                    {{ $orden->created_at->format('d/m/Y') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-gray-400">
                                    Sin órdenes asignadas aún.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($ordenes->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $ordenes->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
