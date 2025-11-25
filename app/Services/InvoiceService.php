<?php

namespace App\Services;

use App\Models\Order;
use Dompdf\Dompdf;
use Dompdf\Options;

class InvoiceService
{
    /**
     * Genera el HTML de la factura usando la vista Blade invoices.show
     */
    public function renderHtml(Order $order): string
    {
        // Aseguramos usuario e items
        $order->loadMissing('user', 'items');

        // Normalizamos billing_snapshot (string JSON o array)
        $raw = $order->billing_snapshot;

        if (is_string($raw)) {
            $snapshot = json_decode($raw, true) ?: [];
        } elseif (is_array($raw)) {
            $snapshot = $raw;
        } else {
            $snapshot = [];
        }

        $customer = $snapshot['customer'] ?? [];
        $shipping = $snapshot['shipping'] ?? [];

        // Datos fijos del vendedor
        $seller = [
            'name'    => 'Enerflux',
            'address' => 'C/ Renovable, 123 · 04001 Almería',
            'email'   => 'info@enerflux.local',
            'phone'   => '+34 600 000 000',
            'vat'     => 'ESX12345678',
            // si quieres IBAN / banco puedes añadirlos
            'bank_name' => 'Banco Santander',
            'iban'      => 'ES12 3456 7891',
            'swift'     => 'ABCDESM1XXX',
        ];

        // Logo en base64 para Dompdf
        $logoData = null;
        $logoPath = public_path('brand/Enerflux.png'); // asegúrate de que existe

        if (is_readable($logoPath)) {
            $type = pathinfo($logoPath, PATHINFO_EXTENSION);
            $data = file_get_contents($logoPath);
            $logoData = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        // Renderizamos la vista
        return view('invoices.show', [
            'order'    => $order,
            'seller'   => $seller,
            'logoData' => $logoData,
            'customer' => $customer,
            'shipping' => $shipping,
        ])->render();
    }

    /**
     * Genera el PDF (bytes) a partir del HTML anterior.
     */
    public function renderPdf(Order $order): string
    {
        $options = new Options();
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($this->renderHtml($order));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }
}
