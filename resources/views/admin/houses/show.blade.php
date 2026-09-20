@extends('layouts.admin')

@section('title', 'عقد: ' . ($house->contract_number ?? $reportNo))

@section('content')
<div x-data="{
    showPaymentModal: false,
    showExpenseModal: false,
    editingPayment: null,
    editingExpense: null,
    copiedContract: false,
    copyToClipboard(text) {
        navigator.clipboard.writeText(text);
        this.copiedContract = true;
        setTimeout(() => { this.copiedContract = false; }, 2000);
    },
    openEditPayment(id, amount, date, method, notes) {
        this.editingPayment = {
            id: id,
            amount: amount,
            payment_date: date,
            payment_method: method,
            notes: notes || ''
        };
    },
    openEditExpense(id, type, amount, date, method, payee, notes) {
        this.editingExpense = {
            id: id,
            expense_type: type,
            amount: amount,
            expense_date: date,
            payment_method: method,
            payee_name: payee || '',
            notes: notes || ''
        };
    }
}">

    {{-- Breadcrumb --}}
    <nav class="flex mb-4" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse text-xs sm:text-sm">
            <li>
                <a href="{{ route('admin.houses.index') }}" class="inline-flex items-center gap-1 font-medium text-slate-400 hover:text-emerald-400 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                    <span>العقود</span>
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-3.5 h-3.5 text-slate-600 rtl:rotate-180" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="ms-1 font-mono font-bold text-emerald-400">{{ $house->contract_number ?? $reportNo }}</span>
                </div>
            </li>
        </ol>
    </nav>

    {{-- ═════════════════════════════════════════════════════════════════════════ --}}
    {{-- TOP CONTRACT HEADER CARD (2-Row Layout - No Overlapping Guaranteed!)     --}}
    {{-- ═════════════════════════════════════════════════════════════════════════ --}}
    <div class="mb-6 rounded-2xl border border-slate-800 bg-slate-900/70 backdrop-blur-md p-5 sm:p-6 shadow-xl">
        {{-- Row 1: Title on Right, Action Buttons on Left --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3 flex-wrap">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight">{{ $house->title }}</h1>
                    <p class="text-xs text-slate-400 mt-0.5">ملف عقد فحص العقار والحسابات المالية المرتبطة</p>
                </div>
                <button type="button"
                        @click="copyToClipboard(@js($house->contract_number ?? $reportNo))"
                        class="font-mono text-emerald-400 font-bold bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/30 px-3 py-1 rounded-lg text-xs flex items-center gap-1.5 transition-colors"
                        title="انقر لنسخ رقم العقد">
                    <span>{{ $house->contract_number ?? $reportNo }}</span>
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                    <span x-show="copiedContract" x-cloak class="text-[10px] text-emerald-300 font-sans">تم النسخ!</span>
                </button>
            </div>

            {{-- Action Buttons: Well Spaced & Proportional --}}
            <div class="flex items-center gap-2 sm:gap-2.5 flex-wrap">
                {{-- PDF Download --}}
                <a href="{{ route('admin.houses.contract.pdf', $house) }}"
                   target="_blank"
                   class="inline-flex items-center gap-1.5 rounded-xl bg-red-600/15 border border-red-500/30 px-3.5 py-2 text-xs sm:text-sm font-bold text-red-400 hover:bg-red-600/25 hover:border-red-500/50 transition-all duration-150 active:scale-[0.98]"
                   title="تحميل العقد بصيغة PDF">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    <span>تحميل PDF</span>
                </a>

                {{-- Word Download --}}
                <a href="{{ route('admin.houses.contract.word', $house) }}"
                   class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600/15 border border-blue-500/30 px-3.5 py-2 text-xs sm:text-sm font-bold text-blue-400 hover:bg-blue-600/25 hover:border-blue-500/50 transition-all duration-150 active:scale-[0.98]"
                   title="تحميل العقد بصيغة Word">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    <span>تحميل Word</span>
                </a>

                {{-- Edit Contract --}}
                <a href="{{ route('admin.houses.edit', $house) }}"
                   class="inline-flex items-center gap-1.5 rounded-xl border border-slate-700 bg-slate-800 px-3.5 py-2 text-xs sm:text-sm font-semibold text-slate-200 hover:bg-slate-700 hover:text-white transition-colors"
                   title="تعديل بيانات العقد">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    <span>تعديل العقد</span>
                </a>

                {{-- Delete Contract --}}
                <form id="delete-contract-form" action="{{ route('admin.houses.destroy', $house) }}" method="POST" class="inline">
                    @csrf @method('DELETE')
                    <button type="button"
                            onclick="confirmAction('هل أنت متأكد تماماً من حذف هذا العقد وجميع الدفعات والمصروفات المرتبطة به؟', () => document.getElementById('delete-contract-form').submit())"
                            class="inline-flex items-center gap-1.5 rounded-xl border border-red-800/40 bg-red-950/25 px-3 py-2 text-xs sm:text-sm font-semibold text-red-400 hover:bg-red-900/40 hover:text-red-300 transition-colors"
                            title="حذف العقد">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        <span>حذف</span>
                    </button>
                </form>
            </div>
        </div>

        {{-- Row 2: Status Badges Bar (Separated with clean top divider) --}}
        <div class="mt-4 pt-3.5 border-t border-slate-800/80 flex items-center gap-2.5 flex-wrap">
            @php
                $statusColors = [
                    'draft' => 'bg-slate-500/15 text-slate-400 border-slate-500/30',
                    'active' => 'bg-blue-500/15 text-blue-400 border-blue-500/30',
                    'completed' => 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30',
                    'cancelled' => 'bg-red-500/15 text-red-400 border-red-500/30'
                ];
                $paymentColors = [
                    'paid' => 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30',
                    'partial' => 'bg-amber-500/15 text-amber-400 border-amber-500/30',
                    'unpaid' => 'bg-red-500/15 text-red-400 border-red-500/30'
                ];
            @endphp
            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold border {{ $statusColors[$house->contract_status] ?? $statusColors['draft'] }}">
                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                حالة العقد: {{ $house->contract_status_label }}
            </span>
            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold border {{ $paymentColors[$house->payment_status] ?? $paymentColors['unpaid'] }}">
                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                حالة الدفع: {{ $house->payment_status_label }}
            </span>
            @if($house->contract_date)
                <span class="inline-flex items-center gap-1 text-xs text-slate-300 font-mono bg-slate-800/90 px-3 py-1 rounded-full border border-slate-700/80">
                    <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    تاريخ العقد: {{ $house->contract_date->format('Y-m-d') }}
                </span>
            @endif
            @if($house->inspection_date)
                <span class="inline-flex items-center gap-1 text-xs text-slate-300 font-mono bg-slate-800/90 px-3 py-1 rounded-full border border-slate-700/80">
                    <svg class="h-3.5 w-3.5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                    تاريخ الفحص: {{ $house->inspection_date->format('Y-m-d') }}
                </span>
            @endif
        </div>
    </div>

    {{-- ═════════════════════════════════════════════════════════════════════════ --}}
    {{-- 4 FINANCIAL METRIC CARDS (ALL 4 SIDE-BY-SIDE IN 1 ROW - NO 2 UNDER 2!)  --}}
    {{-- ═════════════════════════════════════════════════════════════════════════ --}}
    @php
        $price = (float) ($house->price ?? 0);
        $totalPaid = (float) $house->total_paid;
        $remaining = (float) $house->remaining_amount;
        $expenses = (float) $house->total_expenses;
        $netProfit = (float) $house->net_profit;
        $paidPercent = $price > 0 ? ($totalPaid / $price) * 100 : 0;
    @endphp
    <div class="mb-6">
        <div class="financial-metrics-grid">
            {{-- Card 1: Price --}}
            <div class="stat-card rounded-2xl border border-blue-500/20 bg-gradient-to-br from-blue-950/20 to-slate-900/60 backdrop-blur-md p-4 sm:p-5" style="display: flex; flex-direction: column; justify-content: space-between; min-width: 0;">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-blue-300">قيمة العقد</span>
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-500/10 text-blue-400 border border-blue-500/30">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                </div>
                <p class="text-xl sm:text-2xl font-black text-blue-400 tracking-tight font-mono">{{ number_format($price, 2) }} <span class="text-xs font-normal text-blue-300">د.ب</span></p>
                <p class="mt-2 text-[11px] text-slate-400">إجمالي المبلغ المتفق عليه</p>
            </div>

            {{-- Card 2: Total Paid --}}
            <div class="stat-card rounded-2xl border border-emerald-500/20 bg-gradient-to-br from-emerald-950/20 to-slate-900/60 backdrop-blur-md p-4 sm:p-5" style="display: flex; flex-direction: column; justify-content: space-between; min-width: 0;">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-emerald-300">المبلغ المدفوع</span>
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                </div>
                <div>
                    <div class="flex items-baseline justify-between">
                        <p class="text-xl sm:text-2xl font-black text-emerald-400 tracking-tight font-mono">{{ number_format($totalPaid, 2) }} <span class="text-xs font-normal text-emerald-300">د.ب</span></p>
                        <span class="text-[11px] font-bold text-emerald-400 font-mono">{{ number_format($paidPercent, 1) }}%</span>
                    </div>
                    <div class="w-full bg-slate-800 rounded-full h-1.5 mt-2 overflow-hidden">
                        <div class="bg-emerald-500 h-1.5 rounded-full transition-all" style="width: {{ min(100, $paidPercent) }}%"></div>
                    </div>
                </div>
            </div>

            {{-- Card 3: Remaining --}}
            <div class="stat-card rounded-2xl border border-amber-500/20 bg-gradient-to-br from-amber-950/20 to-slate-900/60 backdrop-blur-md p-4 sm:p-5" style="display: flex; flex-direction: column; justify-content: space-between; min-width: 0;">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-amber-300">المبلغ المتبقي</span>
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-500/10 text-amber-400 border border-amber-500/30">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                </div>
                <p class="text-xl sm:text-2xl font-black text-amber-400 tracking-tight font-mono">{{ number_format($remaining, 2) }} <span class="text-xs font-normal text-amber-300">د.ب</span></p>
                <p class="mt-2 text-[11px] text-slate-400">مستحق التحصيل</p>
            </div>

            {{-- Card 4: Net Profit --}}
            <div class="stat-card rounded-2xl border {{ $netProfit >= 0 ? 'border-emerald-500/30 bg-emerald-950/15' : 'border-red-500/30 bg-red-950/15' }} backdrop-blur-md p-4 sm:p-5" style="display: flex; flex-direction: column; justify-content: space-between; min-width: 0;">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold {{ $netProfit >= 0 ? 'text-emerald-300' : 'text-red-300' }}">صافي الربح</span>
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg {{ $netProfit >= 0 ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30' : 'bg-red-500/10 text-red-400 border-red-500/30' }} border">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                    </div>
                </div>
                <p class="text-xl sm:text-2xl font-black {{ $netProfit >= 0 ? 'text-emerald-400' : 'text-red-400' }} tracking-tight font-mono">
                    {{ number_format($netProfit, 2) }} <span class="text-xs font-normal">د.ب</span>
                </p>
                <p class="mt-2 text-[11px] text-slate-400">المصروفات: <span class="text-red-400 font-bold font-mono">{{ number_format($expenses, 2) }} د.ب</span></p>
            </div>
        </div>
    </div>

    {{-- ═════════════════════════════════════════════════════════════════════════ --}}
    {{-- MAIN 2-COLUMN SECTION: LEFT (Tables) & RIGHT (Metadata Cards)             --}}
    {{-- ═════════════════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- LEFT COLUMN: PAYMENTS & EXPENSES (Span 2) --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- 1. PAYMENTS SECTION --}}
            <div class="rounded-2xl border border-slate-800 bg-slate-900/50 backdrop-blur-md overflow-hidden shadow-xl">
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-800 bg-slate-900/80">
                    <div class="flex items-center gap-2.5">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-white">سجل الدفعات والمقبوضات</h3>
                            <p class="text-xs text-slate-400">إجمالي المقبوض: <span class="text-emerald-400 font-bold font-mono">{{ number_format($totalPaid, 2) }} د.ب</span> ({{ $house->payments->count() }} دفعة)</p>
                        </div>
                    </div>
                    <button @click="showPaymentModal = true"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-500 px-3.5 py-2 text-xs font-bold text-slate-950 hover:bg-emerald-400 transition-all shadow-md active:scale-[0.98]">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" /></svg>
                        <span>+ إضافة دفعة جديدة</span>
                    </button>
                </div>

                {{-- Payments List Table --}}
                @if($house->payments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-800 text-right text-xs sm:text-sm">
                        <thead class="bg-slate-950/40 text-slate-400 font-semibold">
                            <tr>
                                <th class="px-5 py-3">المبلغ</th>
                                <th class="px-5 py-3">تاريخ الدفعة</th>
                                <th class="px-5 py-3">طريقة الدفع</th>
                                <th class="px-5 py-3">ملاحظات</th>
                                <th class="px-5 py-3 text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60">
                            @foreach($house->payments as $payment)
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-5 py-3.5 text-emerald-400 font-bold font-mono text-sm">
                                    {{ number_format($payment->amount, 2) }} <span class="text-[11px] font-normal text-slate-400">د.ب</span>
                                </td>
                                <td class="px-5 py-3.5 text-slate-300 font-mono text-xs">
                                    {{ $payment->payment_date->format('Y-m-d') }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex rounded-lg bg-slate-800 px-2.5 py-1 text-xs font-semibold text-slate-300 border border-slate-700">
                                        {{ $payment->payment_method_label }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-slate-400 text-xs">
                                    {{ $payment->notes ?? '—' }}
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        {{-- Edit Button --}}
                                        <button type="button"
                                                @click="openEditPayment(@js($payment->id), @js($payment->amount), @js($payment->payment_date->format('Y-m-d')), @js($payment->payment_method), @js($payment->notes ?? ''))"
                                                class="rounded-lg bg-slate-800 p-1.5 text-blue-400 hover:bg-slate-700 hover:text-blue-300 transition-colors"
                                                title="تعديل الدفعة">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                        </button>

                                        {{-- Delete Form --}}
                                        <form action="{{ route('admin.houses.payments.destroy', [$house, $payment]) }}" method="POST" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="button"
                                                    onclick="confirmAction('حذف هذه الدفعة بمبلغ {{ number_format($payment->amount, 2) }} د.ب؟', () => this.closest('form').submit())"
                                                    class="rounded-lg bg-slate-800 p-1.5 text-red-400 hover:bg-red-950/40 hover:text-red-300 transition-colors"
                                                    title="حذف الدفعة">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="px-5 py-8 text-center text-slate-500 text-xs">
                    لم يتم تسجيل أي دفعات لهذا العقد بعد. اضغط زر "+ إضافة دفعة جديدة" بالأعلى لتسجيل أول دفعة.
                </div>
                @endif
            </div>

            {{-- 2. EXPENSES SECTION --}}
            <div class="rounded-2xl border border-slate-800 bg-slate-900/50 backdrop-blur-md overflow-hidden shadow-xl">
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-800 bg-slate-900/80">
                    <div class="flex items-center gap-2.5">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-500/10 text-red-400 border border-red-500/30">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-white">سجل المصروفات والتكاليف</h3>
                            <p class="text-xs text-slate-400">إجمالي المصروفات: <span class="text-red-400 font-bold font-mono">{{ number_format($expenses, 2) }} د.ب</span> ({{ $house->expenses->count() }} بند)</p>
                        </div>
                    </div>
                    <button @click="showExpenseModal = true"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-red-500 px-3.5 py-2 text-xs font-bold text-white hover:bg-red-400 transition-all shadow-md active:scale-[0.98]">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" /></svg>
                        <span>+ إضافة مصروف جديد</span>
                    </button>
                </div>

                {{-- Expenses List Table --}}
                @if($house->expenses->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-800 text-right text-xs sm:text-sm">
                        <thead class="bg-slate-950/40 text-slate-400 font-semibold">
                            <tr>
                                <th class="px-5 py-3">نوع المصروف</th>
                                <th class="px-5 py-3">المبلغ</th>
                                <th class="px-5 py-3">التاريخ</th>
                                <th class="px-5 py-3">الجهة / المستلم</th>
                                <th class="px-5 py-3">الطريقة</th>
                                <th class="px-5 py-3">ملاحظات</th>
                                <th class="px-5 py-3 text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60">
                            @foreach($house->expenses as $expense)
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex rounded-lg bg-slate-800 px-2.5 py-1 text-xs font-semibold text-slate-300 border border-slate-700">
                                        {{ $expense->expense_type_label }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-red-400 font-bold font-mono text-sm">
                                    {{ number_format($expense->amount, 2) }} <span class="text-[11px] font-normal text-slate-400">د.ب</span>
                                </td>
                                <td class="px-5 py-3.5 text-slate-300 font-mono text-xs">
                                    {{ $expense->expense_date->format('Y-m-d') }}
                                </td>
                                <td class="px-5 py-3.5 text-slate-300">
                                    {{ $expense->payee_name ?? '—' }}
                                </td>
                                <td class="px-5 py-3.5 text-slate-400 text-xs">
                                    {{ $expense->payment_method_label }}
                                </td>
                                <td class="px-5 py-3.5 text-slate-400 text-xs">
                                    {{ $expense->notes ?? '—' }}
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        {{-- Edit Expense Button --}}
                                        <button type="button"
                                                @click="openEditExpense(@js($expense->id), @js($expense->expense_type), @js($expense->amount), @js($expense->expense_date->format('Y-m-d')), @js($expense->payment_method), @js($expense->payee_name ?? ''), @js($expense->notes ?? ''))"
                                                class="rounded-lg bg-slate-800 p-1.5 text-blue-400 hover:bg-slate-700 hover:text-blue-300 transition-colors"
                                                title="تعديل المصروف">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                        </button>

                                        {{-- Delete Expense Form --}}
                                        <form action="{{ route('admin.houses.expenses.destroy', [$house, $expense]) }}" method="POST" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="button"
                                                    onclick="confirmAction('حذف هذا المصروف بمبلغ {{ number_format($expense->amount, 2) }} د.ب؟', () => this.closest('form').submit())"
                                                    class="rounded-lg bg-slate-800 p-1.5 text-red-400 hover:bg-red-950/40 hover:text-red-300 transition-colors"
                                                    title="حذف المصروف">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="px-5 py-8 text-center text-slate-500 text-xs">
                    لا توجد أي مصروفات مسجلة على هذا العقد. اضغط زر "+ إضافة مصروف جديد" لتسجيل بند مصروف.
                </div>
                @endif
            </div>

        </div>

        {{-- RIGHT COLUMN: CLIENT, PROPERTY & CONTRACT METADATA (Re-designed with Clear Fonts & Spacing!) --}}
        <div class="space-y-6">

            {{-- 1. CLIENT INFO CARD --}}
            <div class="rounded-2xl border border-slate-800 bg-slate-900/60 backdrop-blur-md p-5 sm:p-6 shadow-xl">
                <div class="flex items-center justify-between mb-4 border-b border-slate-800 pb-3">
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-500/15 text-emerald-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        </div>
                        <span>بيانات العميل</span>
                    </h3>
                    <span class="rounded-full bg-emerald-500/10 border border-emerald-500/20 px-2.5 py-0.5 text-xs font-bold text-emerald-400">الطرف الثاني</span>
                </div>

                <div class="space-y-4 text-sm">
                    {{-- Client Name --}}
                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-semibold text-slate-400">اسم العميل (المشتري):</span>
                        <span class="text-base font-bold text-white">{{ $clientName }}</span>
                    </div>

                    {{-- Phone & WhatsApp --}}
                    @if($house->phone)
                    <div class="flex flex-col gap-1.5 pt-3 border-t border-slate-800/80">
                        <span class="text-xs font-semibold text-slate-400">رقم الهاتف والتواصل:</span>
                        <div class="flex items-center justify-between gap-2 flex-wrap">
                            <span class="text-base font-mono font-bold text-white">{{ $house->phone }}</span>
                            <div class="flex items-center gap-2">
                                @php
                                    $cleanPhone = preg_replace('/[^0-9]/', '', $house->phone);
                                    if (!str_starts_with($cleanPhone, '973') && strlen($cleanPhone) == 8) {
                                        $cleanPhone = '973' . $cleanPhone;
                                    }
                                @endphp
                                <a href="https://wa.me/{{ $cleanPhone }}"
                                   target="_blank"
                                   class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-500/20 border border-emerald-500/40 px-3 py-1.5 text-xs font-bold text-emerald-400 hover:bg-emerald-500/30 transition-colors"
                                   title="فتح محادثة واتساب">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                    <span>واتساب</span>
                                </a>
                                <a href="tel:{{ $house->phone }}"
                                   class="inline-flex items-center gap-1.5 rounded-xl bg-slate-800 border border-slate-700 px-3 py-1.5 text-xs font-semibold text-slate-200 hover:text-white transition-colors"
                                   title="اتصال هاتفي">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                    <span>اتصال</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- ID / CPR --}}
                    @if($house->id_number)
                    <div class="flex justify-between items-center pt-3 border-t border-slate-800/80">
                        <span class="text-xs font-semibold text-slate-400">الرقم الشخصي / CPR:</span>
                        <span class="text-sm font-mono font-bold text-white bg-slate-800/80 px-2.5 py-1 rounded-lg border border-slate-700/60">{{ $house->id_number }}</span>
                    </div>
                    @endif

                    {{-- Nationality --}}
                    @if($house->nationality)
                    <div class="flex justify-between items-center pt-3 border-t border-slate-800/80">
                        <span class="text-xs font-semibold text-slate-400">الجنسية:</span>
                        <span class="text-sm font-medium text-white">{{ $house->nationality }}</span>
                    </div>
                    @endif

                    {{-- Client Email --}}
                    @if($house->client_email)
                    <div class="flex justify-between items-center pt-3 border-t border-slate-800/80">
                        <span class="text-xs font-semibold text-slate-400">البريد الإلكتروني:</span>
                        <a href="mailto:{{ $house->client_email }}" class="text-xs sm:text-sm font-mono text-emerald-400 hover:underline">{{ $house->client_email }}</a>
                    </div>
                    @endif
                </div>
            </div>

            {{-- 2. PROPERTY SPECIFICATIONS CARD --}}
            <div class="rounded-2xl border border-slate-800 bg-slate-900/60 backdrop-blur-md p-5 sm:p-6 shadow-xl">
                <div class="flex items-center justify-between mb-4 border-b border-slate-800 pb-3">
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-500/15 text-blue-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                        </div>
                        <span>مواصفات العقار</span>
                    </h3>
                    @if($house->property_type)
                        <span class="rounded-full bg-blue-500/10 border border-blue-500/20 px-2.5 py-0.5 text-xs font-bold text-blue-400">{{ $house->property_type }}</span>
                    @endif
                </div>

                <div class="space-y-3.5 text-sm">
                    {{-- Address --}}
                    <div class="flex items-start gap-2">
                        <svg class="h-4 w-4 text-emerald-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        <div class="flex-1">
                            <span class="text-xs text-slate-400 block">العنوان وموقع العقار:</span>
                            <span class="text-sm font-medium text-white leading-relaxed">{{ $propertyAddress }}</span>
                        </div>
                    </div>

                    {{-- Intro Number --}}
                    @if($house->intro_number)
                    <div class="flex justify-between items-center pt-2.5 border-t border-slate-800/80">
                        <span class="text-xs font-semibold text-slate-400">رقم المقدمة:</span>
                        <span class="text-sm font-mono font-bold text-white bg-slate-800/80 px-2.5 py-0.5 rounded-lg border border-slate-700/60">{{ $house->intro_number }}</span>
                    </div>
                    @endif

                    {{-- Document Number --}}
                    @if($house->document_number)
                    <div class="flex justify-between items-center pt-2.5 border-t border-slate-800/80">
                        <span class="text-xs font-semibold text-slate-400">رقم الوثيقة:</span>
                        <span class="text-sm font-mono font-bold text-white bg-slate-800/80 px-2.5 py-0.5 rounded-lg border border-slate-700/60">{{ $house->document_number }}</span>
                    </div>
                    @endif

                    {{-- Building Status --}}
                    @if($house->building_status)
                    <div class="flex justify-between items-center pt-2.5 border-t border-slate-800/80">
                        <span class="text-xs font-semibold text-slate-400">حالة المبنى:</span>
                        <span class="text-sm font-bold text-slate-200">{{ $house->building_status }}</span>
                    </div>
                    @endif

                    {{-- Specs List --}}
                    <div class="pt-3 border-t border-slate-800/80 space-y-2">
                        @if($house->land_area)
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-slate-400">مساحة الأرض:</span>
                            <span class="font-bold text-white font-mono text-sm">{{ $house->land_area }}</span>
                        </div>
                        @endif
                        @if($house->building_area)
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-slate-400">مساحة البناء:</span>
                            <span class="font-bold text-white font-mono text-sm">{{ $house->building_area }}</span>
                        </div>
                        @endif
                        @if($house->floors_count)
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-slate-400">عدد الطوابق:</span>
                            <span class="font-bold text-white text-sm">{{ $house->floors_count }}</span>
                        </div>
                        @endif
                        @if($house->rooms_count)
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-slate-400">عدد الغرف:</span>
                            <span class="font-bold text-white text-sm">{{ $house->rooms_count }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- 3. CONTRACT DETAILS CARD --}}
            <div class="rounded-2xl border border-slate-800 bg-slate-900/60 backdrop-blur-md p-5 sm:p-6 shadow-xl">
                <div class="flex items-center justify-between mb-4 border-b border-slate-800 pb-3">
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-500/15 text-emerald-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        </div>
                        <span>بيانات الاتفاقية</span>
                    </h3>
                </div>

                <div class="space-y-3.5 text-sm">
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-semibold text-slate-400">رقم العقد:</span>
                        <span class="font-mono text-emerald-400 font-bold text-sm">{{ $house->contract_number ?? '—' }}</span>
                    </div>

                    <div class="flex justify-between items-center pt-2.5 border-t border-slate-800/80">
                        <span class="text-xs font-semibold text-slate-400">تاريخ الفحص:</span>
                        <span class="text-sm font-mono text-white">{{ $house->inspection_date?->format('Y-m-d') ?? '—' }}</span>
                    </div>

                    @if($house->reference_code)
                    <div class="flex justify-between items-center pt-2.5 border-t border-slate-800/80">
                        <span class="text-xs font-semibold text-slate-400">الرقم المرجعي:</span>
                        <span class="text-sm font-mono text-white">{{ $house->reference_code }}</span>
                    </div>
                    @endif

                    @if($house->payment_method)
                    <div class="flex justify-between items-center pt-2.5 border-t border-slate-800/80">
                        <span class="text-xs font-semibold text-slate-400">طريقة الدفع المتفق عليها:</span>
                        <span class="text-sm font-bold text-slate-200">{{ match($house->payment_method) { 'benefit' => 'Benefit / بنفت', 'bank_transfer' => 'تحويل بنكي', 'cash' => 'كاش / نقداً', default => $house->payment_method } }}</span>
                    </div>
                    @endif

                    @if($house->contract_notes)
                    <div class="pt-3 border-t border-slate-800/80">
                        <span class="text-xs font-semibold text-slate-400 block mb-1">ملاحظات وشروط إضافية:</span>
                        <p class="text-xs text-slate-300 bg-slate-950/70 p-3 rounded-xl border border-slate-800 leading-relaxed">{{ $house->contract_notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    {{-- ═════════════════════════════════════════════════════════════════════════ --}}
    {{-- ADD PAYMENT MODAL (Centered, Defined Width & Height - NOT Stretched!)    --}}
    {{-- ═════════════════════════════════════════════════════════════════════════ --}}
    <div x-show="showPaymentModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-md p-4 overflow-y-auto">
        <div @click.away="showPaymentModal = false"
             class="bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl p-6 max-w-md w-full max-h-[90vh] overflow-y-auto relative">
            <div class="flex items-center justify-between mb-4 border-b border-slate-800 pb-3">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-500/15 text-emerald-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    </div>
                    <span>إضافة دفعة مالية جديدة</span>
                </h3>
                <button type="button" @click="showPaymentModal = false" class="text-slate-400 hover:text-white transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form method="post" action="{{ route('admin.houses.payments.store', $house) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">المبلغ بالدينار البحريني (د.ب) <span class="text-red-400">*</span></label>
                    <input type="number" name="amount" step="0.01" min="0.01" required
                           class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3.5 py-2.5 text-sm text-white font-mono focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                           placeholder="مثال: 150.00">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">تاريخ استلام الدفعة <span class="text-red-400">*</span></label>
                    <input type="date" name="payment_date" value="{{ now()->format('Y-m-d') }}" required
                           class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3.5 py-2.5 text-sm text-white focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">طريقة استلام الدفع <span class="text-red-400">*</span></label>
                    <select name="payment_method" required
                            class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3.5 py-2.5 text-sm text-white focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        <option value="benefit">Benefit / بنفت</option>
                        <option value="cash">كاش / نقداً</option>
                        <option value="bank_transfer">تحويل بنكي</option>
                        <option value="other">أخرى</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">ملاحظات أو رقم الإيصال</label>
                    <input type="text" name="notes"
                           class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3.5 py-2.5 text-sm text-white focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                           placeholder="تفاصيل الدفعة أو رقم العملية">
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                    <button type="button" @click="showPaymentModal = false"
                            class="rounded-xl border border-slate-700 bg-slate-800 px-4 py-2 text-xs font-semibold text-slate-300 hover:bg-slate-700">
                        إلغاء
                    </button>
                    <button type="submit"
                            class="rounded-xl bg-emerald-500 px-5 py-2 text-xs font-bold text-slate-950 hover:bg-emerald-400 transition-colors shadow-md">
                        حفظ الدفعة
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ═════════════════════════════════════════════════════════════════════════ --}}
    {{-- ADD EXPENSE MODAL (Centered, Defined Width & Height - NOT Stretched!)    --}}
    {{-- ═════════════════════════════════════════════════════════════════════════ --}}
    <div x-show="showExpenseModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-md p-4 overflow-y-auto">
        <div @click.away="showExpenseModal = false"
             class="bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl p-6 max-w-md w-full max-h-[90vh] overflow-y-auto relative">
            <div class="flex items-center justify-between mb-4 border-b border-slate-800 pb-3">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-red-500/15 text-red-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    </div>
                    <span>تسجيل مصروف وتكلفة جديدة</span>
                </h3>
                <button type="button" @click="showExpenseModal = false" class="text-slate-400 hover:text-white transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form method="post" action="{{ route('admin.houses.expenses.store', $house) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">نوع المصروف <span class="text-red-400">*</span></label>
                    <select name="expense_type" required
                            class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3.5 py-2.5 text-sm text-white focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500">
                        @foreach($expenseTypes as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">المبلغ بالدينار البحريني (د.ب) <span class="text-red-400">*</span></label>
                    <input type="number" name="amount" step="0.01" min="0.01" required
                           class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3.5 py-2.5 text-sm text-white font-mono focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500"
                           placeholder="مثال: 50.00">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">تاريخ المصروف <span class="text-red-400">*</span></label>
                    <input type="date" name="expense_date" value="{{ now()->format('Y-m-d') }}" required
                           class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3.5 py-2.5 text-sm text-white focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">طريقة الدفع <span class="text-red-400">*</span></label>
                    <select name="payment_method" required
                            class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3.5 py-2.5 text-sm text-white focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500">
                        <option value="benefit">Benefit / بنفت</option>
                        <option value="cash">كاش / نقداً</option>
                        <option value="bank_transfer">تحويل بنكي</option>
                        <option value="other">أخرى</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">الجهة / الشخص المستلم</label>
                    <input type="text" name="payee_name"
                           class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3.5 py-2.5 text-sm text-white focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500"
                           placeholder="اسم الفاحص أو مزود الخدمة">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">ملاحظات أو تفاصيل</label>
                    <input type="text" name="notes"
                           class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3.5 py-2.5 text-sm text-white focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500"
                           placeholder="سبب المصروف أو تفاصيله">
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                    <button type="button" @click="showExpenseModal = false"
                            class="rounded-xl border border-slate-700 bg-slate-800 px-4 py-2 text-xs font-semibold text-slate-300 hover:bg-slate-700">
                        إلغاء
                    </button>
                    <button type="submit"
                            class="rounded-xl bg-red-500 px-5 py-2 text-xs font-bold text-white hover:bg-red-400 transition-colors shadow-md">
                        حفظ المصروف
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ═════════════════════════════════════════════════════════════════════════ --}}
    {{-- EDIT PAYMENT MODAL (Centered, Defined Width & Height)                   --}}
    {{-- ═════════════════════════════════════════════════════════════════════════ --}}
    <div x-show="editingPayment" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-md p-4 overflow-y-auto">
        <div @click.away="editingPayment = null"
             class="bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl p-6 max-w-md w-full max-h-[90vh] overflow-y-auto relative">
            <div class="flex items-center justify-between mb-4 border-b border-slate-800 pb-3">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-500/15 text-blue-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                    </div>
                    <span>تعديل بيانات الدفعة</span>
                </h3>
                <button type="button" @click="editingPayment = null" class="text-slate-400 hover:text-white">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <template x-if="editingPayment">
                <form :action="'{{ url('admin/houses/' . $house->id . '/payments') }}/' + editingPayment.id" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">المبلغ (د.ب) <span class="text-red-400">*</span></label>
                        <input type="number" step="0.01" min="0.01" required name="amount" x-model="editingPayment.amount"
                               class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3.5 py-2.5 text-sm text-white font-mono focus:border-emerald-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">تاريخ الدفعة <span class="text-red-400">*</span></label>
                        <input type="date" required name="payment_date" x-model="editingPayment.payment_date"
                               class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3.5 py-2.5 text-sm text-white focus:border-emerald-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">طريقة الدفع <span class="text-red-400">*</span></label>
                        <select name="payment_method" required x-model="editingPayment.payment_method"
                                class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3.5 py-2.5 text-sm text-white focus:border-emerald-500 focus:outline-none">
                            <option value="benefit">Benefit / بنفت</option>
                            <option value="cash">كاش / نقداً</option>
                            <option value="bank_transfer">تحويل بنكي</option>
                            <option value="other">أخرى</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">ملاحظات</label>
                        <input type="text" name="notes" x-model="editingPayment.notes"
                               class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3.5 py-2.5 text-sm text-white focus:border-emerald-500 focus:outline-none">
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                        <button type="button" @click="editingPayment = null"
                                class="rounded-xl border border-slate-700 bg-slate-800 px-4 py-2 text-xs font-semibold text-slate-300 hover:bg-slate-700">
                            إلغاء
                        </button>
                        <button type="submit"
                                class="rounded-xl bg-emerald-500 px-5 py-2 text-xs font-bold text-slate-950 hover:bg-emerald-400 transition-colors shadow-sm">
                            حفظ التعديلات
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>

    {{-- ═════════════════════════════════════════════════════════════════════════ --}}
    {{-- EDIT EXPENSE MODAL (Centered, Defined Width & Height)                   --}}
    {{-- ═════════════════════════════════════════════════════════════════════════ --}}
    <div x-show="editingExpense" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-md p-4 overflow-y-auto">
        <div @click.away="editingExpense = null"
             class="bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl p-6 max-w-md w-full max-h-[90vh] overflow-y-auto relative">
            <div class="flex items-center justify-between mb-4 border-b border-slate-800 pb-3">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-500/15 text-blue-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                    </div>
                    <span>تعديل بيانات المصروف</span>
                </h3>
                <button type="button" @click="editingExpense = null" class="text-slate-400 hover:text-white">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <template x-if="editingExpense">
                <form :action="'{{ url('admin/houses/' . $house->id . '/expenses') }}/' + editingExpense.id" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">نوع المصروف <span class="text-red-400">*</span></label>
                        <select name="expense_type" required x-model="editingExpense.expense_type"
                                class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3.5 py-2.5 text-sm text-white focus:border-red-500 focus:outline-none">
                            @foreach($expenseTypes as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">المبلغ (د.ب) <span class="text-red-400">*</span></label>
                        <input type="number" step="0.01" min="0.01" required name="amount" x-model="editingExpense.amount"
                               class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3.5 py-2.5 text-sm text-white font-mono focus:border-red-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">تاريخ المصروف <span class="text-red-400">*</span></label>
                        <input type="date" required name="expense_date" x-model="editingExpense.expense_date"
                               class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3.5 py-2.5 text-sm text-white focus:border-red-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">طريقة الدفع <span class="text-red-400">*</span></label>
                        <select name="payment_method" required x-model="editingExpense.payment_method"
                                class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3.5 py-2.5 text-sm text-white focus:border-red-500 focus:outline-none">
                            <option value="benefit">Benefit / بنفت</option>
                            <option value="cash">كاش / نقداً</option>
                            <option value="bank_transfer">تحويل بنكي</option>
                            <option value="other">أخرى</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">الجهة / المستلم</label>
                        <input type="text" name="payee_name" x-model="editingExpense.payee_name"
                               class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3.5 py-2.5 text-sm text-white focus:border-red-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">ملاحظات</label>
                        <input type="text" name="notes" x-model="editingExpense.notes"
                               class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3.5 py-2.5 text-sm text-white focus:border-red-500 focus:outline-none">
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                        <button type="button" @click="editingExpense = null"
                                class="rounded-xl border border-slate-700 bg-slate-800 px-4 py-2 text-xs font-semibold text-slate-300 hover:bg-slate-700">
                            إلغاء
                        </button>
                        <button type="submit"
                                class="rounded-xl bg-red-500 px-5 py-2 text-xs font-bold text-white hover:bg-red-400 transition-colors shadow-sm">
                            حفظ التعديلات
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>

</div>
@endsection
