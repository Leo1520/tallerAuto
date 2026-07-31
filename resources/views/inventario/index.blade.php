@extends('layouts.app')
@section('title', 'Inventario')
@section('page-title', 'Inventario')

@section('header-actions')
<div class="flex gap-2">
    <a href="{{ route('inventario.movimientos') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 transition-colors border border-gray-600">
        <i class="bi bi-clock-history" style="font-size:14px;"></i> Historial
    </a>
    <a href="{{ route('repuestos.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 transition-colors border border-gray-600">
        <i class="bi bi-box-seam" style="font-size:14px;"></i> Repuestos
    </a>
</div>
@endsection

@section('content')

@if(session('error') || $errors->any())
<div class="flex items-start gap-3 p-4 mb-4 bg-red-900/20 border border-red-800 rounded-xl text-sm text-red-400">
    <i class="bi bi-exclamation-circle-fill flex-shrink-0 mt-0.5" style="font-size:15px;"></i>
    <div>
        @if(session('error'))
            {{ session('error') }}
        @endif
        @foreach($errors->all() as $err)
            <div>{{ $err }}</div>
        @endforeach
    </div>
</div>
@endif

@if($alertasBajoStock > 0)
<div class="flex items-center gap-3 p-4 mb-5 bg-red-900/20 border border-red-800 rounded-xl text-sm text-red-400">
    <i class="bi bi-exclamation-triangle-fill flex-shrink-0" style="font-size:16px;"></i>
    <span><strong>{{ $alertasBajoStock }}</strong> repuesto(s) con stock por debajo del mínimo.</span>
    <a href="?bajo_stock=1&sucursal_id={{ $sucursalId }}" class="underline font-medium hover:text-red-300">Ver alertas</a>
</div>
@endif

