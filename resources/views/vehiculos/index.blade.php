@extends('layouts.app')

@section('title', 'Vehículos')
@section('page-title', 'Vehículos')

@section('header-actions')
    @can('create', App\Models\Vehiculo::class)
        <a href="{{ route('vehiculos.create') }}"
           class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo vehículo
        </a>
    @endcan
@endsection

@section('content')

    {{-- Filtros --}}
    <form method="GET" action="{{ route('vehiculos.index') }}" class="bg-white rounded-xl shadow-sm p-4 mb-4 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-48">
            <label class="block text-xs font-medium text-gray-600 mb-1">Buscar</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Placa, VIN o nombre del cliente..."
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="w-44">
            <label class="block text-xs font-medium text-gray-600 mb-1">Marca</label>
            <select name="marca_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Todas</option>
                @foreach ($marcas as $marca)
                    <option value="{{ $marca->id }}" {{ request('marca_id') == $marca->id ? 'selected' : '' }}>{{ $marca->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-36">
            <label class="block text-xs font-medium text-gray-600 mb-1">Estado</label>
            <select name="activo" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Todos</option>
                <option value="1" {{ request('activo') === '1' ? 'selected' : '' }}>Activos</option>
                <option value="0" {{ request('activo') === '0' ? 'selected' : '' }}>Inactivos</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm rounded-lg hover:bg-gray-700 transition-colors">
            Filtrar
        </button>
        @if (request()->hasAny(['search', 'marca_id', 'activo']))
            <a href="{{ route('vehiculos.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 transition-colors">Limpiar</a>
        @endif
    </form>

    {{-- Tabla --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <p class="text-sm text-gray-500">{{ $vehiculos->total() }} {{ Str::plural('vehículo', $vehiculos->total()) }} encontrados</p>
        </div>

        @if ($vehiculos->isEmpty())
            <div class="py-16 text-center text-gray-400">
                <p class="text-sm">No se encontraron vehículos.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3 text-left">Vehículo</th>
                            <th class="px-6 py-3 text-left">Placa / VIN</th>
                            <th class="px-6 py-3 text-left">Cliente</th>
                            <th class="px-6 py-3 text-left">Km</th>
                            <th class="px-6 py-3 text-left">Estado</th>
                            <th class="px-6 py-3 text-left">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($vehiculos as $vehiculo)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-gray-900">
                                    {{ $vehiculo->modelo->marca->nombre }} {{ $vehiculo->modelo->nombre }}
                                </p>
                                <p class="text-xs text-gray-500">{{ $vehiculo->ano }} · {{ $vehiculo->color ?? 'Sin color' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-mono font-semibold text-gray-900">{{ $vehiculo->placa }}</p>
                                <p class="text-xs text-gray-400 font-mono">{{ substr($vehiculo->vin, 0, 8) }}...</p>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('clientes.show', $vehiculo->cliente) }}"
                                   class="text-sm text-blue-600 hover:underline">
                                    {{ $vehiculo->cliente->persona->nombre }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ number_format($vehiculo->kilometraje) }} km</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $vehiculo->activo ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $vehiculo->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('vehiculos.show', $vehiculo) }}"
                                       class="text-blue-600 hover:text-blue-800 text-sm font-medium transition-colors">Ver</a>
                                    @can('update', $vehiculo)
                                    <a href="{{ route('vehiculos.edit', $vehiculo) }}"
                                       class="text-gray-600 hover:text-gray-800 text-sm font-medium transition-colors">Editar</a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $vehiculos->links() }}
            </div>
        @endif
    </div>

@endsection
