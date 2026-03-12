<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Repair Ticket #{{ $repair->ticket_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 12px; color: #333; padding: 20px; max-width: 80mm; margin: 0 auto; }
        .header { text-align: center; border-bottom: 2px dashed #333; padding-bottom: 10px; margin-bottom: 10px; }
        .header h1 { font-size: 18px; } .header p { font-size: 10px; color: #666; }
        .section { margin-bottom: 10px; }
        .section h3 { font-size: 12px; border-bottom: 1px solid #ccc; padding-bottom: 3px; margin-bottom: 5px; }
        .row { display: flex; justify-content: space-between; padding: 2px 0; }
        .label { color: #666; font-size: 10px; } .value { font-weight: 600; font-size: 11px; }
        .tracking { text-align: center; margin: 10px 0; padding: 8px; background: #f0f0f0; border-radius: 5px; }
        .tracking .code { font-size: 16px; font-weight: bold; letter-spacing: 2px; }
        .footer { text-align: center; border-top: 2px dashed #333; padding-top: 10px; margin-top: 15px; font-size: 10px; color: #666; }
        @media print { body { padding: 0; } @page { margin: 5mm; size: 80mm auto; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>RepairBox</h1>
        <p>Repair Service Receipt</p>
    </div>

    <div class="section">
        <div class="row"><span class="label">Ticket #:</span><span class="value">{{ $repair->ticket_number }}</span></div>
        <div class="row"><span class="label">Date:</span><span class="value">{{ $repair->created_at->format('d/m/Y h:i A') }}</span></div>
        @if($repair->customer)<div class="row"><span class="label">Customer:</span><span class="value">{{ $repair->customer->name }}</span></div>
        <div class="row"><span class="label">Phone:</span><span class="value">{{ $repair->customer->mobile_number }}</span></div>@endif
    </div>

    <div class="section">
        <h3>Device Information</h3>
        <div class="row"><span class="label">Brand/Model:</span><span class="value">{{ $repair->device_brand }} {{ $repair->device_model }}</span></div>
        @if($repair->imei)<div class="row"><span class="label">IMEI:</span><span class="value">{{ $repair->imei }}</span></div>@endif
    </div>

    <div class="section">
        <h3>Problem</h3>
        <p style="font-size:11px; white-space:pre-line;">{{ $repair->problem_description }}</p>
    </div>

    <div class="section">
        <div class="row"><span class="label">Status:</span><span class="value">{{ ucfirst(str_replace('_',' ',$repair->status)) }}</span></div>
        <div class="row"><span class="label">Est. Cost:</span><span class="value">₹{{ number_format($repair->estimated_cost, 2) }}</span></div>
        @if($repair->expected_delivery_date)<div class="row"><span class="label">Expected Delivery:</span><span class="value">{{ \Carbon\Carbon::parse($repair->expected_delivery_date)->format('d/m/Y') }}</span></div>@endif
    </div>

    <div class="tracking">
        <p style="font-size:10px; color:#666;">Track your repair online:</p>
        <p class="code">{{ $repair->tracking_id }}</p>
    </div>

    <div class="footer">
        <p>Keep this receipt for tracking your repair.</p>
        <p>Terms & Conditions Apply</p>
    </div>

    <script>window.onload = function() { window.print(); }</script>
</body>
</html>