<form method="GET" class="bg-gray-800 border border-gray-700 rounded-xl p-4 mb-5 flex flex-wrap gap-3 items-end">
    <input type="hidden" name="sucursal_id" id="sucursalHidden" value="{{ $sucursalId }}">
    <div class="w-52">
        <label class="block text-xs font-medium text-gray-400 mb-1.5">Sucursal</label>
        <select name="sucursal_id" onchange="this.form.submit()"
                class="w-full px-3 py-2 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
            @foreach($sucursales as $suc)
                <option value="{{ $suc->id }}" @selected($suc->id == $sucursalId)>{{ $suc->nombre }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex-1 min-w-48">
        <label class="block text-xs font-medium text-gray-400 mb-1.5">Buscar repuesto</label>
        <div class="relative">
            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-500" style="font-size:13px; pointer-events:none;"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nombre o código..."
                   class="w-full pl-9 py-2 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-500">
        </div>
    </div>
    <div class="flex items-center gap-2 self-end pb-2">
        <input type="checkbox" name="bajo_stock" value="1" id="bajoStock"
               @checked(request('bajo_stock'))
               class="w-4 h-4 rounded border-gray-600 bg-gray-900 text-red-600 focus:ring-red-600 cursor-pointer">
        <label for="bajoStock" class="text-xs font-medium text-gray-400 cursor-pointer">Solo bajo stock</label>
    </div>
    <button type="submit"
            class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-gray-200 text-sm font-medium rounded-lg transition-colors flex items-center gap-1.5 self-end">
        <i class="bi bi-search" style="font-size:13px;"></i> Filtrar
    </button>
</form>

<div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-700">
        <p class="text-sm text-gray-400">
            Stock de <span class="font-semibold text-gray-200">{{ $sucursales->firstWhere('id', $sucursalId)?->nombre ?? 'Sucursal' }}</span>
            &mdash; <span class="font-semibold text-gray-200">{{ $stocks->total() }}</span> repuestos
        </p>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-700">
            <thead class="bg-gray-900/50">
                <tr class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                    <th class="px-5 py-3 text-left">Repuesto</th>
                    <th class="px-5 py-3 text-left">Proveedor</th>
                    <th class="px-5 py-3 text-center">Stock actual</th>
                    <th class="px-5 py-3 text-center">Stock mínimo</th>
                    <th class="px-5 py-3 text-center">Estado</th>
                    @can('registrarMovimiento', App\Policies\InventarioPolicy::class)
                    <th class="px-5 py-3 text-center">Movimiento rápido</th>
                    @endcan
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700/50">
                @forelse($stocks as $inv)
                <tr class="hover:bg-gray-700/30 transition-colors {{ $inv->bajoStock() ? 'bg-red-900/5' : '' }}">
                    <td class="px-5 py-3.5">
                        <a href="{{ route('repuestos.show', $inv->repuesto) }}"
                           class="text-sm font-semibold hover:underline" style="color:#D71920;">
                            {{ $inv->repuesto->nombre }}
                        </a>
                        @if($inv->repuesto->codigo)
                        <p class="text-xs font-mono text-gray-500">{{ $inv->repuesto->codigo }}</p>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-sm text-gray-400">{{ $inv->repuesto->proveedor?->nombre ?? '—' }}</td>
                    <td class="px-5 py-3.5 text-center">
                        <span class="text-2xl font-bold {{ $inv->bajoStock() ? 'text-red-400' : 'text-gray-100' }}">
                            {{ $inv->stock }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-center">
                        <div class="flex items-center justify-center gap-1.5">
                            <span class="text-sm text-gray-400">{{ $inv->stock_minimo }}</span>
                            @can('gestionarRepuestos', App\Policies\InventarioPolicy::class)
                            <button type="button"
                                    onclick="editarStockMinimo({{ $inv->id }}, {{ $inv->stock_minimo }}, '{{ route('inventario.stockMinimo', $inv) }}')"
                                    class="text-gray-600 hover:text-blue-400 transition-colors" title="Editar mínimo">
                                <i class="bi bi-pencil" style="font-size:11px;"></i>
                            </button>
                            @endcan
                        </div>
                    </td>
                    <td class="px-5 py-3.5 text-center">
                        @if($inv->bajoStock())
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-900/40 text-red-400 border border-red-800">
                                <i class="bi bi-exclamation-triangle-fill" style="font-size:9px;"></i> Bajo stock
                            </span>
                        @else
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-900/40 text-green-400 border border-green-800">
                                OK
                            </span>
                        @endif
                    </td>
                    @can('registrarMovimiento', App\Policies\InventarioPolicy::class)
                    <td class="px-5 py-3.5">
                        <form method="POST" action="{{ route('inventario.movimiento') }}"
                              class="flex items-center gap-1.5"
                              x-data="{ tipo: 'Entrada' }">
                            @csrf
                            <input type="hidden" name="repuesto_id" value="{{ $inv->repuesto_id }}">
                            <input type="hidden" name="sucursal_id" value="{{ $inv->sucursal_id }}">
                            <select name="tipo" x-model="tipo"
                                    class="px-2 py-1 text-xs bg-gray-900 border border-gray-600 text-gray-100 rounded-lg focus:outline-none focus:ring-1 focus:ring-red-600">
                                <option value="Entrada" {{ old('tipo') === 'Entrada' || !old('tipo') ? 'selected' : '' }}>Entrada</option>
                                <option value="Salida" {{ old('tipo') === 'Salida' ? 'selected' : '' }}>Salida</option>
                                <option value="Ajuste" {{ old('tipo') === 'Ajuste' ? 'selected' : '' }}>Ajuste</option>
                            </select>
                            <input type="number" name="cantidad" value="{{ old('cantidad', 1) }}" min="1"
                                   class="w-16 px-2 py-1 text-xs text-center bg-gray-900 border border-gray-600 text-gray-100 rounded-lg focus:outline-none focus:ring-1 focus:ring-red-600">
                            <button type="submit"
                                    :class="tipo === 'Salida' ? 'bg-red-600 hover:bg-red-700' : (tipo === 'Ajuste' ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700')"
                                    class="px-2.5 py-1 text-xs text-white rounded-lg font-medium transition-colors">
                                <i class="bi bi-check-lg"></i>
                            </button>
                        </form>
                    </td>
                    @endcan
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-16 text-center text-gray-500">
                        <i class="bi bi-box-seam" style="font-size:40px;"></i>
                        <p class="mt-2 text-sm">No hay repuestos en inventario para esta sucursal.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($stocks->hasPages())
    <div class="px-6 py-4 border-t border-gray-700">{{ $stocks->links() }}</div>
    @endif
</div>

@endsection

@push('scripts')
<script @nonce>
function editarStockMinimo(invId, actual, url) {
    const nuevo = prompt('Nuevo stock mínimo (actual: ' + actual + '):', actual);
    if (nuevo === null || nuevo.trim() === '') return;
    const n = parseInt(nuevo, 10);
    if (isNaN(n) || n < 0) { alert('Ingresa un número válido (0 o más).'); return; }

    const f = document.createElement('form');
    f.method = 'POST';
    f.action = url;
    f.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
                  '<input type="hidden" name="_method" value="PATCH">' +
                  '<input type="hidden" name="stock_minimo" value="' + n + '">';
    document.body.appendChild(f);
    f.submit();
}
</script>
@endpush
