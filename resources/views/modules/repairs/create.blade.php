@extends('layouts.app')
@section('page-title', 'Create Repair')

@section('content')
<div x-data="createRepairPage()" class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Create New Repair</h2>
            <p class="text-sm text-gray-500 mt-0.5">Fill in the details to create a repair ticket</p>
        </div>
        <a href="/repairs" class="btn-secondary inline-flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Customer -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Customer *</label>
                    <div class="flex gap-2">
                        <div class="relative flex-1">
                            <input x-model="custSearch" @input.debounce.300ms="findCustomers()" type="text" class="form-input-custom" placeholder="Search customer by name or mobile...">
                            <div x-show="custResults.length > 0" class="absolute z-20 w-full bg-white border rounded-lg shadow-lg mt-1 max-h-40 overflow-y-auto">
                                <template x-for="c in custResults" :key="c.id">
                                    <button @click="selectCustomer(c)" class="w-full text-left px-4 py-2.5 hover:bg-primary-50 text-sm border-b last:border-0 transition">
                                        <span class="font-medium" x-text="c.name"></span>
                                        <span class="text-gray-400 ml-2" x-text="c.mobile_number"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                        <button type="button" @click="showAddCust = true; newCust = {name:'', mobile_number:'', email:'', address:''}" class="btn-secondary text-sm px-3 whitespace-nowrap inline-flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                            New
                        </button>
                    </div>
                    <div x-show="form.customer_id" class="mt-2 inline-flex items-center gap-2 bg-primary-50 text-primary-700 px-3 py-1.5 rounded-lg text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span class="font-medium" x-text="selectedCust?.name"></span>
                        <button @click="form.customer_id = null; selectedCust = null" class="text-primary-400 hover:text-red-500 ml-1">&times;</button>
                    </div>
                </div>
                <div x-data="{ brandOpen: false, brandSearch: '', get filteredBrands() { const q = this.brandSearch.toLowerCase(); return this.brandList.filter(b => b.toLowerCase().includes(q)); } }" x-init="$watch('form.device_brand', v => brandSearch = v)" @click.away="brandOpen = false" class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Device Brand</label>
                    <input x-model="form.device_brand" @focus="brandOpen = true; brandSearch = form.device_brand" @input="brandOpen = true; brandSearch = form.device_brand" type="text" class="form-input-custom" placeholder="e.g. Samsung, Apple" autocomplete="off">
                    <div x-show="brandOpen && filteredBrands.length > 0" x-cloak class="absolute z-20 w-full bg-white border rounded-lg shadow-lg mt-1 max-h-40 overflow-y-auto">
                        <template x-for="b in filteredBrands" :key="b">
                            <button type="button" @click="form.device_brand = b; brandOpen = false" class="w-full text-left px-4 py-2 hover:bg-primary-50 text-sm border-b last:border-0 transition" x-text="b"></button>
                        </template>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Device Model</label>
                    <input x-model="form.device_model" type="text" class="form-input-custom" placeholder="e.g. Galaxy S24, iPhone 15">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">IMEI / Serial No.</label>
                    <input x-model="form.imei" type="text" class="form-input-custom" placeholder="Device IMEI or serial number">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estimated Cost (₹)</label>
                    <input x-model="form.estimated_cost" type="number" step="0.01" class="form-input-custom" placeholder="0.00">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Problem Description</label>
                    <textarea x-model="form.problem_description" class="form-input-custom" rows="2" placeholder="Describe the issue..."></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Expected Delivery</label>
                    <input x-model="form.expected_delivery_date" type="date" class="form-input-custom">
                </div>
                <!-- Advance Payment Section -->
                <div class="md:col-span-2 bg-amber-50 border border-amber-200 rounded-lg p-4 mt-1">
                    <h4 class="text-sm font-semibold text-amber-800 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Advance Payment (Optional)
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <label class="text-xs text-gray-600 mb-1 block">Amount (₹)</label>
                            <input x-model="form.advance_amount" type="number" step="0.01" class="form-input-custom text-sm" placeholder="0.00">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600 mb-1 block">Method</label>
                            <select x-model="form.advance_method" class="form-select-custom text-sm w-full">
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="upi">UPI</option>
                                <option value="bank_transfer">Bank Transfer</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs text-gray-600 mb-1 block">Reference</label>
                            <input x-model="form.advance_reference" type="text" class="form-input-custom text-sm" placeholder="Transaction ID">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer flex justify-end gap-3">
            <a href="/repairs" class="btn-secondary">Cancel</a>
            <button @click="save()" class="btn-primary inline-flex items-center gap-2" :disabled="saving">
                <span x-show="saving" class="spinner"></span>
                Create Repair
            </button>
        </div>
    </div>

    <!-- Add Customer Modal -->
    <div x-show="showAddCust" class="modal-overlay" x-cloak>
        <div class="modal-container max-w-md" @click.away="showAddCust = false">
            <div class="modal-header">
                <h3 class="text-lg font-bold">Add Customer</h3>
                <button @click="showAddCust = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <div class="modal-body">
                <div class="space-y-3">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Name *</label><input x-model="newCust.name" type="text" class="form-input-custom"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Mobile *</label><input x-model="newCust.mobile_number" type="text" class="form-input-custom"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Email</label><input x-model="newCust.email" type="email" class="form-input-custom"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Address</label><input x-model="newCust.address" type="text" class="form-input-custom"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button @click="showAddCust = false" class="btn-secondary">Cancel</button>
                <button @click="saveNewCust()" class="btn-primary">Save & Select</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function createRepairPage() {
    return {
        saving: false,
        showAddCust: false,
        custSearch: '', custResults: [], selectedCust: null,
        newCust: { name: '', mobile_number: '', email: '', address: '' },
        brandList: @json($brands),
        form: {
            customer_id: null, device_brand: '', device_model: '', imei: '',
            problem_description: '', estimated_cost: '', expected_delivery_date: '',
            advance_amount: '', advance_method: 'cash', advance_reference: ''
        },

        async findCustomers() {
            if (this.custSearch.length < 2) { this.custResults = []; return; }
            const r = await RepairBox.ajax('/customers-search?q=' + encodeURIComponent(this.custSearch));
            if (r.data) this.custResults = r.data;
        },
        selectCustomer(c) { this.selectedCust = c; this.form.customer_id = c.id; this.custResults = []; this.custSearch = ''; },
        async saveNewCust() {
            if (!this.newCust.name || !this.newCust.mobile_number) { RepairBox.toast('Name and mobile are required', 'error'); return; }
            const r = await RepairBox.ajax('/customers', 'POST', this.newCust);
            if (r.success !== false && r.data) { this.selectCustomer(r.data); this.showAddCust = false; RepairBox.toast('Customer added', 'success'); }
        },
        async save() {
            if (!this.form.customer_id) { RepairBox.toast('Please select a customer', 'error'); return; }
            this.saving = true;
            const r = await RepairBox.ajax('/repairs', 'POST', this.form);
            this.saving = false;
            if (r.success !== false) {
                RepairBox.toast('Repair created: ' + r.data.ticket_number, 'success');
                window.location.href = '/repairs/' + r.data.id;
            }
        },
    };
}
</script>
@endpush
