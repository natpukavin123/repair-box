@extends('layouts.app')
@section('page-title', 'Dashboard')

@section('content')
<div x-data="dashboardPage()" x-init="loadStats()">
    <!-- Stats Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Today Sales</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1" x-text="formatCurrency(stats.today_sales)">₹0</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Monthly Sales</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1" x-text="formatCurrency(stats.monthly_sales)">₹0</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Today Repairs</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1" x-text="stats.today_repairs">0</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Pending Repairs</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1" x-text="stats.pending_repairs">0</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="stat-card">
            <p class="text-xs text-gray-500 uppercase">Monthly Expenses</p>
            <p class="text-xl font-bold text-red-600 mt-1" x-text="formatCurrency(stats.monthly_expenses)">₹0</p>
        </div>
        <div class="stat-card">
            <p class="text-xs text-gray-500 uppercase">Monthly Purchases</p>
            <p class="text-xl font-bold text-yellow-600 mt-1" x-text="formatCurrency(stats.monthly_purchases)">₹0</p>
        </div>
        <div class="stat-card">
            <p class="text-xs text-gray-500 uppercase">Today Recharges</p>
            <p class="text-xl font-bold text-purple-600 mt-1" x-text="formatCurrency(stats.today_recharges)">₹0</p>
        </div>
        <div class="stat-card">
            <p class="text-xs text-gray-500 uppercase">Monthly Net</p>
            <p class="text-xl font-bold mt-1" :class="(stats.monthly_revenue - stats.monthly_outflow) >= 0 ? 'text-green-600' : 'text-red-600'" x-text="formatCurrency(stats.monthly_revenue - stats.monthly_outflow)">₹0</p>
        </div>
    </div>

    <!-- Quick Actions Grid -->
    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3 mb-6">
        <a href="/pos" class="card hover:shadow-lg transition-shadow p-4 text-center group">
            <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center mx-auto group-hover:bg-primary-200 transition-colors">
                <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            </div>
            <p class="text-xs font-medium text-gray-700 mt-2">New Bill</p>
        </a>
        <a href="/repairs" class="card hover:shadow-lg transition-shadow p-4 text-center group" onclick="event.preventDefault(); window.location='/repairs?new=1'">
            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center mx-auto group-hover:bg-orange-200 transition-colors">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <p class="text-xs font-medium text-gray-700 mt-2">New Repair</p>
        </a>
        <a href="/customers" class="card hover:shadow-lg transition-shadow p-4 text-center group">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mx-auto group-hover:bg-green-200 transition-colors">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            </div>
            <p class="text-xs font-medium text-gray-700 mt-2">Add Customer</p>
        </a>
        <a href="/recharges" class="card hover:shadow-lg transition-shadow p-4 text-center group">
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mx-auto group-hover:bg-purple-200 transition-colors">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            </div>
            <p class="text-xs font-medium text-gray-700 mt-2">Recharge</p>
        </a>
        <a href="/reports" class="card hover:shadow-lg transition-shadow p-4 text-center group">
            <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center mx-auto group-hover:bg-indigo-200 transition-colors">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <p class="text-xs font-medium text-gray-700 mt-2">Reports</p>
        </a>
        <a href="/expenses" class="card hover:shadow-lg transition-shadow p-4 text-center group">
            <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mx-auto group-hover:bg-red-200 transition-colors">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <p class="text-xs font-medium text-gray-700 mt-2">Expense</p>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Invoices -->
        <div class="card">
            <div class="card-header"><h3 class="font-semibold text-gray-800">Recent Invoices</h3></div>
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead><tr><th>#</th><th>Customer</th><th>Amount</th><th>Date</th></tr></thead>
                        <tbody>
                            <template x-for="inv in stats.recent_invoices" :key="inv.id">
                                <tr>
                                    <td><span class="text-primary-600 font-medium" x-text="inv.invoice_number"></span></td>
                                    <td x-text="inv.customer ? inv.customer.name : 'Walk-in'"></td>
                                    <td x-text="formatCurrency(inv.total_amount)"></td>
                                    <td x-text="new Date(inv.created_at).toLocaleDateString()"></td>
                                </tr>
                            </template>
                            <tr x-show="!stats.recent_invoices || stats.recent_invoices.length === 0">
                                <td colspan="4" class="text-center text-gray-400 py-6">No invoices yet</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Repairs -->
        <div class="card">
            <div class="card-header"><h3 class="font-semibold text-gray-800">Recent Repairs</h3></div>
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead><tr><th>Ticket</th><th>Customer</th><th>Status</th><th>Date</th></tr></thead>
                        <tbody>
                            <template x-for="rep in stats.recent_repairs" :key="rep.id">
                                <tr>
                                    <td><span class="text-primary-600 font-medium" x-text="rep.ticket_number"></span></td>
                                    <td x-text="rep.customer ? rep.customer.name : 'N/A'"></td>
                                    <td><span class="badge" :class="statusBadge(rep.status)" x-text="rep.status"></span></td>
                                    <td x-text="new Date(rep.created_at).toLocaleDateString()"></td>
                                </tr>
                            </template>
                            <tr x-show="!stats.recent_repairs || stats.recent_repairs.length === 0">
                                <td colspan="4" class="text-center text-gray-400 py-6">No repairs yet</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function dashboardPage() {
    return {
        stats: { today_sales: 0, monthly_sales: 0, today_repairs: 0, pending_repairs: 0, monthly_expenses: 0, monthly_purchases: 0, today_recharges: 0, monthly_revenue: 0, monthly_outflow: 0, recent_invoices: [], recent_repairs: [], sales_chart: { labels: [], data: [] } },
        formatCurrency(val) { return '₹' + Number(val || 0).toLocaleString('en-IN', { minimumFractionDigits: 2 }); },
        statusBadge(s) {
            const m = { 'received': 'badge-info', 'in_progress': 'badge-warning', 'completed': 'badge-success', 'delivered': 'badge-success', 'cancelled': 'badge-danger' };
            return m[s] || 'badge-secondary';
        },
        async loadStats() {
            const res = await RepairBox.ajax('/dashboard');
            if (res.success !== false) Object.assign(this.stats, res);
        }
    };
}
</script>
@endpush
