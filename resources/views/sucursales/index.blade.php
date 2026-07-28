@extends('layouts.app')

@section('title', 'Sucursales')
@section('page-title', 'Sucursales')

@section('header-actions')
    @if(auth()->user()->isAdmin())
    <a href="{{ route('sucursales.mapa') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 transition-colors">
        <i class="bi bi-map" style="font-size:15px;"></i>
        Ver mapa
    </a>
    <a href="{{ route('sucursales.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white transition-colors"
       style="background:#D71920;" onmouseover="this.style.background='#b81218'" onmouseout="this.style.background='#D71920'">
        <i class="bi bi-plus-lg" style="font-size:15px;"></i>
        Nueva sucursal
    </a>
    @endif
@endsection

@section('content')

<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
    @forelse($sucursales as $suc)
    <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden flex flex-col">

        {{-- Header de la tarjeta --}}
        <div class="px-5 py-4 border-b border-gray-700 flex items-start justify-between gap-2">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background:rgba(215,25,32,.15);">
                    <i class="bi bi-geo-alt-fill" style="color:#D71920; font-size:18px;"></i>
                </div>
                <div class="min-w-0">
                    <p class="font-semibold text-gray-100 truncate">{{ $suc->nombre }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ $suc->ciudad ?? 'Sin ciudad' }}</p>
                </div>
            </div>
            <span class="flex-shrink-0 px-2 py-0.5 rounded-full text-xs font-semibold
                {{ $suc->activo ? 'bg-green-900/40 text-green-400 border border-green-700' : 'bg-gray-700 text-gray-500 border border-gray-600' }}">
                {{ $suc->activo ? 'Activa' : 'Inactiva' }}
            </span>
        </div>

        {{-- Info --}}
        <div class="px-5 py-4 flex-1 space-y-2">
            @if($suc->direccion)
            <div class="flex items-start gap-2 text-sm text-gray-400">
                <i class="bi bi-pin-map flex-shrink-0 mt-0.5" style="font-size:13px;"></i>
                <span class="leading-snug">{{ $suc->direccion }}</span>
            </div>
            @endif
            @if($suc->telefono)
            <div class="flex items-center gap-2 text-sm text-gray-400">
                <i class="bi bi-telephone flex-shrink-0" style="font-size:13px;"></i>
                <span>{{ $suc->telefono }}</span>
            </div>
            @endif
            @if($suc->email)
            <div class="flex items-center gap-2 text-sm text-gray-400">
                <i class="bi bi-envelope flex-shrink-0" style="font-size:13px;"></i>
                <span class="truncate">{{ $suc->email }}</span>
            </div>
            @endif
        </div>

        {{-- Stats --}}
        <div class="px-5 py-3 bg-gray-900/40 border-t border-gray-700 flex items-center justify-between">
            <div class="text-center">
                <p class="text-lg font-bold text-gray-100">{{ $suc->mecanicos_activos }}</p>
                <p class="text-xs text-gray-500">Mecánicos</p>
            </div>
            <div class="w-px h-8 bg-gray-700"></div>
            <div class="text-center">
                <p class="text-lg font-bold text-gray-100">{{ $suc->ordenes_activas }}</p>
                <p class="text-xs text-gray-500">Órdenes activas</p>
            </div>
            @if(auth()->user()->isAdmin())
            <div class="w-px h-8 bg-gray-700"></div>
            <a href="{{ route('sucursales.edit', $suc) }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors">
                <i class="bi bi-pencil" style="font-size:11px;"></i>
                Editar
            </a>
            @endif
        </div>

    </div>
    @empty
    <div class="sm:col-span-2 xl:col-span-3 py-20 text-center">
        <i class="bi bi-geo-alt text-gray-600" style="font-size:48px;"></i>
        <p class="mt-3 text-gray-500">No hay sucursales registradas.</p>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('sucursales.create') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white"
           style="background:#D71920;">
            <i class="bi bi-plus-lg"></i> Crear primera sucursal
        </a>
        @endif
    </div>
    @endforelse
</div>

@endsection
