@extends('layouts.app')

@section('title', 'Órdenes de servicio')
@section('page-title', 'Órdenes de servicio')

@section('header-actions')
    @can('create', App\Models\OrdenServicio::class)
        <a href="{{ route('ordenes.create') }}"
           class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nueva orden
        </a>
    @endcan
@endsection

@section('content')

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
$prioridadColor = ['Baja' => 'text-gray-400', 'Media' => 'text-blue-500', 'Alta' => 'text-orange-500', 'Urgente' => 'text-red-600'];
@endphp

    {{-- Filtros --}}
    <form method="GET" class="bg-white rounded-xl shadow-sm p-4 mb-4 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-44">
            <label class="block text-xs font-medium text-gray-600 mb-1">Buscar</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="N° orden, placa o cliente..."
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="w-44">
            <label class="block text-xs font-medium text-gray-600 mb-1">Estado</label>
            <select name="estado" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Todos</option>
                @foreach (App\Http\Controllers\OrdenServicioController::ESTADOS as $e)
                    <option value="{{ $e }}" {{ request('estado') === $e ? 'selected' : '' }}>{{ $e }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-36">
            <label class="block text-xs font-medium text-gray-600 mb-1">Prioridad</label>
            <select name="prioridad" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Todas</option>
                @foreach (['Baja','Media','Alta','Urgente'] as $p)
                    <option value="{{ $p }}" {{ request('prioridad') === $p ? 'selected' : '' }}>{{ $p }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-44">
            <label class="block text-xs font-medium text-gray-600 mb-1">Mecánico</label>
            <select name="mecanico_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Todos</option>
                @foreach ($mecanicos as $m)
                    <option value="{{ $m->id }}" {{ request('mecanico_id') == $m->id ? 'selected' : '' }}>{{ $m->persona->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-36">
            <label class="block text-xs font-medium text-gray-600 mb-1">Desde</label>
            <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="w-36">
            <label class="block text-xs font-medium text-gray-600 mb-1">Hasta</label>
            <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm rounded-lg hover:bg-gray-700 transition-colors">Filtrar</button>
        @if (request()->hasAny(['search','estado','prioridad','mecanico_id','fecha_desde','fecha_hasta']))
            <a href="{{ route('ordenes.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Limpiar</a>
        @endif
    </form>

    {{-- Tabla --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <p class="text-sm text-gray-500">{{ $ordenes->total() }} {{ Str::plural('orden', $ordenes->total()) }}</p>
        </div>

        @if ($ordenes->isEmpty())
            <div class="py-16 text-center text-gray-400 text-sm">No se encontraron órdenes de servicio.</div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3 text-left">N° Orden</th>
                            <th class="px-6 py-3 text-left">Vehículo / Cliente</th>
                            <th class="px-6 py-3 text-left">Mecánico</th>
                            <th class="px-6 py-3 text-left">Estado</th>
                            <th class="px-6 py-3 text-left">Prioridad</th>
                            <th class="px-6 py-3 text-left">Total</th>
                            <th class="px-6 py-3 text-left">Fecha</th>
                            <th class="px-6 py-3 text-left">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($ordenes as $orden)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-mono text-sm font-semibold text-gray-900">{{ $orden->numero }}</td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-gray-900">{{ $orden->vehiculo->placa }}</p>
                                <p class="text-xs text-gray-500">{{ $orden->vehiculo->cliente->persona->nombre }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $orden->mecanico?->persona->nombre ?? '—' }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $colores[$orden->estado] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $orden->estado }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-semibold {{ $prioridadColor[$orden->prioridad] ?? '' }}">{{ $orden->prioridad }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">Bs {{ number_format($orden->total, 2) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $orden->fecha_ingreso->format('d/m/Y') }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('ordenes.show', $orden) }}"
                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">Ver</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-100">{{ $ordenes->links() }}</div>
        @endif
    </div>

@endsection
