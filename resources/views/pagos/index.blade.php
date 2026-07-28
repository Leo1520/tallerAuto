@extends('layouts.app')
@section('title', 'Pagos')
@section('page-title', 'Pagos')

@section('header-actions')
    <a href="{{ route('pagos.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-colors"
       style="background:#D71920;" onmouseover="this.style.background='#b81218'" onmouseout="this.style.background='#D71920'">
        <span class="relative inline-flex items-center" style="font-size:15px;">
                <i class="bi bi-wallet-fill"></i>
                <i class="bi bi-plus-lg" style="font-size:9px; font-weight:900; position:absolute; top:-4px; right:-5px;"></i>
            </span> Registrar pago
    </a>
@endsection

@section('content')

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 flex items-center gap-4">
        <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-green-900/30 border border-green-800">
            <i class="bi bi-cash-stack text-green-400" style="font-size:18px;"></i>
        </div>
        <div>
            <p class="text-xs text-gray-500 font-medium">Total confirmado</p>
            <p class="text-xl font-bold text-gray-100">Bs {{ number_format($totalConfirmado ?? 0, 2) }}</p>
        </div>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 flex items-center gap-4">
        <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-blue-900/30 border border-blue-800">
            <i class="bi bi-receipt text-blue-400" style="font-size:18px;"></i>
        </div>
        <div>
            <p class="text-xs text-gray-500 font-medium">Total de registros</p>
            <p class="text-xl font-bold text-gray-100">{{ $pagos->total() }}</p>
        </div>
    </div>
</div>

<form id="filtroForm" method="GET" action="{{ route('pagos.index') }}"
      class="bg-gray-800 border border-gray-700 rounded-xl p-4 mb-5 flex flex-wrap gap-3 items-end">
    <div class="w-44">
        <label class="block text-xs font-medium text-gray-400 mb-1.5">Estado</label>
        <select name="estado"
                class="w-full px-3 py-2 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
            <option value="">Todos</option>
            <option value="Pendiente"   {{ request('estado') === 'Pendiente'   ? 'selected' : '' }}>Pendiente</option>
            <option value="Confirmado"  {{ request('estado') === 'Confirmado'  ? 'selected' : '' }}>Confirmado</option>
            <option value="Anulado"     {{ request('estado') === 'Anulado'     ? 'selected' : '' }}>Anulado</option>
        </select>
    </div>
    <div class="w-48">
        <label class="block text-xs font-medium text-gray-400 mb-1.5">Método de pago</label>
        <select name="metodo_pago_id"
                class="w-full px-3 py-2 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
            <option value="">Todos</option>
            @foreach ($metodos as $m)
                <option value="{{ $m->id }}" {{ request('metodo_pago_id') == $m->id ? 'selected' : '' }}>{{ $m->nombre }}</option>
            @endforeach
        </select>
    </div>
    <div class="w-36">
        <label class="block text-xs font-medium text-gray-400 mb-1.5">Desde</label>
        <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}"
               class="w-full px-3 py-2 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
    </div>
    <div class="w-36">
        <label class="block text-xs font-medium text-gray-400 mb-1.5">Hasta</label>
        <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}"
               class="w-full px-3 py-2 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
    </div>
    <button type="submit"
            class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-gray-200 text-sm font-medium rounded-lg transition-colors flex items-center gap-1.5">
        <i class="bi bi-search" style="font-size:13px;"></i> Filtrar
    </button>
    @if (request()->hasAny(['estado', 'metodo_pago_id', 'fecha_desde', 'fecha_hasta']))
        <a href="{{ route('pagos.index') }}"
           class="px-4 py-2 text-sm text-gray-400 hover:text-gray-200 transition-colors flex items-center gap-1">
            <i class="bi bi-x-lg" style="font-size:12px;"></i> Limpiar
        </a>
    @endif
</form>

