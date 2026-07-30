<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; color: #1f2937; background: #fff; }

    .page { padding: 30px 36px; }

    /* Header */
    .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 28px; border-bottom: 2px solid #D71920; padding-bottom: 16px; }
    .brand h1 { font-size: 22px; font-weight: 700; color: #D71920; letter-spacing: -0.5px; }
    .brand p { font-size: 10px; color: #6b7280; margin-top: 2px; }
    .factura-info { text-align: right; }
    .factura-numero { font-size: 18px; font-weight: 700; color: #D71920; font-family: monospace; }
    .factura-info p { font-size: 10px; color: #6b7280; margin-top: 3px; }
    .badge { display: inline-block; padding: 2px 10px; border-radius: 999px; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 6px; }
    .badge-emitida { background: #d1fae5; color: #065f46; }
    .badge-anulada { background: #fee2e2; color: #991b1b; }
    .badge-borrador { background: #fef3c7; color: #92400e; }

    /* Partes */
    .parties { display: flex; gap: 20px; margin-bottom: 20px; }
    .party { flex: 1; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 12px; }
    .party h3 { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #6b7280; margin-bottom: 8px; }
    .party p { font-size: 11px; color: #374151; margin-bottom: 3px; }
    .party .label { font-size: 9px; color: #9ca3af; display: block; }
    .party .value { font-weight: 600; color: #111827; }

    /* Tabla */
    table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
    .section-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #6b7280; margin-bottom: 6px; margin-top: 16px; }
    thead tr { background: #D71920; color: #fff; }
    thead th { padding: 8px 10px; text-align: left; font-size: 9px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
    thead th.right { text-align: right; }
    thead th.center { text-align: center; }
    tbody tr { border-bottom: 1px solid #f3f4f6; }
    tbody tr:nth-child(even) { background: #f9fafb; }
    tbody td { padding: 7px 10px; font-size: 10px; color: #374151; }
    tbody td.right { text-align: right; }
    tbody td.center { text-align: center; }
    .subtotal-name { font-size: 9px; color: #9ca3af; display: block; }

    /* Totales */
    .totales-container { display: flex; justify-content: flex-end; margin-top: 8px; }
    .totales { width: 240px; border: 1px solid #e5e7eb; border-radius: 6px; overflow: hidden; }
    .totales-row { display: flex; justify-content: space-between; padding: 7px 12px; font-size: 11px; border-bottom: 1px solid #f3f4f6; }
    .totales-row .label { color: #6b7280; }
    .totales-row .amount { font-weight: 600; color: #111827; }
    .totales-total { display: flex; justify-content: space-between; padding: 10px 12px; background: #D71920; color: #fff; }
    .totales-total .label { font-weight: 600; font-size: 11px; }
    .totales-total .amount { font-weight: 700; font-size: 14px; }

    /* Pagos */
    .pagos { margin-top: 20px; }
    .pagos h3 { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #6b7280; margin-bottom: 8px; }
    .pago-item { display: flex; justify-content: space-between; padding: 6px 10px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 4px; margin-bottom: 4px; font-size: 10px; }
    .pago-item .metodo { color: #374151; font-weight: 500; }
    .pago-item .monto { color: #166534; font-weight: 700; }

    /* Footer */
    .footer { margin-top: 32px; padding-top: 12px; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; font-size: 9px; color: #9ca3af; }
    .anulada-watermark { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-30deg); font-size: 80px; font-weight: 900; color: rgba(220, 38, 38, 0.12); text-transform: uppercase; letter-spacing: 4px; pointer-events: none; }
</style>
</head>
<body>
<div class="page">

    @if($factura->estaAnulada())
    <div class="anulada-watermark">ANULADA</div>
    @endif

    {{-- Header --}}
    <div class="header">
        <div class="brand">
            <h1>Taller Automotrices SC-BOL</h1>
            <p>{{ $factura->orden->sucursal->nombre ?? 'Sucursal Principal' }}</p>
            <p>{{ $factura->orden->sucursal->direccion ?? '' }}</p>
            <p>Cochabamba, Bolivia</p>
        </div>
        <div class="factura-info">
            <div class="factura-numero">{{ $factura->numero }}</div>
            <p>Fecha de emisión: {{ $factura->fecha_emision?->format('d/m/Y H:i') }}</p>
            <p>Orden: <strong>{{ $factura->orden->numero }}</strong></p>
            <span class="badge badge-{{ strtolower($factura->estado) }}">{{ $factura->estado }}</span>
        </div>
    </div>

    {{-- Datos del cliente y vehículo --}}
    <div class="parties">
        <div class="party">
            <h3>Cliente</h3>
            <p>
                <span class="label">Nombre completo</span>
                <span class="value">{{ $factura->orden->vehiculo->cliente->persona->nombre ?? '—' }}</span>
            </p>
            <p style="margin-top:6px">
                <span class="label">CI / NIT</span>
                <span class="value">{{ $factura->orden->vehiculo->cliente->numero_documento ?? '—' }}</span>
            </p>
            <p style="margin-top:6px">
                <span class="label">Teléfono</span>
                <span class="value">{{ $factura->orden->vehiculo->cliente->persona->telefono ?? '—' }}</span>
            </p>
        </div>
        <div class="party">
            <h3>Vehículo</h3>
            <p>
                <span class="label">Marca / Modelo / Año</span>
                <span class="value">
                    {{ $factura->orden->vehiculo->modelo->marca->nombre ?? '' }}
                    {{ $factura->orden->vehiculo->modelo->nombre ?? '' }}
                    {{ $factura->orden->vehiculo->anio ? '(' . $factura->orden->vehiculo->anio . ')' : '' }}
                </span>
            </p>
            <p style="margin-top:6px">
                <span class="label">Placa</span>
                <span class="value" style="font-family:monospace; font-size:13px; font-weight:700">{{ $factura->orden->vehiculo->placa }}</span>
            </p>
            @if($factura->orden->mecanico)
            <p style="margin-top:6px">
                <span class="label">Mecánico responsable</span>
                <span class="value">{{ $factura->orden->mecanico->persona->nombre ?? '—' }}</span>
            </p>
            @endif
        </div>
    </div>

    {{-- Servicios --}}
    @if($factura->orden->detalles->isNotEmpty())
    <p class="section-title">Servicios realizados</p>
    <table>
        <thead>
            <tr>
                <th>Descripción del servicio</th>
                <th class="right">Precio unit.</th>
                <th class="center">Cant.</th>
                <th class="right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($factura->orden->detalles as $det)
            <tr>
                <td>
                    {{ $det->servicio->nombre }}
                    @if($det->descripcion)
                        <span class="subtotal-name">{{ $det->descripcion }}</span>
                    @endif
                </td>
                <td class="right">Bs {{ number_format($det->precio_unitario, 2) }}</td>
                <td class="center">{{ $det->cantidad }}</td>
                <td class="right">Bs {{ number_format($det->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- Repuestos --}}
    @if($factura->orden->repuestos->isNotEmpty())
    <p class="section-title">Repuestos y materiales</p>
    <table>
        <thead>
            <tr>
                <th>Repuesto / Material</th>
                <th class="right">Precio unit.</th>
                <th class="center">Cant.</th>
                <th class="right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($factura->orden->repuestos as $rep)
            <tr>
                <td>{{ $rep->repuesto->nombre }}</td>
                <td class="right">Bs {{ number_format($rep->precio_unitario, 2) }}</td>
                <td class="center">{{ $rep->cantidad }}</td>
                <td class="right">Bs {{ number_format($rep->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- Totales --}}
    <div class="totales-container">
        <div class="totales">
            <div class="totales-row">
                <span class="label">Subtotal</span>
                <span class="amount">Bs {{ number_format($factura->subtotal, 2) }}</span>
            </div>
            <div class="totales-row">
                <span class="label">IVA (13%)</span>
                <span class="amount">Bs {{ number_format($factura->iva, 2) }}</span>
            </div>
            @if($factura->orden->descuento > 0)
            <div class="totales-row" style="color:#166534">
                <span class="label">Descuento</span>
                <span class="amount">- Bs {{ number_format($factura->orden->descuento, 2) }}</span>
            </div>
            @endif
            <div class="totales-total">
                <span class="label">TOTAL A PAGAR</span>
                <span class="amount">Bs {{ number_format($factura->total, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- Pagos --}}
    @if($factura->orden->pagos->where('estado','Confirmado')->isNotEmpty())
    <div class="pagos">
        <h3>Pagos recibidos</h3>
        @foreach($factura->orden->pagos->where('estado','Confirmado') as $pago)
        <div class="pago-item">
            <span class="metodo">
                {{ $pago->metodoPago->nombre }} — {{ $pago->created_at->format('d/m/Y') }}
                @if($pago->referencia) · Ref: {{ $pago->referencia }} @endif
            </span>
            <span class="monto">Bs {{ number_format($pago->monto, 2) }}</span>
        </div>
        @endforeach
    </div>
    @endif

    @if($factura->observaciones && !$factura->estaAnulada())
    <div style="margin-top:16px; padding:10px; background:#f9fafb; border-radius:4px; border:1px solid #e5e7eb">
        <p style="font-size:9px; font-weight:700; text-transform:uppercase; color:#6b7280; margin-bottom:4px">Observaciones</p>
        {{-- Skill: laravel-security — {{ }} escapa XSS --}}
        <p style="font-size:10px; color:#374151">{{ $factura->observaciones }}</p>
    </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <span>Taller Automotrices SC-BOL — Sistema de Gestión Automotriz</span>
        <span>Generado: {{ now()->format('d/m/Y H:i') }}</span>
        <span>Este documento es válido como comprobante de servicio</span>
    </div>
</div>
</body>
</html>
