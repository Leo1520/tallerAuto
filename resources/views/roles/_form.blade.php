{{-- Datos básicos --}}
<div class="bg-gray-800 border border-gray-700 rounded-xl p-6 space-y-4">
    <h2 class="text-sm font-semibold text-gray-300 uppercase tracking-wide">Información del rol</h2>

    <div class="grid grid-cols-2 gap-4">
        <div class="col-span-2 sm:col-span-1">
            <label class="block text-sm font-medium text-gray-300 mb-1">
                Nombre <span class="text-red-400">*</span>
            </label>
            <input type="text" name="nombre" value="{{ old('nombre', $role->nombre ?? '') }}"
                   placeholder="Ej: Supervisor, Cajero..."
                   class="w-full px-3 py-2 text-sm bg-gray-900 border @error('nombre') border-red-500 @else border-gray-600 @enderror text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-500">
            @error('nombre') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
        </div>

        <div class="col-span-2 sm:col-span-1 flex items-center gap-3 pt-6">
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="activo" value="1"
                       class="sr-only peer"
                       {{ old('activo', ($role->activo ?? true) ? '1' : '0') == '1' ? 'checked' : '' }}>
                <div class="w-10 h-5 bg-gray-600 peer-focus:outline-none rounded-full peer
                            peer-checked:after:translate-x-full after:content-[''] after:absolute
                            after:top-0.5 after:left-[2px] after:bg-white after:rounded-full
                            after:h-4 after:w-4 after:transition-all peer-checked:bg-red-600"></div>
            </label>
            <span class="text-sm text-gray-300">Rol activo</span>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-300 mb-1">Descripción</label>
        <input type="text" name="descripcion" value="{{ old('descripcion', $role->descripcion ?? '') }}"
               placeholder="Breve descripción de este rol..."
               class="w-full px-3 py-2 text-sm bg-gray-900 border border-gray-600 text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-500">
    </div>
</div>

{{-- Permisos por módulo --}}
<div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-sm font-semibold text-gray-300 uppercase tracking-wide">Permisos por módulo</h2>
        <div class="flex gap-2">
            <button type="button" data-toggle-all="1"
                    class="px-2.5 py-1 text-xs font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors border border-gray-600">
                Marcar todos
            </button>
            <button type="button" data-toggle-all="0"
                    class="px-2.5 py-1 text-xs font-medium text-gray-400 hover:text-gray-200 rounded-lg transition-colors">
                Quitar todos
            </button>
        </div>
    </div>

    @php
    $etiquetas = [
        'ver'           => ['label' => 'Ver',           'icon' => 'bi-eye',            'color' => '#60a5fa'],
        'crear'         => ['label' => 'Crear',         'icon' => 'bi-plus-circle',    'color' => '#4ade80'],
        'editar'        => ['label' => 'Editar',        'icon' => 'bi-pencil',         'color' => '#facc15'],
        'eliminar'      => ['label' => 'Eliminar',      'icon' => 'bi-trash',          'color' => '#f87171'],
        'cambiar_estado'=> ['label' => 'Cambiar estado','icon' => 'bi-arrow-repeat',   'color' => '#a78bfa'],
        'movimientos'   => ['label' => 'Movimientos',   'icon' => 'bi-box-arrow-in-right','color' => '#34d399'],
        'confirmar'     => ['label' => 'Confirmar',     'icon' => 'bi-check-circle',   'color' => '#4ade80'],
        'reembolsar'    => ['label' => 'Reembolsar',    'icon' => 'bi-arrow-counterclockwise','color' => '#fb923c'],
        'emitir'        => ['label' => 'Emitir',        'icon' => 'bi-send',           'color' => '#4ade80'],
        'anular'        => ['label' => 'Anular',        'icon' => 'bi-x-circle',       'color' => '#f87171'],
        'exportar'      => ['label' => 'Exportar',      'icon' => 'bi-download',       'color' => '#60a5fa'],
        'asignar'       => ['label' => 'Asignar',       'icon' => 'bi-person-check',   'color' => '#a78bfa'],
    ];

    $moduloNombres = [
        'clientes'   => 'Clientes',
        'vehiculos'  => 'Vehículos',
        'ordenes'    => 'Órdenes',
        'inventario' => 'Inventario',
        'pagos'      => 'Pagos',
        'facturas'   => 'Facturas',
        'mecanicos'  => 'Mecánicos',
        'reportes'   => 'Reportes',
        'usuarios'   => 'Usuarios',
        'roles'      => 'Roles',
    ];
    @endphp

    <div class="space-y-3">
        @foreach($permisos as $modulo => $listaPermisos)
        <div class="border border-gray-700 rounded-xl overflow-hidden">
            {{-- Cabecera del módulo --}}
            <div class="flex items-center justify-between px-4 py-2.5 bg-gray-900/60">
                <div class="flex items-center gap-2">
                    <input type="checkbox" class="modulo-check w-4 h-4 rounded text-red-600 border-gray-500 bg-gray-700 focus:ring-red-600 cursor-pointer"
                           data-modulo="{{ $modulo }}"
                           data-modulo-master="1">
                    <span class="text-sm font-semibold text-gray-200">
                        {{ $moduloNombres[$modulo] ?? ucfirst($modulo) }}
                    </span>
                </div>
                <span class="text-xs text-gray-500">{{ $listaPermisos->count() }} permisos</span>
            </div>

            {{-- Permisos del módulo --}}
            <div class="px-4 py-3 flex flex-wrap gap-2">
                @foreach($listaPermisos as $permiso)
                @php
                    $info = $etiquetas[$permiso->accion] ?? ['label' => ucfirst($permiso->accion), 'icon' => 'bi-circle', 'color' => '#9ca3af'];
                    $checked = in_array($permiso->id, (array) $permisosActivos);
                @endphp
                <label class="perm-label flex items-center gap-2 px-3 py-1.5 rounded-lg border cursor-pointer transition-all select-none
                              {{ $checked
                                  ? 'border-opacity-60 bg-opacity-20'
                                  : 'border-gray-700 bg-gray-900/40 hover:border-gray-500' }}"
                       data-modulo="{{ $modulo }}"
                       style="{{ $checked ? "border-color:{$info['color']}40;background:rgba(0,0,0,.2);" : '' }}"
                       x-bind-color="{{ $info['color'] }}">
                    <input type="checkbox" name="permissions[]" value="{{ $permiso->id }}"
                           class="perm-check sr-only" data-modulo="{{ $modulo }}"
                           {{ $checked ? 'checked' : '' }}>
                    <i class="bi {{ $info['icon'] }}" style="font-size:12px;color:{{ $info['color'] }};"></i>
                    <span class="text-xs font-medium text-gray-300">{{ $info['label'] }}</span>
                </label>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</div>

