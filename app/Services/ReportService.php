<?php

namespace App\Services;

use App\Models\{Invoice, Repair, Recharge, Expense, LedgerTransaction, Purchase};
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function getDashboardStats(): array
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        return [
            'today_sales' => Invoice::whereDate('created_at', $today)->sum('final_amount'),
            'monthly_sales' => Invoice::where('created_at', '>=', $thisMonth)->sum('final_amount'),
            'today_repairs' => Repair::whereDate('created_at', $today)->where('record_type', '!=', 'void')->count(),
            'pending_repairs' => Repair::whereNotIn('status', ['completed', 'delivered', 'cancelled'])->where('record_type', '!=', 'void')->count(),
            'monthly_expenses' => Expense::where('expense_date', '>=', $thisMonth)->sum('amount'),
            'monthly_purchases' => Purchase::where('purchase_date', '>=', $thisMonth)->sum('total_amount'),
            'today_recharges' => Recharge::whereDate('created_at', $today)->sum('recharge_amount'),
            'monthly_revenue' => LedgerTransaction::where('direction', 'IN')
                ->where('created_at', '>=', $thisMonth)->sum('amount'),
            'monthly_outflow' => LedgerTransaction::where('direction', 'OUT')
                ->where('created_at', '>=', $thisMonth)->sum('amount'),
            'recent_invoices' => Invoice::with('customer')->latest()->take(5)->get(),
            'recent_repairs' => Repair::with('customer')->where('record_type', '!=', 'void')->latest()->take(5)->get(),
            'sales_chart' => $this->getSalesChart(),
        ];
    }

    private function getSalesChart(): array
    {
        $labels = [];
        $sales = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('M d');
            $sales[] = (float) Invoice::whereDate('created_at', $date)->sum('final_amount');
        }
        return ['labels' => $labels, 'data' => $sales];
    }

    public function getSalesReport(string $from, string $to): array
    {
        return [
            'invoices' => Invoice::with('customer', 'items')
                ->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()])
                ->get(),
            'total' => Invoice::whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()])->sum('final_amount'),
        ];
    }

    public function getProfitReport(string $from, string $to): array
    {
        $revenue = LedgerTransaction::where('direction', 'IN')
            ->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()])
            ->sum('amount');
        $expenses = LedgerTransaction::where('direction', 'OUT')
            ->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()])
            ->sum('amount');

        return [
            'revenue' => $revenue,
            'expenses' => $expenses,
            'profit' => $revenue - $expenses,
        ];
    }
}
