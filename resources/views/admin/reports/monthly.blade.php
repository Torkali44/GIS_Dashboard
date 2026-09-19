@extends('layouts.admin')

@section('title', 'التقرير الشهري — ' . \Carbon\Carbon::create($year, $month)->translatedFormat('F Y'))

@section('content')
    {{-- Header with Title and Actions --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-8">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">التقرير الشهري</h1>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/30 px-3 py-1 text-xs font-bold text-emerald-400">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    {{ \Carbon\Carbon::create($year, $month)->translatedFormat('F Y') }}
                </span>
            </div>
            <p class="mt-1.5 text-sm text-slate-400">تحليل مالي شامل للعقود والإيرادات والمصروفات وصافي الأرباح</p>
        </div>

        {{-- Top Actions: Excel Export --}}
        <div class="flex items-center gap-3 flex-wrap">
            <a href="{{ route('admin.reports.monthly.excel', ['month' => $month, 'year' => $year]) }}"
               class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-emerald-900/30 hover:from-emerald-500 hover:to-teal-500 transition-all duration-200 hover:scale-[1.02] active:scale-[0.98]">
                <svg class="h-5 w-5 text-emerald-100" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>تصدير Excel (.xlsx)</span>
            </a>
            <a href="{{ route('admin.reports.annual', ['year' => $year]) }}"
               class="inline-flex items-center gap-2 rounded-xl border border-slate-700 bg-slate-800/80 px-4 py-2.5 text-sm font-semibold text-slate-300 hover:bg-slate-700 hover:text-white transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span>التقرير السنوي</span>
            </a>
        </div>
    </div>

    {{-- Month Navigator Bar --}}
    @php
        $currentDate = \Carbon\Carbon::create($year, $month, 1);
        $prevDate = $currentDate->copy()->subMonth();
        $nextDate = $currentDate->copy()->addMonth();
    @endphp
    <div class="mb-8 rounded-2xl border border-slate-800 bg-slate-900/60 backdrop-blur-md p-4 sm:p-5 shadow-xl">
        <div class="flex flex-col lg:flex-row items-center justify-between gap-4">
            {{-- Quick Arrow Steppers --}}
            <div class="flex items-center gap-2 w-full lg:w-auto justify-between lg:justify-start">
                <a href="{{ route('admin.reports.monthly', ['month' => $prevDate->month, 'year' => $prevDate->year]) }}"
                   class="inline-flex items-center gap-1.5 rounded-xl border border-slate-700 bg-slate-800/80 px-3.5 py-2 text-xs font-bold text-slate-300 hover:bg-slate-700 hover:text-white transition-all">
                    <svg class="h-4 w-4 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                    <span>{{ $prevDate->translatedFormat('F') }}</span>
                </a>

                <a href="{{ route('admin.reports.monthly', ['month' => now()->month, 'year' => now()->year]) }}"
                   class="rounded-xl px-3 py-2 text-xs font-bold transition-all {{ ($month == now()->month && $year == now()->year) ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40' : 'bg-slate-800 text-slate-400 hover:text-white' }}">
                    الشهر الحالي
                </a>

                <a href="{{ route('admin.reports.monthly', ['month' => $nextDate->month, 'year' => $nextDate->year]) }}"
                   class="inline-flex items-center gap-1.5 rounded-xl border border-slate-700 bg-slate-800/80 px-3.5 py-2 text-xs font-bold text-slate-300 hover:bg-slate-700 hover:text-white transition-all">
                    <span>{{ $nextDate->translatedFormat('F') }}</span>
                    <svg class="h-4 w-4 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>
            </div>

            {{-- Month & Year Dropdown Form --}}
            <form method="GET" action="{{ route('admin.reports.monthly') }}" class="flex items-center gap-3 w-full lg:w-auto">
                <div class="flex-1 lg:flex-initial">
                    <select name="month" class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3.5 py-2 text-xs sm:text-sm text-white focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ sprintf('%02d', $m) }} - {{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="flex-1 lg:flex-initial">
                    <select name="year" class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3.5 py-2 text-xs sm:text-sm text-white focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        @for($y = now()->year - 4; $y <= now()->year + 1; $y++)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <button type="submit" class="rounded-xl bg-emerald-500 px-5 py-2 text-xs sm:text-sm font-bold text-slate-950 hover:bg-emerald-400 transition-colors shadow-md">
                    عرض
                </button>
            </form>
        </div>
    </div>

    {{-- Primary KPI Cards (4 Cards) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- Total Contracts --}}
        <div class="stat-card relative overflow-hidden rounded-2xl border border-slate-800 bg-gradient-to-br from-slate-900/80 to-slate-900/40 p-5 backdrop-blur-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-400">إجمالي العقود</span>
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-800 text-slate-300 border border-slate-700/50">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                </div>
            </div>
            <p class="text-3xl font-black text-white tracking-tight">{{ number_format($contracts->count()) }}</p>
            <p class="text-xs text-slate-400 mt-2">متوسط العقد: <span class="font-bold text-slate-300">{{ number_format($avgValue, 2) }} د.ب</span></p>
        </div>

        {{-- Total Value --}}
        <div class="stat-card relative overflow-hidden rounded-2xl border border-blue-500/20 bg-gradient-to-br from-blue-950/30 to-slate-900/40 p-5 backdrop-blur-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-blue-300">قيمة العقود الإجمالية</span>
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-500/10 text-blue-400 border border-blue-500/30">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
            </div>
            <p class="text-3xl font-black text-blue-400 tracking-tight">{{ number_format($totalValue, 2) }} <span class="text-sm font-medium text-blue-300">د.ب</span></p>
            <p class="text-xs text-slate-400 mt-2">القيمة الإجمالية المسجلة بالشهر</p>
        </div>

        {{-- Total Collected --}}
        <div class="stat-card relative overflow-hidden rounded-2xl border border-emerald-500/20 bg-gradient-to-br from-emerald-950/30 to-slate-900/40 p-5 backdrop-blur-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-emerald-300">المحصل فعلياً</span>
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
            </div>
            <p class="text-3xl font-black text-emerald-400 tracking-tight">{{ number_format($totalCollected, 2) }} <span class="text-sm font-medium text-emerald-300">د.ب</span></p>
            <div class="mt-2 flex items-center justify-between text-xs">
                <span class="text-slate-400">نسبة التحصيل:</span>
                <span class="font-bold text-emerald-400">{{ number_format($collectionRate, 1) }}%</span>
            </div>
            <div class="w-full bg-slate-800 rounded-full h-1.5 mt-1.5 overflow-hidden">
                <div class="bg-emerald-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ min(100, $collectionRate) }}%"></div>
            </div>
        </div>

        {{-- Total Remaining --}}
        <div class="stat-card relative overflow-hidden rounded-2xl border border-amber-500/20 bg-gradient-to-br from-amber-950/30 to-slate-900/40 p-5 backdrop-blur-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-amber-300">المتبقي للتحصيل</span>
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-500/10 text-amber-400 border border-amber-500/30">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
            </div>
            <p class="text-3xl font-black text-amber-400 tracking-tight">{{ number_format($totalRemaining, 2) }} <span class="text-sm font-medium text-amber-300">د.ب</span></p>
            <p class="text-xs text-slate-400 mt-2">مستحقات آجلة قيد التحصيل</p>
        </div>
    </div>

    {{-- Secondary KPI Cards (Expenses & Profits) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        {{-- Expenses --}}
        <div class="stat-card relative overflow-hidden rounded-2xl border border-red-500/20 bg-gradient-to-br from-red-950/30 to-slate-900/40 p-5 backdrop-blur-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-red-300">إجمالي المصروفات</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-500/10 text-red-400 border border-red-500/30">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
            </div>
            <p class="text-2xl font-black text-red-400 tracking-tight">{{ number_format($totalExpenses, 2) }} <span class="text-xs text-red-300">د.ب</span></p>
            <p class="text-xs text-slate-400 mt-2">تكاليف ومصاريف تشغيلية</p>
        </div>

        {{-- Net Profit --}}
        <div class="stat-card relative overflow-hidden rounded-2xl border {{ $totalProfit >= 0 ? 'border-emerald-500/30 bg-gradient-to-br from-emerald-950/30' : 'border-red-500/30 bg-gradient-to-br from-red-950/30' }} to-slate-900/40 p-5 backdrop-blur-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold {{ $totalProfit >= 0 ? 'text-emerald-300' : 'text-red-300' }}">صافي الأرباح</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg {{ $totalProfit >= 0 ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30' : 'bg-red-500/10 text-red-400 border-red-500/30' }} border">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                </div>
            </div>
            <p class="text-2xl font-black {{ $totalProfit >= 0 ? 'text-emerald-400' : 'text-red-400' }} tracking-tight">
                {{ number_format($totalProfit, 2) }} <span class="text-xs font-medium">د.ب</span>
            </p>
            @php $profitMargin = $totalValue > 0 ? ($totalProfit / $totalValue) * 100 : 0; @endphp
            <p class="text-xs text-slate-400 mt-2">هامش الربح: <span class="font-bold {{ $profitMargin >= 0 ? 'text-emerald-400' : 'text-red-400' }}">{{ number_format($profitMargin, 1) }}%</span></p>
        </div>

        {{-- Avg Profit --}}
        <div class="stat-card relative overflow-hidden rounded-2xl border border-violet-500/20 bg-gradient-to-br from-violet-950/30 to-slate-900/40 p-5 backdrop-blur-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-violet-300">متوسط الربح لكل عقد</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-violet-500/10 text-violet-400 border border-violet-500/30">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                </div>
            </div>
            <p class="text-2xl font-black text-violet-400 tracking-tight">{{ number_format($avgProfit, 2) }} <span class="text-xs text-violet-300">د.ب</span></p>
            <p class="text-xs text-slate-400 mt-2">معدل العائد لكل عقد فحص</p>
        </div>

        {{-- Visual Balance Progress --}}
        <div class="stat-card relative overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/40 p-5 backdrop-blur-sm flex flex-col justify-between">
            <span class="text-xs font-semibold text-slate-400 mb-2">توازن الإيرادات والمصروفات</span>
            <div class="space-y-2">
                <div class="flex justify-between text-xs font-semibold">
                    <span class="text-emerald-400">صافي الأرباح</span>
                    <span class="text-red-400">المصروفات</span>
                </div>
                <div class="flex h-2.5 w-full overflow-hidden rounded-full bg-slate-800">
                    @php
                        $totalExpProf = $totalExpenses + max(0, $totalProfit);
                        $profPct = $totalExpProf > 0 ? (max(0, $totalProfit) / $totalExpProf) * 100 : 50;
                        $expPct = $totalExpProf > 0 ? ($totalExpenses / $totalExpProf) * 100 : 50;
                    @endphp
                    <div class="bg-emerald-500 h-full transition-all" style="width: {{ $profPct }}%"></div>
                    <div class="bg-red-500 h-full transition-all" style="width: {{ $expPct }}%"></div>
                </div>
                <div class="flex justify-between text-[11px] text-slate-500 font-mono">
                    <span>{{ number_format($profPct, 0) }}%</span>
                    <span>{{ number_format($expPct, 0) }}%</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Contracts Detailed Table Card --}}
    <div class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/50 backdrop-blur-md shadow-2xl">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between px-6 py-5 border-b border-slate-800/80 bg-slate-900/80 gap-3">
            <div>
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <svg class="h-5 w-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>تفاصيل عقود الشهر ({{ $contracts->count() }})</span>
                </h3>
                <p class="text-xs text-slate-400 mt-1">كشف تفصيلي ببيانات كل عقد والمدفوعات والمصروفات المحملة عليه</p>
            </div>
            <a href="{{ route('admin.reports.monthly.excel', ['month' => $month, 'year' => $year]) }}"
               class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-3.5 py-1.5 text-xs font-bold text-emerald-400 hover:bg-emerald-500/20 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                <span>تحميل كشف Excel</span>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-800 text-right text-xs sm:text-sm">
                <thead class="bg-slate-950/60 font-semibold text-slate-300">
                    <tr>
                        <th class="px-4 py-3.5 whitespace-nowrap">رقم العقد</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">العميل</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">العقار / المنطقة</th>
                        <th class="px-4 py-3.5 whitespace-nowrap text-left">قيمة العقد</th>
                        <th class="px-4 py-3.5 whitespace-nowrap text-left">المدفوع</th>
                        <th class="px-4 py-3.5 whitespace-nowrap text-left">المتبقي</th>
                        <th class="px-4 py-3.5 whitespace-nowrap text-left">المصروفات</th>
                        <th class="px-4 py-3.5 whitespace-nowrap text-left">صافي الربح</th>
                        <th class="px-4 py-3.5 whitespace-nowrap text-center">حالة الدفع</th>
                        <th class="px-4 py-3.5 whitespace-nowrap text-center">تاريخ العقد</th>
                        <th class="px-4 py-3.5 whitespace-nowrap text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($contracts as $c)
                    @php
                        $paymentColors = [
                            'paid' => 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30',
                            'partial' => 'bg-amber-500/15 text-amber-400 border border-amber-500/30',
                            'unpaid' => 'bg-red-500/15 text-red-400 border border-red-500/30',
                        ];
                    @endphp
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            <a href="{{ route('admin.houses.show', $c) }}"
                               class="font-mono text-xs font-bold text-emerald-400 hover:text-emerald-300 hover:underline">
                                {{ $c->contract_number ?? '—' }}
                            </a>
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            <div class="font-medium text-white">{{ $c->buyer_name ?? $c->client_name ?? '—' }}</div>
                            @if($c->phone)
                                <div class="text-[11px] text-slate-400 font-mono mt-0.5">{{ $c->phone }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap text-slate-300">
                            {{ Str::limit($c->area ?? $c->address ?? $c->title, 25) }}
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap text-left font-bold text-white font-mono">
                            {{ number_format($c->price ?? 0, 2) }} <span class="text-[11px] font-normal text-slate-400">د.ب</span>
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap text-left font-bold text-emerald-400 font-mono">
                            {{ number_format($c->total_paid, 2) }}
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap text-left font-bold text-amber-400 font-mono">
                            {{ number_format($c->remaining_amount, 2) }}
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap text-left font-bold text-red-400 font-mono">
                            {{ number_format($c->total_expenses, 2) }}
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap text-left font-bold font-mono {{ $c->net_profit >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                            {{ number_format($c->net_profit, 2) }}
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap text-center">
                            <span class="inline-flex rounded-lg px-2.5 py-1 text-xs font-bold {{ $paymentColors[$c->payment_status] ?? $paymentColors['unpaid'] }}">
                                {{ $c->payment_status_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap text-center text-xs text-slate-400 font-mono">
                            {{ $c->contract_date?->format('Y-m-d') ?? '—' }}
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.houses.show', $c) }}"
                                   class="rounded-lg bg-slate-800 px-2.5 py-1 text-xs font-bold text-slate-300 hover:bg-slate-700 hover:text-white transition-colors"
                                   title="عرض العقد">
                                    فتح
                                </a>
                                <a href="{{ route('admin.houses.contract.pdf', $c) }}"
                                   target="_blank"
                                   class="rounded-lg bg-red-600/20 px-2 py-1 text-xs font-bold text-red-400 hover:bg-red-600/30 transition-colors"
                                   title="تحميل PDF">
                                    PDF
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-800/80 text-slate-500 mb-3 border border-slate-700/60">
                                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                </div>
                                <h4 class="text-base font-bold text-white mb-1">لا توجد عقود مسجلة في هذا الشهر</h4>
                                <p class="text-xs text-slate-400 mb-4">يمكنك اختيار شهر آخر من شريط التنقل بالأعلى أو إضافة عقد جديد</p>
                                <a href="{{ route('admin.houses.create') }}" class="rounded-xl bg-emerald-500 px-4 py-2 text-xs font-bold text-slate-950 hover:bg-emerald-400 transition-colors">
                                    + إنشاء عقد جديد
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($contracts->count() > 0)
                <tfoot class="bg-slate-950/80 border-t-2 border-emerald-500/40 text-xs sm:text-sm font-bold">
                    <tr>
                        <td colspan="3" class="px-4 py-4 text-emerald-400 text-center font-black">
                            الإجمالي الشهري ({{ $contracts->count() }} عقد)
                        </td>
                        <td class="px-4 py-4 text-left font-mono text-white font-black">
                            {{ number_format($totalValue, 2) }} <span class="text-[11px] font-normal text-slate-400">د.ب</span>
                        </td>
                        <td class="px-4 py-4 text-left font-mono text-emerald-400 font-black">
                            {{ number_format($totalCollected, 2) }}
                        </td>
                        <td class="px-4 py-4 text-left font-mono text-amber-400 font-black">
                            {{ number_format($totalRemaining, 2) }}
                        </td>
                        <td class="px-4 py-4 text-left font-mono text-red-400 font-black">
                            {{ number_format($totalExpenses, 2) }}
                        </td>
                        <td class="px-4 py-4 text-left font-mono font-black {{ $totalProfit >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                            {{ number_format($totalProfit, 2) }}
                        </td>
                        <td colspan="3" class="px-4 py-4 text-center text-slate-400 text-xs font-normal">
                            نسبة التحصيل: <span class="font-bold text-emerald-400">{{ number_format($collectionRate, 1) }}%</span>
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
@endsection
