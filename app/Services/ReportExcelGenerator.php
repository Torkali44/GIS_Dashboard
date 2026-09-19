<?php

namespace App\Services;

use App\Models\ContractExpense;
use App\Models\ContractPayment;
use App\Models\PropertyHouse;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportExcelGenerator
{
    /**
     * Generate and download Monthly Excel Report (.xlsx)
     */
    public function downloadMonthly(int $year, int $month): StreamedResponse
    {
        $monthName = Carbon::create($year, $month)->translatedFormat('F');
        $contracts = PropertyHouse::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->with(['payments', 'expenses'])
            ->latest()
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("تقرير {$monthName} {$year}");
        $sheet->setRightToLeft(true);

        // Header Title
        $sheet->mergeCells('A1:K1');
        $sheet->setCellValue('A1', 'شركة جي أي إس للتجارة والمقاولات والتقييم والتثمين — GIS Trading & Contracting');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '064E3B']],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(32);

        // Subtitle
        $sheet->mergeCells('A2:K2');
        $sheet->setCellValue('A2', "تقرير الحسابات والعقود الشهري — {$monthName} {$year}");
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '064E3B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'ECFDF5']],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(24);

        // Summary KPI Box
        $totalContracts = $contracts->count();
        $totalValue = (float) $contracts->sum('price');
        $contractIds = $contracts->pluck('id');
        $totalCollected = (float) ContractPayment::whereIn('property_house_id', $contractIds)->sum('amount');
        $totalExpenses = (float) ContractExpense::whereIn('property_house_id', $contractIds)->sum('amount');
        $totalRemaining = $totalValue - $totalCollected;
        $totalProfit = $totalValue - $totalExpenses;
        $collectionRate = $totalValue > 0 ? ($totalCollected / $totalValue) * 100 : 0;

        $sheet->setCellValue('B4', 'عدد العقود:');
        $sheet->setCellValue('C4', $totalContracts);
        $sheet->setCellValue('E4', 'قيمة العقود:');
        $sheet->setCellValue('F4', $totalValue);
        $sheet->setCellValue('H4', 'المحصل:');
        $sheet->setCellValue('I4', $totalCollected);

        $sheet->setCellValue('B5', 'المتبقي:');
        $sheet->setCellValue('C5', $totalRemaining);
        $sheet->setCellValue('E5', 'المصروفات:');
        $sheet->setCellValue('F5', $totalExpenses);
        $sheet->setCellValue('H5', 'صافي الربح:');
        $sheet->setCellValue('I5', $totalProfit);

        $sheet->getStyle('B4:I5')->applyFromArray([
            'font' => ['bold' => true, 'size' => 10],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle('C4:I5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F4')->getNumberFormat()->setFormatCode('#,##0.00 "د.ب"');
        $sheet->getStyle('I4')->getNumberFormat()->setFormatCode('#,##0.00 "د.ب"');
        $sheet->getStyle('C5')->getNumberFormat()->setFormatCode('#,##0.00 "د.ب"');
        $sheet->getStyle('F5')->getNumberFormat()->setFormatCode('#,##0.00 "د.ب"');
        $sheet->getStyle('I5')->getNumberFormat()->setFormatCode('#,##0.00 "د.ب"');

        // Table Headers
        $headers = [
            'A7' => '#',
            'B7' => 'رقم العقد',
            'C7' => 'اسم العميل',
            'D7' => 'الهاتف',
            'E7' => 'العقار / المنطقة',
            'F7' => 'قيمة العقد (د.ب)',
            'G7' => 'المدفوع (د.ب)',
            'H7' => 'المتبقي (د.ب)',
            'I7' => 'المصروفات (د.ب)',
            'J7' => 'صافي الربح (د.ب)',
            'K7' => 'حالة الدفع',
        ];

        foreach ($headers as $cell => $title) {
            $sheet->setCellValue($cell, $title);
        }

        $sheet->getStyle('A7:K7')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '047857']],
        ]);
        $sheet->getRowDimension(7)->setRowHeight(26);

        // Data Rows
        $row = 8;
        $idx = 1;
        foreach ($contracts as $c) {
            $sheet->setCellValue("A{$row}", $idx++);
            $sheet->setCellValue("B{$row}", $c->contract_number ?? '—');
            $sheet->setCellValue("C{$row}", $c->buyer_name ?? $c->client_name ?? '—');
            $sheet->setCellValue("D{$row}", $c->phone ?? '—');
            $sheet->setCellValue("E{$row}", $c->area ?? $c->address ?? '—');
            $sheet->setCellValue("F{$row}", (float) ($c->price ?? 0));
            $sheet->setCellValue("G{$row}", (float) $c->total_paid);
            $sheet->setCellValue("H{$row}", (float) $c->remaining_amount);
            $sheet->setCellValue("I{$row}", (float) $c->total_expenses);
            $sheet->setCellValue("J{$row}", (float) $c->net_profit);
            $sheet->setCellValue("K{$row}", $c->payment_status_label);

            // Row style
            $sheet->getStyle("A{$row}:K{$row}")->applyFromArray([
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']],
                ],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("K{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getStyle("F{$row}:J{$row}")->getNumberFormat()->setFormatCode('#,##0.00');

            if ($c->net_profit >= 0) {
                $sheet->getStyle("J{$row}")->getFont()->getColor()->setRGB('047857');
            } else {
                $sheet->getStyle("J{$row}")->getFont()->getColor()->setRGB('DC2626');
            }

            $sheet->getRowDimension($row)->setRowHeight(22);
            $row++;
        }

        // Summary Total Row
        $sheet->setCellValue("A{$row}", 'الإجمالي');
        $sheet->mergeCells("A{$row}:E{$row}");
        $sheet->setCellValue("F{$row}", "=SUM(F8:F" . ($row - 1) . ")");
        $sheet->setCellValue("G{$row}", "=SUM(G8:G" . ($row - 1) . ")");
        $sheet->setCellValue("H{$row}", "=SUM(H8:H" . ($row - 1) . ")");
        $sheet->setCellValue("I{$row}", "=SUM(I8:I" . ($row - 1) . ")");
        $sheet->setCellValue("J{$row}", "=SUM(J8:J" . ($row - 1) . ")");
        $sheet->setCellValue("K{$row}", "نسبة التحصيل: " . number_format($collectionRate, 1) . "%");

        $sheet->getStyle("A{$row}:K{$row}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '064E3B']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D1FAE5']],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '047857']],
            ],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("F{$row}:J{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle("K{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension($row)->setRowHeight(26);

        // Auto-fit column widths
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = "GIS-Monthly-Report-{$year}-" . sprintf('%02d', $month) . ".xlsx";

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Generate and download Annual Excel Report (.xlsx)
     */
    public function downloadAnnual(int $year): StreamedResponse
    {
        $monthlyBreakdown = [];
        for ($m = 1; $m <= 12; $m++) {
            $contractIds = PropertyHouse::whereYear('created_at', $year)
                ->whereMonth('created_at', $m)
                ->pluck('id');

            $contractsCount = $contractIds->count();
            $revenue = (float) PropertyHouse::whereIn('id', $contractIds)->sum('price');
            $collected = (float) ContractPayment::whereIn('property_house_id', $contractIds)->sum('amount');
            $expenses = (float) ContractExpense::whereIn('property_house_id', $contractIds)->sum('amount');
            $remaining = $revenue - $collected;
            $profit = $revenue - $expenses;
            $rate = $revenue > 0 ? ($collected / $revenue) * 100 : 0;

            $monthlyBreakdown[] = [
                'month' => $m,
                'month_name' => Carbon::create($year, $m)->translatedFormat('F'),
                'contracts_count' => $contractsCount,
                'revenue' => $revenue,
                'collected' => $collected,
                'remaining' => $remaining,
                'expenses' => $expenses,
                'profit' => $profit,
                'rate' => $rate,
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

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("تقرير سنة {$year}");
        $sheet->setRightToLeft(true);

        // Header Title
        $sheet->mergeCells('A1:I1');
        $sheet->setCellValue('A1', 'شركة جي أي إس للتجارة والمقاولات والتقييم والتثمين — GIS Trading & Contracting');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '064E3B']],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(32);

        // Subtitle
        $sheet->mergeCells('A2:I2');
        $sheet->setCellValue('A2', "التقرير المالي السنوي ومقارنة الشهور لعام {$year}");
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '064E3B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'ECFDF5']],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(24);

        // KPI Box
        $sheet->setCellValue('B4', 'إجمالي العقود:');
        $sheet->setCellValue('C4', $totals['contracts']);
        $sheet->setCellValue('E4', 'إجمالي الإيرادات:');
        $sheet->setCellValue('F4', $totals['revenue']);
        $sheet->setCellValue('H4', 'المحصل:');
        $sheet->setCellValue('I4', $totals['collected']);

        $sheet->setCellValue('B5', 'المتبقي:');
        $sheet->setCellValue('C5', $totals['remaining']);
        $sheet->setCellValue('E5', 'المصروفات:');
        $sheet->setCellValue('F5', $totals['expenses']);
        $sheet->setCellValue('H5', 'صافي الأرباح:');
        $sheet->setCellValue('I5', $totals['profit']);

        $sheet->getStyle('B4:I5')->applyFromArray([
            'font' => ['bold' => true, 'size' => 10],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle('C4:I5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F4')->getNumberFormat()->setFormatCode('#,##0.00 "د.ب"');
        $sheet->getStyle('I4')->getNumberFormat()->setFormatCode('#,##0.00 "د.ب"');
        $sheet->getStyle('C5')->getNumberFormat()->setFormatCode('#,##0.00 "د.ب"');
        $sheet->getStyle('F5')->getNumberFormat()->setFormatCode('#,##0.00 "د.ب"');
        $sheet->getStyle('I5')->getNumberFormat()->setFormatCode('#,##0.00 "د.ب"');

        // Headers
        $headers = [
            'A7' => '#',
            'B7' => 'الشهر',
            'C7' => 'عدد العقود',
            'D7' => 'الإيرادات (د.ب)',
            'E7' => 'المحصل (د.ب)',
            'F7' => 'المتبقي (د.ب)',
            'G7' => 'المصروفات (د.ب)',
            'H7' => 'صافي الربح (د.ب)',
            'I7' => 'نسبة التحصيل',
        ];

        foreach ($headers as $cell => $title) {
            $sheet->setCellValue($cell, $title);
        }

        $sheet->getStyle('A7:I7')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '047857']],
        ]);
        $sheet->getRowDimension(7)->setRowHeight(26);

        // 12 Months
        $row = 8;
        foreach ($monthlyBreakdown as $m) {
            $sheet->setCellValue("A{$row}", $m['month']);
            $sheet->setCellValue("B{$row}", $m['month_name']);
            $sheet->setCellValue("C{$row}", $m['contracts_count']);
            $sheet->setCellValue("D{$row}", $m['revenue']);
            $sheet->setCellValue("E{$row}", $m['collected']);
            $sheet->setCellValue("F{$row}", $m['remaining']);
            $sheet->setCellValue("G{$row}", $m['expenses']);
            $sheet->setCellValue("H{$row}", $m['profit']);
            $sheet->setCellValue("I{$row}", number_format($m['rate'], 1) . '%');

            $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']],
                ],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->getStyle("A{$row}:C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("I{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D{$row}:H{$row}")->getNumberFormat()->setFormatCode('#,##0.00');

            if ($m['profit'] >= 0) {
                $sheet->getStyle("H{$row}")->getFont()->getColor()->setRGB('047857');
            } else {
                $sheet->getStyle("H{$row}")->getFont()->getColor()->setRGB('DC2626');
            }

            $sheet->getRowDimension($row)->setRowHeight(22);
            $row++;
        }

        // Totals row
        $sheet->setCellValue("A{$row}", 'الإجمالي السنوي');
        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->setCellValue("C{$row}", "=SUM(C8:C" . ($row - 1) . ")");
        $sheet->setCellValue("D{$row}", "=SUM(D8:D" . ($row - 1) . ")");
        $sheet->setCellValue("E{$row}", "=SUM(E8:E" . ($row - 1) . ")");
        $sheet->setCellValue("F{$row}", "=SUM(F8:F" . ($row - 1) . ")");
        $sheet->setCellValue("G{$row}", "=SUM(G8:G" . ($row - 1) . ")");
        $sheet->setCellValue("H{$row}", "=SUM(H8:H" . ($row - 1) . ")");
        $overallRate = $totals['revenue'] > 0 ? ($totals['collected'] / $totals['revenue']) * 100 : 0;
        $sheet->setCellValue("I{$row}", number_format($overallRate, 1) . '%');

        $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '064E3B']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D1FAE5']],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '047857']],
            ],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle("A{$row}:C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("D{$row}:H{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle("I{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension($row)->setRowHeight(26);

        // Auto-fit column widths
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = "GIS-Annual-Report-{$year}.xlsx";

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
