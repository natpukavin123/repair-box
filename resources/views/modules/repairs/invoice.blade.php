<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Repair Invoice - {{ $repair->ticket_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 12px; color: #333; padding: 20px; max-width: 80mm; margin: 0 auto; }
        .header { text-align: center; border-bottom: 2px dashed #333; padding-bottom: 10px; margin-bottom: 10px; }
        .header h1 { font-size: 18px; font-weight: bold; }
        .header p { font-size: 10px; color: #666; }
        .title { text-align: center; font-size: 13px; font-weight: bold; text-transform: uppercase; margin-bottom: 10px; letter-spacing: 1px; }
        .info { margin-bottom: 10px; }
        .info div { display: flex; justify-content: space-between; padding: 1px 0; }
        .info span.label { color: #666; }
        .section-title { font-weight: bold; font-size: 11px; text-transform: uppercase; color: #555; margin-top: 10px; margin-bottom: 4px; border-bottom: 1px solid #ddd; padding-bottom: 2px; }
        table { width: 100%; border-collapse: collapse; margin: 4px 0 8px; }
        th { text-align: left; border-bottom: 1px solid #333; padding: 3px 0; font-size: 10px; text-transform: uppercase; color: #555; }
        td { padding: 3px 0; font-size: 11px; }
        td:last-child, th:last-child { text-align: right; }
        .totals { border-top: 1px dashed #333; padding-top: 5px; margin-top: 5px; }
        .totals div { display: flex; justify-content: space-between; padding: 2px 0; }
        .totals .grand { font-size: 14px; font-weight: bold; border-top: 2px solid #333; padding-top: 5px; margin-top: 5px; }
        .totals .balance { font-size: 13px; font-weight: bold; color: #c00; }
        .totals .paid-full { font-size: 12px; font-weight: bold; color: #090; }
        .payments { margin-top: 6px; }
        .payments div { font-size: 10px; padding: 1px 0; }
        .return-section { background: #fff5f5; border: 1px solid #fecaca; padding: 6px; margin-top: 8px; font-size: 11px; }
        .return-section .ret-title { font-weight: bold; font-size: 11px; text-align: center; text-transform: uppercase; color: #c00; margin-bottom: 4px; }
        .return-section div { display: flex; justify-content: space-between; padding: 1px 0; }
        .return-section .ret-total { font-weight: bold; font-size: 12px; border-top: 1px solid #fca5a5; padding-top: 3px; margin-top: 3px; color: #c00; }
        .net-section { background: #f0fdf4; border: 1px solid #bbf7d0; padding: 6px; margin-top: 6px; font-size: 11px; }
        .net-section div { display: flex; justify-content: space-between; padding: 1px 0; }
        .net-section .net-total { font-weight: bold; font-size: 14px; color: #059669; }
        .footer { text-align: center; border-top: 2px dashed #333; padding-top: 10px; margin-top: 15px; font-size: 10px; color: #666; }
        @media print { body { padding: 0; } @page { margin: 5mm; size: 80mm auto; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>RepairBox</h1>
        <p>Mobile Shop Management</p>
    </div>

    <div class="title">Repair Invoice</div>

    <div class="info">
        <div><span class="label">Ticket:</span><span>{{ $repair->ticket_number }}</span></div>
        <div><span class="label">Date:</span><span>{{ $repair->created_at->format('d/m/Y') }}</span></div>
        @if($repair->customer)
        <div><span class="label">Customer:</span><span>{{ $repair->customer->name }}</span></div>
        <div><span class="label">Phone:</span><span>{{ $repair->customer->mobile_number }}</span></div>
        @endif
        <div><span class="label">Device:</span><span>{{ $repair->device_brand }} {{ $repair->device_model }}</span></div>
        @if($repair->imei)<div><span class="label">IMEI:</span><span>{{ $repair->imei }}</span></div>@endif
    </div>

    <div class="section-title">Problem</div>
    <p style="font-size:11px; margin-bottom:8px; white-space:pre-line;">{{ $repair->problem_description ?: '-' }}</p>

    @if($repair->parts->count())
    <div class="section-title">Parts Used</div>
    <table>
        <thead><tr><th>Part</th><th>Qty</th><th>Amount</th></tr></thead>
        <tbody>
            @foreach($repair->parts as $part)
            <tr>
                <td>{{ $part->part ? $part->part->name : 'Part' }}</td>
                <td>{{ $part->quantity }}</td>
                <td>₹{{ number_format($part->cost_price * $part->quantity, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if($repair->repairServices->count())
    <div class="section-title">Services</div>
    <table>
        <thead><tr><th>Service</th><th>Amount</th></tr></thead>
        <tbody>
            @foreach($repair->repairServices as $svc)
            <tr>
                <td>{{ $svc->service_type_name }}</td>
                <td>₹{{ number_format($svc->customer_charge, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @php
        $partsTotal = $repair->parts->sum(fn($p) => $p->cost_price * $p->quantity);
        $servicesTotal = $repair->repairServices->sum('customer_charge');
        $serviceCharge = $repair->service_charge ?? 0;
        $grandTotal = $partsTotal + $servicesTotal + $serviceCharge;
        $totalPaidIn = $repair->payments->where('direction', 'IN')->sum('amount');
        $totalRefunded = $repair->payments->where('direction', 'OUT')->sum('amount');
        $totalReturned = $repair->repairReturns->sum('total_return_amount');
        $netAfterReturns = $grandTotal - $totalReturned;
        $balanceDue = max(0, $grandTotal - $totalPaidIn);
    @endphp

    <div class="totals">
        <div><span>Parts Total:</span><span>₹{{ number_format($partsTotal, 2) }}</span></div>
        @if($servicesTotal > 0)
        <div><span>Other Services:</span><span>₹{{ number_format($servicesTotal, 2) }}</span></div>
        @endif
        <div><span>Our Service Fee:</span><span>₹{{ number_format($serviceCharge, 2) }}</span></div>
        <div class="grand"><span>Grand Total:</span><span>₹{{ number_format($grandTotal, 2) }}</span></div>
    </div>

    {{-- ===== RETURNS SECTION (only if returns exist) ===== --}}
    @if($repair->repairReturns->count() > 0)
    <div class="return-section">
        <div class="ret-title">Returns / Credit Notes</div>
        @foreach($repair->repairReturns as $ret)
        <div>
            <span>{{ $ret->return_number }} ({{ $ret->created_at->format('d/m/Y') }})</span>
            <span style="color:#c00;">-₹{{ number_format($ret->total_return_amount, 2) }}</span>
        </div>
        @if($ret->items->count())
            @foreach($ret->items as $retItem)
            <div style="font-size:10px; color:#888; padding-left:8px;">
                <span>↳ {{ $retItem->item_name }} ×{{ $retItem->quantity }}</span>
                <span>-₹{{ number_format($retItem->return_amount, 2) }}</span>
            </div>
            @endforeach
        @endif
        @endforeach
        <div class="ret-total"><span>Total Returned:</span><span>-₹{{ number_format($totalReturned, 2) }}</span></div>
    </div>

    <div class="net-section">
        <div class="net-total"><span>Net Amount (after returns):</span><span>₹{{ number_format($netAfterReturns, 2) }}</span></div>
    </div>
    @endif

    {{-- ===== PAYMENTS ===== --}}
    @if($repair->payments->count())
    <div class="section-title" style="margin-top:8px">Payments</div>
    <div class="payments">
        @foreach($repair->payments as $pay)
        <div style="display:flex; justify-content:space-between;">
            <span>
                {{ ucfirst($pay->payment_type) }} ({{ ucfirst($pay->payment_method) }})
                @if($pay->direction === 'OUT') <span style="color:#c00;">[Refund]</span> @endif
            </span>
            <span style="{{ $pay->direction === 'OUT' ? 'color:#c00;' : '' }}">
                {{ $pay->direction === 'OUT' ? '-' : '' }}₹{{ number_format($pay->amount, 2) }}
            </span>
        </div>
        @endforeach
    </div>
    @endif

    <div class="totals" style="margin-top:6px;">
        <div><span>Total Paid (IN):</span><span style="color:#090; font-weight:bold;">₹{{ number_format($totalPaidIn, 2) }}</span></div>
        @if($totalRefunded > 0)
        <div><span>Total Refunded (OUT):</span><span style="color:#c00; font-weight:bold;">-₹{{ number_format($totalRefunded, 2) }}</span></div>
        <div><span>Net Paid:</span><span style="font-weight:bold;">₹{{ number_format($totalPaidIn - $totalRefunded, 2) }}</span></div>
        @endif
        @if($balanceDue > 0)
        <div class="balance"><span>Balance Due:</span><span>₹{{ number_format($balanceDue, 2) }}</span></div>
        @else
        <div class="paid-full"><span>✓ PAID IN FULL</span><span></span></div>
        @endif
    </div>

    <div class="footer">
        <p>Thank you for choosing RepairBox!</p>
        <p>Tracking ID: {{ $repair->tracking_id }}</p>
        @if($repair->repairReturns->count() > 0)
        <p style="margin-top:4px; font-size:9px; color:#999;">This invoice includes {{ $repair->repairReturns->count() }} return(s). Last updated: {{ $repair->repairReturns->max('created_at')->format('d/m/Y h:i A') }}</p>
        @endif
    </div>

    <script>window.onload = function() { window.print(); }</script>
</body>
</html>
