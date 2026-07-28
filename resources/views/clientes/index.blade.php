@extends('layouts.app')

@section('title', 'Clientes')
@section('page-title', 'Clientes')

@section('header-actions')
    @can('create', App\Models\Cliente::class)
        <a href="{{ route('clientes.create') }}"
           class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo cliente
        </a>
    @endcan
@endsection

@section('content')

    {{-- Filtros --}}
    <form method="GET" action="{{ route('clientes.index') }}" class="bg-white rounded-xl shadow-sm p-4 mb-4 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-48">
            <label class="block text-xs font-medium text-gray-600 mb-1">Buscar</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Nombre, email, teléfono o documento..."
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="w-48">
            <label class="block text-xs font-medium text-gray-600 mb-1">Ciudad</label>
            <select name="ciudad" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Todas</option>
                @foreach ($ciudades as $ciudad)
                    <option value="{{ $ciudad }}" {{ request('ciudad') === $ciudad ? 'selected' : '' }}>{{ $ciudad }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm rounded-lg hover:bg-gray-700 transition-colors">
            Filtrar
        </button>
        @if (request()->hasAny(['search', 'ciudad']))
            <a href="{{ route('clientes.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 transition-colors">
                Limpiar
            </a>
        @endif
    </form>

    {{-- Tabla --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <p class="text-sm text-gray-500">
                {{ $clientes->total() }} {{ Str::plural('cliente', $clientes->total()) }} encontrados
            </p>
        </div>

        @if ($clientes->isEmpty())
            <div class="py-16 text-center text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <p class="text-sm">No se encontraron clientes.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3 text-left">Nombre</th>
                            <th class="px-6 py-3 text-left">Documento</th>
                            <th class="px-6 py-3 text-left">Teléfono</th>
                            <th class="px-6 py-3 text-left">Ciudad</th>
                            <th class="px-6 py-3 text-left">Vehículos</th>
                            <th class="px-6 py-3 text-left">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($clientes as $cliente)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">
                                        {{ strtoupper(substr($cliente->persona->nombre, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $cliente->persona->nombre }}</p>
                                        <p class="text-xs text-gray-500">{{ $cliente->persona->email ?? '—' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                @if ($cliente->numero_documento)
                                    <span class="text-xs text-gray-400">{{ $cliente->tipo_documento }}</span><br>
                                    <span class="font-mono">{{ $cliente->numero_documento }}</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $cliente->persona->telefono ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $cliente->ciudad ?? '—' }}</td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-gray-900">{{ $cliente->vehiculos_count ?? $cliente->vehiculos->count() }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('clientes.show', $cliente) }}"
                                       class="text-blue-600 hover:text-blue-800 text-sm font-medium transition-colors">Ver</a>
                                    @can('update', $cliente)
                                    <a href="{{ route('clientes.edit', $cliente) }}"
                                       class="text-gray-600 hover:text-gray-800 text-sm font-medium transition-colors">Editar</a>
                                    @endcan
                                    @can('delete', $cliente)
                                    <form method="POST" action="{{ route('clientes.destroy', $cliente) }}"
                                          onsubmit="return confirm('¿Eliminar este cliente?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-medium transition-colors">
                                            Eliminar
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $clientes->links() }}
            </div>
        @endif
    </div>

@endsection
