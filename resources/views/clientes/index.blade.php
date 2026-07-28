@extends('layouts.app')

@section('title', 'Clientes')
@section('page-title', 'Clientes')

@section('header-actions')
    @can('create', App\Models\Cliente::class)
        <a href="{{ route('clientes.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-colors"
           style="background:#D71920;" onmouseover="this.style.background='#b81218'" onmouseout="this.style.background='#D71920'">
            <i class="bi bi-person-plus-fill" style="font-size:15px;"></i>
            Nuevo cliente
        </a>
    @endcan
@endsection

@section('content')

{{-- Filtros --}}
<form method="GET" action="{{ route('clientes.index') }}"
      class="bg-gray-800 border border-gray-700 rounded-xl p-4 mb-5 flex flex-wrap gap-3 items-end">
    <div class="flex-1 min-w-48">
        <label class="block text-xs font-medium text-gray-400 mb-1.5">Buscar</label>
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Nombre, email, teléfono o documento..."
               class="w-full px-3 py-2 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-500">
    </div>
    <div class="w-44">
        <label class="block text-xs font-medium text-gray-400 mb-1.5">Ciudad</label>
        <select name="ciudad"
                class="w-full px-3 py-2 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
            <option value="">Todas</option>
            @foreach ($ciudades as $ciudad)
                <option value="{{ $ciudad }}" {{ request('ciudad') === $ciudad ? 'selected' : '' }}>{{ $ciudad }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit"
            class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-gray-200 text-sm font-medium rounded-lg transition-colors flex items-center gap-1.5">
        <i class="bi bi-search" style="font-size:13px;"></i> Filtrar
    </button>
    @if (request()->hasAny(['search', 'ciudad']))
        <a href="{{ route('clientes.index') }}"
           class="px-4 py-2 text-sm text-gray-400 hover:text-gray-200 transition-colors flex items-center gap-1">
            <i class="bi bi-x-lg" style="font-size:12px;"></i> Limpiar
        </a>
    @endif
</form>

{{-- Tabla --}}
<div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">

    <div class="px-6 py-4 border-b border-gray-700 flex items-center justify-between">
        <p class="text-sm text-gray-400">
            <span class="font-semibold text-gray-200">{{ $clientes->total() }}</span>
            {{ Str::plural('cliente', $clientes->total()) }} encontrados
        </p>
    </div>

    @if ($clientes->isEmpty())
        <div class="py-20 text-center">
            <i class="bi bi-people text-gray-600" style="font-size:48px;"></i>
            <p class="mt-3 text-sm text-gray-500">No se encontraron clientes.</p>
            @can('create', App\Models\Cliente::class)
            <a href="{{ route('clientes.create') }}"
               class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white"
               style="background:#D71920;">
                <i class="bi bi-plus-lg"></i> Crear cliente
            </a>
            @endcan
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700">
                <thead class="bg-gray-900/50">
                    <tr class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        <th class="px-6 py-3 text-left">Nombre</th>
                        <th class="px-6 py-3 text-left">Documento</th>
                        <th class="px-6 py-3 text-left">Teléfono</th>
                        <th class="px-6 py-3 text-left">Ciudad</th>
                        <th class="px-6 py-3 text-center">Vehículos</th>
                        <th class="px-6 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/50">
                    @foreach ($clientes as $cliente)
                    <tr class="hover:bg-gray-700/30 transition-colors">

                        <td class="px-6 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white flex-shrink-0"
                                     style="background:#D71920;">
                                    {{ strtoupper(substr($cliente->persona->nombre, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-100">{{ $cliente->persona->nombre }}</p>
                                    <p class="text-xs text-gray-500">{{ $cliente->persona->email ?? '—' }}</p>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-3.5">
                            @if ($cliente->numero_documento)
                                <p class="text-xs text-gray-500">{{ $cliente->tipo_documento }}</p>
                                <p class="text-sm font-mono text-gray-300">{{ $cliente->numero_documento }}</p>
                            @else
                                <span class="text-gray-600">—</span>
                            @endif
                        </td>

                        <td class="px-6 py-3.5 text-sm text-gray-300">{{ $cliente->persona->telefono ?? '—' }}</td>
                        <td class="px-6 py-3.5 text-sm text-gray-300">{{ $cliente->ciudad ?? '—' }}</td>

                        <td class="px-6 py-3.5 text-center">
                            <span class="px-2.5 py-0.5 text-xs font-bold rounded-full bg-gray-700 text-gray-300">
                                {{ $cliente->vehiculos_count ?? $cliente->vehiculos->count() }}
                            </span>
                        </td>

                        <td class="px-6 py-3.5">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('clientes.show', $cliente) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors border border-gray-600">
                                    <i class="bi bi-eye" style="font-size:11px;"></i> Ver
                                </a>
                                @can('update', $cliente)
                                <a href="{{ route('clientes.edit', $cliente) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-blue-300 bg-blue-900/30 hover:bg-blue-900/50 rounded-lg transition-colors border border-blue-800/50">
                                    <i class="bi bi-pencil" style="font-size:11px;"></i> Editar
                                </a>
                                @endcan
                                @can('delete', $cliente)
                                <form method="POST" action="{{ route('clientes.destroy', $cliente) }}"
                                      onsubmit="return confirm('¿Eliminar a {{ addslashes($cliente->persona->nombre) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-red-400 bg-red-900/20 hover:bg-red-900/40 rounded-lg transition-colors border border-red-900/50">
                                        <i class="bi bi-trash" style="font-size:11px;"></i> Eliminar
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

        @if ($clientes->hasPages())
        <div class="px-6 py-4 border-t border-gray-700">
            {{ $clientes->links() }}
        </div>
        @endif
    @endif
</div>

@endsection
