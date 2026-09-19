<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContractExpense;
use App\Models\ContractPayment;
use App\Models\PropertyHouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        // Overview stats
        $totalContracts = PropertyHouse::count();
        $totalContractsThisMonth = PropertyHouse::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $totalContractValue = PropertyHouse::sum('price');
        $totalCollected = ContractPayment::sum('amount');
        $totalRemaining = $totalContractValue - $totalCollected;
        $totalExpenses = ContractExpense::sum('amount');
        $totalNetProfit = $totalContractValue - $totalExpenses;
        $avgProfitPerContract = $totalContracts > 0 ? $totalNetProfit / $totalContracts : 0;

        // Recent contracts
        $recentContracts = PropertyHouse::latest()->take(5)->get();

        // Monthly chart data (last 12 months)
        $monthlyData = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->format('Y-m');
            $monthLabel = $date->translatedFormat('M Y');

            $monthContracts = PropertyHouse::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('price');

            $monthPayments = ContractPayment::whereYear('payment_date', $date->year)
                ->whereMonth('payment_date', $date->month)
                ->sum('amount');

            $monthExpenses = ContractExpense::whereYear('expense_date', $date->year)
                ->whereMonth('expense_date', $date->month)
                ->sum('amount');

            $monthlyData[] = [
                'month' => $monthLabel,
                'contracts' => (float) $monthContracts,
                'payments' => (float) $monthPayments,
                'expenses' => (float) $monthExpenses,
                'profit' => (float) ($monthContracts - $monthExpenses),
            ];
        }

        // Payment status breakdown
        $paidCount = 0;
        $partialCount = 0;
        $unpaidCount = 0;
        PropertyHouse::whereNotNull('price')->where('price', '>', 0)->chunk(100, function ($houses) use (&$paidCount, &$partialCount, &$unpaidCount) {
            foreach ($houses as $house) {
                match ($house->payment_status) {
                    'paid' => $paidCount++,
                    'partial' => $partialCount++,
                    default => $unpaidCount++,
                };
            }
        });

        // Contract status breakdown
        $activeContracts = PropertyHouse::where('contract_status', 'active')->count();
        $completedContracts = PropertyHouse::where('contract_status', 'completed')->count();

        return view('admin.dashboard', compact(
            'totalContracts',
            'totalContractsThisMonth',
            'totalContractValue',
            'totalCollected',
            'totalRemaining',
            'totalExpenses',
            'totalNetProfit',
            'avgProfitPerContract',
            'activeContracts',
            'completedContracts',
            'recentContracts',
            'monthlyData',
            'paidCount',
            'partialCount',
            'unpaidCount',
        ));
    }

    /**
     * Monthly report page with month/year selection.
     */
    public function monthlyReport(Request $request): View
    {
        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);

        $contracts = PropertyHouse::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->latest()
            ->get();

        $totalValue = $contracts->sum('price');
        $contractIds = $contracts->pluck('id');

        $totalCollected = ContractPayment::whereIn('property_house_id', $contractIds)->sum('amount');
        $totalExpenses = ContractExpense::whereIn('property_house_id', $contractIds)->sum('amount');
        $totalRemaining = $totalValue - $totalCollected;
        $totalProfit = $totalValue - $totalExpenses;
        $avgValue = $contracts->count() > 0 ? $totalValue / $contracts->count() : 0;
        $avgProfit = $contracts->count() > 0 ? $totalProfit / $contracts->count() : 0;
        $collectionRate = $totalValue > 0 ? ($totalCollected / $totalValue) * 100 : 0;

        return view('admin.reports.monthly', compact(
            'contracts',
            'month',
            'year',
            'totalValue',
            'totalCollected',
            'totalRemaining',
            'totalExpenses',
            'totalProfit',
            'avgValue',
            'avgProfit',
            'collectionRate',
        ));
    }

    /**
     * Export Monthly Report to Excel (.xlsx)
     */
    public function exportMonthlyExcel(Request $request, \App\Services\ReportExcelGenerator $generator): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);

        return $generator->downloadMonthly($year, $month);
    }

    /**
     * Annual report page with month-by-month comparison.
     */
    public function annualReport(Request $request): View
    {
        $year = (int) $request->query('year', now()->year);

        $monthlyBreakdown = [];
        for ($m = 1; $m <= 12; $m++) {
            $contractIds = PropertyHouse::whereYear('created_at', $year)
                ->whereMonth('created_at', $m)
                ->pluck('id');

            $contractsCount = $contractIds->count();
            $revenue = PropertyHouse::whereIn('id', $contractIds)->sum('price');
            $collected = ContractPayment::whereIn('property_house_id', $contractIds)->sum('amount');
            $expenses = ContractExpense::whereIn('property_house_id', $contractIds)->sum('amount');
            $remaining = $revenue - $collected;
            $profit = $revenue - $expenses;
            $collectionRate = $revenue > 0 ? ($collected / $revenue) * 100 : 0;

            $monthlyBreakdown[] = [
                'month' => $m,
                'month_name' => \Carbon\Carbon::create($year, $m)->translatedFormat('F'),
                'contracts_count' => $contractsCount,
                'revenue' => (float) $revenue,
                'collected' => (float) $collected,
                'remaining' => (float) $remaining,
                'expenses' => (float) $expenses,
                'profit' => (float) $profit,
                'collection_rate' => $collectionRate,
            ];
        }

        $totals = [
            'contracts' => array_sum(array_column($monthlyBreakdown, 'contracts_count')),
            'revenue' => array_sum(array_column($monthlyBreakdown, 'revenue')),
            'collected' => array_sum(array_column($monthlyBreakdown, 'collected')),
            'remaining' => array_sum(array_column($monthlyBreakdown, 'remaining')),
            'expenses' => array_sum(array_column($monthlyBreakdown, 'expenses')),
            'profit' => array_sum(array_column($monthlyBreakdown, 'profit')),
        ];

        $annualCollectionRate = $totals['revenue'] > 0 ? ($totals['collected'] / $totals['revenue']) * 100 : 0;
        $profitMargin = $totals['revenue'] > 0 ? ($totals['profit'] / $totals['revenue']) * 100 : 0;

        return view('admin.reports.annual', compact(
            'year',
            'monthlyBreakdown',
            'totals',
            'annualCollectionRate',
            'profitMargin',
        ));
    }

    /**
     * Export Annual Report to Excel (.xlsx)
     */
    public function exportAnnualExcel(Request $request, \App\Services\ReportExcelGenerator $generator): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $year = (int) $request->query('year', now()->year);

        return $generator->downloadAnnual($year);
    }
}