<script @nonce>
(function() {
    // Sync estado visual de labels al cargar
    document.querySelectorAll('.perm-check').forEach(function(cb) {
        syncLabel(cb);
    });

    // Sync estado del checkbox-módulo al cargar
    document.querySelectorAll('.modulo-check').forEach(function(mc) {
        syncModuloCheck(mc.dataset.modulo);
    });

    // Click en label → toggle check → sincronizar visual
    document.querySelectorAll('.perm-label').forEach(function(label) {
        label.addEventListener('click', function() {
            var cb = label.querySelector('.perm-check');
            cb.checked = !cb.checked;
            syncLabel(cb);
            syncModuloCheck(cb.dataset.modulo);
        });
    });

    function syncLabel(cb) {
        var label = cb.closest('.perm-label');
        var color = label.getAttribute('x-bind-color') || '#9ca3af';
        if (cb.checked) {
            label.style.borderColor = color + '60';
            label.style.background  = color + '18';
        } else {
            label.style.borderColor = '';
            label.style.background  = '';
        }
    }

    function syncModuloCheck(modulo) {
        var checks  = document.querySelectorAll('.perm-check[data-modulo="' + modulo + '"]');
        var mc      = document.querySelector('.modulo-check[data-modulo="' + modulo + '"]');
        if (!mc) return;
        var total   = checks.length;
        var checked = Array.from(checks).filter(function(c) { return c.checked; }).length;
        mc.indeterminate = checked > 0 && checked < total;
        mc.checked       = checked === total;
    }

    window.toggleModulo = function(modulo, state) {
        document.querySelectorAll('.perm-check[data-modulo="' + modulo + '"]').forEach(function(cb) {
            cb.checked = state;
            syncLabel(cb);
        });
        syncModuloCheck(modulo);
    };

    window.toggleAll = function(state) {
        document.querySelectorAll('.perm-check').forEach(function(cb) {
            cb.checked = state;
            syncLabel(cb);
        });
        document.querySelectorAll('.modulo-check').forEach(function(mc) {
            mc.indeterminate = false;
            mc.checked = state;
        });
    };

    document.addEventListener('click', function(e) {
        var btn = e.target.closest('[data-toggle-all]');
        if (btn) { toggleAll(btn.dataset.toggleAll === '1'); }
    });

    document.addEventListener('change', function(e) {
        var el = e.target.closest('[data-modulo-master]');
        if (el) { toggleModulo(el.dataset.modulo, el.checked); }
    });
})();
</script>
