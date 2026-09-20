<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContractExpense;
use App\Models\ContractPayment;
use App\Models\PropertyHouse;
use App\Services\ReportExcelGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalContracts = PropertyHouse::count();
        $totalContractsThisMonth = PropertyHouse::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $totalContractValue = (float) PropertyHouse::sum('price');
        $totalCollected = (float) ContractPayment::sum('amount');
        $totalRemaining = $totalContractValue - $totalCollected;
        $totalExpenses = (float) ContractExpense::sum('amount');
        $totalNetProfit = $totalContractValue - $totalExpenses;
        $avgProfitPerContract = $totalContracts > 0 ? $totalNetProfit / $totalContracts : 0;

        $recentContracts = PropertyHouse::query()
            ->latest()
            ->withSum('payments', 'amount')
            ->take(5)
            ->get();

        $monthlyData = $this->lastTwelveMonthsChart();
        [$paidCount, $partialCount, $unpaidCount] = $this->paymentStatusCounts();

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

    public function monthlyReport(Request $request): View
    {
        [$month, $year] = ReportExcelGenerator::normalizeMonthYear(
            $request->query('month'),
            $request->query('year'),
        );

        $contracts = PropertyHouse::query()
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->withSum('payments', 'amount')
            ->withSum('expenses', 'amount')
            ->latest()
            ->get();

        $totalValue = (float) $contracts->sum('price');
        $contractIds = $contracts->pluck('id');

        $totalCollected = (float) ContractPayment::whereIn('property_house_id', $contractIds)->sum('amount');
        $totalExpenses = (float) ContractExpense::whereIn('property_house_id', $contractIds)->sum('amount');
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

    public function exportMonthlyExcel(Request $request, ReportExcelGenerator $generator): StreamedResponse
    {
        [$month, $year] = ReportExcelGenerator::normalizeMonthYear(
            $request->query('month'),
            $request->query('year'),
        );

        return $generator->downloadMonthly($year, $month);
    }

    public function annualReport(Request $request, ReportExcelGenerator $generator): View
    {
        $year = ReportExcelGenerator::normalizeYear($request->query('year'));
        ['monthly' => $monthlyBreakdown, 'totals' => $totals] = $generator->annualBreakdown($year);

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

    public function exportAnnualExcel(Request $request, ReportExcelGenerator $generator): StreamedResponse
    {
        $year = ReportExcelGenerator::normalizeYear($request->query('year'));

        return $generator->downloadAnnual($year);
    }

    /**
     * @return list<array{month: string, contracts: float, payments: float, expenses: float, profit: float}>
     */
    private function lastTwelveMonthsChart(): array
    {
        $from = now()->copy()->startOfMonth()->subMonths(11);

        $contractYm = $this->yearMonthExpression('created_at');
        $paymentYm = $this->yearMonthExpression('payment_date');
        $expenseYm = $this->yearMonthExpression('expense_date');

        $contractsByMonth = PropertyHouse::query()
            ->where('created_at', '>=', $from)
            ->selectRaw("{$contractYm} as ym, COALESCE(SUM(price), 0) as total")
            ->groupByRaw($contractYm)
            ->pluck('total', 'ym');

        $paymentsByMonth = ContractPayment::query()
            ->where('payment_date', '>=', $from->toDateString())
            ->selectRaw("{$paymentYm} as ym, COALESCE(SUM(amount), 0) as total")
            ->groupByRaw($paymentYm)
            ->pluck('total', 'ym');

        $expensesByMonth = ContractExpense::query()
            ->where('expense_date', '>=', $from->toDateString())
            ->selectRaw("{$expenseYm} as ym, COALESCE(SUM(amount), 0) as total")
            ->groupByRaw($expenseYm)
            ->pluck('total', 'ym');

        $monthlyData = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $key = $date->format('Y-m');
            $contracts = (float) ($contractsByMonth[$key] ?? 0);
            $payments = (float) ($paymentsByMonth[$key] ?? 0);
            $expenses = (float) ($expensesByMonth[$key] ?? 0);

            $monthlyData[] = [
                'month' => $date->translatedFormat('M Y'),
                'contracts' => $contracts,
                'payments' => $payments,
                'expenses' => $expenses,
                'profit' => $contracts - $expenses,
            ];
        }

        return $monthlyData;
    }

    /**
     * @return array{0: int, 1: int, 2: int}
     */
    private function paymentStatusCounts(): array
    {
        $paidSub = ContractPayment::query()
            ->selectRaw('property_house_id, SUM(amount) as paid')
            ->groupBy('property_house_id');

        $bucketSql = "CASE
            WHEN COALESCE(p.paid, 0) <= 0 THEN 'unpaid'
            WHEN COALESCE(p.paid, 0) >= property_houses.price THEN 'paid'
            ELSE 'partial'
        END";

        $rows = PropertyHouse::query()
            ->where('price', '>', 0)
            ->leftJoinSub($paidSub, 'p', 'p.property_house_id', '=', 'property_houses.id')
            ->selectRaw("{$bucketSql} as payment_bucket")
            ->selectRaw('COUNT(*) as aggregate')
            ->groupByRaw($bucketSql)
            ->pluck('aggregate', 'payment_bucket');

        return [
            (int) ($rows['paid'] ?? 0),
            (int) ($rows['partial'] ?? 0),
            (int) ($rows['unpaid'] ?? 0),
        ];
    }

    private function yearMonthExpression(string $column): string
    {
        $column = match ($column) {
            'created_at', 'payment_date', 'expense_date' => $column,
            default => throw new \InvalidArgumentException('Invalid date column.'),
        };

        return DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', {$column})"
            : "DATE_FORMAT({$column}, '%Y-%m')";
    }
}
