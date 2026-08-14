<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Receipt - {{ $order->invoice_number }}</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 0;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 78mm;
            margin: 0 auto;
            padding: 8px;
            color: #000;
            background: #fff;
            font-size: 12px;
            line-height: 1.3;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .header { margin-bottom: 10px; border-b: 1px dashed #000; padding-bottom: 8px; }
        .company-name { font-size: 16px; font-weight: bold; text-transform: uppercase; }
        .info-row { display: flex; justify-content: space-between; font-size: 11px; margin-bottom: 2px; }
        .items-table { width: 100%; border-collapse: collapse; margin: 8px 0; border-top: 1px dashed #000; border-bottom: 1px dashed #000; }
        .items-table th { text-align: left; font-size: 10px; border-bottom: 1px solid #000; padding: 4px 0; }
        .items-table td { padding: 4px 0; font-size: 11px; vertical-align: top; }
        .totals { margin-top: 6px; border-bottom: 1px dashed #000; padding-bottom: 6px; }
        .totals-row { display: flex; justify-content: space-between; font-size: 11px; margin-bottom: 2px; }
        .grand-total { font-size: 14px; font-weight: bold; margin-top: 4px; border-top: 1px solid #000; padding-top: 4px; }
        .footer { margin-top: 12px; font-size: 10px; border-t: 1px dashed #000; padding-top: 8px; }
        
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
        
        .print-btn {
            background: #059669;
            color: white;
            border: none;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            margin-bottom: 12px;
        }
    </style>
</head>
<body>

    <button onclick="window.print()" class="no-print print-btn">🖨️ Print Thermal Receipt</button>

    <div class="header text-center">
        <div class="company-name">LANKA RETAIL ENTERPRISE</div>
        <div>No. 120, Galle Road, Colombo 03, Sri Lanka</div>
        <div>Tel: +94 11 234 5678 | VAT Reg: 123456789-V</div>
        <div style="margin-top: 6px;" class="bold">TAX INVOICE / RECEIPT</div>
    </div>

    <div class="info-row">
        <span>Invoice No:</span>
        <span class="bold">{{ $order->invoice_number }}</span>
    </div>
    <div class="info-row">
        <span>Date & Time:</span>
        <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
    </div>
    <div class="info-row">
        <span>Cashier:</span>
        <span>{{ $order->user?->name ?? 'Staff' }}</span>
    </div>
    <div class="info-row">
        <span>Customer:</span>
        <span>{{ $order->customer?->name ?? 'Walk-in Customer' }}</span>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 45%;">Item</th>
                <th style="width: 15%; text-align: center;">Qty</th>
                <th style="width: 20%; text-align: right;">Price</th>
                <th style="width: 20%; text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product?->name }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                <td class="text-right">{{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="totals-row">
            <span>Subtotal (LKR):</span>
            <span>Rs. {{ number_format($order->subtotal, 2) }}</span>
        </div>
        <div class="totals-row">
            <span>VAT / Tax (10%):</span>
            <span>Rs. {{ number_format($order->tax_amount, 2) }}</span>
        </div>
        @if($order->discount_amount > 0)
        <div class="totals-row">
            <span>Discount:</span>
            <span>- Rs. {{ number_format($order->discount_amount, 2) }}</span>
        </div>
        @endif
        <div class="totals-row grand-total">
            <span>GRAND TOTAL:</span>
            <span>Rs. {{ number_format($order->grand_total, 2) }}</span>
        </div>
        <div class="totals-row" style="margin-top: 4px;">
            <span>Paid ({{ strtoupper($order->payments->first()?->payment_method ?? 'CASH') }}):</span>
            <span>Rs. {{ number_format($order->paid_amount, 2) }}</span>
        </div>
        <div class="totals-row">
            <span>Balance / Change:</span>
            <span>Rs. {{ number_format(max(0, $order->paid_amount - $order->grand_total), 2) }}</span>
        </div>
    </div>

    <div class="footer text-center">
        <div class="bold">Thank you for shopping with us!</div>
        <div>Please keep this receipt for warranty/exchange within 7 days.</div>
        <div style="margin-top: 4px;">*** Software by LankaPOS ***</div>
    </div>

    <script>
        // Auto trigger browser print modal if opened via POS modal
        if (window.opener || window.location.search.includes('autoprint=1')) {
            window.onload = function() { window.print(); }
        }
    </script>
</body>
</html>
