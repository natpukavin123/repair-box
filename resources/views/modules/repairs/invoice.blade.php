<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tax Invoice – {{ $repair->ticket_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

    @php
        $shopName        = \App\Models\Setting::getValue('shop_name',        'Shree Mobile Shop');
        $shopAddress     = \App\Models\Setting::getValue('shop_address',     'G/456, Basant Lok Comm., Vasant Vihar, Tardeo Road, Maharashtra - 400001');
        $shopPhone       = \App\Models\Setting::getValue('shop_phone',       '111 2222 3333');
        $shopEmail       = \App\Models\Setting::getValue('shop_email',       '<a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="f29b9c949db2819a8097979f9d909b9e97dc919d9f">[email&#160;protected]</a>');
        $shopGstin       = \App\Models\Setting::getValue('shop_gstin',       '');
        $shopSlogan      = \App\Models\Setting::getValue('shop_slogan',      'Your Trusted Mobile Partner');
        $shopUpiId       = \App\Models\Setting::getValue('upi_id',           'ifox@icici');
        $invoiceHeaderBanner = \App\Models\Setting::getValue('invoice_header_banner', 'Get All Your Desired Smart Phones From Apple To Vivo On Huge Discounts And Easy EMI');
        $invoiceFooterText   = \App\Models\Setting::getValue('invoice_footer_text',   'Subject to Maharashtra Junction. Our Responsibility Ceases as soon as goods leave our Premises. Goods once sold will not be taken back. Delivery Ex-Premises.');

        /* Tax Rates — 0 for now */
        $igstRate = 0;
        $cgstRate = 0;
        $sgstRate = 0;

        $customerGstin   = $repair->customer->gstin   ?? '-';
        $customerAddress = $repair->customer->address ?? '-';
        $placeOfSupply   = $repair->customer->state   ?? '-';

        $lineItems = collect();
        foreach ($repair->parts as $part) {
            $tax = $igstRate > 0 ? round($part->cost_price * $part->quantity * $igstRate / 100, 2) : 0;
            $lineItems->push([
                'name'    => $part->part ? $part->part->name : 'Part',
                'sub'     => $part->imei ?? null,
                'hsn'     => $part->part->hsn_code ?? '',
                'qty'     => $part->quantity,
                'rate'    => $part->cost_price,
                'taxable' => $part->cost_price * $part->quantity,
                'igst'    => $tax,
                'total'   => $part->cost_price * $part->quantity + $tax,
            ]);
        }
        foreach ($repair->repairServices as $svc) {
            $tax = $igstRate > 0 ? round($svc->customer_charge * $igstRate / 100, 2) : 0;
            $lineItems->push([
                'name'    => $svc->service_type_name, 'sub' => null, 'hsn' => '', 'qty' => 1,
                'rate'    => $svc->customer_charge,   'taxable' => $svc->customer_charge,
                'igst'    => $tax, 'total' => $svc->customer_charge + $tax,
            ]);
        }
        $serviceCharge = $repair->service_charge ?? 0;
        if ($serviceCharge > 0) {
            $tax = $igstRate > 0 ? round($serviceCharge * $igstRate / 100, 2) : 0;
            $lineItems->push(['name'=>'Service Charge','sub'=>null,'hsn'=>'','qty'=>1,
                'rate'=>$serviceCharge,'taxable'=>$serviceCharge,'igst'=>$tax,'total'=>$serviceCharge+$tax]);
        }

        $taxableAmount   = $lineItems->sum('taxable');
        $igstAmount      = $lineItems->sum('igst');
        $cgstAmount      = $cgstRate > 0 ? round($taxableAmount * $cgstRate / 100, 2) : 0;
        $sgstAmount      = $sgstRate > 0 ? round($taxableAmount * $sgstRate / 100, 2) : 0;
        $totalTax        = $igstAmount + $cgstAmount + $sgstAmount;
        $grandTotal      = $taxableAmount + $totalTax;
        $totalQty        = $lineItems->sum('qty');
        $totalPaidIn     = $repair->payments->where('direction','IN')->sum('amount');
        $totalRefunded   = $repair->payments->where('direction','OUT')->sum('amount');
        $totalReturned   = $repair->repairReturns->sum('total_return_amount');
        $netAfterReturns = $grandTotal - $totalReturned;
        $balanceDue      = max(0, $grandTotal - $totalPaidIn);

        function numWords(float $n): string {
            $o=['','ONE','TWO','THREE','FOUR','FIVE','SIX','SEVEN','EIGHT','NINE','TEN','ELEVEN',
                'TWELVE','THIRTEEN','FOURTEEN','FIFTEEN','SIXTEEN','SEVENTEEN','EIGHTEEN','NINETEEN'];
            $t=['','','TWENTY','THIRTY','FORTY','FIFTY','SIXTY','SEVENTY','EIGHTY','NINETY'];
            $c=function(int $x)use($o,$t,&$c):string{
                if($x<20)  return $o[$x];
                if($x<100) return $t[(int)($x/10)].($x%10?' '.$o[$x%10]:'');
                if($x<1000)return $o[(int)($x/100)].' HUNDRED'.($x%100?' '.$c($x%100):'');
                if($x<100000)   return $c((int)($x/1000)).' THOUSAND'.($x%1000?' '.$c($x%1000):'');
                if($x<10000000) return $c((int)($x/100000)).' LAKH'.($x%100000?' '.$c($x%100000):'');
                return $c((int)($x/10000000)).' CRORE'.($x%10000000?' '.$c($x%10000000):'');
            };
            return $c((int)$n).' RUPEES ONLY';
        }
        $amountInWords = numWords($grandTotal);
        $showIgst = $igstRate > 0;
        $showCgstSgst = !$showIgst && ($cgstRate > 0 || $sgstRate > 0);
    @endphp

    <style>
        * { margin:0; padding:0; box-sizing:border-box; }

        body {
            font-family: 'DM Sans', sans-serif;
            font-size: 10.5px;
            color: #111;
            background: #ccc;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 8mm auto;
            background: #f0f0f0;
            box-shadow: 0 8px 40px rgba(0,0,0,0.18);
            padding: 10mm;
        }

        /* Inner white card with all content */
        .invoice-card {
            background: #fff;
            border: 1px solid #ccc;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }

        @page { size: A4; margin: 10mm; }
        @media print {
            body { background: #fff; }
            .page {
                margin: 0; padding: 0;
                box-shadow: none; width: 100%;
                background: #fff;
            }
            .invoice-card { box-shadow: none; border: 1px solid #ccc; }
        }

        /* ═══════════════════════════════
           HEADER
        ═══════════════════════════════ */
        .hdr {
            background: #111;
            padding: 18px 22px 16px;
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .logo-ring {
            width: 60px; height: 60px;
            border: 2px solid #fff;
            border-radius: 50%;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            flex-shrink: 0;
            background: rgba(255,255,255,0.06);
        }
        .logo-ring .lr1 { font-size: 8px; font-weight: 700; color: #fff; letter-spacing: 1px; text-align: center; line-height: 1.3; }
        .logo-ring .lr2 { font-size: 6px; color: rgba(255,255,255,0.4); text-align: center; margin-top: 2px; }

        .hdr-text { flex: 1; }
        .hdr-text .shop-name {
            font-family: 'Playfair Display', serif;
            font-size: 26px; font-weight: 900;
            color: #fff; letter-spacing: -0.3px; line-height: 1;
        }
        .hdr-text .shop-tag {
            font-size: 9px; color: rgba(255,255,255,0.45);
            letter-spacing: 1.5px; text-transform: uppercase; margin-top: 4px;
        }
        .hdr-text .shop-contact {
            font-size: 9.5px; color: rgba(255,255,255,0.65);
            margin-top: 8px; line-height: 1.8;
        }

        /* Brand grid on right */
        .hdr-brands { display: flex; flex-direction: column; gap: 5px; align-items: flex-end; }
        .brand-row  { display: flex; gap: 4px; flex-wrap: wrap; justify-content: flex-end; }
        .bp {
            padding: 2px 7px; border-radius: 2px;
            font-size: 8px; font-weight: 700; color: #fff;
            letter-spacing: 0.3px;
        }
        .bp-apple   { background: #2c2c2e; border: 1px solid #444; }
        .bp-samsung { background: #1428a0; }
        .bp-mi      { background: #f47920; }
        .bp-oneplus { background: #eb0029; }
        .bp-google  { background: #4285f4; }
        .bp-oppo    { background: #1d6b36; }
        .bp-huawei  { background: #c0392b; }
        .bp-vivo    { background: #415fff; }
        .bp-nokia   { background: #124191; }
        .bp-asus    { background: #444; border: 1px solid #666; }
        .bp-sony    { background: #555; }
        .bp-honor   { background: #7d1515; }

        /* Thin white rule then black rule */
        .hdr-rule {
            height: 4px;
            background: #fff;
            border-bottom: 2px solid #111;
        }

        /* Promo banner */
        .banner {
            background: #f5f5f5;
            border-bottom: 1px solid #ddd;
            text-align: center;
            font-size: 9.5px; font-style: italic;
            color: #555; padding: 5px 20px;
            letter-spacing: 0.2px;
        }

        /* ═══════════════════════════════
           TITLE ROW
        ═══════════════════════════════ */
        .title-row {
            display: flex; align-items: stretch;
            border-top: 2px solid #111;
            border-bottom: 2px solid #111;
        }
        .gstin-blk {
            padding: 9px 16px;
            font-size: 11px; font-weight: 700; color: #111;
            border-right: 1.5px solid #111;
            display: flex; align-items: center; gap: 5px;
            background: #f5f5f5;
        }
        .gstin-blk .gv { font-weight: 400; color: #555; font-size: 10.5px; }
        .title-mid {
            flex: 1; display: flex; align-items: center;
            justify-content: center; padding: 6px;
            background: #fff;
        }
        .title-mid h2 {
            font-family: 'Playfair Display', serif;
            font-size: 20px; font-weight: 900; color: #111;
            letter-spacing: 5px; text-transform: uppercase;
        }
        .orig-blk {
            padding: 9px 14px; font-size: 9px; font-weight: 700;
            color: #111; border-left: 1.5px solid #111;
            background: #f5f5f5;
            display: flex; align-items: center; justify-content: center;
            letter-spacing: 0.3px; text-align: center; line-height: 1.5;
        }

        /* ═══════════════════════════════
           INFO GRID
        ═══════════════════════════════ */
        .info-grid { display: flex; border-bottom: 2px solid #111; }
        .info-col  { flex: 1; }
        .info-col + .info-col { border-left: 1.5px solid #bbb; }

        .col-hdr {
            background: #111; color: #fff;
            font-size: 8.5px; font-weight: 700;
            letter-spacing: 1.5px; text-transform: uppercase;
            padding: 5px 14px;
        }
        .col-body { padding: 10px 14px; }
        .irow { display: flex; padding: 2.5px 0; }
        .irow .lb {
            font-weight: 600; color: #666; font-size: 9.5px;
            min-width: 95px; flex-shrink: 0;
        }
        .irow .vl { color: #111; font-size: 10.5px; }
        .inv-num {
            font-family: 'Playfair Display', serif;
            font-size: 26px; font-weight: 900; color: #111; line-height: 1;
        }

        /* ═══════════════════════════════
           ITEMS TABLE
        ═══════════════════════════════ */
        .tbl-wrap { border-bottom: 2px solid #111; }
        table.items { width: 100%; border-collapse: collapse; font-size: 10px; }

        table.items thead th {
            background: #111; color: #fff;
            font-weight: 600; font-size: 8.5px;
            letter-spacing: 0.8px; text-transform: uppercase;
            padding: 8px 8px;
            border-right: 1px solid #333;
            text-align: center;
        }
        table.items thead th:first-child { border-left: none; }
        table.items thead th.tl { text-align: left; }
        table.items thead tr.th2 th {
            background: #333; font-size: 8px; padding: 3px 8px;
            border-right: 1px solid #555;
        }

        table.items tbody tr { border-bottom: 1px solid #e8e8e8; }
        table.items tbody tr:nth-child(even) { background: #f9f9f9; }
        table.items tbody td {
            padding: 7px 8px;
            border-right: 1px solid #e8e8e8;
            vertical-align: top;
        }
        table.items tbody td:last-child { border-right: none; }

        .tc { text-align: center; }
        .tr { text-align: right; font-feature-settings: "tnum"; }
        .item-sub { font-size: 8.5px; color: #888; font-style: italic; margin-top: 2px; }

        /* Subtotal row inside body */
        table.items tfoot tr { border-top: 2px solid #111; }
        table.items tfoot td {
            background: #111; color: #fff;
            font-weight: 700; font-size: 10px;
            padding: 8px 8px;
            text-align: right;
            border-right: 1px solid #333;
        }
        table.items tfoot td.tc { text-align: center; }
        table.items tfoot td:last-child { border-right: none; }

        /* ═══════════════════════════════
           BOTTOM — words | tax summary
        ═══════════════════════════════ */
        .bottom { display: flex; border-bottom: 2px solid #111; }
        .b-left  { flex: 1; border-right: 1.5px solid #bbb; }
        .b-right { width: 230px; }

        .s-hdr {
            background: #111; color: #fff;
            font-size: 8.5px; font-weight: 700;
            letter-spacing: 1.5px; text-transform: uppercase;
            padding: 5px 13px;
        }
        .s-body { padding: 10px 13px; }

        /* Amount in words */
        .words-box {
            border: 1px solid #111;
            padding: 8px 11px;
            font-size: 10px; font-weight: 600; color: #111;
            letter-spacing: 0.3px; line-height: 1.5;
            background: #f9f9f9;
        }

        /* Payments list */
        .pay-item {
            display: flex; justify-content: space-between;
            padding: 4px 0; font-size: 10px;
            border-bottom: 1px dashed #ddd;
        }
        .pay-item:last-child { border-bottom: none; }
        .p-in  { color: #1a6e3a; font-weight: 700; }
        .p-out { color: #c0392b; font-weight: 700; }

        /* QR area (no bank details) */
        .qr-area {
            margin-top: 10px;
            display: flex; align-items: center; gap: 10px;
            padding: 8px; background: #f5f5f5; border: 1px solid #ddd;
        }
        .qr-box {
            width: 72px; height: 72px; border: 1.5px solid #111;
            display: flex; align-items: center; justify-content: center;
            font-size: 8px; color: #888; text-align: center;
            flex-shrink: 0; background: #fff;
        }
        .qr-meta .qr-upi  { font-weight: 700; font-size: 10.5px; color: #111; }
        .qr-meta .qr-scan { font-size: 8.5px; color: #666; margin-top: 3px; }

        /* Tax summary table */
        .tax-tbl { width: 100%; border-collapse: collapse; font-size: 10.5px; }
        .tax-tbl td { padding: 5px 13px; border-bottom: 1px solid #eee; }
        .tax-tbl td:last-child { text-align: right; font-weight: 600; font-feature-settings: "tnum"; }
        .tax-tbl .sep td { border-top: 1.5px solid #bbb; }
        .tax-tbl .grand td {
            background: #111; color: #fff;
            font-family: 'Playfair Display', serif;
            font-size: 13px; font-weight: 700;
            padding: 10px 13px; border: none;
        }
        .tax-tbl .note  td { font-size: 8px; color: #aaa; border: none; padding-top: 1px; }
        .tax-tbl .bal   td { color: #c0392b; font-weight: 700; font-size: 12px; }
        .tax-tbl .green td { color: #1a6e3a; font-weight: 700; }
        .tax-tbl .full  td { color: #1a6e3a; font-weight: 700; text-align: center; font-size: 11px; }

        /* Sign box */
        .sign-box { padding: 10px 13px; text-align: center; }
        .sign-cert { font-size: 8px; color: #aaa; line-height: 1.5; }
        .sign-for  {
            font-family: 'Playfair Display', serif;
            font-size: 12px; font-weight: 700;
            color: #111; margin-top: 8px;
        }
        .sign-line { border-top: 1.5px solid #111; margin: 34px 10px 4px; }
        .sign-auth { font-size: 8.5px; font-weight: 700; color: #555; letter-spacing: 1px; text-transform: uppercase; }

        /* Returns */
        .ret-wrap { margin: 10px 14px; }
        .ret-box  { border: 1.5px solid #fca5a5; }
        .ret-hdr  { background: #fee2e2; padding: 5px 10px; font-weight: 700; color: #c0392b; font-size: 10px; text-align: center; letter-spacing: 0.5px; }
        .ret-body { padding: 7px 10px; font-size: 10px; }
        .ret-row  { display: flex; justify-content: space-between; padding: 2px 0; }
        .neg      { color: #c0392b; }
        .ret-tot  { border-top: 1px solid #fca5a5; margin-top: 4px; padding-top: 4px; font-weight: 700; }
        .net-box  {
            margin-top: 6px; background: #f0fdf4; border: 1.5px solid #86efac;
            display: flex; justify-content: space-between;
            padding: 6px 10px; font-weight: 700; font-size: 11px; color: #1a6e3a;
        }

        /* ═══════════════════════════════
           T&C + FOOTER
        ═══════════════════════════════ */
        .tc-row { display: flex; border-bottom: 1px solid #ddd; }
        .tc-col { flex: 1; padding: 9px 14px; font-size: 9.5px; color: #555; line-height: 1.7; }
        .tc-col + .tc-col { border-left: 1.5px solid #ddd; }
        .tc-hdr { font-weight: 700; color: #111; margin-bottom: 4px; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; }

        .doc-foot {
            background: #111; color: rgba(255,255,255,0.5);
            text-align: center; font-size: 8.5px;
            padding: 6px 20px; letter-spacing: 0.3px;
        }
        .doc-foot strong { color: #fff; }

        /* ─ Utility ─ */
        .mt4 { margin-top: 4px; }
        .mt8 { margin-top: 8px; }
    </style>
</head>
<body>
<div class="page">
<div class="invoice-card">

    {{-- ══ HEADER ══ --}}
    <div class="hdr">
        <div class="logo-ring">
            <div class="lr1">PHONE<br>SHOP</div>
            <div class="lr2">{{ $shopSlogan }}</div>
        </div>

        <div class="hdr-text">
            <div class="shop-name">{{ $shopName }}</div>
            <div class="shop-tag">{{ $shopSlogan }}</div>
            <div class="shop-contact">
                📍 {{ $shopAddress }}<br>
                📞 {{ $shopPhone }}@if($shopEmail) &nbsp;·&nbsp; ✉ {{ $shopEmail }}@endif
            </div>
        </div>

        <div class="hdr-brands">
            <div class="brand-row">
                <span class="bp bp-apple">Apple</span>
                <span class="bp bp-samsung">SAMSUNG</span>
                <span class="bp bp-mi">mi</span>
                <span class="bp bp-oneplus">OnePlus</span>
                <span class="bp bp-google">Google</span>
                <span class="bp bp-oppo">oppo</span>
            </div>
            <div class="brand-row">
                <span class="bp bp-huawei">HUAWEI</span>
                <span class="bp bp-vivo">vivo</span>
                <span class="bp bp-nokia">NOKIA</span>
                <span class="bp bp-asus">ASUS</span>
                <span class="bp bp-sony">SONY</span>
                <span class="bp bp-honor">HONOR</span>
            </div>
        </div>
    </div>

    <div class="hdr-rule"></div>
    <div class="banner">{{ $invoiceHeaderBanner }}</div>

    {{-- ══ TITLE ROW ══ --}}
    <div class="title-row">
        <div class="gstin-blk">
            GSTIN&nbsp;:&nbsp;<span class="gv">{{ $shopGstin ?: 'N/A' }}</span>
        </div>
        <div class="title-mid"><h2>Tax Invoice</h2></div>
        <div class="orig-blk">ORIGINAL<br>FOR RECIPIENT</div>
    </div>

    {{-- ══ INFO GRID ══ --}}
    <div class="info-grid">
        <div class="info-col">
            <div class="col-hdr">Customer Detail</div>
            <div class="col-body">
                @if($repair->customer)
                <div class="irow"><span class="lb">Name</span><span class="vl">{{ $repair->customer->name }}</span></div>
                <div class="irow"><span class="lb">Address</span><span class="vl">{{ $customerAddress }}</span></div>
                <div class="irow"><span class="lb">Phone</span><span class="vl">{{ $repair->customer->mobile_number ?? '-' }}</span></div>
                <div class="irow"><span class="lb">GSTIN</span><span class="vl">{{ $customerGstin }}</span></div>
                <div class="irow"><span class="lb">Place of Supply</span><span class="vl">{{ $placeOfSupply }}</span></div>
                @else
                <div class="irow"><span class="lb">Name</span><span class="vl">Walk-in Customer</span></div>
                @endif
            </div>
        </div>

        <div class="info-col" style="max-width:215px;">
            <div class="col-hdr">Invoice Detail</div>
            <div class="col-body">
                <div class="irow"><span class="lb">Invoice No.</span><span class="vl inv-num">{{ $repair->ticket_number }}</span></div>
                <div style="height:6px;"></div>
                <div class="irow"><span class="lb">Invoice Date</span><span class="vl">{{ $repair->created_at->format('d M Y') }}</span></div>
                <div class="irow"><span class="lb">Device</span><span class="vl">{{ $repair->device_brand }} {{ $repair->device_model }}</span></div>
                @if($repair->imei)
                <div class="irow"><span class="lb">IMEI</span><span class="vl">{{ $repair->imei }}</span></div>
                @endif
            </div>
        </div>
    </div>

    {{-- ══ ITEMS TABLE ══ --}}
    <div class="tbl-wrap">
        <table class="items">
            <thead>
                <tr>
                    <th style="width:26px;">Sr.</th>
                    <th class="tl">Name of Product / Service</th>
                    <th style="width:44px;">HSN/<br>SAC</th>
                    <th style="width:52px;">Qty</th>
                    <th style="width:78px;">Rate (₹)</th>
                    <th style="width:82px;">Taxable Value</th>
                    @if($showIgst)
                        <th colspan="2" style="width:100px;">IGST</th>
                    @elseif($showCgstSgst)
                        <th colspan="2">CGST</th>
                        <th colspan="2">SGST</th>
                    @endif
                    <th style="width:82px;">Total (₹)</th>
                </tr>
                @if($showIgst)
                <tr class="th2">
                    <th></th><th></th><th></th><th></th><th></th><th></th>
                    <th style="width:28px;">%</th><th style="width:72px;">Amount</th>
                    <th></th>
                </tr>
                @elseif($showCgstSgst)
                <tr class="th2">
                    <th></th><th></th><th></th><th></th><th></th><th></th>
                    <th>%</th><th>Amt</th><th>%</th><th>Amt</th><th></th>
                </tr>
                @endif
            </thead>
            <tbody>
                @foreach($lineItems as $idx => $item)
                <tr>
                    <td class="tc">{{ $idx + 1 }}</td>
                    <td>
                        <strong>{{ $item['name'] }}</strong>
                        @if($item['sub'])<div class="item-sub">IMEI {{ $item['sub'] }}</div>@endif
                    </td>
                    <td class="tc">{{ $item['hsn'] }}</td>
                    <td class="tc">{{ number_format($item['qty']) }} NOS</td>
                    <td class="tr">{{ number_format($item['rate'], 2) }}</td>
                    <td class="tr">{{ number_format($item['taxable'], 2) }}</td>
                    @if($showIgst)
                        <td class="tc">{{ $igstRate }}.00</td>
                        <td class="tr">{{ number_format($item['igst'], 2) }}</td>
                    @elseif($showCgstSgst)
                        <td class="tc">{{ $cgstRate }}.00</td>
                        <td class="tr">{{ number_format(round($item['taxable']*$cgstRate/100,2),2) }}</td>
                        <td class="tc">{{ $sgstRate }}.00</td>
                        <td class="tr">{{ number_format(round($item['taxable']*$sgstRate/100,2),2) }}</td>
                    @endif
                    <td class="tr"><strong>{{ number_format($item['total'], 2) }}</strong></td>
                </tr>
                @endforeach

                @for($e=0; $e < max(0, 7-$lineItems->count()); $e++)
                <tr style="height:26px;">
                    <td></td><td></td><td></td><td></td><td></td><td></td>
                    @if($showIgst)<td></td><td></td>
                    @elseif($showCgstSgst)<td></td><td></td><td></td><td></td>
                    @endif
                    <td></td>
                </tr>
                @endfor
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align:right;letter-spacing:1px;">TOTAL</td>
                    <td class="tc">{{ number_format($totalQty) }}</td>
                    <td></td>
                    <td>{{ number_format($taxableAmount, 2) }}</td>
                    @if($showIgst)
                        <td></td><td>{{ number_format($igstAmount,2) }}</td>
                    @elseif($showCgstSgst)
                        <td></td><td>{{ number_format($cgstAmount,2) }}</td>
                        <td></td><td>{{ number_format($sgstAmount,2) }}</td>
                    @endif
                    <td>{{ number_format($lineItems->sum('total'),2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- ══ RETURNS ══ --}}
    @if($repair->repairReturns->count() > 0)
    <div class="ret-wrap">
        <div class="ret-box">
            <div class="ret-hdr">⟵ Returns / Credit Notes</div>
            <div class="ret-body">
                @foreach($repair->repairReturns as $ret)
                <div class="ret-row">
                    <span>{{ $ret->return_number }} ({{ $ret->created_at->format('d/m/Y') }})</span>
                    <span class="neg">−₹{{ number_format($ret->total_return_amount,2) }}</span>
                </div>
                @if($ret->items->count())
                    @foreach($ret->items as $ri)
                    <div class="ret-row" style="font-size:9px;color:#aaa;padding-left:12px;">
                        <span>↳ {{ $ri->item_name }} ×{{ $ri->quantity }}</span>
                        <span class="neg">−₹{{ number_format($ri->return_amount,2) }}</span>
                    </div>
                    @endforeach
                @endif
                @endforeach
                <div class="ret-row ret-tot">
                    <span>Total Returned</span>
                    <span class="neg">−₹{{ number_format($totalReturned,2) }}</span>
                </div>
            </div>
        </div>
        <div class="net-box">
            <span>Net Amount (after returns)</span>
            <span>₹{{ number_format($netAfterReturns,2) }}</span>
        </div>
    </div>
    @endif

    {{-- ══ BOTTOM SECTION ══ --}}
    <div class="bottom">

        {{-- LEFT: words + payments + UPI QR only --}}
        <div class="b-left">
            <div class="s-hdr">Amount in Words</div>
            <div class="s-body">
                <div class="words-box">{{ $amountInWords }}</div>
            </div>

            @if($repair->payments->count())
            <div class="s-hdr mt4">Payments Received</div>
            <div class="s-body">
                @foreach($repair->payments as $pay)
                <div class="pay-item">
                    <span>
                        {{ ucfirst($pay->payment_type) }} · {{ ucfirst($pay->payment_method) }}
                        @if($pay->direction==='OUT') <span style="color:#c0392b;font-size:8.5px;">[Refund]</span>@endif
                    </span>
                    <span class="{{ $pay->direction==='OUT' ? 'p-out' : 'p-in' }}">
                        {{ $pay->direction==='OUT' ? '−' : '+' }}₹{{ number_format($pay->amount,2) }}
                    </span>
                </div>
                @endforeach
            </div>
            @endif

            @if($shopUpiId)
            <div class="s-body mt4">
                <div class="qr-area">
                    <div class="qr-box">QR<br>CODE</div>
                    <div class="qr-meta">
                        <div class="qr-upi">{{ $shopUpiId }}</div>
                        <div class="qr-scan">Scan to pay via any UPI app</div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- RIGHT: tax summary + sign --}}
        <div class="b-right">
            <div class="s-hdr">Tax Summary</div>
            <table class="tax-tbl">
                <tr><td>Taxable Amount</td><td>{{ number_format($taxableAmount,2) }}</td></tr>
                @if($igstAmount>0)
                <tr><td>Add : IGST ({{ $igstRate }}%)</td><td>{{ number_format($igstAmount,2) }}</td></tr>
                @endif
                @if($cgstAmount>0)
                <tr><td>Add : CGST ({{ $cgstRate }}%)</td><td>{{ number_format($cgstAmount,2) }}</td></tr>
                @endif
                @if($sgstAmount>0)
                <tr><td>Add : SGST ({{ $sgstRate }}%)</td><td>{{ number_format($sgstAmount,2) }}</td></tr>
                @endif
                <tr class="sep"><td>Total Tax</td><td>{{ number_format($totalTax,2) }}</td></tr>
                @if($totalTax==0)
                <tr class="note"><td colspan="2">* Tax rates not configured yet</td></tr>
                @endif
                <tr class="grand"><td>Total Amount After Tax</td><td>₹{{ number_format($grandTotal,2) }}</td></tr>
                <tr class="note"><td colspan="2">&nbsp;(E &amp; O.E.)</td></tr>

                @if($totalPaidIn > 0)
                <tr class="sep green"><td>Total Paid</td><td>₹{{ number_format($totalPaidIn,2) }}</td></tr>
                @if($totalRefunded > 0)
                <tr><td>Refunded</td><td style="color:#c0392b;">−₹{{ number_format($totalRefunded,2) }}</td></tr>
                <tr><td>Net Paid</td><td style="font-weight:700;">₹{{ number_format($totalPaidIn-$totalRefunded,2) }}</td></tr>
                @endif
                @if($balanceDue > 0)
                <tr class="bal"><td>Balance Due</td><td>₹{{ number_format($balanceDue,2) }}</td></tr>
                @else
                <tr class="full"><td colspan="2">✓ &nbsp;PAID IN FULL</td></tr>
                @endif
                @endif
            </table>

            <div class="sign-box">
                <div class="sign-cert">Certified that the particulars given above are true and correct.</div>
                <div class="sign-for">For {{ $shopName }}</div>
                <div class="sign-line"></div>
                <div class="sign-auth">Authorised Signatory</div>
            </div>
        </div>

    </div>

    {{-- ══ TERMS ══ --}}
    <div class="tc-row">
        <div class="tc-col">
            <div class="tc-hdr">Terms &amp; Conditions</div>
            {{ $invoiceFooterText }}
        </div>
        <div class="tc-col" style="max-width:185px;display:flex;align-items:center;justify-content:center;text-align:center;font-size:9px;color:#bbb;">
            This is a computer<br>generated invoice
        </div>
    </div>

    {{-- ══ FOOTER ══ --}}
    <div class="doc-foot">
        <strong>Tracking ID:</strong> {{ $repair->tracking_id ?? '—' }}
        &nbsp;|&nbsp; {{ $shopName }} &nbsp;·&nbsp; {{ $shopPhone }}
        @if($repair->repairReturns->count()>0)
        &nbsp;|&nbsp; {{ $repair->repairReturns->count() }} return(s) · Last updated: {{ $repair->repairReturns->max('created_at')->format('d/m/Y h:i A') }}

        @endif
    </div>

</div><!-- .invoice-card -->
</div><!-- .page -->
<script>window.onload=function(){window.print();}</script>
</body>
</html>
