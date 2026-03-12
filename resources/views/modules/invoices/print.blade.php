<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 12px; color: #333; padding: 20px; max-width: 80mm; margin: 0 auto; }
        .header { text-align: center; border-bottom: 2px dashed #333; padding-bottom: 10px; margin-bottom: 10px; }
        .header h1 { font-size: 18px; font-weight: bold; }
        .header p { font-size: 10px; color: #666; }
        .info { margin-bottom: 10px; }
        .info div { display: flex; justify-content: space-between; padding: 1px 0; }
        .info span.label { color: #666; }
        table { width: 100%; border-collapse: collapse; margin: 8px 0; }
        th { text-align: left; border-bottom: 1px solid #333; padding: 3px 0; font-size: 11px; }
        td { padding: 3px 0; font-size: 11px; }
        td:last-child, th:last-child { text-align: right; }
        .totals { border-top: 1px dashed #333; padding-top: 5px; }
        .totals div { display: flex; justify-content: space-between; padding: 2px 0; }
        .totals .grand { font-size: 16px; font-weight: bold; border-top: 2px solid #333; padding-top: 5px; margin-top: 5px; }
        .footer { text-align: center; border-top: 2px dashed #333; padding-top: 10px; margin-top: 15px; font-size: 10px; color: #666; }
        .payments { margin-top: 8px; font-size: 10px; }
        @media print { body { padding: 0; } @page { margin: 5mm; size: 80mm auto; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>RepairBox</h1>
        <p>Mobile Shop Management</p>
        <p>Contact: your-phone | your-email</p>
    </div>

    <div class="info">
        <div><span class="label">Invoice:</span><span>{{ $invoice->invoice_number }}</span></div>
        <div><span class="label">Date:</span><span>{{ $invoice->created_at->format('d/m/Y h:i A') }}</span></div>
        @if($invoice->customer)<div><span class="label">Customer:</span><span>{{ $invoice->customer->name }}</span></div>@endif
    </div>

    <table>
        <thead><tr><th>Item</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->item_name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>₹{{ number_format($item->price, 2) }}</td>
                <td>₹{{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div><span>Subtotal:</span><span>₹{{ number_format($invoice->sub_total, 2) }}</span></div>
        @if($invoice->discount > 0)<div><span>Discount:</span><span>-₹{{ number_format($invoice->discount, 2) }}</span></div>@endif
        <div class="grand"><span>Total:</span><span>₹{{ number_format($invoice->total_amount, 2) }}</span></div>
    </div>

    <div class="payments">
        <strong>Payments:</strong>
        @foreach($invoice->payments as $pay)
        <div>{{ ucfirst($pay->payment_method) }}: ₹{{ number_format($pay->amount, 2) }} {{ $pay->transaction_reference ? '('.$pay->transaction_reference.')' : '' }}</div>
        @endforeach
    </div>

    <div class="footer">
        <p>Thank you for your business!</p>
        <p>Terms & Conditions Apply</p>
    </div>

    <script>window.onload = function() { window.print(); }</script>
</body>
</html>
