@extends('layouts.app')
@section('title', $repuesto->nombre)

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-start justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('repuestos.index') }}"
               class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $repuesto->nombre }}</h1>
                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium
                        {{ $repuesto->activo ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-500' }}">
                        {{ $repuesto->activo ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 font-mono">{{ $repuesto->codigo }}</p>
            </div>
        </div>
        @can('gestionarRepuestos', App\Policies\InventarioPolicy::class)
        <a href="{{ route('repuestos.edit', $repuesto) }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Editar
        </a>
        @endcan
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Info principal --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-4">Información</h2>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Proveedor</dt>
                        <dd class="font-medium text-gray-900 dark:text-white mt-0.5">
                            @if($repuesto->proveedor)
                                <a href="{{ route('proveedores.edit', $repuesto->proveedor) }}"
                                   class="text-blue-600 dark:text-blue-400 hover:underline">{{ $repuesto->proveedor->nombre }}</a>
                            @else
                                <span class="text-gray-400">Sin proveedor</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Descripción</dt>
                        <dd class="text-gray-900 dark:text-white mt-0.5">{{ $repuesto->descripcion ?: '—' }}</dd>
                    </div>
                    <div class="grid grid-cols-2 gap-4 pt-2 border-t border-gray-100 dark:border-gray-700">
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Precio compra</dt>
                            <dd class="font-semibold text-gray-900 dark:text-white mt-0.5">Bs {{ number_format($repuesto->precio_compra, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Precio venta</dt>
                            <dd class="font-semibold text-green-600 dark:text-green-400 mt-0.5">Bs {{ number_format($repuesto->precio_venta, 2) }}</dd>
                        </div>
                    </div>
                    <div class="pt-2 border-t border-gray-100 dark:border-gray-700">
                        <dt class="text-gray-500 dark:text-gray-400">Margen</dt>
                        @php $margen = $repuesto->precio_compra > 0 ? (($repuesto->precio_venta - $repuesto->precio_compra) / $repuesto->precio_compra) * 100 : 0; @endphp
                        <dd class="font-medium {{ $margen >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600' }} mt-0.5">
                            {{ number_format($margen, 1) }}%
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Stock por sucursal --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-4">Stock por sucursal</h2>
                @forelse($repuesto->inventarios as $inv)
                <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $inv->sucursal->nombre }}</p>
                        <p class="text-xs text-gray-400">Mín: {{ $inv->stock_minimo }}</p>
                    </div>
                    <span class="text-lg font-bold {{ $inv->bajoStock() ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                        {{ $inv->stock }}
                        @if($inv->bajoStock()) <span class="text-xs">⚠</span> @endif
                    </span>
                </div>
                @empty
                <p class="text-sm text-gray-400">Sin stock registrado</p>
                @endforelse
            </div>
        </div>

        {{-- Movimientos --}}
        <div class="lg:col-span-2 space-y-6">
            @can('registrarMovimiento', App\Policies\InventarioPolicy::class)
            {{-- Registrar movimiento rápido --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Registrar movimiento</h2>

                @if($errors->any())
                <div class="flex items-start gap-2 p-3 mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-sm text-red-600 dark:text-red-400">
                    <i class="bi bi-exclamation-circle-fill flex-shrink-0 mt-0.5"></i>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('inventario.movimiento') }}" class="flex flex-wrap gap-3 items-end">
                    @csrf
                    <input type="hidden" name="repuesto_id" value="{{ $repuesto->id }}">

                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Tipo</label>
                        <select name="tipo" required
                                class="px-3 py-2 text-sm border rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('tipo') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' }}">
                            <option value="Entrada" {{ old('tipo', 'Entrada') === 'Entrada' ? 'selected' : '' }}>Entrada</option>
                            <option value="Salida" {{ old('tipo') === 'Salida' ? 'selected' : '' }}>Salida</option>
                            <option value="Ajuste" {{ old('tipo') === 'Ajuste' ? 'selected' : '' }}>Ajuste</option>
                        </select>
                        @error('tipo')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Sucursal</label>
                        <select name="sucursal_id" required
                                class="px-3 py-2 text-sm border rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('sucursal_id') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' }}">
                            @foreach($repuesto->inventarios as $inv)
                                <option value="{{ $inv->sucursal_id }}" {{ old('sucursal_id') == $inv->sucursal_id ? 'selected' : '' }}>
                                    {{ $inv->sucursal->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('sucursal_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Cantidad</label>
                        <input type="number" name="cantidad" min="1" value="{{ old('cantidad', 1) }}" required
                               class="w-24 px-3 py-2 text-sm border rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('cantidad') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' }}">
                        @error('cantidad')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Motivo</label>
                        <input type="text" name="motivo" value="{{ old('motivo') }}"
                               placeholder="Ej: Compra a proveedor, Uso en orden..."
                               class="w-full px-3 py-2 text-sm border rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('motivo') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' }}">
                        @error('motivo')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                        Registrar
                    </button>
                </form>
            </div>
            @endcan

            {{-- Historial de movimientos --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Historial de movimientos</h2>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Fecha</th>
                            <th class="px-4 py-3 text-left">Tipo</th>
                            <th class="px-4 py-3 text-center">Cantidad</th>
                            <th class="px-4 py-3 text-left">Sucursal</th>
                            <th class="px-4 py-3 text-left">Motivo</th>
                            <th class="px-4 py-3 text-left">Usuario</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($movimientos as $mov)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($mov->created_at)->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $mov->tipo === 'Entrada' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' :
                                       ($mov->tipo === 'Salida'  ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' :
                                        'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400') }}">
                                    {{ $mov->tipo }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center font-medium
                                {{ $mov->tipo === 'Entrada' ? 'text-green-600 dark:text-green-400' :
                                   ($mov->tipo === 'Salida'  ? 'text-red-600 dark:text-red-400' : 'text-yellow-600') }}">
                                {{ $mov->tipo === 'Salida' ? '-' : '+' }}{{ $mov->cantidad }}
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $mov->sucursal->nombre }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $mov->motivo ?: '—' }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $mov->user->persona->nombre ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400">Sin movimientos registrados</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                @if($movimientos->hasPages())
                <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">{{ $movimientos->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
