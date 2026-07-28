@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">

        <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4">
            <div class="p-3 bg-blue-50 rounded-lg">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500">Clientes</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['clientes']) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4">
            <div class="p-3 bg-emerald-50 rounded-lg">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500">Vehículos</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['vehiculos']) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4">
            <div class="p-3 bg-orange-50 rounded-lg">
                <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500">Órdenes activas</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['ordenes_activas']) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4">
            <div class="p-3 bg-purple-50 rounded-lg">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500">Órdenes hoy</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['ordenes_hoy']) }}</p>
            </div>
        </div>
    </div>

    {{-- Órdenes recientes --}}
    <div class="bg-white rounded-xl shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-800">Órdenes recientes</h2>
        </div>

        @if ($ordenes_recientes->isEmpty())
            <div class="px-6 py-12 text-center text-gray-400 text-sm">
                No hay órdenes de servicio registradas aún.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead>
                        <tr class="bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3 text-left">N° Orden</th>
                            <th class="px-6 py-3 text-left">Cliente</th>
                            <th class="px-6 py-3 text-left">Vehículo</th>
                            <th class="px-6 py-3 text-left">Mecánico</th>
                            <th class="px-6 py-3 text-left">Estado</th>
                            <th class="px-6 py-3 text-left">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($ordenes_recientes as $orden)
                        @php
                            $colores = [
                                'Recibido'            => 'bg-blue-100 text-blue-800',
                                'En diagnóstico'      => 'bg-yellow-100 text-yellow-800',
                                'En reparación'       => 'bg-orange-100 text-orange-800',
                                'Esperando repuestos' => 'bg-purple-100 text-purple-800',
                                'Listo'               => 'bg-green-100 text-green-800',
                                'Entregado'           => 'bg-gray-100 text-gray-600',
                                'Cancelado'           => 'bg-red-100 text-red-700',
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm font-mono font-medium text-gray-900">
                                {{ $orden->numero }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ $orden->vehiculo->cliente->persona->nombre ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $orden->vehiculo->placa ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $orden->mecanico?->persona->nombre ?? 'Sin asignar' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                    {{ $colores[$orden->estado] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $orden->estado }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $orden->fecha_ingreso->format('d/m/Y') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

@endsection
