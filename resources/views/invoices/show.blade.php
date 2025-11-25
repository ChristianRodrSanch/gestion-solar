@php
// Normalizados desde InvoiceService
$c = $customer ?? [];
$s = $shipping ?? [];

if (is_string($c)) {
    $c = json_decode($c, true) ?: [];
}
if (is_string($s)) {
    $s = json_decode($s, true) ?: [];
}

$dueDate   = $order->created_at ? $order->created_at->copy()->addDays(15) : null;
$subtotal  = $order->subtotal ?? null;
$taxAmount = $order->tax_amount ?? null;
$taxPercent= $order->tax_percent ?? null;
@endphp
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Factura #{{ $order->id }} · Enerflux</title>
  <style>
    @page {
      margin: 20mm 15mm;
    }

    * {
      box-sizing: border-box;
    }

    html, body {
      margin: 0;
      padding: 0;
    }

    body {
      background: #f3f4f6;
      color: #1f2933;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
      font-size: 12px;
      line-height: 1.4;
    }

    .page {
      background: #ffffff;
      padding: 24px 32px 32px;
    }

    .muted {
      color: #6b7280;
    }

    .title-factura {
      font-size: 40px;
      font-weight: 800;
      letter-spacing: 0.08em;
      color: #1e3a8a;
      text-transform: uppercase;
      margin: 0 0 10px;
    }

    .seller-block {
      font-size: 11px;
      line-height: 1.4;
    }

    /* HEADER con tabla (compatible Dompdf) */
    .header-table {
      width: 100%;
      border-collapse: collapse;
    }
    .header-left {
      vertical-align: top;
    }
    .header-right {
      vertical-align: top;
      text-align: right;
      width: 120px;
    }

    .logo-circle {
      width: 90px;
      height: 90px;
      border-radius: 999px;
      background: #d1d5db;
      display: inline-block;
      text-align: center;
      line-height: 90px;
      font-size: 14px;
      color: #374151;
      text-transform: uppercase;
      overflow: hidden;
    }
    .logo-circle img {
      max-width: 100%;
      max-height: 100%;
      vertical-align: middle;
    }

    /* Bloques de info principal */
    .info-row {
      width: 100%;
      margin-top: 24px;
    }
    .info-col {
      width: 33.33%;
      vertical-align: top;
      font-size: 11px;
    }

    .info-block-title {
      font-weight: 700;
      color: #1e3a8a;
      text-transform: uppercase;
      font-size: 11px;
      margin-bottom: 4px;
    }

    .info-block {
      line-height: 1.5;
    }

    .info-label {
      font-weight: 700;
      text-transform: uppercase;
      color: #1e3a8a;
      font-size: 11px;
      margin-top: 2px;
    }

    .info-value {
      font-size: 11px;
      margin-bottom: 4px;
    }

    .divider {
      margin-top: 20px;
      border-top: 2px solid #e11d48;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
      font-size: 11px;
    }

    th, td {
      padding: 7px 6px;
      border-bottom: 1px solid #e5e7eb;
    }

    th {
      text-transform: uppercase;
      font-weight: 700;
      font-size: 11px;
      color: #1e3a8a;
    }

    .right {
      text-align: right;
    }

    .center {
      text-align: center;
    }

    .summary {
      margin-top: 16px;
      width: 220px;
      margin-left: auto;
      font-size: 11px;
    }

    .summary td {
      border-bottom: none;
      padding: 3px 0;
    }

    .summary-label {
      text-align: right;
    }

    .summary-total {
      font-weight: 800;
      font-size: 13px;
      color: #1e3a8a;
      padding-top: 6px;
    }

    .summary-amount {
      text-align: right;
    }

    .footer {
      margin-top: 40px;
      width: 100%;
    }
    .footer-col {
      vertical-align: bottom;
      width: 50%;
      font-size: 10px;
    }

    .footer-thanks {
      font-family: "Brush Script MT", "Segoe Script", cursive;
      font-size: 42px;
      color: #1e3a8a;
    }

    .footer-conditions-title {
      text-transform: uppercase;
      font-weight: 700;
      font-size: 11px;
      color: #e11d48;
      margin-bottom: 6px;
    }

    .footer-conditions {
      font-size: 10px;
      line-height: 1.5;
    }

    .mt4 { margin-top: 4px; }
    .mt8 { margin-top: 8px; }

    small {
      color: #9ca3af;
      font-size: 9px;
    }
  </style>
