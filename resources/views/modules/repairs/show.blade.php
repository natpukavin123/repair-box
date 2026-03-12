@extends('layouts.app')
@section('page-title', 'Repair #' . $repair->ticket_number)

@section('content')
<!-- Skeleton Loader (shows instantly, hidden once Alpine initializes) -->
<div x-data="{ ready: false }" x-init="ready = true" x-show="!ready" class="animate-pulse">
    <div class="mb-5">
        <div class="h-4 w-28 bg-gray-200 rounded mb-3"></div>
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="h-8 w-40 bg-gray-200 rounded"></div>
                <div class="h-6 w-20 bg-gray-200 rounded-full"></div>
            </div>
            <div class="flex items-center gap-2">
                <div class="h-9 w-20 bg-gray-200 rounded-lg"></div>
                <div class="h-9 w-20 bg-gray-200 rounded-lg"></div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border p-4 mb-5">
        <div class="flex items-center justify-between max-w-2xl mx-auto">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-gray-200 rounded-full"></div>
                <div class="w-8 h-0.5 bg-gray-200"></div>
                <div class="w-9 h-9 bg-gray-200 rounded-full"></div>
                <div class="w-8 h-0.5 bg-gray-200"></div>
                <div class="w-9 h-9 bg-gray-200 rounded-full"></div>
                <div class="w-8 h-0.5 bg-gray-200"></div>
                <div class="w-9 h-9 bg-gray-200 rounded-full"></div>
                <div class="w-8 h-0.5 bg-gray-200"></div>
                <div class="w-9 h-9 bg-gray-200 rounded-full"></div>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
        <div class="lg:col-span-2 space-y-5">
            <div class="bg-white rounded-xl shadow-sm border p-5">
                <div class="h-5 w-32 bg-gray-200 rounded mb-4"></div>
                <div class="space-y-3">
                    <div class="flex justify-between"><div class="h-4 w-24 bg-gray-200 rounded"></div><div class="h-4 w-32 bg-gray-200 rounded"></div></div>
                    <div class="flex justify-between"><div class="h-4 w-20 bg-gray-200 rounded"></div><div class="h-4 w-28 bg-gray-200 rounded"></div></div>
                    <div class="flex justify-between"><div class="h-4 w-28 bg-gray-200 rounded"></div><div class="h-4 w-36 bg-gray-200 rounded"></div></div>
                    <div class="flex justify-between"><div class="h-4 w-16 bg-gray-200 rounded"></div><div class="h-4 w-24 bg-gray-200 rounded"></div></div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border p-5">
                <div class="h-5 w-24 bg-gray-200 rounded mb-4"></div>
                <div class="space-y-2">
                    <div class="h-10 bg-gray-200 rounded"></div>
                    <div class="h-10 bg-gray-200 rounded"></div>
                    <div class="h-10 bg-gray-200 rounded"></div>
                </div>
            </div>
        </div>
        <div class="space-y-5">
            <div class="bg-white rounded-xl shadow-sm border p-5">
                <div class="h-5 w-28 bg-gray-200 rounded mb-4"></div>
                <div class="space-y-3">
                    <div class="flex justify-between"><div class="h-4 w-20 bg-gray-200 rounded"></div><div class="h-4 w-16 bg-gray-200 rounded"></div></div>
                    <div class="flex justify-between"><div class="h-4 w-24 bg-gray-200 rounded"></div><div class="h-4 w-16 bg-gray-200 rounded"></div></div>
                    <div class="flex justify-between"><div class="h-5 w-28 bg-gray-200 rounded"></div><div class="h-5 w-20 bg-gray-200 rounded"></div></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div x-data="repairDetail()" x-init="init()" x-cloak>

    <!-- ===== BREADCRUMB & HEADER ===== -->
    <div class="mb-5">
        <a href="/repairs" class="text-sm text-primary-600 hover:text-primary-800 inline-flex items-center gap-1 mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Repairs
        </a>
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3 flex-wrap">
                <h2 class="text-2xl font-bold text-gray-800" x-text="repair.ticket_number"></h2>
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold" :class="statusBadgeClass(repair.status)" x-text="statusLabel(repair.status)"></span>
                <template x-if="repair.record_type !== 'original'">
                    <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded" :class="repair.record_type === 'void' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700'" x-text="repair.record_type"></span>
                </template>
                <template x-if="repair.is_locked">
                    <span class="text-xs text-gray-400 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Locked
                    </span>
                </template>
            </div>
            <div class="flex items-center gap-2">
                <a :href="'/repairs/' + repair.id + '/print'" target="_blank" class="btn-secondary text-sm inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Print
                </a>
                <a x-show="repair.is_fully_paid" :href="'/repairs/' + repair.id + '/invoice'" target="_blank" class="btn-primary text-sm inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    Invoice
                </a>
                <a x-show="repair.is_fully_paid && repair.has_returnable_items" :href="'/repairs/' + repair.id + '/returns/create'" class="btn-secondary text-sm inline-flex items-center gap-1.5 !border-orange-300 !text-orange-700 hover:!bg-orange-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                    Return
                </a>
            </div>
        </div>
    </div>

    <!-- ===== PROGRESS BAR ===== -->
    <div class="bg-white rounded-xl shadow-sm border p-4 mb-5" x-show="repair.status !== 'cancelled'">
        <div class="flex items-center justify-between max-w-2xl mx-auto">
            <template x-for="(step, idx) in progressSteps" :key="step.key">
                <div class="flex items-center" :class="idx < progressSteps.length - 1 ? 'flex-1' : ''">
                    <div class="flex flex-col items-center">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold transition-all border-2"
                            :class="stepReached(repair.status, step.key)
                                ? (repair.status === step.key ? statusDotCurrent(step.key) : 'bg-green-500 border-green-500 text-white')
                                : 'bg-white border-gray-200 text-gray-300'"
                            x-text="idx + 1">
                        </div>
                        <span class="text-[10px] mt-1 font-medium" :class="stepReached(repair.status, step.key) ? 'text-gray-700' : 'text-gray-300'" x-text="step.label"></span>
                    </div>
                    <div x-show="idx < progressSteps.length - 1" class="flex-1 h-0.5 mx-2 mt-[-14px]"
                        :class="stepReached(repair.status, step.key) && stepReached(repair.status, progressSteps[idx+1].key) ? 'bg-green-500' : 'bg-gray-200'"></div>
                </div>
            </template>
        </div>
    </div>
    <div x-show="repair.status === 'cancelled'" class="bg-red-50 border border-red-200 rounded-xl p-4 mb-5 flex items-center gap-3">
        <svg class="w-6 h-6 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
        <div>
            <p class="font-bold text-red-800">This repair has been cancelled</p>
            <p class="text-sm text-red-600" x-show="repair.cancel_reason" x-text="'Reason: ' + repair.cancel_reason"></p>
            <p x-show="repair.total_refunded > 0" class="text-sm text-red-700 mt-1">Refunded: <span class="font-bold" x-text="'₹' + Number(repair.total_refunded).toFixed(2)"></span></p>
        </div>
    </div>

    <!-- ===== TWO COLUMN LAYOUT ===== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <!-- LEFT COLUMN (2/3) -->
        <div class="lg:col-span-2 space-y-5">

            <!-- Info Cards -->
            <div class="bg-white rounded-xl shadow-sm border p-5">
                <h3 class="text-sm font-bold uppercase tracking-wider text-gray-400 mb-4">Repair Details</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <div class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold mb-1">Customer</div>
                        <div class="text-sm font-semibold text-gray-800" x-text="repair.customer?.name || 'Walk-in'"></div>
                        <div class="text-xs text-gray-400" x-text="repair.customer?.mobile_number || ''"></div>
                    </div>
                    <div>
                        <div class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold mb-1">Device</div>
                        <div class="text-sm font-semibold text-gray-800" x-text="(repair.device_brand||'') + ' ' + (repair.device_model||'')"></div>
                        <div class="text-xs text-gray-400" x-text="repair.imei ? 'IMEI: ' + repair.imei : ''"></div>
                    </div>
                    <div>
                        <div class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold mb-1">Tracking ID</div>
                        <div class="text-sm font-semibold text-primary-600" x-text="repair.tracking_id"></div>
                    </div>
                    <div>
                        <div class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold mb-1">Estimated Cost</div>
                        <div class="text-sm font-semibold text-gray-800" x-text="'₹' + Number(repair.estimated_cost||0).toFixed(2)"></div>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t" x-show="repair.problem_description">
                    <div class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold mb-1">Problem Description</div>
                    <div class="text-sm text-gray-700 whitespace-pre-line" x-text="repair.problem_description"></div>
                </div>
                <div class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-4 pt-3 border-t">
                    <div>
                        <div class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold mb-1">Created</div>
                        <div class="text-sm text-gray-600" x-text="formatDateTime(repair.created_at)"></div>
                    </div>
                    <div x-show="repair.expected_delivery_date">
                        <div class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold mb-1">Expected Delivery</div>
                        <div class="text-sm text-gray-600" x-text="formatDate(repair.expected_delivery_date)"></div>
                    </div>
                    <div x-show="repair.technician">
                        <div class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold mb-1">Technician</div>
                        <div class="text-sm text-gray-600" x-text="repair.technician?.name || '-'"></div>
                    </div>
                    <div x-show="repair.completed_at">
                        <div class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold mb-1">Completed At</div>
                        <div class="text-sm text-gray-600" x-text="formatDateTime(repair.completed_at)"></div>
                    </div>
                </div>
            </div>

            <!-- ===== STATUS WORKFLOW ===== -->
            <template x-if="!repair.is_locked && repair.status !== 'cancelled' && repair.record_type !== 'void'">
                <div class="bg-white rounded-xl shadow-sm border p-5">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-gray-400 mb-3">Update Status</h3>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="nextStatus in (repair.allowed_transitions || [])" :key="nextStatus">
                            <button @click="handleStatusTransition(nextStatus)" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold transition-all" :class="statusTransitionBtnClass(nextStatus)">
                                <span x-text="statusLabel(nextStatus)"></span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </button>
                        </template>
                    </div>
                    <!-- Inline notes for status change -->
                    <div x-show="pendingTransition" class="mt-3 p-3 bg-gray-50 rounded-lg border" x-cloak>
                        <div class="text-sm font-medium text-gray-700 mb-2">
                            Changing to: <span class="font-bold" :class="'text-' + (statusMeta[pendingTransition]?.color || 'gray') + '-600'" x-text="statusLabel(pendingTransition)"></span>
                        </div>
                        <input x-model="statusForm.notes" type="text" class="form-input-custom text-sm mb-2" placeholder="Add notes (optional)...">
                        <template x-if="pendingTransition === 'cancelled'">
                            <input x-model="statusForm.cancel_reason" type="text" class="form-input-custom text-sm mb-2" placeholder="Reason for cancellation *">
                        </template>
                        <div class="flex gap-2">
                            <button @click="confirmStatusChange()" class="btn-primary text-sm">Confirm</button>
                            <button @click="pendingTransition = null" class="btn-secondary text-sm">Cancel</button>
                        </div>
                    </div>
                </div>
            </template>

            <!-- ===== PARTS (in_progress: editable, completed/payment/closed: read-only) ===== -->
            <template x-if="repair.status === 'in_progress'">
                <div class="bg-white rounded-xl shadow-sm border">
                    <div class="bg-amber-50 px-5 py-3 border-b flex items-center gap-2 rounded-t-xl">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0"/></svg>
                        <h3 class="font-semibold text-sm text-amber-800">Parts Used</h3>
                    </div>
                    <div class="p-5">
                        <!-- Existing Parts -->
                        <div x-show="(repair.parts || []).length > 0" class="mb-4">
                            <table class="w-full text-sm">
                                <thead><tr class="text-xs text-gray-500 uppercase"><th class="text-left pb-2">Part</th><th class="text-center pb-2">Qty</th><th class="text-right pb-2">Price</th><th class="text-right pb-2">Total</th><th class="pb-2"></th></tr></thead>
                                <tbody>
                                    <template x-for="p in repair.parts || []" :key="p.id">
                                        <tr class="border-t">
                                            <td class="py-2" x-text="p.part ? p.part.name : '-'"></td>
                                            <td class="py-2 text-center" x-text="p.quantity"></td>
                                            <td class="py-2 text-right" x-text="'₹' + Number(p.cost_price).toFixed(2)"></td>
                                            <td class="py-2 text-right font-medium" x-text="'₹' + (Number(p.cost_price) * p.quantity).toFixed(2)"></td>
                                            <td class="py-2 text-right">
                                                <button @click="removePart(p.id)" class="text-red-400 hover:text-red-600 transition" title="Remove">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                                <tfoot>
                                    <tr class="border-t-2 font-semibold">
                                        <td colspan="3" class="py-2 text-right">Parts Total:</td>
                                        <td class="py-2 text-right text-primary-600" x-text="'₹' + partsTotal().toFixed(2)"></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <!-- Add Part Form -->
                        <div class="bg-gray-50 rounded-lg p-3">
                            <div class="relative mb-2">
                                <input x-model="partSearch" @input.debounce.300ms="searchParts(1)" @focus="if(partResults.length === 0) searchParts(1)" @click.away="partResults = []" type="text" class="form-input-custom text-sm" placeholder="Search parts...">
                                <div x-show="partResults.length > 0" class="absolute z-50 w-full bg-white border rounded-lg shadow-lg mt-1 max-h-48 overflow-y-auto" @scroll="handlePartScroll($event)">
                                    <template x-for="pr in partResults" :key="pr.id">
                                        <button @click="selectPart(pr)" class="w-full text-left px-3 py-2 hover:bg-primary-50 text-sm border-b last:border-0 transition">
                                            <span class="font-medium" x-text="pr.name"></span>
                                            <span class="text-gray-400 ml-2" x-text="'₹' + Number(pr.cost_price).toFixed(2)"></span>
                                            <span class="text-gray-300 ml-1" x-text="'(Stock: ' + (pr.stock_quantity || 0) + ')'"></span>
                                        </button>
                                    </template>
                                    <div x-show="partLoading" class="px-3 py-2 text-xs text-gray-400 text-center">Loading...</div>
                                </div>
                            </div>
                            <div x-show="partForm.part_id" class="text-xs text-gray-600 mb-2 flex items-center gap-1">
                                <svg class="w-3 h-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Selected: <span class="font-semibold" x-text="partForm._name"></span>
                                <button @click="partForm.part_id = null; partForm._name = ''" class="text-red-400 ml-1">&times;</button>
                            </div>
                            <div class="flex gap-2">
                                <input x-model="partForm.quantity" type="number" min="1" class="form-input-custom text-sm w-20" placeholder="Qty">
                                <input x-model="partForm.cost_price" type="number" step="0.01" class="form-input-custom text-sm w-28" placeholder="Price ₹">
                                <button @click="addPart()" class="btn-primary text-sm whitespace-nowrap">Add Part</button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Parts Summary (read-only, completed/payment/closed/cancelled) -->
            <template x-if="['completed','payment','closed','cancelled'].includes(repair.status) && (repair.parts || []).length > 0">
                <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                    <div class="bg-gray-50 px-5 py-3 border-b"><h3 class="font-semibold text-sm text-gray-600">Parts Used</h3></div>
                    <div class="p-5">
                        <table class="w-full text-sm">
                            <thead><tr class="text-xs text-gray-500 uppercase"><th class="text-left pb-2">Part</th><th class="text-center pb-2">Qty</th><th class="text-right pb-2">Price</th><th class="text-right pb-2">Total</th></tr></thead>
                            <tbody>
                                <template x-for="p in repair.parts || []" :key="p.id">
                                    <tr class="border-t"><td class="py-2" x-text="p.part ? p.part.name : '-'"></td><td class="py-2 text-center" x-text="p.quantity"></td><td class="py-2 text-right" x-text="'₹' + Number(p.cost_price).toFixed(2)"></td><td class="py-2 text-right font-medium" x-text="'₹' + (Number(p.cost_price) * p.quantity).toFixed(2)"></td></tr>
                                </template>
                            </tbody>
                            <tfoot><tr class="border-t-2 font-semibold"><td colspan="3" class="py-2 text-right">Parts Total:</td><td class="py-2 text-right text-primary-600" x-text="'₹' + partsTotal().toFixed(2)"></td></tr></tfoot>
                        </table>
                    </div>
                </div>
            </template>

            <!-- ===== SERVICES (in_progress/completed/payment: editable) ===== -->
            <template x-if="['in_progress','completed','payment'].includes(repair.status) && !repair.is_locked">
                <div class="bg-white rounded-xl shadow-sm border">
                    <div class="bg-indigo-50 px-5 py-3 border-b flex items-center gap-2 rounded-t-xl">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <h3 class="font-semibold text-sm text-indigo-800">Services</h3>
                    </div>
                    <div class="p-5">
                        <!-- Existing Services List -->
                        <div x-show="(repair.repair_services || []).length > 0" class="mb-4">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="text-xs text-gray-500 uppercase">
                                        <th class="text-left pb-2">Service</th>
                                        <th class="text-left pb-2">Vendor</th>
                                        <th class="text-right pb-2">Cust. Charge</th>
                                        <th class="text-right pb-2">Vendor Charge</th>
                                        <th class="text-center pb-2">Payment</th>
                                        <th class="text-center pb-2">Status</th>
                                        <th class="pb-2"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="svc in repair.repair_services || []" :key="svc.id">
                                        <tr class="border-t group">
                                            <td class="py-2">
                                                <div class="font-medium" x-text="svc.service_type_name"></div>
                                                <div class="text-xs text-gray-400" x-show="svc.reference_no" x-text="'Ref: ' + svc.reference_no"></div>
                                                <div class="text-xs text-gray-400" x-show="svc.description" x-text="svc.description"></div>
                                            </td>
                                            <td class="py-2 text-sm" x-text="svc.vendor ? svc.vendor.name : '-'"></td>
                                            <td class="py-2 text-right font-medium" x-text="'₹' + Number(svc.customer_charge).toFixed(2)"></td>
                                            <td class="py-2 text-right text-gray-500" x-text="'₹' + Number(svc.vendor_charge).toFixed(2)"></td>
                                            <td class="py-2 text-center">
                                                <button @click="toggleServicePayment(svc)" class="text-xs px-2 py-0.5 rounded-full font-medium cursor-pointer"
                                                    :class="svc.payment_status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'"
                                                    x-text="svc.payment_status === 'completed' ? 'Paid' : 'Pending'">
                                                </button>
                                            </td>
                                            <td class="py-2 text-center">
                                                <select @change="updateServiceStatus(svc, $event.target.value)" :value="svc.status" class="text-xs border rounded px-1 py-0.5">
                                                    <option value="pending">Pending</option>
                                                    <option value="in_progress">In Progress</option>
                                                    <option value="completed">Completed</option>
                                                    <option value="cancelled">Cancelled</option>
                                                </select>
                                            </td>
                                            <td class="py-2 text-right">
                                                <button @click="removeService(svc.id)" class="text-red-400 hover:text-red-600 transition opacity-0 group-hover:opacity-100" title="Remove">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                                <tfoot>
                                    <tr class="border-t-2 font-semibold">
                                        <td colspan="2" class="py-2 text-right">Services Total:</td>
                                        <td class="py-2 text-right text-indigo-600" x-text="'₹' + servicesTotal().toFixed(2)"></td>
                                        <td class="py-2 text-right text-gray-400" x-text="'₹' + vendorChargesTotal().toFixed(2)"></td>
                                        <td colspan="3"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <!-- Add Service Form -->
                        <div class="bg-gray-50 rounded-lg p-3">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-2">
                                <!-- Service Type (auto-suggest + custom) -->
                                <div class="relative">
                                    <input x-model="svcForm.service_type_name" @input.debounce.300ms="searchServiceTypes(1)" @focus="if(svcTypeResults.length === 0) searchServiceTypes(1)" @click.away="svcTypeResults = []" type="text" class="form-input-custom text-sm" placeholder="Service type (type to search or enter custom)...">
                                    <div x-show="svcTypeResults.length > 0" class="absolute z-50 w-full bg-white border rounded-lg shadow-lg mt-1 max-h-48 overflow-y-auto" @scroll="handleSvcTypeScroll($event)">
                                        <template x-for="st in svcTypeResults" :key="st.id">
                                            <button @click="selectServiceType(st)" class="w-full text-left px-3 py-2 hover:bg-primary-50 text-sm border-b last:border-0 transition">
                                                <span class="font-medium" x-text="st.name"></span>
                                                <span class="text-gray-400 ml-2" x-show="st.default_price" x-text="'₹' + Number(st.default_price).toFixed(2)"></span>
                                            </button>
                                        </template>
                                        <div x-show="svcTypeLoading" class="px-3 py-2 text-xs text-gray-400 text-center">Loading...</div>
                                    </div>
                                </div>
                                <!-- Vendor (auto-suggest) -->
                                <div class="relative">
                                    <input x-model="vendorSearch" @input.debounce.300ms="searchVendors(1)" @focus="if(vendorResults.length === 0) searchVendors(1)" @click.away="vendorResults = []" type="text" class="form-input-custom text-sm" placeholder="Vendor (type to search)...">
                                    <div x-show="vendorResults.length > 0" class="absolute z-50 w-full bg-white border rounded-lg shadow-lg mt-1 max-h-48 overflow-y-auto" @scroll="handleVendorScroll($event)">
                                        <template x-for="v in vendorResults" :key="v.id">
                                            <button @click="selectVendor(v)" class="w-full text-left px-3 py-2 hover:bg-primary-50 text-sm border-b last:border-0 transition">
                                                <span class="font-medium" x-text="v.name"></span>
                                                <span class="text-gray-400 ml-2 text-xs" x-show="v.specialization" x-text="v.specialization"></span>
                                            </button>
                                        </template>
                                        <div x-show="vendorLoading" class="px-3 py-2 text-xs text-gray-400 text-center">Loading...</div>
                                    </div>
                                    <div x-show="svcForm.vendor_id" class="text-xs text-gray-600 mt-1 flex items-center gap-1">
                                        <svg class="w-3 h-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Vendor: <span class="font-semibold" x-text="svcForm._vendor_name"></span>
                                        <button @click="svcForm.vendor_id = null; svcForm._vendor_name = ''; vendorSearch = ''" class="text-red-400 ml-1">&times;</button>
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-2">
                                <input x-model="svcForm.customer_charge" type="number" step="0.01" class="form-input-custom text-sm" placeholder="Cust. Charge ₹">
                                <input x-model="svcForm.vendor_charge" type="number" step="0.01" class="form-input-custom text-sm" placeholder="Vendor Charge ₹">
                                <input x-model="svcForm.reference_no" type="text" class="form-input-custom text-sm" placeholder="Reference No">
                                <select x-model="svcForm.status" class="form-select-custom text-sm">
                                    <option value="pending">Pending</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>
                            <div class="flex gap-2">
                                <input x-model="svcForm.description" type="text" class="form-input-custom text-sm flex-1" placeholder="Description (optional)...">
                                <button @click="addService()" class="btn-primary text-sm whitespace-nowrap">Add Service</button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Services Summary (read-only, closed/cancelled) -->
            <template x-if="['closed','cancelled'].includes(repair.status) && (repair.repair_services || []).length > 0">
                <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                    <div class="bg-gray-50 px-5 py-3 border-b"><h3 class="font-semibold text-sm text-gray-600">Services</h3></div>
                    <div class="p-5">
                        <table class="w-full text-sm">
                            <thead><tr class="text-xs text-gray-500 uppercase"><th class="text-left pb-2">Service</th><th class="text-left pb-2">Vendor</th><th class="text-right pb-2">Charge</th><th class="text-center pb-2">Status</th></tr></thead>
                            <tbody>
                                <template x-for="svc in repair.repair_services || []" :key="svc.id">
                                    <tr class="border-t">
                                        <td class="py-2">
                                            <div class="font-medium" x-text="svc.service_type_name"></div>
                                            <div class="text-xs text-gray-400" x-show="svc.reference_no" x-text="'Ref: ' + svc.reference_no"></div>
                                        </td>
                                        <td class="py-2 text-sm" x-text="svc.vendor ? svc.vendor.name : '-'"></td>
                                        <td class="py-2 text-right font-medium" x-text="'₹' + Number(svc.customer_charge).toFixed(2)"></td>
                                        <td class="py-2 text-center">
                                            <span class="text-xs px-2 py-0.5 rounded-full font-medium"
                                                :class="{'bg-green-100 text-green-700': svc.status === 'completed', 'bg-yellow-100 text-yellow-700': svc.status === 'pending', 'bg-blue-100 text-blue-700': svc.status === 'in_progress', 'bg-red-100 text-red-700': svc.status === 'cancelled'}"
                                                x-text="svc.status.replace('_', ' ')"></span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot><tr class="border-t-2 font-semibold"><td colspan="2" class="py-2 text-right">Services Total:</td><td class="py-2 text-right text-indigo-600" x-text="'₹' + servicesTotal().toFixed(2)"></td><td></td></tr></tfoot>
                        </table>
                    </div>
                </div>
            </template>

            <!-- ===== SERVICE CHARGE (completed/payment only, not locked) ===== -->
            <template x-if="['completed','payment'].includes(repair.status) && !repair.is_locked">
                <div class="bg-white rounded-xl shadow-sm border-2 border-emerald-200 overflow-hidden">
                    <div class="bg-emerald-50 px-5 py-3 border-b flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <h3 class="font-semibold text-sm text-emerald-800">Service Charge</h3>
                    </div>
                    <div class="p-5">
                        <div class="flex items-center gap-3">
                            <label class="text-sm font-medium text-gray-700">Service Charge (₹):</label>
                            <input x-model="serviceChargeInput" type="number" step="0.01" min="0" class="form-input-custom text-sm w-36" placeholder="0.00">
                            <button @click="saveServiceCharge()" class="btn-primary text-sm">Save</button>
                            <span x-show="repair.service_charge > 0" class="text-xs text-green-600 font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Saved
                            </span>
                        </div>
                    </div>
                </div>
            </template>

            <!-- ===== INVOICE SUMMARY (completed/payment/closed/cancelled) ===== -->
            <template x-if="['completed','payment','closed','cancelled'].includes(repair.status)">
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-5">
                    <h3 class="font-bold text-sm text-blue-800 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                        Invoice Summary
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                        <div class="bg-white rounded-lg p-3 text-center">
                            <div class="text-[10px] uppercase text-gray-400 font-semibold">Parts</div>
                            <div class="text-lg font-bold text-gray-800" x-text="'₹' + partsTotal().toFixed(2)"></div>
                        </div>
                        <div class="bg-white rounded-lg p-3 text-center">
                            <div class="text-[10px] uppercase text-gray-400 font-semibold">Services</div>
                            <div class="text-lg font-bold text-indigo-600" x-text="'₹' + servicesTotal().toFixed(2)"></div>
                        </div>
                        <div class="bg-white rounded-lg p-3 text-center">
                            <div class="text-[10px] uppercase text-gray-400 font-semibold">Service Charge</div>
                            <div class="text-lg font-bold text-gray-800" x-text="'₹' + Number(repair.service_charge || 0).toFixed(2)"></div>
                        </div>
                        <div class="bg-white rounded-lg p-3 text-center">
                            <div class="text-[10px] uppercase text-gray-400 font-semibold">Grand Total</div>
                            <div class="text-lg font-bold text-primary-600" x-text="'₹' + grandTotal().toFixed(2)"></div>
                        </div>
                        <div class="bg-white rounded-lg p-3 text-center">
                            <div class="text-[10px] uppercase text-gray-400 font-semibold">Balance</div>
                            <div class="text-lg font-bold" :class="balanceDue() > 0 ? 'text-red-600' : 'text-green-600'" x-text="'₹' + balanceDue().toFixed(2)"></div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- ===== PAYMENT COLLECTION (payment status) ===== -->
            <template x-if="repair.status === 'payment'">
                <div class="bg-white rounded-xl shadow-sm border-2 border-purple-200 overflow-hidden">
                    <div class="bg-purple-50 px-5 py-3 border-b flex items-center gap-2">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <h3 class="font-semibold text-sm text-purple-800">Payment Collection</h3>
                    </div>
                    <div class="p-5">
                        <!-- Collect Payment -->
                        <div x-show="balanceDue() > 0" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                            <div class="flex items-center gap-2 mb-3">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="font-bold text-yellow-800">Balance Due: <span x-text="'₹' + balanceDue().toFixed(2)"></span></span>
                            </div>
                            <div class="flex gap-2 items-end">
                                <div class="flex-1">
                                    <label class="text-xs text-gray-600 mb-1 block">Amount</label>
                                    <input x-model="payForm.amount" type="number" step="0.01" class="form-input-custom text-sm" placeholder="Amount">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-600 mb-1 block">Method</label>
                                    <select x-model="payForm.payment_method" class="form-select-custom text-sm w-28">
                                        <option value="cash">Cash</option>
                                        <option value="card">Card</option>
                                        <option value="upi">UPI</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                    </select>
                                </div>
                                <button @click="collectPayment()" class="btn-success text-sm whitespace-nowrap px-4">Collect</button>
                            </div>
                        </div>
                        <!-- Fully Paid -->
                        <div x-show="balanceDue() <= 0 && grandTotal() > 0" class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                            <svg class="w-10 h-10 text-green-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="font-bold text-green-700 text-lg">Fully Paid!</p>
                            <p class="text-sm text-green-600">You can now close this repair and download the invoice.</p>
                        </div>
                    </div>
                </div>
            </template>

            <!-- ===== CLOSED - DOWNLOAD INVOICE ===== -->
            <template x-if="repair.status === 'closed'">
                <div class="bg-green-50 border-2 border-green-200 rounded-xl p-6 text-center">
                    <svg class="w-12 h-12 text-green-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="font-bold text-green-800 text-xl mb-1">Repair Closed</p>
                    <p class="text-sm text-green-600 mb-4">This repair has been completed and payment is settled.</p>
                    <a :href="'/repairs/' + repair.id + '/invoice'" target="_blank" class="btn-primary inline-flex items-center gap-2 text-base px-6 py-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Download Paid Invoice
                    </a>
                </div>
            </template>
        </div>

        <!-- RIGHT COLUMN (1/3) -->
        <div class="space-y-5">

            <!-- ===== PAYMENT HISTORY ===== -->
            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b"><h3 class="font-semibold text-sm text-gray-600">Payments</h3></div>
                <div class="p-4">
                    <template x-if="(repair.payments || []).length > 0">
                        <div class="space-y-0">
                            <template x-for="p in repair.payments" :key="p.id">
                                <div class="flex items-center justify-between text-sm py-2.5 border-b last:border-0">
                                    <div>
                                        <span class="font-medium capitalize" x-text="p.payment_type"></span>
                                        <span class="text-gray-400 text-xs">via</span>
                                        <span class="inline-flex items-center gap-1 text-xs font-medium px-1.5 py-0.5 rounded-full" :class="{'bg-green-100 text-green-700': p.payment_method === 'cash', 'bg-blue-100 text-blue-700': p.payment_method === 'upi', 'bg-purple-100 text-purple-700': p.payment_method === 'card', 'bg-gray-100 text-gray-700': p.payment_method === 'bank_transfer'}">
                                            <template x-if="p.payment_method === 'cash'"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg></template>
                                            <template x-if="p.payment_method === 'upi'"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg></template>
                                            <template x-if="p.payment_method === 'card'"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg></template>
                                            <template x-if="p.payment_method === 'bank_transfer'"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg></template>
                                            <span x-text="p.payment_method === 'bank_transfer' ? 'Bank' : (p.payment_method || '').toUpperCase()"></span>
                                        </span>
                                        <div class="text-[10px] text-gray-400 mt-0.5" x-text="formatDate(p.created_at)"></div>
                                    </div>
                                    <div class="font-semibold" :class="p.direction === 'OUT' ? 'text-red-600' : 'text-green-600'" x-text="(p.direction === 'OUT' ? '-' : '+') + '₹' + Number(p.amount).toFixed(2)"></div>
                                </div>
                            </template>
                            <div class="pt-3 mt-2 border-t flex items-center justify-between text-sm font-bold">
                                <span>Net Paid</span>
                                <span class="text-primary-600" x-text="'₹' + (totalPaid() - totalRefunded()).toFixed(2)"></span>
                            </div>
                        </div>
                    </template>
                    <template x-if="(repair.payments || []).length === 0">
                        <p class="text-sm text-gray-400 text-center py-4">No payments yet</p>
                    </template>
                </div>
            </div>

            <!-- ===== MORE ACTIONS ===== -->
            <template x-if="repair.record_type !== 'void' && repair.status !== 'cancelled'">
                <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                    <div class="bg-gray-50 px-4 py-3 border-b"><h3 class="font-semibold text-sm text-gray-600">Actions</h3></div>
                    <div class="p-4 space-y-2">
                        <template x-if="!repair.is_locked && repair.status !== 'cancelled'">
                            <button @click="showCancelRefund = true" class="w-full inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium border-2 border-red-200 text-red-700 bg-red-50 hover:bg-red-100 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                Cancel & Refund
                            </button>
                        </template>
                        <template x-if="repair.status === 'received'">
                            <button @click="showVoid = true" class="w-full inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium border-2 border-gray-200 text-gray-700 bg-gray-50 hover:bg-gray-100 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Void
                            </button>
                        </template>
                    </div>
                </div>
            </template>

            <!-- ===== CHILD REPAIRS (voids/duplicates) ===== -->
            <template x-if="(repair.child_repairs || []).length > 0">
                <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                    <div class="bg-gray-50 px-4 py-3 border-b"><h3 class="font-semibold text-sm text-gray-600">Related Repairs</h3></div>
                    <div class="p-3">
                        <template x-for="child in repair.child_repairs" :key="child.id">
                            <a :href="'/repairs/' + child.id" class="flex items-center justify-between py-2.5 border-b last:border-0 text-sm hover:bg-gray-50 rounded px-2 -mx-2 transition">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-primary-600" x-text="child.ticket_number"></span>
                                    <span class="text-[10px] font-bold uppercase px-1.5 py-0.5 rounded" :class="child.record_type === 'void' ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-600'" x-text="child.record_type"></span>
                                </div>
                                <span class="text-xs text-gray-400" x-text="formatDate(child.created_at)"></span>
                            </a>
                        </template>
                    </div>
                </div>
            </template>

            <!-- ===== RETURNS ===== -->
            <template x-if="(repair.repair_returns || []).length > 0">
                <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                    <div class="bg-orange-50 px-4 py-3 border-b flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                            <h3 class="font-semibold text-sm text-orange-700">Returns</h3>
                        </div>
                        <template x-if="repair.return_status === 'partial'">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-amber-100 text-amber-700">Partial</span>
                        </template>
                        <template x-if="repair.return_status === 'fully_returned'">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-green-100 text-green-700">Fully Returned</span>
                        </template>
                    </div>
                    <div class="p-3">
                        <template x-for="ret in repair.repair_returns" :key="ret.id">
                            <a :href="'/repairs/' + repair.id + '/returns/' + ret.id" class="flex items-center justify-between py-2.5 border-b last:border-0 text-sm hover:bg-gray-50 rounded px-2 -mx-2 transition">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-orange-600" x-text="ret.return_number"></span>
                                    <span class="text-[10px] font-bold uppercase px-1.5 py-0.5 rounded"
                                        :class="{
                                            'bg-gray-100 text-gray-600': ret.status === 'draft',
                                            'bg-blue-100 text-blue-600': ret.status === 'confirmed',
                                            'bg-green-100 text-green-600': ret.status === 'refunded'
                                        }"
                                        x-text="ret.status"></span>
                                    <span class="text-sm font-semibold text-gray-700" x-text="'₹' + Number(ret.total_return_amount).toFixed(2)"></span>
                                </div>
                                <span class="text-xs text-gray-400" x-text="formatDate(ret.created_at)"></span>
                            </a>
                        </template>
                    </div>
                </div>
            </template>

            <!-- ===== STATUS HISTORY ===== -->
            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <h3 class="font-semibold text-sm text-gray-600">Status History</h3>
                </div>
                <div class="p-4">
                    <div class="relative">
                        <div class="absolute left-3 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                        <div class="space-y-4">
                            <template x-for="sh in (repair.status_history || []).slice().reverse()" :key="sh.id">
                                <div class="relative flex items-start gap-3 pl-8">
                                    <div class="absolute left-1.5 top-1 w-3.5 h-3.5 rounded-full border-2 border-white" :class="statusDotBg(sh.status)"></div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-semibold" x-text="statusLabel(sh.status)"></span>
                                            <span class="text-xs text-gray-400" x-text="formatDateTime(sh.created_at)"></span>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-0.5" x-show="sh.notes" x-text="sh.notes"></p>
                                        <p class="text-xs text-gray-400" x-show="sh.updater" x-text="'by ' + (sh.updater?.name || '')"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== CANCEL & REFUND MODAL ===== -->
    <div x-show="showCancelRefund" class="modal-overlay" x-cloak>
        <div class="modal-container max-w-md" @click.away="showCancelRefund = false">
            <div class="modal-header">
                <h3 class="text-lg font-bold text-red-700">Cancel Repair & Refund</h3>
                <button @click="showCancelRefund = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <div class="modal-body">
                <div class="bg-red-50 rounded-lg p-3 mb-4 text-sm text-red-700">
                    <p class="font-medium">This will cancel the repair and refund any advance payments.</p>
                    <p class="mt-1" x-show="repair.net_paid > 0">Advance paid: <strong x-text="'₹' + Number(repair.net_paid || 0).toFixed(2)"></strong> will be refunded.</p>
                </div>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Reason *</label>
                        <textarea x-model="cancelForm.reason" class="form-input-custom" rows="2" placeholder="Why is this being cancelled?"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Refund Method</label>
                        <select x-model="cancelForm.refund_method" class="form-select-custom w-full">
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="upi">UPI</option>
                            <option value="bank_transfer">Bank Transfer</option>
                        </select>
                    </div>
                    <!-- Parts handling -->
                    <template x-if="(repair.parts || []).length > 0">
                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-3">
                            <label class="block text-sm font-medium text-amber-800 mb-2 flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0"/></svg>
                                Parts Used (<span x-text="(repair.parts || []).length"></span>) — Total: <span x-text="'₹' + partsTotal().toFixed(2)"></span>
                            </label>
                            <div class="space-y-2">
                                <label class="flex items-center gap-2 p-2 rounded-lg cursor-pointer hover:bg-amber-100 transition" :class="cancelForm.parts_action === 'return_stock' ? 'bg-amber-100 ring-1 ring-amber-400' : ''">
                                    <input type="radio" x-model="cancelForm.parts_action" value="return_stock" class="text-amber-600">
                                    <div>
                                        <span class="text-sm font-medium text-gray-800">Return to stock</span>
                                        <p class="text-xs text-gray-500">Parts are fine, add back to inventory. No loss.</p>
                                    </div>
                                </label>
                                <label class="flex items-center gap-2 p-2 rounded-lg cursor-pointer hover:bg-amber-100 transition" :class="cancelForm.parts_action === 'write_off' ? 'bg-amber-100 ring-1 ring-amber-400' : ''">
                                    <input type="radio" x-model="cancelForm.parts_action" value="write_off" class="text-amber-600">
                                    <div>
                                        <span class="text-sm font-medium text-gray-800">Write off as loss</span>
                                        <p class="text-xs text-gray-500">Parts are damaged/used. Count as loss in expenses.</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            <div class="modal-footer">
                <button @click="showCancelRefund = false" class="btn-secondary">Go Back</button>
                <button @click="cancelWithRefund()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">Cancel & Refund</button>
            </div>
        </div>
    </div>

    <!-- ===== VOID MODAL ===== -->
    <div x-show="showVoid" class="modal-overlay" x-cloak>
        <div class="modal-container max-w-md" @click.away="showVoid = false">
            <div class="modal-header">
                <h3 class="text-lg font-bold text-red-700">Void Repair</h3>
                <button @click="showVoid = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <div class="modal-body">
                <div class="bg-red-50 rounded-lg p-3 mb-4 text-sm text-red-700">
                    <p>This will mark the repair as <strong>void</strong> (mistake entry). It will be locked and excluded from all reports and revenue calculations.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reason *</label>
                    <textarea x-model="voidForm.reason" class="form-input-custom" rows="2" placeholder="Reason for voiding..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button @click="showVoid = false" class="btn-secondary">Cancel</button>
                <button @click="createVoid()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">Void Repair</button>
            </div>
        </div>
    </div>

    <!-- ===== COMPLETED CONFIRMATION MODAL ===== -->
    <div x-show="showCompletedConfirm" class="modal-overlay" x-cloak>
        <div class="modal-container max-w-md" @click.away="showCompletedConfirm = false">
            <div class="modal-header">
                <h3 class="text-lg font-bold text-emerald-700">Confirm Repair Completed</h3>
                <button @click="showCompletedConfirm = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <div class="modal-body">
                <div class="bg-emerald-50 rounded-lg p-4 mb-4 text-center">
                    <svg class="w-12 h-12 text-emerald-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="font-semibold text-emerald-800">Mark this repair as completed?</p>
                    <p class="text-sm text-emerald-600 mt-1">Once confirmed, you cannot change it back to in-progress.</p>
                    <p class="text-sm text-emerald-600 mt-1">You can add service charges after marking as completed.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes (optional)</label>
                    <input x-model="statusForm.notes" type="text" class="form-input-custom text-sm" placeholder="Completion notes...">
                </div>
            </div>
            <div class="modal-footer">
                <button @click="showCompletedConfirm = false" class="btn-secondary">Go Back</button>
                <button @click="confirmCompleted()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">Yes, Mark Completed</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function repairDetail() {
    return {
        repair: @json($repair),
        statusMeta: @json($statusMeta),

        // Progress steps
        progressSteps: [
            { key: 'received', label: 'Received' },
            { key: 'in_progress', label: 'In Progress' },
            { key: 'completed', label: 'Completed' },
            { key: 'payment', label: 'Payment' },
            { key: 'closed', label: 'Closed' },
        ],

        // Modals
        showCancelRefund: false,
        showVoid: false,
        showCompletedConfirm: false,

        // Status
        statusForm: { status: '', notes: '', cancel_reason: '', confirm: false },
        pendingTransition: null,

        // Parts
        partForm: { part_id: null, _name: '', quantity: 1, cost_price: '' },
        partSearch: '', partResults: [], partHasMore: false, partPage: 1, partLoading: false,

        // Payment
        payForm: { payment_type: 'final', payment_method: 'cash', amount: '' },

        // Service charge
        serviceChargeInput: '',

        // Services
        svcForm: { service_type_id: null, service_type_name: '', vendor_id: null, _vendor_name: '', customer_charge: '', vendor_charge: '', status: 'pending', reference_no: '', description: '' },
        svcTypeResults: [], svcTypeHasMore: false, svcTypePage: 1, svcTypeLoading: false,
        vendorSearch: '', vendorResults: [], vendorHasMore: false, vendorPage: 1, vendorLoading: false,

        // Cancel form
        cancelForm: { reason: '', refund_method: 'cash', parts_action: 'return_stock' },

        // Void form
        voidForm: { reason: '' },

        init() {
            this.serviceChargeInput = this.repair.service_charge || '';
            if (this.repair.status === 'payment' && this.balanceDue() > 0) {
                this.payForm.amount = this.balanceDue().toFixed(2);
            }
        },

        // Reload repair data from server
        async reload() {
            const r = await RepairBox.ajax('/repairs/' + this.repair.id);
            if (r.data) {
                this.repair = r.data;
                this.serviceChargeInput = this.repair.service_charge || '';
                if (this.repair.status === 'payment' && this.balanceDue() > 0) {
                    this.payForm.amount = this.balanceDue().toFixed(2);
                }
            }
        },

        // ===== STATUS HELPERS =====
        statusLabel(status) {
            return this.statusMeta[status]?.label || status?.replace('_', ' ') || '';
        },
        statusBadgeClass(status) {
            const map = { received: 'bg-blue-100 text-blue-700', in_progress: 'bg-amber-100 text-amber-700', completed: 'bg-emerald-100 text-emerald-700', payment: 'bg-purple-100 text-purple-700', closed: 'bg-green-100 text-green-800', cancelled: 'bg-red-100 text-red-700' };
            return map[status] || 'bg-gray-100 text-gray-700';
        },
        statusTransitionBtnClass(status) {
            const map = { in_progress: 'bg-amber-500 hover:bg-amber-600 text-white shadow-sm', completed: 'bg-emerald-500 hover:bg-emerald-600 text-white shadow-sm', payment: 'bg-purple-500 hover:bg-purple-600 text-white shadow-sm', closed: 'bg-green-600 hover:bg-green-700 text-white shadow-sm', cancelled: 'bg-red-100 hover:bg-red-200 text-red-700 border border-red-200' };
            return map[status] || 'bg-gray-500 hover:bg-gray-600 text-white';
        },
        statusDotCurrent(status) {
            const map = { received: 'bg-blue-500 border-blue-500 text-white ring-2 ring-blue-200', in_progress: 'bg-amber-500 border-amber-500 text-white ring-2 ring-amber-200', completed: 'bg-emerald-500 border-emerald-500 text-white ring-2 ring-emerald-200', payment: 'bg-purple-500 border-purple-500 text-white ring-2 ring-purple-200', closed: 'bg-green-600 border-green-600 text-white ring-2 ring-green-200' };
            return map[status] || 'bg-primary-600 border-primary-600 text-white ring-2 ring-primary-200';
        },
        statusDotBg(status) {
            const map = { received: 'bg-blue-500', in_progress: 'bg-amber-500', completed: 'bg-emerald-500', payment: 'bg-purple-500', closed: 'bg-green-600', cancelled: 'bg-red-500' };
            return map[status] || 'bg-gray-400';
        },
        stepReached(current, step) {
            const order = ['received', 'in_progress', 'completed', 'payment', 'closed'];
            return order.indexOf(current) >= order.indexOf(step);
        },

        // ===== DATE FORMATTING =====
        formatDate(d) { if (!d) return ''; return new Date(d).toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' }); },
        formatDateTime(d) { if (!d) return ''; return new Date(d).toLocaleString('en-IN', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' }); },

        // ===== STATUS TRANSITIONS =====
        handleStatusTransition(nextStatus) {
            if (nextStatus === 'completed') { this.statusForm.notes = ''; this.showCompletedConfirm = true; return; }
            this.pendingTransition = nextStatus;
            this.statusForm = { status: nextStatus, notes: '', cancel_reason: '', confirm: false };
        },
        async confirmStatusChange() {
            const status = this.pendingTransition;
            if (!status) return;
            if (status === 'cancelled' && !this.statusForm.cancel_reason) { RepairBox.toast('Please provide a cancellation reason', 'error'); return; }
            this.statusForm.status = status;
            const r = await RepairBox.ajax('/repairs/' + this.repair.id + '/status', 'PUT', this.statusForm);
            if (r.success !== false) {
                RepairBox.toast('Status updated to ' + this.statusLabel(status), 'success');
                this.pendingTransition = null;
                await this.reload();
            }
        },
        async confirmCompleted() {
            const r = await RepairBox.ajax('/repairs/' + this.repair.id + '/status', 'PUT', { status: 'completed', notes: this.statusForm.notes || 'Repair completed', confirm: true });
            if (r.success !== false) {
                RepairBox.toast('Repair marked as completed', 'success');
                this.showCompletedConfirm = false;
                await this.reload();
            }
        },

        // ===== PARTS =====
        async searchParts(page) {
            page = page || 1;
            if (page === 1) { this.partResults = []; this.partPage = 1; }
            this.partLoading = true;
            const r = await RepairBox.ajax('/parts-search?q=' + encodeURIComponent(this.partSearch || '') + '&page=' + page);
            this.partLoading = false;
            if (r.data) {
                this.partResults = page === 1 ? r.data : this.partResults.concat(r.data);
                this.partHasMore = r.has_more || false;
                this.partPage = page;
            }
        },
        handlePartScroll(e) {
            const el = e.target;
            if (el.scrollTop + el.clientHeight >= el.scrollHeight - 10 && this.partHasMore && !this.partLoading) {
                this.searchParts(this.partPage + 1);
            }
        },
        selectPart(pr) {
            this.partForm.part_id = pr.id;
            this.partForm._name = pr.name;
            this.partForm.cost_price = pr.cost_price || '';
            this.partResults = [];
            this.partSearch = '';
            this.partHasMore = false;
        },
        async addPart() {
            if (!this.partForm.part_id) { RepairBox.toast('Please search & select a part', 'error'); return; }
            const r = await RepairBox.ajax('/repairs/' + this.repair.id + '/parts', 'POST', { part_id: this.partForm.part_id, quantity: this.partForm.quantity, cost_price: this.partForm.cost_price });
            if (r.success !== false) {
                RepairBox.toast('Part added', 'success');
                this.partForm = { part_id: null, _name: '', quantity: 1, cost_price: '' };
                await this.reload();
            }
        },
        async removePart(partId) {
            if (!confirm('Remove this part?')) return;
            const r = await RepairBox.ajax('/repairs/' + this.repair.id + '/parts/' + partId, 'DELETE');
            if (r.success !== false) { RepairBox.toast('Part removed', 'success'); await this.reload(); }
        },

        // ===== SERVICES =====
        async searchServiceTypes(page) {
            page = page || 1;
            if (page === 1) { this.svcTypeResults = []; this.svcTypePage = 1; }
            this.svcTypeLoading = true;
            const r = await RepairBox.ajax('/service-types-search?q=' + encodeURIComponent(this.svcForm.service_type_name || '') + '&page=' + page);
            this.svcTypeLoading = false;
            if (r.data) {
                this.svcTypeResults = page === 1 ? r.data : this.svcTypeResults.concat(r.data);
                this.svcTypeHasMore = r.has_more || false;
                this.svcTypePage = page;
            }
        },
        handleSvcTypeScroll(e) {
            const el = e.target;
            if (el.scrollTop + el.clientHeight >= el.scrollHeight - 10 && this.svcTypeHasMore && !this.svcTypeLoading) {
                this.searchServiceTypes(this.svcTypePage + 1);
            }
        },
        selectServiceType(st) {
            this.svcForm.service_type_id = st.id;
            this.svcForm.service_type_name = st.name;
            if (st.default_price) this.svcForm.customer_charge = st.default_price;
            this.svcTypeResults = [];
            this.svcTypeHasMore = false;
        },
        async searchVendors(page) {
            page = page || 1;
            if (page === 1) { this.vendorResults = []; this.vendorPage = 1; }
            this.vendorLoading = true;
            const r = await RepairBox.ajax('/vendors-search?q=' + encodeURIComponent(this.vendorSearch || '') + '&page=' + page);
            this.vendorLoading = false;
            if (r.data) {
                this.vendorResults = page === 1 ? r.data : this.vendorResults.concat(r.data);
                this.vendorHasMore = r.has_more || false;
                this.vendorPage = page;
            }
        },
        handleVendorScroll(e) {
            const el = e.target;
            if (el.scrollTop + el.clientHeight >= el.scrollHeight - 10 && this.vendorHasMore && !this.vendorLoading) {
                this.searchVendors(this.vendorPage + 1);
            }
        },
        selectVendor(v) {
            this.svcForm.vendor_id = v.id;
            this.svcForm._vendor_name = v.name;
            this.vendorResults = [];
            this.vendorSearch = '';
            this.vendorHasMore = false;
        },
        async addService() {
            if (!this.svcForm.service_type_name) { RepairBox.toast('Please enter a service type', 'error'); return; }
            if (!this.svcForm.customer_charge || Number(this.svcForm.customer_charge) < 0) { RepairBox.toast('Please enter customer charge', 'error'); return; }
            const r = await RepairBox.ajax('/repairs/' + this.repair.id + '/services', 'POST', {
                service_type_id: this.svcForm.service_type_id,
                service_type_name: this.svcForm.service_type_name,
                vendor_id: this.svcForm.vendor_id,
                customer_charge: this.svcForm.customer_charge,
                vendor_charge: this.svcForm.vendor_charge || 0,
                status: this.svcForm.status,
                reference_no: this.svcForm.reference_no,
                description: this.svcForm.description,
            });
            if (r.success !== false) {
                RepairBox.toast('Service added', 'success');
                this.svcForm = { service_type_id: null, service_type_name: '', vendor_id: null, _vendor_name: '', customer_charge: '', vendor_charge: '', status: 'pending', reference_no: '', description: '' };
                await this.reload();
            }
        },
        async removeService(serviceId) {
            if (!confirm('Remove this service?')) return;
            const r = await RepairBox.ajax('/repairs/' + this.repair.id + '/services/' + serviceId, 'DELETE');
            if (r.success !== false) { RepairBox.toast('Service removed', 'success'); await this.reload(); }
        },
        async toggleServicePayment(svc) {
            const newStatus = svc.payment_status === 'completed' ? 'pending' : 'completed';
            const r = await RepairBox.ajax('/repairs/' + this.repair.id + '/services/' + svc.id, 'PUT', { payment_status: newStatus });
            if (r.success !== false) { RepairBox.toast('Payment status updated', 'success'); await this.reload(); }
        },
        async updateServiceStatus(svc, newStatus) {
            const r = await RepairBox.ajax('/repairs/' + this.repair.id + '/services/' + svc.id, 'PUT', { status: newStatus });
            if (r.success !== false) { RepairBox.toast('Service status updated', 'success'); await this.reload(); }
        },

        // ===== SERVICE CHARGE =====
        async saveServiceCharge() {
            if (this.serviceChargeInput === '' || Number(this.serviceChargeInput) < 0) { RepairBox.toast('Enter valid service charge', 'error'); return; }
            const r = await RepairBox.ajax('/repairs/' + this.repair.id + '/service-charge', 'PUT', { service_charge: this.serviceChargeInput });
            if (r.success !== false) { RepairBox.toast('Service charge saved', 'success'); await this.reload(); }
        },

        // ===== PAYMENTS =====
        async collectPayment() {
            if (!this.payForm.amount || Number(this.payForm.amount) <= 0) { RepairBox.toast('Enter payment amount', 'error'); return; }
            const r = await RepairBox.ajax('/repairs/' + this.repair.id + '/payment', 'POST', { payment_type: 'final', payment_method: this.payForm.payment_method, amount: this.payForm.amount });
            if (r.success !== false) {
                RepairBox.toast('Payment collected', 'success');
                this.payForm = { payment_type: 'final', payment_method: 'cash', amount: '' };
                await this.reload();
            }
        },

        // ===== CANCEL WITH REFUND =====
        async cancelWithRefund() {
            if (!this.cancelForm.reason) { RepairBox.toast('Please provide a reason', 'error'); return; }
            const r = await RepairBox.ajax('/repairs/' + this.repair.id + '/cancel-refund', 'POST', this.cancelForm);
            if (r.success !== false) {
                RepairBox.toast('Repair cancelled with refund', 'success');
                this.showCancelRefund = false;
                await this.reload();
            }
        },

        // ===== VOID =====
        async createVoid() {
            if (!this.voidForm.reason) { RepairBox.toast('Please provide a reason', 'error'); return; }
            const r = await RepairBox.ajax('/repairs/' + this.repair.id + '/void', 'POST', this.voidForm);
            if (r.success !== false) {
                RepairBox.toast('Repair marked as void', 'success');
                this.showVoid = false;
                await this.reload();
            }
        },

        // ===== DUPLICATE =====
        async duplicateRepair() {
            if (!confirm('Create a duplicate of this repair?')) return;
            const r = await RepairBox.ajax('/repairs/' + this.repair.id + '/duplicate', 'POST');
            if (r.success !== false) {
                RepairBox.toast('Duplicate created: ' + r.data.ticket_number, 'success');
                window.location.href = '/repairs/' + r.data.id;
            }
        },

        // ===== CALCULATIONS =====
        partsTotal() { return (this.repair.parts || []).reduce((s, p) => s + Number(p.cost_price) * p.quantity, 0); },
        servicesTotal() { return (this.repair.repair_services || []).reduce((s, svc) => s + Number(svc.customer_charge), 0); },
        vendorChargesTotal() { return (this.repair.repair_services || []).reduce((s, svc) => s + Number(svc.vendor_charge), 0); },
        grandTotal() { return this.partsTotal() + Number(this.repair.service_charge || 0) + this.servicesTotal(); },
        totalPaid() { return (this.repair.payments || []).filter(p => p.direction === 'IN').reduce((s, p) => s + Number(p.amount), 0); },
        totalRefunded() { return (this.repair.payments || []).filter(p => p.direction === 'OUT').reduce((s, p) => s + Number(p.amount), 0); },
        balanceDue() { return Math.max(0, this.grandTotal() - this.totalPaid() + this.totalRefunded()); },
    };
}
</script>
@endpush
