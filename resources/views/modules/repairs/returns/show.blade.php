@extends('layouts.app')
@section('page-title', 'Return ' . $return->return_number)

@section('content')
<div x-data="returnDetail()" x-init="init()">

    <!-- Breadcrumb -->
    <div class="mb-5">
        <div class="flex items-center gap-2 text-sm mb-2">
            <a href="/repairs" class="text-primary-600 hover:text-primary-800">Repairs</a>
            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="/repairs/{{ $repair->id }}" class="text-primary-600 hover:text-primary-800">{{ $repair->ticket_number }}</a>
            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-500">Return {{ $return->return_number }}</span>
        </div>
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-3 flex-wrap">
                <h2 class="text-2xl font-bold text-gray-800">{{ $return->return_number }}</h2>
                @php
                    $statusClass = match($return->status) {
                        'draft' => 'bg-gray-100 text-gray-700',
                        'confirmed' => 'bg-amber-100 text-amber-700',
                        'refunded' => 'bg-green-100 text-green-700',
                    };
                @endphp
                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">{{ ucfirst($return->status) }}</span>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                @if($hasReturnableItems)
                <a href="/repairs/{{ $repair->id }}/returns/create" class="btn-primary text-sm inline-flex items-center gap-1.5 !bg-orange-500 !border-orange-500 hover:!bg-orange-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                    Return More Items
                </a>
                @endif
                <a href="/repairs/{{ $repair->id }}/returns/{{ $return->id }}/invoice" target="_blank" class="btn-secondary text-sm inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Print Credit Note
                </a>
                <a href="/repairs/{{ $repair->id }}" class="btn-secondary text-sm inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Back to Repair
                </a>
            </div>
        </div>
    </div>

    <!-- ===== RETURN MORE ITEMS BANNER ===== -->
    @if($hasReturnableItems)
    <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 mb-5 flex items-center gap-3">
        <svg class="w-6 h-6 text-orange-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div class="flex-1">
            <p class="text-sm font-semibold text-orange-800">Partial Return — Some parts / services are still eligible for return.</p>
            <p class="text-xs text-orange-600 mt-0.5">You can create another return for the remaining items on this repair.</p>
        </div>
        <a href="/repairs/{{ $repair->id }}/returns/create" class="shrink-0 inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold bg-orange-500 text-white hover:bg-orange-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
            Return More Items
        </a>
    </div>
    @else
    <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-5 flex items-center gap-3">
        <svg class="w-6 h-6 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div>
            <p class="text-sm font-semibold text-green-800">Fully Returned — All parts and services have been returned for this repair.</p>
        </div>
    </div>
    @endif

    <!-- ===== REFUND SECTION (ACTION REQUIRED) ===== -->
    @if($return->status !== 'refunded')
    <div class="bg-white rounded-xl border shadow-sm mb-6">
        <div class="px-5 py-4 border-b">
            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide flex items-center gap-2">
                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                Process Refund
            </h3>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Refund Amount (₹) *</label>
                    <input type="number" step="0.01" min="0.01" max="{{ $return->total_return_amount }}"
                        x-model.number="refundForm.refund_amount"
                        class="w-full border rounded-lg px-3 py-2 text-sm font-bold text-red-600 bg-red-50">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Refund Method *</label>
                    <select x-model="refundForm.refund_method" class="w-full border rounded-lg px-3 py-2 text-sm">
                        <option value="cash">Cash</option>
                        <option value="upi">UPI</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="card">Card</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Reference / Transaction ID</label>
                    <input type="text" x-model="refundForm.refund_reference" placeholder="e.g. UPI Ref, NEFT #" class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Notes</label>
                    <input type="text" x-model="refundForm.refund_notes" placeholder="Additional notes..." class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
            </div>
            <div class="flex items-center justify-between">
                <p class="text-xs text-gray-500">Refund will be recorded as an OUT payment on the original repair.</p>
                <button @click="processRefund()" :disabled="saving || !refundForm.refund_amount"
                    class="btn-primary text-sm px-6 py-2.5 disabled:opacity-40">
                    <span x-show="!saving">Process Refund</span>
                    <span x-show="saving">Processing...</span>
                </button>
            </div>
        </div>
    </div>
    @else
    <!-- Refund Completed Banner -->
    <div class="bg-green-50 border border-green-200 rounded-xl p-5 mb-6">
        <div class="flex items-start gap-3">
            <svg class="w-6 h-6 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                <h4 class="font-bold text-green-800 text-lg">Refund Completed Check</h4>
                <div class="text-sm text-green-700 mt-1 space-y-0.5">
                    <p>Amount: <strong>₹{{ number_format($return->refund_amount, 2) }}</strong> via <strong>{{ ucfirst(str_replace('_', ' ', $return->refund_method)) }}</strong></p>
                    @if($return->refund_reference)<p>Reference: <strong>{{ $return->refund_reference }}</strong></p>@endif
                    @if($return->refund_notes)<p>Notes: {{ $return->refund_notes }}</p>@endif
                    <p>Refunded on: {{ $return->refunded_at->format('d M Y, h:i A') }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- ===== INFO CARDS ===== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
        <!-- Return Info -->
        <div class="bg-white rounded-xl border shadow-sm p-5">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Return Details</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Return #</span><span class="font-medium">{{ $return->return_number }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Repair #</span><a href="/repairs/{{ $repair->id }}" class="font-medium text-primary-600">{{ $repair->ticket_number }}</a></div>
                <div class="flex justify-between"><span class="text-gray-500">Customer</span><span class="font-medium">{{ $return->customer?->name ?? 'Walk-in' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Device</span><span class="font-medium">{{ $repair->device_brand }} {{ $repair->device_model }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Created</span><span class="font-medium">{{ $return->created_at->format('d M Y, h:i A') }}</span></div>
                @if($return->creator)
                <div class="flex justify-between"><span class="text-gray-500">Created By</span><span class="font-medium">{{ $return->creator->name }}</span></div>
                @endif
            </div>
        </div>

        <!-- Reason -->
        <div class="bg-white rounded-xl border shadow-sm p-5">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Return Reason</h3>
            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $return->reason }}</p>
        </div>

        <!-- Financial Summary -->
        <div class="bg-white rounded-xl border shadow-sm p-5 border-l-4 border-l-red-500">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Total Return</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Return Amount</span><span class="text-xl font-bold text-red-600">₹{{ number_format($return->total_return_amount, 2) }}</span></div>
                @if($return->status === 'refunded')
                <div class="flex justify-between"><span class="text-gray-500">Status</span><span class="font-bold text-green-600">Refunded</span></div>
                @else
                <div class="mt-2 text-xs text-amber-600 font-semibold">⚠ Refund pending</div>
                @endif
            </div>
        </div>
    </div>

    <!-- ===== ORIGINAL REPAIR SUMMARY ===== -->
    @php
        $repairPartsTotal    = $repair->parts->sum(fn($rp) => $rp->cost_price * $rp->quantity);
        $repairServicesTotal = $repair->repairServices->sum('customer_charge');
        $repairGrandTotal    = $repairPartsTotal + $repairServicesTotal + ($repair->service_charge ?? 0);
        $allReturnsTotal     = $repair->repairReturns->sum('total_return_amount');
    @endphp
    <div class="bg-blue-50 border border-blue-100 rounded-xl p-5 mb-6">
        <h3 class="text-xs font-bold text-blue-600 uppercase tracking-wide mb-3 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
            Original Repair Invoice — {{ $repair->ticket_number }}
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg px-4 py-3 text-center">
                <div class="text-[10px] uppercase text-gray-400 font-semibold mb-0.5">Parts</div>
                <div class="text-base font-bold text-gray-800">₹{{ number_format($repairPartsTotal, 2) }}</div>
            </div>
            <div class="bg-white rounded-lg px-4 py-3 text-center">
                <div class="text-[10px] uppercase text-gray-400 font-semibold mb-0.5">Services</div>
                <div class="text-base font-bold text-indigo-600">₹{{ number_format($repairServicesTotal, 2) }}</div>
            </div>
            <div class="bg-white rounded-lg px-4 py-3 text-center">
                <div class="text-[10px] uppercase text-gray-400 font-semibold mb-0.5">Grand Total</div>
                <div class="text-base font-bold text-primary-600">₹{{ number_format($repairGrandTotal, 2) }}</div>
            </div>
            <div class="bg-white rounded-lg px-4 py-3 text-center">
                <div class="text-[10px] uppercase text-gray-400 font-semibold mb-0.5">Total Returned</div>
                <div class="text-base font-bold text-red-600">₹{{ number_format($allReturnsTotal, 2) }}</div>
            </div>
        </div>
        @if($repair->repairReturns->count() > 1)
        <div class="mt-3 text-xs text-blue-500 font-medium">
            {{ $repair->repairReturns->count() }} return(s) processed against this repair.
        </div>
        @endif
    </div>

    <!-- ===== RETURNED ITEMS ===== -->
    <div class="bg-white rounded-xl border shadow-sm mb-6">
        <div class="px-5 py-4 border-b">
            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide">Returned Items ({{ $return->items->count() }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">#</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Item</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Qty</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Unit Price</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Return Amount</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Reason</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($return->items as $i => $item)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-3 text-sm text-gray-400">{{ $i + 1 }}</td>
                        <td class="px-5 py-3">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $item->item_type === 'part' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">{{ ucfirst($item->item_type) }}</span>
                        </td>
                        <td class="px-5 py-3 text-sm font-medium text-gray-800">{{ $item->item_name }}</td>
                        <td class="px-5 py-3 text-center text-sm">{{ $item->quantity }}</td>
                        <td class="px-5 py-3 text-right text-sm">₹{{ number_format($item->unit_price, 2) }}</td>
                        <td class="px-5 py-3 text-right text-sm font-semibold text-red-600">₹{{ number_format($item->return_amount, 2) }}</td>
                        <td class="px-5 py-3 text-sm text-gray-500">{{ $item->reason ?: '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 font-semibold">
                    <tr>
                        <td class="px-5 py-3 text-sm" colspan="5">Total Return Amount</td>
                        <td class="px-5 py-3 text-right text-sm text-red-600">₹{{ number_format($return->total_return_amount, 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>



</div>
@endsection

@push('scripts')
<script>
function returnDetail() {
    return {
        saving: false,
        refundForm: {
            refund_amount: {{ $return->total_return_amount }},
            refund_method: 'cash',
            refund_reference: '',
            refund_notes: '',
        },

        init() {},

        async processRefund() {
            if (!this.refundForm.refund_amount || this.refundForm.refund_amount <= 0) {
                return RepairBox.toast('Enter a valid refund amount', 'error');
            }

            this.saving = true;
            const r = await RepairBox.ajax('/repairs/{{ $repair->id }}/returns/{{ $return->id }}/refund', 'POST', this.refundForm);
            this.saving = false;
            if (r.data?.success !== false) {
                window.location.reload();
            }
        },
    };
}
</script>
@endpush