</head>
<body>
  <div class="page">

    {{-- CABECERA PRINCIPAL (tabla para Dompdf) --}}
    <table class="header-table">
      <tr>
        <td class="header-left">
          <div class="title-factura">FACTURA</div>
          <div class="seller-block">
            <strong>{{ $seller['name'] ?? 'Enerflux' }}</strong><br>
            {{ $seller['address'] ?? '' }}<br>
            {{ $seller['email'] ?? '' }} · {{ $seller['phone'] ?? '' }}<br>
            CIF/NIF: {{ $seller['vat'] ?? '' }}
          </div>
        </td>
        <td class="header-right">
          <div class="logo-circle">
            @if(!empty($logoData))
              <img src="{{ $logoData }}" alt="Enerflux">
            @else
              <img src="{{ asset('brand/Enerflux.png') }}" alt="Enerflux">
            @endif
          </div>
        </td>
      </tr>
    </table>

    {{-- BLOQUES FACTURAR / ENVIAR / DATOS FACTURA --}}
    <table class="info-row">
      <tr>
        <td class="info-col">
          <div class="info-block">
            <div class="info-block-title">Facturar a</div>
            <div><strong>{{ $c['name'] ?? '—' }}</strong></div>
            @if(!empty($c['address']))
              <div class="mt4">
                {{ $c['address']['line1'] ?? '' }} {{ $c['address']['line2'] ?? '' }}<br>
                {{ $c['address']['postal_code'] ?? '' }} {{ $c['address']['city'] ?? '' }}<br>
                {{ $c['address']['country'] ?? '' }}
              </div>
            @endif
            <div class="mt4 muted">
              {{ $c['email'] ?? '' }}
              @if(!empty($c['phone'])) · {{ $c['phone'] }} @endif
            </div>
          </div>
        </td>

        <td class="info-col">
          <div class="info-block">
            <div class="info-block-title">Enviar a</div>
            <div><strong>{{ $s['name'] ?? '—' }}</strong></div>
            @if(!empty($s['address']))
              <div class="mt4">
                {{ $s['address']['line1'] ?? '' }} {{ $s['address']['line2'] ?? '' }}<br>
                {{ $s['address']['postal_code'] ?? '' }} {{ $s['address']['city'] ?? '' }}<br>
                {{ $s['address']['country'] ?? '' }}
              </div>
            @endif
            @if(!empty($s['phone']))
              <div class="mt4 muted">{{ $s['phone'] }}</div>
            @endif
          </div>
        </td>

        <td class="info-col">
          <div class="info-block">
            <div class="info-label">Nº de factura</div>
            <div class="info-value">#{{ $order->id }}</div>

            <div class="info-label">Fecha</div>
            <div class="info-value">
              {{ $order->created_at ? $order->created_at->format('d/m/Y') : '—' }}
            </div>

            <div class="info-label">Nº de pedido</div>
            <div class="info-value">
              {{ $order->reference ?? ('#'.$order->id) }}
            </div>

            <div class="info-label">Fecha vencimiento</div>
            <div class="info-value">
              {{ $dueDate ? $dueDate->format('d/m/Y') : '—' }}
            </div>

            <div class="info-label">Moneda</div>
            <div class="info-value">{{ strtoupper($order->currency ?? 'EUR') }}</div>

            @if(!empty($order->stripe_payment_intent_id))
              <div class="info-label">Pago (PI)</div>
              <div class="info-value">{{ $order->stripe_payment_intent_id }}</div>
            @endif
          </div>
        </td>
      </tr>
    </table>

    <div class="divider"></div>

    {{-- LÍNEAS DE PRODUCTO --}}
    <div class="mt8">
      <table>
        <thead>
          <tr>
            <th class="center" style="width:8%;">Cant.</th>
            <th>Descripción</th>
            <th class="right" style="width:18%;">Precio unitario</th>
            <th class="right" style="width:18%;">Importe</th>
          </tr>
        </thead>
        <tbody>
          @forelse($order->items as $it)
            <tr>
              <td class="center">{{ $it->quantity }}</td>
              <td>{{ $it->name }}</td>
              <td class="right">
                {{ number_format((float)$it->unit_price, 2, ',', '.') }}
                {{ strtoupper($order->currency ?? 'EUR') }}
              </td>
              <td class="right">
                {{ number_format((float)$it->line_total, 2, ',', '.') }}
                {{ strtoupper($order->currency ?? 'EUR') }}
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="right muted">No hay líneas de producto.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- RESUMEN TOTALES --}}
    <table class="summary">
      @if(!is_null($subtotal))
        <tr>
          <td class="summary-label">Subtotal</td>
          <td class="summary-amount">
            {{ number_format((float)$subtotal, 2, ',', '.') }}
            {{ strtoupper($order->currency ?? 'EUR') }}
          </td>
        </tr>
      @endif

      @if(!is_null($taxAmount))
        <tr>
          <td class="summary-label">
            IVA{{ $taxPercent ? ' '.number_format((float)$taxPercent, 1, ',', '.').'%' : '' }}
          </td>
          <td class="summary-amount">
            {{ number_format((float)$taxAmount, 2, ',', '.') }}
            {{ strtoupper($order->currency ?? 'EUR') }}
          </td>
        </tr>
      @endif

      <tr>
        <td class="summary-label summary-total">Total</td>
        <td class="summary-amount summary-total">
          {{ number_format((float)$order->amount, 2, ',', '.') }}
          {{ strtoupper($order->currency ?? 'EUR') }}
        </td>
      </tr>
    </table>

    {{-- PIE --}}
    <table class="footer">
      <tr>
        <td class="footer-col">
          <div class="footer-thanks">Gracias</div>
        </td>
        <td class="footer-col">
          <div class="footer-conditions-title">
            Condiciones y forma de pago
          </div>
          <div class="footer-conditions">
            El pago se realizará en un plazo de 15 días.<br><br>
            Banco: {{ $seller['bank_name'] ?? 'Banco Santander' }}<br>
            IBAN: {{ $seller['iban'] ?? 'ES12 3456 7891' }}<br>
            SWIFT/BIC: {{ $seller['swift'] ?? 'ABCDESM1XXX' }}
          </div>

          <div class="mt8">
            <small>Documento generado automáticamente por Enerflux.</small>
          </div>
        </td>
      </tr>
    </table>

  </div>
</body>
</html>