<div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-700">
        <p class="text-sm text-gray-400">
            <span class="font-semibold text-gray-200">{{ $pagos->total() }}</span>
            {{ Str::plural('pago', $pagos->total()) }} encontrados
        </p>
    </div>

    @if ($pagos->isEmpty())
        <div class="py-20 text-center">
            <i class="bi bi-wallet2 text-gray-600" style="font-size:48px;"></i>
            <p class="mt-3 text-sm text-gray-500">No se encontraron pagos.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700">
                <thead class="bg-gray-900/50">
                    <tr class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        <th class="px-6 py-3 text-left">N° Pago</th>
                        <th class="px-6 py-3 text-left">Orden</th>
                        <th class="px-6 py-3 text-left">Cliente</th>
                        <th class="px-6 py-3 text-left">Método</th>
                        <th class="px-6 py-3 text-right">Monto</th>
                        <th class="px-6 py-3 text-center">Estado</th>
                        <th class="px-6 py-3 text-left">Fecha</th>
                        <th class="px-6 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/50">
                    @foreach ($pagos as $pago)
                    @php
                        $estadoClass = match($pago->estado) {
                            'Confirmado' => 'bg-green-900/40 text-green-400 border border-green-800',
                            'Pendiente'  => 'bg-yellow-900/40 text-yellow-400 border border-yellow-800',
                            'Anulado'    => 'bg-red-900/40 text-red-400 border border-red-800',
                            default      => 'bg-gray-700 text-gray-400 border border-gray-600',
                        };
                    @endphp
                    <tr class="hover:bg-gray-700/30 transition-colors">
                        <td class="px-6 py-3.5 font-mono text-sm font-semibold text-gray-100">{{ $pago->numero }}</td>
                        <td class="px-6 py-3.5">
                            <a href="{{ route('ordenes.show', $pago->orden) }}"
                               class="text-sm font-medium hover:underline" style="color:#D71920;">
                                {{ $pago->orden->numero }}
                            </a>
                        </td>
                        <td class="px-6 py-3.5 text-sm text-gray-300">{{ $pago->orden->vehiculo->cliente->persona->nombre }}</td>
                        <td class="px-6 py-3.5 text-sm text-gray-400">{{ $pago->metodoPago->nombre }}</td>
                        <td class="px-6 py-3.5 text-right text-sm font-semibold text-gray-100">Bs {{ number_format($pago->monto, 2) }}</td>
                        <td class="px-6 py-3.5 text-center">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $estadoClass }}">
                                {{ $pago->estado }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5 text-sm text-gray-400">{{ $pago->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-3.5">
                            <div class="flex items-center justify-end gap-1.5">
                                @if ($pago->estado === 'Pendiente')
                                <form method="POST" action="{{ route('pagos.confirmar', $pago) }}"
                                      data-confirm="¿Confirmar el pago #{{ $pago->numero }}?"
                                      data-confirm-type="warning"
                                      data-confirm-title="Confirmar pago"
                                      data-confirm-ok="Sí, confirmar">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-green-300 bg-green-900/30 hover:bg-green-900/50 rounded-lg transition-colors border border-green-800/50">
                                        <i class="bi bi-check-lg" style="font-size:11px;"></i> Confirmar
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('pagos.anular', $pago) }}"
                                      data-confirm="¿Anular el pago #{{ $pago->numero }}? Esta acción no se puede deshacer.">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-orange-300 bg-orange-900/20 hover:bg-orange-900/40 rounded-lg transition-colors border border-orange-900/50">
                                        <i class="bi bi-x-circle" style="font-size:11px;"></i> Anular
                                    </button>
                                </form>
                                @else
                                <a href="{{ route('ordenes.show', $pago->orden) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors border border-gray-600">
                                    <i class="bi bi-eye" style="font-size:11px;"></i> Ver orden
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="px-6 py-4 border-t border-gray-700 {{ $pagos->hasPages() ? '' : 'hidden' }}">
        {{ $pagos->links() }}
    </div>
</div>

@endsection
