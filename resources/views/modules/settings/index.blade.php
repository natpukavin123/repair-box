@extends('layouts.app')
@section('page-title', 'Settings')

@section('content')
<div x-data="settingsPage()" x-init="init()">
    {{-- Tabs --}}
    <div class="flex flex-wrap gap-2 mb-6">
        <button @click="tab='general'; updateUrl()" :class="tab==='general' ? 'btn-primary' : 'btn-secondary'" class="text-sm">General</button>
        <button @click="tab='service-types'; updateUrl()" :class="tab==='service-types' ? 'btn-primary' : 'btn-secondary'" class="text-sm">Service Types</button>
        <button @click="tab='recharge-providers'; updateUrl()" :class="tab==='recharge-providers' ? 'btn-primary' : 'btn-secondary'" class="text-sm">Recharge Providers</button>
        <button @click="tab='email-templates'; updateUrl()" :class="tab==='email-templates' ? 'btn-primary' : 'btn-secondary'" class="text-sm">Email Templates</button>
        <button @click="tab='backups'; updateUrl()" :class="tab==='backups' ? 'btn-primary' : 'btn-secondary'" class="text-sm">Backups</button>
    </div>

    {{-- General Settings --}}
    <div x-show="tab==='general'" class="card">
        <div class="card-header"><h3 class="text-lg font-semibold">General Settings</h3></div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <template x-for="key in settingKeys" :key="key">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1" x-text="formatLabel(key)"></label>
                        <input :value="settings[key] || ''" @input="settings[key] = $event.target.value" type="text" class="form-input-custom">
                    </div>
                </template>
            </div>
            <div class="mt-4">
                <button @click="saveSettings()" class="btn-primary" :disabled="saving"><span x-show="saving" class="spinner mr-1"></span> Save Settings</button>
            </div>
        </div>
    </div>

    {{-- Service Types --}}
    <div x-show="tab==='service-types'" class="card">
        <div class="card-header flex items-center justify-between">
            <h3 class="text-lg font-semibold">Service Types</h3>
            <button @click="stForm={name:'',default_price:'',description:''}; stEditing=null; showStModal=true" class="btn-primary text-sm">Add Type</button>
        </div>
        <div class="card-body p-0">
            <table class="data-table">
                <thead><tr><th>Name</th><th>Default Price</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    <template x-for="st in serviceTypes" :key="st.id">
                        <tr>
                            <td class="font-medium" x-text="st.name"></td>
                            <td x-text="st.default_price ? RepairBox.formatCurrency(st.default_price) : '-'"></td>
                            <td><span class="badge" :class="st.status==='active' ? 'badge-success' : 'badge-danger'" x-text="st.status || 'active'"></span></td>
                            <td><button @click="stEditing=st.id; stForm={name:st.name,default_price:st.default_price||'',description:st.description||'',status:st.status||'active'}; showStModal=true" class="text-primary-600 hover:text-primary-800"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button></td>
                        </tr>
                    </template>
                    <tr x-show="serviceTypes.length===0"><td colspan="4" class="text-center text-gray-400 py-6">No service types</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recharge Providers --}}
    <div x-show="tab==='recharge-providers'" class="card">
        <div class="card-header flex items-center justify-between">
            <h3 class="text-lg font-semibold">Recharge Providers</h3>
            <button @click="rpForm={name:'',provider_type:'',commission_percentage:''}; showRpModal=true" class="btn-primary text-sm">Add Provider</button>
        </div>
        <div class="card-body p-0">
            <table class="data-table">
                <thead><tr><th>Name</th><th>Type</th><th>Commission %</th></tr></thead>
                <tbody>
                    <template x-for="rp in rechargeProviders" :key="rp.id"><tr><td class="font-medium" x-text="rp.name"></td><td x-text="rp.provider_type"></td><td x-text="rp.commission_percentage + '%'"></td></tr></template>
                    <tr x-show="rechargeProviders.length===0"><td colspan="3" class="text-center text-gray-400 py-6">No providers</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Email Templates --}}
    <div x-show="tab==='email-templates'" class="card">
        <div class="card-header"><h3 class="text-lg font-semibold">Email Templates</h3></div>
        <div class="card-body p-0">
            <table class="data-table">
                <thead><tr><th>Name</th><th>Subject</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    <template x-for="et in emailTemplates" :key="et.id">
                        <tr>
                            <td class="font-medium" x-text="et.name"></td>
                            <td x-text="et.subject"></td>
                            <td><span class="badge" :class="et.status==='active' ? 'badge-success' : 'badge-danger'" x-text="et.status"></span></td>
                            <td><button @click="etEditing=et; etForm={subject:et.subject,body:et.body||'',status:et.status}; showEtModal=true" class="text-primary-600 hover:text-primary-800"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button></td>
                        </tr>
                    </template>
                    <tr x-show="emailTemplates.length===0"><td colspan="4" class="text-center text-gray-400 py-6">No templates</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Backups --}}
    <div x-show="tab==='backups'" class="card">
        <div class="card-header flex items-center justify-between">
            <h3 class="text-lg font-semibold">Backups</h3>
            <button @click="createBackup()" class="btn-primary text-sm" :disabled="saving"><span x-show="saving" class="spinner mr-1"></span> Create Backup</button>
        </div>
        <div class="card-body p-0">
            <table class="data-table">
                <thead><tr><th>Type</th><th>File</th><th>Size</th><th>Status</th><th>Date</th></tr></thead>
                <tbody>
                    <template x-for="b in backups" :key="b.id">
                        <tr>
                            <td x-text="b.backup_type"></td>
                            <td class="text-sm" x-text="b.file_path"></td>
                            <td x-text="b.file_size ? (b.file_size / 1024).toFixed(1)+' KB' : '-'"></td>
                            <td><span class="badge badge-success" x-text="b.status"></span></td>
                            <td x-text="new Date(b.created_at).toLocaleString()"></td>
                        </tr>
                    </template>
                    <tr x-show="backups.length===0"><td colspan="5" class="text-center text-gray-400 py-6">No backups</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Service Type Modal --}}
    <div x-show="showStModal" class="modal-overlay" x-cloak @click.self="showStModal=false">
        <div class="modal-container">
            <div class="modal-header"><h3 class="text-lg font-semibold" x-text="stEditing ? 'Edit Service Type' : 'Add Service Type'"></h3><button @click="showStModal=false" class="text-gray-400 hover:text-gray-600">&times;</button></div>
            <div class="modal-body space-y-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Name *</label><input x-model="stForm.name" type="text" class="form-input-custom"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Default Price</label><input x-model="stForm.default_price" type="number" step="0.01" class="form-input-custom"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Description</label><textarea x-model="stForm.description" class="form-input-custom" rows="2"></textarea></div>
                <template x-if="stEditing">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select x-model="stForm.status" class="form-select-custom"><option value="active">Active</option><option value="inactive">Inactive</option></select>
                    </div>
                </template>
            </div>
            <div class="modal-footer"><button @click="showStModal=false" class="btn-secondary">Cancel</button><button @click="saveServiceType()" class="btn-primary" :disabled="saving"><span x-show="saving" class="spinner mr-1"></span> Save</button></div>
        </div>
    </div>

    {{-- Recharge Provider Modal --}}
    <div x-show="showRpModal" class="modal-overlay" x-cloak @click.self="showRpModal=false">
        <div class="modal-container">
            <div class="modal-header"><h3 class="text-lg font-semibold">Add Recharge Provider</h3><button @click="showRpModal=false" class="text-gray-400 hover:text-gray-600">&times;</button></div>
            <div class="modal-body space-y-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Name *</label><input x-model="rpForm.name" type="text" class="form-input-custom"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                    <select x-model="rpForm.provider_type" class="form-select-custom"><option value="">Select</option><option value="mobile">Mobile</option><option value="dth">DTH</option><option value="data_card">Data Card</option><option value="electricity">Electricity</option></select>
                </div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Commission % *</label><input x-model="rpForm.commission_percentage" type="number" step="0.01" min="0" max="100" class="form-input-custom" placeholder="e.g. 3.5"></div>
            </div>
            <div class="modal-footer"><button @click="showRpModal=false" class="btn-secondary">Cancel</button><button @click="saveRechargeProvider()" class="btn-primary" :disabled="saving"><span x-show="saving" class="spinner mr-1"></span> Save</button></div>
        </div>
    </div>

    {{-- Email Template Modal --}}
    <div x-show="showEtModal" class="modal-overlay" x-cloak @click.self="showEtModal=false">
        <div class="modal-container modal-lg">
            <div class="modal-header"><h3 class="text-lg font-semibold">Edit Email Template</h3><button @click="showEtModal=false" class="text-gray-400 hover:text-gray-600">&times;</button></div>
            <div class="modal-body space-y-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Subject</label><input x-model="etForm.subject" type="text" class="form-input-custom"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Body</label><textarea x-model="etForm.body" class="form-input-custom" rows="8"></textarea></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select x-model="etForm.status" class="form-select-custom"><option value="active">Active</option><option value="inactive">Inactive</option></select>
                </div>
            </div>
            <div class="modal-footer"><button @click="showEtModal=false" class="btn-secondary">Cancel</button><button @click="saveEmailTemplate()" class="btn-primary" :disabled="saving"><span x-show="saving" class="spinner mr-1"></span> Save</button></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function settingsPage() {
    return {
        tab: 'general', saving: false,
        settings: {}, settingKeys: ['shop_name','shop_address','shop_phone','shop_email','shop_gst','currency_symbol','invoice_prefix','repair_prefix','tax_percentage','low_stock_threshold'],
        serviceTypes: [], showStModal: false, stEditing: null, stForm: {},
        rechargeProviders: [], showRpModal: false, rpForm: {},
        emailTemplates: [], showEtModal: false, etEditing: null, etForm: {},
        backups: [],
        init() {
            const p = new URLSearchParams(window.location.search);
            if (p.has('tab')) this.tab = p.get('tab');
            this.load();
        },
        updateUrl() {
            const params = new URLSearchParams();
            if (this.tab !== 'general') params.set('tab', this.tab);
            const qs = params.toString();
            history.replaceState(null, '', window.location.pathname + (qs ? '?' + qs : ''));
        },
        async load() {
            const [s, st, rp, et, b] = await Promise.all([
                RepairBox.ajax('/settings'), RepairBox.ajax('/service-types'),
                RepairBox.ajax('/recharge-providers'), RepairBox.ajax('/email-templates'),
                RepairBox.ajax('/backups')
            ]);
            if(s.data) this.settings = s.data; if(st.data) this.serviceTypes = st.data;
            if(rp.data) this.rechargeProviders = rp.data; if(et.data) this.emailTemplates = et.data;
            if(b.data) this.backups = b.data;
        },
        formatLabel(key) { return key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()); },
        async saveSettings() {
            this.saving = true;
            const r = await RepairBox.ajax('/settings', 'PUT', { settings: this.settings });
            this.saving = false; if(r.success !== false) RepairBox.toast('Settings saved', 'success');
        },
        async saveServiceType() {
            this.saving = true;
            const r = await RepairBox.ajax(this.stEditing ? `/service-types/${this.stEditing}` : '/service-types', this.stEditing ? 'PUT' : 'POST', this.stForm);
            this.saving = false; if(r.success !== false) { RepairBox.toast('Saved', 'success'); this.showStModal = false; const st = await RepairBox.ajax('/service-types'); if(st.data) this.serviceTypes = st.data; }
        },
        async saveRechargeProvider() {
            this.saving = true;
            const r = await RepairBox.ajax('/recharge-providers', 'POST', this.rpForm);
            this.saving = false; if(r.success !== false) { RepairBox.toast('Saved', 'success'); this.showRpModal = false; const rp = await RepairBox.ajax('/recharge-providers'); if(rp.data) this.rechargeProviders = rp.data; }
        },
        async saveEmailTemplate() {
            this.saving = true;
            const r = await RepairBox.ajax(`/email-templates/${this.etEditing.id}`, 'PUT', this.etForm);
            this.saving = false; if(r.success !== false) { RepairBox.toast('Saved', 'success'); this.showEtModal = false; const et = await RepairBox.ajax('/email-templates'); if(et.data) this.emailTemplates = et.data; }
        },
        async createBackup() {
            this.saving = true;
            const r = await RepairBox.ajax('/backups', 'POST');
            this.saving = false; if(r.success !== false) { RepairBox.toast('Backup created', 'success'); const b = await RepairBox.ajax('/backups'); if(b.data) this.backups = b.data; }
        }
    };
}
</script>
@endpush
