@extends('layouts.admin')

@section('title', 'التقرير السنوي — ' . $year)

@section('content')
    {{-- Header with Title and Actions --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-8">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">التقرير السنوي</h1>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/30 px-3 py-1 text-xs font-bold text-emerald-400">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    سنة {{ $year }}
                </span>
            </div>
            <p class="mt-1.5 text-sm text-slate-400">مقارنة شهرية شاملة للأداء المالي والإيرادات والمصروفات وصافي الأرباح</p>
        </div>

        {{-- Top Actions: Excel Export & Monthly Link --}}
        <div class="flex items-center gap-3 flex-wrap">
            <a href="{{ route('admin.reports.annual.excel', ['year' => $year]) }}"
               class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-emerald-900/30 hover:from-emerald-500 hover:to-teal-500 transition-all duration-200 hover:scale-[1.02] active:scale-[0.98]">
                <svg class="h-5 w-5 text-emerald-100" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>تصدير Excel (.xlsx)</span>
            </a>
            <a href="{{ route('admin.reports.monthly') }}"
               class="inline-flex items-center gap-2 rounded-xl border border-slate-700 bg-slate-800/80 px-4 py-2.5 text-sm font-semibold text-slate-300 hover:bg-slate-700 hover:text-white transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>التقرير الشهري</span>
            </a>
        </div>
    </div>

    {{-- Year Stepper & Selector Navigator Bar --}}
    <div class="mb-8 rounded-2xl border border-slate-800 bg-slate-900/60 backdrop-blur-md p-4 sm:p-5 shadow-xl">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            {{-- Quick Arrow Steppers --}}
            <div class="flex items-center gap-2 w-full sm:w-auto justify-between sm:justify-start">
                <a href="{{ route('admin.reports.annual', ['year' => $year - 1]) }}"
                   class="inline-flex items-center gap-1.5 rounded-xl border border-slate-700 bg-slate-800/80 px-3.5 py-2 text-xs font-bold text-slate-300 hover:bg-slate-700 hover:text-white transition-all">
                    <svg class="h-4 w-4 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                    <span>سنة {{ $year - 1 }}</span>
                </a>

                <a href="{{ route('admin.reports.annual', ['year' => now()->year]) }}"
                   class="rounded-xl px-3 py-2 text-xs font-bold transition-all {{ ($year == now()->year) ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40' : 'bg-slate-800 text-slate-400 hover:text-white' }}">
                    السنة الحالية
                </a>

                <a href="{{ route('admin.reports.annual', ['year' => $year + 1]) }}"
                   class="inline-flex items-center gap-1.5 rounded-xl border border-slate-700 bg-slate-800/80 px-3.5 py-2 text-xs font-bold text-slate-300 hover:bg-slate-700 hover:text-white transition-all">
                    <span>سنة {{ $year + 1 }}</span>
                    <svg class="h-4 w-4 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>
            </div>

            {{-- Year Select Form --}}
            <form method="GET" action="{{ route('admin.reports.annual') }}" class="flex items-center gap-3 w-full sm:w-auto">
                <label class="text-xs font-semibold text-slate-400 whitespace-nowrap">اختر سنة محددة:</label>
                <select name="year" class="rounded-xl border border-slate-700 bg-slate-950 px-4 py-2 text-xs sm:text-sm text-white focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    @for($y = now()->year - 5; $y <= now()->year + 2; $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <button type="submit" class="rounded-xl bg-emerald-500 px-5 py-2 text-xs sm:text-sm font-bold text-slate-950 hover:bg-emerald-400 transition-colors shadow-md">
                    عرض
                </button>
            </form>
        </div>
    </div>

    {{-- Computed Values for Cards --}}
    @php
        $avgYearlyValue = $totals['contracts'] > 0 ? $totals['revenue'] / $totals['contracts'] : 0;
        $avgYearlyProfit = $totals['contracts'] > 0 ? $totals['profit'] / $totals['contracts'] : 0;
    @endphp

    {{-- Primary KPI Cards (4 Cards - Side by Side) --}}
    <div class="overflow-x-auto pb-2 mb-6">
        <div class="financial-metrics-grid" style="display: grid !important; grid-template-columns: repeat(4, minmax(0, 1fr)) !important; gap: 1rem !important; width: 100% !important; min-width: 680px !important;">
            {{-- Card 1: Contracts Count --}}
            <div class="stat-card relative overflow-hidden rounded-2xl border border-slate-800 bg-gradient-to-br from-slate-900/80 to-slate-900/40 p-5 backdrop-blur-sm" style="display: flex; flex-direction: column; justify-content: space-between; min-width: 0;">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-slate-400">إجمالي العقود</span>
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-800 text-slate-300 border border-slate-700/50">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    </div>
                </div>
                <div>
                    <p class="text-3xl font-black text-white tracking-tight font-mono">{{ number_format($totals['contracts']) }}</p>
                    <p class="text-xs text-slate-400 mt-2">متوسط العقد: <span class="font-bold text-slate-300 font-mono">{{ number_format($avgYearlyValue, 2) }} د.ب</span></p>
                </div>
            </div>

            {{-- Card 2: Total Revenue --}}
            <div class="stat-card relative overflow-hidden rounded-2xl border border-blue-500/20 bg-gradient-to-br from-blue-950/30 to-slate-900/40 p-5 backdrop-blur-sm" style="display: flex; flex-direction: column; justify-content: space-between; min-width: 0;">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-blue-300">قيمة العقود الإجمالية</span>
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-500/10 text-blue-400 border border-blue-500/30">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                </div>
                <div>
                    <p class="text-3xl font-black text-blue-400 tracking-tight font-mono">{{ number_format($totals['revenue'], 2) }} <span class="text-sm font-medium text-blue-300">د.ب</span></p>
                    <p class="text-xs text-slate-400 mt-2">إجمالي الإيرادات المسجلة بالسنة</p>
                </div>
            </div>

            {{-- Card 3: Total Collected --}}
            <div class="stat-card relative overflow-hidden rounded-2xl border border-emerald-500/20 bg-gradient-to-br from-emerald-950/30 to-slate-900/40 p-5 backdrop-blur-sm" style="display: flex; flex-direction: column; justify-content: space-between; min-width: 0;">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-emerald-300">المحصل فعلياً</span>
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                </div>
                <div>
                    <p class="text-3xl font-black text-emerald-400 tracking-tight font-mono">{{ number_format($totals['collected'], 2) }} <span class="text-sm font-medium text-emerald-300">د.ب</span></p>
                    <div class="mt-2 flex items-center justify-between text-xs">
                        <span class="text-slate-400">نسبة التحصيل:</span>
                        <span class="font-bold text-emerald-400 font-mono">{{ number_format($annualCollectionRate, 1) }}%</span>
                    </div>
                    <div class="w-full bg-slate-800 rounded-full h-1.5 mt-1.5 overflow-hidden">
                        <div class="bg-emerald-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ min(100, $annualCollectionRate) }}%"></div>
                    </div>
                </div>
            </div>

            {{-- Card 4: Total Remaining --}}
            <div class="stat-card relative overflow-hidden rounded-2xl border border-amber-500/20 bg-gradient-to-br from-amber-950/30 to-slate-900/40 p-5 backdrop-blur-sm" style="display: flex; flex-direction: column; justify-content: space-between; min-width: 0;">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-amber-300">المتبقي للتحصيل</span>
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-500/10 text-amber-400 border border-amber-500/30">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                </div>
                <div>
                    <p class="text-3xl font-black text-amber-400 tracking-tight font-mono">{{ number_format($totals['remaining'], 2) }} <span class="text-sm font-medium text-amber-300">د.ب</span></p>
                    <p class="text-xs text-slate-400 mt-2">مستحقات سنوية آجلة قيد التحصيل</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Secondary KPI Cards (Expenses & Profits - 4 Cards Across) --}}
    <div class="overflow-x-auto pb-2 mb-8">
        <div class="financial-metrics-grid" style="display: grid !important; grid-template-columns: repeat(4, minmax(0, 1fr)) !important; gap: 1rem !important; width: 100% !important; min-width: 680px !important;">
            {{-- Expenses --}}
            <div class="stat-card relative overflow-hidden rounded-2xl border border-red-500/20 bg-gradient-to-br from-red-950/30 to-slate-900/40 p-5 backdrop-blur-sm" style="display: flex; flex-direction: column; justify-content: space-between; min-width: 0;">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-red-300">إجمالي المصروفات</span>
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-500/10 text-red-400 border border-red-500/30">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                </div>
                <div>
                    <p class="text-2xl font-black text-red-400 tracking-tight font-mono">{{ number_format($totals['expenses'], 2) }} <span class="text-xs text-red-300">د.ب</span></p>
                    <p class="text-xs text-slate-400 mt-2">تكاليف ومصاريف سنوية تشغيلية</p>
                </div>
            </div>

            {{-- Net Profit --}}
            <div class="stat-card relative overflow-hidden rounded-2xl border {{ $totals['profit'] >= 0 ? 'border-emerald-500/30 bg-gradient-to-br from-emerald-950/30' : 'border-red-500/30 bg-gradient-to-br from-red-950/30' }} to-slate-900/40 p-5 backdrop-blur-sm" style="display: flex; flex-direction: column; justify-content: space-between; min-width: 0;">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold {{ $totals['profit'] >= 0 ? 'text-emerald-300' : 'text-red-300' }}">صافي الأرباح</span>
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg {{ $totals['profit'] >= 0 ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30' : 'bg-red-500/10 text-red-400 border-red-500/30' }} border">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                    </div>
                </div>
                <div>
                    <p class="text-2xl font-black {{ $totals['profit'] >= 0 ? 'text-emerald-400' : 'text-red-400' }} tracking-tight font-mono">
                        {{ number_format($totals['profit'], 2) }} <span class="text-xs font-medium">د.ب</span>
                    </p>
                    <p class="text-xs text-slate-400 mt-2">هامش الربح السنوي: <span class="font-bold font-mono {{ $profitMargin >= 0 ? 'text-emerald-400' : 'text-red-400' }}">{{ number_format($profitMargin, 1) }}%</span></p>
                </div>
            </div>

            {{-- Avg Profit --}}
            <div class="stat-card relative overflow-hidden rounded-2xl border border-violet-500/20 bg-gradient-to-br from-violet-950/30 to-slate-900/40 p-5 backdrop-blur-sm" style="display: flex; flex-direction: column; justify-content: space-between; min-width: 0;">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-violet-300">متوسط الربح لكل عقد</span>
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-violet-500/10 text-violet-400 border border-violet-500/30">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                    </div>
                </div>
                <div>
                    <p class="text-2xl font-black text-violet-400 tracking-tight font-mono">{{ number_format($avgYearlyProfit, 2) }} <span class="text-xs text-violet-300">د.ب</span></p>
                    <p class="text-xs text-slate-400 mt-2">معدل العائد لكل عقد فحص</p>
                </div>
            </div>

            {{-- Visual Balance Progress --}}
            <div class="stat-card relative overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/40 p-5 backdrop-blur-sm" style="display: flex; flex-direction: column; justify-content: space-between; min-width: 0;">
                <span class="text-xs font-semibold text-slate-400 mb-2">توازن الإيرادات والمصروفات</span>
                <div class="space-y-2">
                    <div class="flex justify-between text-xs font-semibold">
                        <span class="text-emerald-400">صافي الأرباح</span>
                        <span class="text-red-400">المصروفات</span>
                    </div>
                    <div class="flex h-2.5 w-full overflow-hidden rounded-full bg-slate-800">
                        @php
                            $totalExpProf = $totals['expenses'] + max(0, $totals['profit']);
                            $profPct = $totalExpProf > 0 ? (max(0, $totals['profit']) / $totalExpProf) * 100 : 50;
                            $expPct = $totalExpProf > 0 ? ($totals['expenses'] / $totalExpProf) * 100 : 50;
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
    </div>

    {{-- Interactive Chart Card --}}
    <div class="rounded-2xl border border-slate-800 bg-slate-900/50 backdrop-blur-md p-6 mb-8 shadow-2xl">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-2">
            <div>
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <svg class="h-5 w-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" /></svg>
                    <span>المقارنة الشهرية لحركة الأموال — {{ $year }}</span>
                </h3>
                <p class="text-xs text-slate-400 mt-1">تتبع مسار الإيرادات، المصروفات، وصافي الأرباح على مدار الـ 12 شهراً</p>
            </div>
            <div class="flex items-center gap-4 text-xs">
                <span class="inline-flex items-center gap-1.5 text-blue-400 font-semibold"><span class="h-3 w-3 rounded-sm bg-blue-500"></span> الإيرادات</span>
                <span class="inline-flex items-center gap-1.5 text-red-400 font-semibold"><span class="h-3 w-3 rounded-sm bg-red-500"></span> المصروفات</span>
                <span class="inline-flex items-center gap-1.5 text-emerald-400 font-semibold"><span class="h-3 w-3 rounded-sm bg-emerald-500"></span> صافي الأرباح</span>
            </div>
        </div>
        <div class="relative w-full h-[280px] sm:h-[340px]">
            <canvas id="annualChart"></canvas>
        </div>
    </div>

    {{-- Monthly Breakdown Table Card --}}
    <div class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/50 backdrop-blur-md shadow-2xl">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between px-6 py-5 border-b border-slate-800/80 bg-slate-900/80 gap-3">
            <div>
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <svg class="h-5 w-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>جدول الأداء الشهري للعام {{ $year }}</span>
                </h3>
                <p class="text-xs text-slate-400 mt-1">انقر على اسم أي شهر للانتقال إلى التقرير التفصيلي لعقوده</p>
            </div>
            <a href="{{ route('admin.reports.annual.excel', ['year' => $year]) }}"
               class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-3.5 py-1.5 text-xs font-bold text-emerald-400 hover:bg-emerald-500/20 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                <span>تحميل كشف Excel للعام</span>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-800 text-right text-xs sm:text-sm">
                <thead class="bg-slate-950/60 font-semibold text-slate-300">
                    <tr>
                        <th class="px-4 py-3.5 whitespace-nowrap">الشهر</th>
                        <th class="px-4 py-3.5 whitespace-nowrap text-center">عدد العقود</th>
                        <th class="px-4 py-3.5 whitespace-nowrap text-left">الإيرادات</th>
                        <th class="px-4 py-3.5 whitespace-nowrap text-left">المحصل</th>
                        <th class="px-4 py-3.5 whitespace-nowrap text-left">المتبقي</th>
                        <th class="px-4 py-3.5 whitespace-nowrap text-left">المصروفات</th>
                        <th class="px-4 py-3.5 whitespace-nowrap text-left">صافي الربح</th>
                        <th class="px-4 py-3.5 whitespace-nowrap text-center">نسبة التحصيل</th>
                        <th class="px-4 py-3.5 whitespace-nowrap text-center">التفاصيل</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @foreach($monthlyBreakdown as $m)
                    @php
                        $isCurrentMonth = ($year == now()->year && $m['month'] == now()->month);
                    @endphp
                    <tr class="transition-colors hover:bg-slate-800/40 {{ $isCurrentMonth ? 'bg-emerald-500/5' : '' }} {{ $m['contracts_count'] > 0 ? '' : 'opacity-40' }}">
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                @if($isCurrentMonth)
                                    <span class="h-2 w-2 rounded-full bg-emerald-400" title="الشهر الحالي"></span>
                                @endif
                                <a href="{{ route('admin.reports.monthly', ['month' => $m['month'], 'year' => $year]) }}"
                                   class="font-bold text-white hover:text-emerald-400 transition-colors">
                                    {{ $m['month_name'] }}
                                </a>
                                @if($isCurrentMonth)
                                    <span class="rounded bg-emerald-500/20 px-1.5 py-0.5 text-[10px] font-bold text-emerald-400">الحالي</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap text-center font-bold text-slate-200">
                            {{ $m['contracts_count'] }}
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap text-left font-mono font-bold text-blue-400">
                            {{ number_format($m['revenue'], 2) }} <span class="text-[11px] font-normal text-slate-500">د.ب</span>
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap text-left font-mono font-bold text-emerald-400">
                            {{ number_format($m['collected'], 2) }}
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap text-left font-mono font-bold text-amber-400">
                            {{ number_format($m['remaining'], 2) }}
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap text-left font-mono font-bold text-red-400">
                            {{ number_format($m['expenses'], 2) }}
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap text-left font-mono font-bold {{ $m['profit'] >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                            {{ number_format($m['profit'], 2) }}
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap text-center">
                            @if($m['revenue'] > 0)
                                <span class="inline-flex rounded-lg px-2 py-0.5 text-xs font-bold font-mono {{ $m['collection_rate'] >= 80 ? 'bg-emerald-500/20 text-emerald-400' : ($m['collection_rate'] >= 50 ? 'bg-amber-500/20 text-amber-400' : 'bg-red-500/20 text-red-400') }}">
                                    {{ number_format($m['collection_rate'], 1) }}%
                                </span>
                            @else
                                <span class="text-slate-600 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap text-center">
                            <a href="{{ route('admin.reports.monthly', ['month' => $m['month'], 'year' => $year]) }}"
                               class="inline-flex items-center gap-1 rounded-lg bg-slate-800 px-2.5 py-1 text-xs font-semibold text-slate-300 hover:bg-slate-700 hover:text-white transition-colors">
                                <span>تقرير الشهر</span>
                                <svg class="h-3 w-3 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-950/80 border-t-2 border-emerald-500/40 text-xs sm:text-sm font-bold">
                    <tr>
                        <td class="px-4 py-4 text-emerald-400 font-black">
                            الإجمالي السنوي
                        </td>
                        <td class="px-4 py-4 text-center text-white font-black">
                            {{ number_format($totals['contracts']) }}
                        </td>
                        <td class="px-4 py-4 text-left font-mono text-blue-400 font-black">
                            {{ number_format($totals['revenue'], 2) }} <span class="text-[11px] font-normal text-slate-500">د.ب</span>
                        </td>
                        <td class="px-4 py-4 text-left font-mono text-emerald-400 font-black">
                            {{ number_format($totals['collected'], 2) }}
                        </td>
                        <td class="px-4 py-4 text-left font-mono text-amber-400 font-black">
                            {{ number_format($totals['remaining'], 2) }}
                        </td>
                        <td class="px-4 py-4 text-left font-mono text-red-400 font-black">
                            {{ number_format($totals['expenses'], 2) }}
                        </td>
                        <td class="px-4 py-4 text-left font-mono font-black {{ $totals['profit'] >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                            {{ number_format($totals['profit'], 2) }}
                        </td>
                        <td class="px-4 py-4 text-center font-mono font-black text-emerald-400">
                            {{ number_format($annualCollectionRate, 1) }}%
                        </td>
                        <td class="px-4 py-4 text-center">
                            <a href="{{ route('admin.reports.annual.excel', ['year' => $year]) }}"
                               class="rounded bg-emerald-500/20 px-2 py-1 text-xs font-bold text-emerald-400 hover:bg-emerald-500/30 transition-colors">
                                Excel
                            </a>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    const ctx = document.getElementById('annualChart');
    if (ctx) {
        const data = @json($monthlyBreakdown);
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(d => d.month_name),
                datasets: [
                    {
                        label: 'الإيرادات (د.ب)',
                        data: data.map(d => d.revenue),
                        backgroundColor: 'rgba(59, 130, 246, 0.75)',
                        borderColor: '#3b82f6',
                        borderWidth: 1.5,
                        borderRadius: 6,
                        hoverBackgroundColor: 'rgba(59, 130, 246, 0.95)',
                    },
                    {
                        label: 'المصروفات (د.ب)',
                        data: data.map(d => d.expenses),
                        backgroundColor: 'rgba(239, 68, 68, 0.75)',
                        borderColor: '#ef4444',
                        borderWidth: 1.5,
                        borderRadius: 6,
                        hoverBackgroundColor: 'rgba(239, 68, 68, 0.95)',
                    },
                    {
                        label: 'صافي الربح (د.ب)',
                        data: data.map(d => d.profit),
                        backgroundColor: 'rgba(16, 185, 129, 0.75)',
                        borderColor: '#10b981',
                        borderWidth: 1.5,
                        borderRadius: 6,
                        hoverBackgroundColor: 'rgba(16, 185, 129, 0.95)',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        rtl: true,
                        labels: {
                            color: '#cbd5e1',
                            font: { family: 'IBM Plex Sans Arabic', size: 12, weight: 'bold' },
                            usePointStyle: true,
                            boxWidth: 8,
                            padding: 20
                        }
                    },
                    tooltip: {
                        rtl: true,
                        backgroundColor: '#0f172a',
                        titleColor: '#f8fafc',
                        titleFont: { family: 'IBM Plex Sans Arabic', size: 13, weight: 'bold' },
                        bodyFont: { family: 'IBM Plex Sans Arabic', size: 12 },
                        borderColor: '#334155',
                        borderWidth: 1,
                        padding: 12,
                        boxPadding: 6,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + Number(context.raw).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' د.ب';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            color: '#94a3b8',
                            font: { family: 'IBM Plex Sans Arabic', size: 11 }
                        },
                        grid: { color: 'rgba(51, 65, 85, 0.25)' }
                    },
                    y: {
                        ticks: {
                            color: '#94a3b8',
                            font: { size: 11 },
                            callback: function(value) {
                                return value.toLocaleString('en-US');
                            }
                        },
                        grid: { color: 'rgba(51, 65, 85, 0.25)' }
                    }
                }
            }
        });
    }
</script>
@endpush
