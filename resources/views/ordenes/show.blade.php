@extends('layouts.app')

@section('title', $orden->numero)
@section('page-title', 'Orden ' . $orden->numero)

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
$estados = App\Http\Controllers\OrdenServicioController::ESTADOS;
@endphp

@section('header-actions')
    @can('update', $orden)
        <a href="{{ route('ordenes.edit', $orden) }}"
           class="inline-flex items-center gap-2 bg-gray-800 hover:bg-gray-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
            Editar
        </a>
    @endcan
@endsection

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Columna principal --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Servicios detalle --}}
        <div class="bg-white rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-semibold text-gray-800">Servicios</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3 text-left">Servicio</th>
                            <th class="px-6 py-3 text-center">Cant.</th>
                            <th class="px-6 py-3 text-right">Precio unit.</th>
                            <th class="px-6 py-3 text-right">Subtotal</th>
                            <th class="px-6 py-3 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($orden->detalles as $det)
                        <tr>
                            <td class="px-6 py-3">
                                <p class="text-sm font-medium text-gray-900">{{ $det->servicio->nombre }}</p>
                                <p class="text-xs text-gray-500">{{ $det->servicio->tipoServicio?->nombre }}</p>
                            </td>
                            <td class="px-6 py-3 text-center text-sm text-gray-700">{{ $det->cantidad }}</td>
                            <td class="px-6 py-3 text-right text-sm text-gray-700">Bs {{ number_format($det->precio_unitario, 2) }}</td>
                            <td class="px-6 py-3 text-right text-sm font-semibold text-gray-900">Bs {{ number_format($det->subtotal, 2) }}</td>
                            <td class="px-6 py-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $det->estado === 'Completado' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $det->estado }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 text-sm">
                        <tr>
                            <td colspan="3" class="px-6 py-2 text-right text-gray-500">Subtotal</td>
                            <td class="px-6 py-2 text-right font-medium text-gray-900">Bs {{ number_format($orden->subtotal, 2) }}</td>
                            <td></td>
                        </tr>
                        @if ($orden->descuento > 0)
                        <tr>
                            <td colspan="3" class="px-6 py-2 text-right text-gray-500">Descuento</td>
                            <td class="px-6 py-2 text-right font-medium text-red-600">- Bs {{ number_format($orden->descuento, 2) }}</td>
                            <td></td>
                        </tr>
                        @endif
                        <tr>
                            <td colspan="3" class="px-6 py-2 text-right text-gray-500">IVA (13%)</td>
                            <td class="px-6 py-2 text-right font-medium text-gray-900">Bs {{ number_format($orden->impuestos, 2) }}</td>
                            <td></td>
                        </tr>
                        <tr class="border-t border-gray-200">
                            <td colspan="3" class="px-6 py-3 text-right font-bold text-gray-900">Total</td>
                            <td class="px-6 py-3 text-right font-bold text-lg text-gray-900">Bs {{ number_format($orden->total, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Repuestos utilizados --}}
        @php $puedeEditarRepuestos = !in_array($orden->estado, ['Entregado','Cancelado']) && auth()->user()->can('update', $orden); @endphp
        <div class="bg-white rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-semibold text-gray-800">Repuestos utilizados</h3>
                    @if($orden->repuestos->isNotEmpty())
                    <p class="text-xs text-gray-500 mt-0.5">Bs {{ number_format($orden->repuestos->sum('subtotal'), 2) }} en repuestos</p>
                    @endif
                </div>
            </div>

            {{-- Tabla de repuestos actuales --}}
            @if($orden->repuestos->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-xs font-medium text-gray-500 uppercase">
                        <tr>
                            <th class="px-6 py-3 text-left">Repuesto</th>
                            <th class="px-6 py-3 text-center">Cant.</th>
                            <th class="px-6 py-3 text-right">P. Unit.</th>
                            <th class="px-6 py-3 text-right">Subtotal</th>
                            @if($puedeEditarRepuestos)<th class="px-4 py-3"></th>@endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($orden->repuestos as $rep)
                        <tr>
                            <td class="px-6 py-3">
                                <p class="font-medium text-gray-900">{{ $rep->repuesto->nombre }}</p>
                                <p class="text-xs text-gray-400 font-mono">{{ $rep->repuesto->codigo }}</p>
                            </td>
                            <td class="px-6 py-3 text-center text-gray-700">{{ $rep->cantidad }}</td>
                            <td class="px-6 py-3 text-right text-gray-700">Bs {{ number_format($rep->precio_unitario, 2) }}</td>
                            <td class="px-6 py-3 text-right font-semibold text-gray-900">Bs {{ number_format($rep->subtotal, 2) }}</td>
                            @if($puedeEditarRepuestos)
                            <td class="px-4 py-3 text-center">
                                <form method="POST" action="{{ route('ordenes.repuestos.quitar', [$orden, $rep]) }}"
                                      data-confirm="¿Quitar {{ $rep->repuesto->nombre }} de la orden? El stock se devolverá al inventario."
                                      data-confirm-title="Quitar repuesto"
                                      data-confirm-ok="Sí, quitar"
                                      data-confirm-type="warning">
                                    @csrf @method('DELETE')
                                    <button type="button" data-confirm-open
                                            class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                                        <i class="bi bi-x-lg"></i> Quitar
                                    </button>
                                </form>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="px-6 py-4 text-sm text-gray-400">No se han agregado repuestos a esta orden.</p>
            @endif

            {{-- Formulario agregar repuesto --}}
            @if($puedeEditarRepuestos && $repuestosDisponibles->isNotEmpty())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50"
                 x-data="{
                     repuestoId: '',
                     precio: '',
                     stockMax: 1,
                     seleccionar(inv) {
                         this.repuestoId = inv.repuesto_id;
                         this.precio = inv.repuesto.precio_venta;
                         this.stockMax = inv.stock;
                     }
                 }">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                    <i class="bi bi-plus-circle me-1"></i> Agregar repuesto
                </p>
                <form method="POST" action="{{ route('ordenes.repuestos.agregar', $orden) }}"
                      class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
                    @csrf
                    <input type="hidden" name="repuesto_id" :value="repuestoId">

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Repuesto (stock en sucursal)</label>
                        <select class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white text-gray-800 focus:ring-2 focus:ring-red-500 focus:outline-none"
                                @change="seleccionar({{ $repuestosDisponibles->keyBy('repuesto_id')->toJson() }}[parseInt($event.target.value)] || {})">
                            <option value="">— Seleccionar —</option>
                            @foreach($repuestosDisponibles as $inv)
                            <option value="{{ $inv->repuesto_id }}">
                                {{ $inv->repuesto->nombre }} ({{ $inv->repuesto->codigo }}) — Stock: {{ $inv->stock }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Cantidad</label>
                        <input type="number" name="cantidad" min="1" :max="stockMax" value="1"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white text-gray-800 focus:ring-2 focus:ring-red-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Precio unit. (Bs)</label>
                        <input type="number" name="precio_unitario" min="0" step="0.01" :value="precio"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white text-gray-800 focus:ring-2 focus:ring-red-500 focus:outline-none">
                    </div>

                    <div class="sm:col-span-4 flex justify-end">
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white rounded-lg transition-colors btn-taller-red"
                                :disabled="!repuestoId">
                            <i class="bi bi-plus-lg"></i> Agregar y descontar stock
                        </button>
                    </div>
                </form>
            </div>
            @elseif($puedeEditarRepuestos && $repuestosDisponibles->isEmpty())
            <div class="px-6 py-3 border-t border-gray-100 bg-gray-50/50">
                <p class="text-xs text-gray-400">
                    <i class="bi bi-info-circle me-1"></i>
                    No hay repuestos con stock en la sucursal de esta orden.
                </p>
            </div>
            @endif
        </div>

        {{-- Pagos --}}
        @php $totalPagado = $orden->pagos->where('estado','Confirmado')->sum('monto'); @endphp
        <div class="bg-white rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-semibold text-gray-800">Pagos</h3>
                    @if($totalPagado < $orden->total)
                    <p class="text-xs text-red-500 mt-0.5">Pendiente: Bs {{ number_format($orden->total - $totalPagado, 2) }}</p>
                    @else
                    <p class="text-xs text-green-600 mt-0.5">Orden totalmente pagada</p>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    @if(Route::has('pagos.create') && $orden->estado !== 'Cancelado' && $totalPagado < $orden->total)
                    <a href="{{ route('pagos.create', ['orden_id' => $orden->id]) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Registrar Pago
                    </a>
                    @endif
                    @if(Route::has('facturas.emitir') && $totalPagado >= $orden->total && !$orden->factura?->estaEmitida())
                    <form method="POST" action="{{ route('facturas.emitir') }}">
                        @csrf
                        <input type="hidden" name="orden_id" value="{{ $orden->id }}">
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Emitir Factura
                        </button>
                    </form>
                    @endif
                    @if($orden->factura?->estaEmitida())
                    <a href="{{ route('facturas.show', $orden->factura) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-green-700 dark:text-green-400 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg hover:bg-green-100 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Ver Factura
                    </a>
                    @endif
                </div>
            </div>
        @if ($orden->pagos->isNotEmpty())
            <div>
            <div class="divide-y divide-gray-50">
                @foreach ($orden->pagos as $pago)
                <div class="px-6 py-4 flex items-center justify-between text-sm">
                    <div>
                        <p class="font-medium text-gray-900">{{ $pago->metodoPago->nombre }}</p>
                        <p class="text-xs text-gray-500">{{ $pago->created_at->format('d/m/Y H:i') }}
                            @if ($pago->referencia) · Ref: {{ $pago->referencia }} @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                            {{ $pago->estado === 'Confirmado' ? 'bg-green-100 text-green-700' : ($pago->estado === 'Pendiente' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                            {{ $pago->estado }}
                        </span>
                        <span class="font-bold text-gray-900">Bs {{ number_format($pago->monto, 2) }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <p class="px-6 py-4 text-sm text-gray-400">Sin pagos registrados aún.</p>
        @endif
        </div>

        {{-- Observaciones --}}
        @if ($orden->observaciones)
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-sm font-semibold text-gray-700 mb-2">Observaciones</p>
            <p class="text-sm text-gray-600 whitespace-pre-line">{{ $orden->observaciones }}</p>
        </div>
        @endif

        {{-- Adjuntos --}}
        <div class="bg-white rounded-xl shadow-sm" x-data="{ uploading: false }">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-800 flex items-center gap-2">
                    <i class="bi bi-paperclip text-gray-500"></i>
                    Adjuntos
                    @if($orden->adjuntos?->count())
                    <span class="px-1.5 py-0.5 text-xs font-bold rounded-full bg-gray-100 text-gray-600">
                        {{ $orden->adjuntos->count() }}
                    </span>
                    @endif
                </h3>
                @can('update', $orden)
                <button type="button" @click="uploading = !uploading"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white rounded-lg transition-colors btn-taller-red">
                    <i class="bi bi-cloud-upload" style="font-size:13px;"></i>
                    Subir archivo
                </button>
                @endcan
            </div>

            @can('update', $orden)
            <div x-show="uploading" x-cloak class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <form method="POST"
                      action="{{ route('adjuntos.store', $orden) }}"
                      enctype="multipart/form-data"
                      class="flex flex-col sm:flex-row gap-3 items-start sm:items-end">
                    @csrf
                    <div class="flex-1 min-w-0">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nombre descriptivo (opcional)</label>
                        <input type="text" name="nombre" placeholder="Ej: Foto diagnóstico motor"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 bg-white text-gray-900">
                    </div>
                    <div class="flex-1 min-w-0">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Archivo <span class="text-gray-400">(JPG, PNG, PDF, DOC — max 10 MB)</span></label>
                        <input type="file" name="archivo" required accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx"
                               class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-red-600 file:text-white hover:file:bg-red-700 file:cursor-pointer">
                    </div>
                    <div class="flex gap-2 flex-shrink-0">
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-medium text-white rounded-lg transition-colors btn-taller-red">
                            <i class="bi bi-upload"></i> Adjuntar
                        </button>
                        <button type="button" @click="uploading = false"
                                class="inline-flex items-center px-3 py-2 text-xs font-medium text-gray-600 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors">
                            Cancelar
                        </button>
                    </div>
                </form>
                @error('archivo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            @endcan

            @if($orden->adjuntos?->isNotEmpty())
            <div class="divide-y divide-gray-50">
                @foreach($orden->adjuntos as $adj)
                <div class="px-6 py-3 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0"
                         style="background:#1E293B;">
                        @if($adj->esImagen())
                            <i class="bi bi-image text-blue-400" style="font-size:16px;"></i>
                        @elseif(str_contains($adj->tipo ?? '', 'pdf'))
                            <i class="bi bi-file-earmark-pdf text-red-400" style="font-size:16px;"></i>
                        @else
                            <i class="bi bi-file-earmark-text text-gray-400" style="font-size:16px;"></i>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $adj->nombre }}</p>
                        <p class="text-xs text-gray-400">
                            {{ $adj->tamanoFormateado() }}
                            @if($adj->created_at)
                            &nbsp;·&nbsp;{{ $adj->created_at->format('d/m/Y H:i') }}
                            @endif
                            @if($adj->user)
                            &nbsp;·&nbsp;{{ $adj->user->nombre }}
                            @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-1.5 flex-shrink-0">
                        <a href="{{ route('adjuntos.download', $adj) }}"
                           class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-500 hover:text-blue-600 hover:bg-blue-50 transition-colors"
                           title="Descargar">
                            <i class="bi bi-download" style="font-size:14px;"></i>
                        </a>
                        @can('update', $orden)
                        <form method="POST" action="{{ route('adjuntos.destroy', $adj) }}"
                              data-confirm="¿Eliminar el adjunto «{{ $adj->nombre }}»?">
                            @csrf @method('DELETE')
                            <button type="button" data-confirm-open
                                    class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors"
                                    title="Eliminar">
                                <i class="bi bi-trash" style="font-size:14px;"></i>
                            </button>
                        </form>
                        @endcan
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="px-6 py-5 text-sm text-gray-400 text-center">Sin archivos adjuntos.</p>
            @endif
        </div>

    </div>

    {{-- Sidebar --}}
    <div class="space-y-4">

        {{-- Info de la orden --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $colores[$orden->estado] ?? 'bg-gray-100 text-gray-600' }}">
                    {{ $orden->estado }}
                </span>
                <span class="text-sm font-bold {{ $prioridadColor[$orden->prioridad] ?? '' }}">
                    ● {{ $orden->prioridad }}
                </span>
            </div>

            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Ingreso</dt>
                    <dd class="font-medium text-gray-900">{{ $orden->fecha_ingreso->format('d/m/Y H:i') }}</dd>
                </div>
                @if ($orden->fecha_entrega_estimada)
                <div class="flex justify-between">
                    <dt class="text-gray-500">Entrega estimada</dt>
                    <dd class="font-medium text-gray-900">{{ $orden->fecha_entrega_estimada->format('d/m/Y H:i') }}</dd>
                </div>
                @endif
                @if ($orden->fecha_entrega_real)
                <div class="flex justify-between">
                    <dt class="text-gray-500">Entrega real</dt>
                    <dd class="font-medium text-green-700">{{ $orden->fecha_entrega_real->format('d/m/Y H:i') }}</dd>
                </div>
                @endif
                <div class="flex justify-between">
                    <dt class="text-gray-500">Sucursal</dt>
                    <dd class="font-medium text-gray-900">{{ $orden->sucursal?->nombre ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Mecánico</dt>
                    <dd class="font-medium text-gray-900">{{ $orden->mecanico?->persona->nombre ?? 'Sin asignar' }}</dd>
                </div>
            </dl>
        </div>

        {{-- Vehículo --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Vehículo</p>
            <p class="font-bold text-gray-900 font-mono text-lg">{{ $orden->vehiculo->placa }}</p>
            <p class="text-sm text-gray-600">{{ $orden->vehiculo->modelo->marca->nombre }} {{ $orden->vehiculo->modelo->nombre }} {{ $orden->vehiculo->ano }}</p>
            <p class="text-xs text-gray-500 mt-1">VIN: <span class="font-mono">{{ $orden->vehiculo->vin }}</span></p>
            <div class="mt-3 pt-3 border-t border-gray-100">
                <p class="text-xs text-gray-500">Propietario</p>
                <p class="text-sm font-semibold text-gray-900">{{ $orden->vehiculo->cliente->persona->nombre }}</p>
                <p class="text-xs text-gray-500">{{ $orden->vehiculo->cliente->persona->telefono ?? '' }}</p>
            </div>
            <a href="{{ route('vehiculos.show', $orden->vehiculo) }}" class="block mt-2 text-xs text-blue-600 hover:underline">Ver vehículo →</a>
        </div>

        {{-- Cambiar estado --}}
        @can('cambiarEstado', $orden)
        @if (! in_array($orden->estado, ['Entregado', 'Cancelado']))
        <div class="bg-white rounded-xl shadow-sm p-5" x-data="{ open: false }">
            <button type="button" @click="open = !open"
                    class="w-full flex items-center justify-between text-sm font-semibold text-gray-700">
                Cambiar estado
                <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open" x-cloak class="mt-4">
                <form method="POST" action="{{ route('ordenes.estado', $orden) }}" class="space-y-3">
                    @csrf @method('PATCH')
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">Nuevo estado</label>
                        <select name="estado" required
                                class="w-full px-3 py-2 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
                            @foreach ($estados as $e)
                                <option value="{{ $e }}" {{ $orden->estado === $e ? 'selected' : '' }} style="background:#111827;">{{ $e }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">Nota (opcional)</label>
                        <textarea name="observaciones" rows="2"
                                  class="w-full px-3 py-2 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-500 resize-none"
                                  placeholder="Motivo del cambio..."></textarea>
                    </div>
                    <button type="submit"
                            class="w-full py-2 text-white text-sm font-medium rounded-lg transition-colors btn-taller-red">
                        Actualizar estado
                    </button>
                </form>
            </div>
        </div>
        @endif
        @endcan

    </div>

</div>

@endsection

@push('styles')
<style>.btn-taller-red{background:#D71920}.btn-taller-red:hover{background:#b81218}</style>
@endpush

@push('scripts')
<script @nonce>
document.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-confirm-open]');
    if (btn) {
        e.preventDefault();
        tpOpen(btn.closest('form'));
    }
});
</script>
@endpush
