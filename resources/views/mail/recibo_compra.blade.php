<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Recibo de Compra</title>
<style>
  body { margin:0; padding:0; background:#f4f4f5; font-family:'Segoe UI',Arial,sans-serif; color:#1a1a2e; }
  .wrap { max-width:580px; margin:32px auto; background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,.10); }
  .header { background:#D71920; padding:32px 40px 24px; text-align:center; }
  .header img { width:64px; height:64px; border-radius:50%; background:#fff; padding:8px; }
  .header h1 { color:#fff; font-size:22px; font-weight:700; margin:12px 0 4px; }
  .header p  { color:rgba(255,255,255,.8); font-size:13px; margin:0; }
  .badge { display:inline-block; background:rgba(255,255,255,.2); color:#fff; font-size:12px; font-weight:700; padding:4px 14px; border-radius:20px; margin-top:10px; letter-spacing:.04em; }
  .body { padding:32px 40px; }
  .greeting { font-size:20px; font-weight:700; color:#1a1a2e; margin-bottom:6px; }
  .subtitle  { font-size:14px; color:#6b7280; margin-bottom:28px; }
  .section-label { font-size:10px; font-weight:700; color:#9ca3af; text-transform:uppercase; letter-spacing:.08em; margin-bottom:10px; }
  .recibo-box { background:#f9fafb; border:1px solid #e5e7eb; border-radius:10px; padding:20px 24px; margin-bottom:20px; }
  .meta-row { display:flex; justify-content:space-between; font-size:13px; color:#374151; margin-bottom:6px; }
  .meta-row:last-child { margin-bottom:0; }
  .meta-row span:first-child { color:#6b7280; }
  .meta-row strong { font-weight:700; }
  .divider { border:none; border-top:1px solid #e5e7eb; margin:16px 0; }
  .total-row { display:flex; justify-content:space-between; align-items:center; }
  .total-label { font-size:14px; font-weight:700; color:#374151; }
  .total-amount { font-size:28px; font-weight:800; color:#D71920; letter-spacing:-.02em; }
  .info-box { background:#eff6ff; border:1px solid #bfdbfe; border-radius:10px; padding:14px 18px; margin-bottom:20px; font-size:13px; color:#1d4ed8; line-height:1.6; }
  .footer { background:#f9fafb; border-top:1px solid #e5e7eb; padding:20px 40px; text-align:center; font-size:12px; color:#9ca3af; }
  .footer strong { color:#6b7280; }
</style>
</head>
<body>
<div class="wrap">

  {{-- Encabezado --}}
  <div class="header">
    <h1>Taller Automotrices SC-BOL</h1>
    <p>Sistema automotriz de gestión</p>
    <span class="badge">✅ PAGO CONFIRMADO</span>
  </div>

  {{-- Cuerpo --}}
  <div class="body">

    <p class="greeting">¡Hola, {{ $consulta->nombre }}!</p>
    <p class="subtitle">Tu pago fue verificado exitosamente. Aquí tienes tu recibo de compra.</p>

    {{-- Datos del recibo --}}
    <p class="section-label">Recibo de compra</p>
    <div class="recibo-box">
      <div class="meta-row">
        <span>N.º Solicitud</span>
        <strong>#{{ $consulta->id }}</strong>
      </div>
      <div class="meta-row">
        <span>Fecha de pago</span>
        <strong>{{ now()->format('d/m/Y H:i') }}</strong>
      </div>
      <div class="meta-row">
        <span>Método de pago</span>
        <strong>Transferencia / QR Banco Ganadero</strong>
      </div>
    </div>

    {{-- Detalle del producto --}}
    <p class="section-label">Detalle del producto</p>
    <div class="recibo-box">
      <div class="meta-row">
        <span>Producto</span>
        <strong>{{ $repuesto?->nombre ?? '—' }}</strong>
      </div>
      @if($repuesto?->codigo)
      <div class="meta-row">
        <span>Código</span>
        <strong style="font-family:monospace;">{{ $repuesto->codigo }}</strong>
      </div>
      @endif
      <div class="meta-row">
        <span>Precio unitario</span>
        <strong>Bs {{ number_format($repuesto?->precio_venta ?? 0, 2) }}</strong>
      </div>
      <div class="meta-row">
        <span>Cantidad</span>
        <strong>{{ $consulta->cantidad }} unidad(es)</strong>
      </div>
      <hr class="divider">
      <div class="total-row">
        <span class="total-label">Total pagado</span>
        <span class="total-amount">Bs {{ number_format(($repuesto?->precio_venta ?? 0) * $consulta->cantidad, 2) }}</span>
      </div>
    </div>

    {{-- Nota del equipo --}}
    @if($consulta->pago_notas)
    <div class="info-box">
      <strong>Nota del equipo:</strong> {{ $consulta->pago_notas }}
    </div>
    @endif

    {{-- Siguiente paso --}}
    <div class="info-box" style="background:#f0fdf4;border-color:#bbf7d0;color:#166534;">
      <strong>¿Qué sigue?</strong> Nuestro equipo se pondrá en contacto contigo para coordinar la entrega o el retiro del producto.
    </div>

  </div>

  {{-- Pie --}}
  <div class="footer">
    <strong>Taller Automotrices SC-BOL</strong><br>
    Este recibo es el comprobante oficial de tu compra.<br>
    © {{ date('Y') }} Taller Automotrices SC-BOL. Todos los derechos reservados.
  </div>

</div>
</body>
</html>
