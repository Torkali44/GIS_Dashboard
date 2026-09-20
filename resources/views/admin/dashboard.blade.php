@extends('layouts.admin')

@section('title', 'لوحة التحكم')

@section('content')
    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">لوحة التحكم والمؤشرات</h1>
            <p class="mt-1 text-xs sm:text-sm text-slate-400">نظرة شاملة ومباشرة على العقود، الإيرادات، المدفوعات، والمصروفات</p>
        </div>
        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ route('admin.houses.create') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 px-4 py-2 text-xs sm:text-sm font-bold text-slate-950 shadow-md shadow-emerald-900/20 hover:from-emerald-400 hover:to-teal-400 transition-all hover:scale-[1.02] active:scale-[0.98]">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                <span>إنشاء عقد جديد</span>
            </a>
            <a href="{{ route('admin.reports.monthly') }}"
               class="inline-flex items-center gap-1.5 rounded-xl border border-slate-700 bg-slate-800/80 px-3.5 py-2 text-xs sm:text-sm font-semibold text-slate-300 hover:bg-slate-700 hover:text-white transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                <span>تقرير شهري</span>
            </a>
            <a href="{{ route('admin.reports.annual') }}"
               class="inline-flex items-center gap-1.5 rounded-xl border border-slate-700 bg-slate-800/80 px-3.5 py-2 text-xs sm:text-sm font-semibold text-slate-300 hover:bg-slate-700 hover:text-white transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                <span>تقرير سنوي</span>
            </a>
        </div>
    </div>

    {{-- Main Stats Cards: Exactly 4 across the screen --}}
    @php
        $avgVal = $totalContracts > 0 ? $totalContractValue / $totalContracts : 0;
        $collectionRate = $totalContractValue > 0 ? ($totalCollected / $totalContractValue) * 100 : 0;
        $profitMargin = $totalContractValue > 0 ? ($totalNetProfit / $totalContractValue) * 100 : 0;
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
        {{-- Card 1: Total Contracts --}}
        <div class="stat-card rounded-2xl border border-slate-800 bg-slate-900/60 backdrop-blur-md p-4 sm:p-5 relative overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-400">إجمالي العقود</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                </div>
            </div>
            <div class="flex items-baseline justify-between">
                <p class="text-2xl font-black text-white tracking-tight font-mono">{{ number_format($totalContracts) }}</p>
                <span class="text-[11px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-2 py-0.5 rounded-md">
                    هذا الشهر: {{ $totalContractsThisMonth }}
                </span>
            </div>
            <p class="mt-2 text-[11px] text-slate-400">جميع العقود المسجلة بالنظام</p>
        </div>

        {{-- Card 2: Total Contract Value --}}
        <div class="stat-card rounded-2xl border border-blue-500/20 bg-gradient-to-br from-blue-950/20 to-slate-900/60 backdrop-blur-md p-4 sm:p-5 relative overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-blue-300">قيمة العقود الإجمالية</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-500/10 text-blue-400 border border-blue-500/30">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
            </div>
            <p class="text-2xl font-black text-blue-400 tracking-tight font-mono">{{ number_format($totalContractValue, 2) }} <span class="text-xs font-normal text-blue-300">د.ب</span></p>
            <p class="mt-2 text-[11px] text-slate-400">متوسط قيمة العقد: <span class="font-bold text-slate-300 font-mono">{{ number_format($avgVal, 2) }} د.ب</span></p>
        </div>

        {{-- Card 3: Collected --}}
        <div class="stat-card rounded-2xl border border-emerald-500/20 bg-gradient-to-br from-emerald-950/20 to-slate-900/60 backdrop-blur-md p-4 sm:p-5 relative overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-emerald-300">المبالغ المحصّلة</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
            </div>
            <div class="flex items-baseline justify-between">
                <p class="text-2xl font-black text-emerald-400 tracking-tight font-mono">{{ number_format($totalCollected, 2) }} <span class="text-xs font-normal text-emerald-300">د.ب</span></p>
                <span class="text-[11px] font-bold text-emerald-400 font-mono">{{ number_format($collectionRate, 1) }}%</span>
            </div>
            <div class="w-full bg-slate-800 rounded-full h-1.5 mt-2 overflow-hidden">
                <div class="bg-emerald-500 h-1.5 rounded-full transition-all" style="width: {{ min(100, $collectionRate) }}%"></div>
            </div>
        </div>

        {{-- Card 4: Remaining --}}
        <div class="stat-card rounded-2xl border border-amber-500/20 bg-gradient-to-br from-amber-950/20 to-slate-900/60 backdrop-blur-md p-4 sm:p-5 relative overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-amber-300">المبالغ المتبقية</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-500/10 text-amber-400 border border-amber-500/30">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
            </div>
            <p class="text-2xl font-black text-amber-400 tracking-tight font-mono">{{ number_format($totalRemaining, 2) }} <span class="text-xs font-normal text-amber-300">د.ب</span></p>
            <p class="mt-2 text-[11px] text-slate-400">مستحقات آجلة قيد التحصيل</p>
        </div>
    </div>

    {{-- Second Row: 4 Cards across screen --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        {{-- Card 5: Expenses --}}
        <div class="stat-card rounded-2xl border border-red-500/20 bg-gradient-to-br from-red-950/20 to-slate-900/60 backdrop-blur-md p-4 sm:p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-red-300">إجمالي المصروفات</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-500/10 text-red-400 border border-red-500/30">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
            </div>
            <p class="text-2xl font-black text-red-400 tracking-tight font-mono">{{ number_format($totalExpenses, 2) }} <span class="text-xs font-normal text-red-300">د.ب</span></p>
            <p class="mt-2 text-[11px] text-slate-400">تكاليف وفحوصات ومصاريف</p>
        </div>

        {{-- Card 6: Net Profit --}}
        <div class="stat-card rounded-2xl border {{ $totalNetProfit >= 0 ? 'border-emerald-500/30 bg-emerald-950/15' : 'border-red-500/30 bg-red-950/15' }} backdrop-blur-md p-4 sm:p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold {{ $totalNetProfit >= 0 ? 'text-emerald-300' : 'text-red-300' }}">صافي الأرباح</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg {{ $totalNetProfit >= 0 ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30' : 'bg-red-500/10 text-red-400 border-red-500/30' }} border">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                </div>
            </div>
            <p class="text-2xl font-black {{ $totalNetProfit >= 0 ? 'text-emerald-400' : 'text-red-400' }} tracking-tight font-mono">
                {{ number_format($totalNetProfit, 2) }} <span class="text-xs font-normal">د.ب</span>
            </p>
            <p class="mt-2 text-[11px] text-slate-400">هامش الربح: <span class="font-bold {{ $profitMargin >= 0 ? 'text-emerald-400' : 'text-red-400' }} font-mono">{{ number_format($profitMargin, 1) }}%</span></p>
        </div>

        {{-- Card 7: Avg Profit Per Contract --}}
        <div class="stat-card rounded-2xl border border-violet-500/20 bg-gradient-to-br from-violet-950/20 to-slate-900/60 backdrop-blur-md p-4 sm:p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-violet-300">متوسط ربح العقد</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-violet-500/10 text-violet-400 border border-violet-500/30">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                </div>
            </div>
            <p class="text-2xl font-black text-violet-400 tracking-tight font-mono">{{ number_format($avgProfitPerContract, 2) }} <span class="text-xs font-normal text-violet-300">د.ب</span></p>
            <p class="mt-2 text-[11px] text-slate-400">معدل العائد لكل فحص منجز</p>
        </div>

        {{-- Card 8: Active & Completed Contracts --}}
        <div class="stat-card rounded-2xl border border-slate-800 bg-slate-900/60 backdrop-blur-md p-4 sm:p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-400">حالة سريان العقود</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-800 text-slate-300 border border-slate-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                </div>
            </div>
            <div class="flex items-center justify-between text-sm">
                <div class="flex items-center gap-1.5">
                    <span class="h-2 w-2 rounded-full bg-blue-400"></span>
                    <span class="text-slate-300 text-xs">نشط: <strong class="text-white font-mono">{{ $activeContracts }}</strong></span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                    <span class="text-slate-300 text-xs">مكتمل: <strong class="text-white font-mono">{{ $completedContracts }}</strong></span>
                </div>
            </div>
            @php
                $totStatus = max(1, $activeContracts + $completedContracts);
                $actPct = ($activeContracts / $totStatus) * 100;
                $compPct = ($completedContracts / $totStatus) * 100;
            @endphp
            <div class="w-full bg-slate-800 rounded-full h-1.5 mt-3 overflow-hidden flex">
                <div class="bg-blue-500 h-1.5" style="width: {{ $actPct }}%"></div>
                <div class="bg-emerald-500 h-1.5" style="width: {{ $compPct }}%"></div>
            </div>
        </div>
    </div>

    {{-- Payment Status + Monthly Chart (Compact & Refined) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {{-- Payment Status Breakdown (Compact & Sharp) --}}
        <div class="rounded-2xl border border-slate-800 bg-slate-900/50 backdrop-blur-md p-5 shadow-xl">
            <div class="flex items-center justify-between mb-4 border-b border-slate-800 pb-3">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <svg class="h-4 w-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                    <span>توزيع حالات الدفع</span>
                </h3>
                <span class="text-[11px] text-slate-400 font-mono">{{ $paidCount + $partialCount + $unpaidCount }} عقد</span>
            </div>

            @php $totalStatusCount = max(1, $paidCount + $partialCount + $unpaidCount); @endphp
            <div class="space-y-3 text-xs">
                {{-- Paid --}}
                <div class="flex items-center justify-between rounded-xl bg-slate-950/40 border border-slate-800/80 p-2.5">
                    <div class="flex items-center gap-2">
                        <span class="h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
                        <span class="text-slate-300 font-medium">مدفوع بالكامل</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-emerald-400 font-mono">{{ $paidCount }}</span>
                        <span class="rounded bg-emerald-500/10 px-1.5 py-0.5 text-[10px] font-bold text-emerald-400 font-mono">{{ number_format(($paidCount / $totalStatusCount) * 100, 0) }}%</span>
                    </div>
                </div>

                {{-- Partial --}}
                <div class="flex items-center justify-between rounded-xl bg-slate-950/40 border border-slate-800/80 p-2.5">
                    <div class="flex items-center gap-2">
                        <span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span>
                        <span class="text-slate-300 font-medium">مدفوع جزئي</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-amber-400 font-mono">{{ $partialCount }}</span>
                        <span class="rounded bg-amber-500/10 px-1.5 py-0.5 text-[10px] font-bold text-amber-400 font-mono">{{ number_format(($partialCount / $totalStatusCount) * 100, 0) }}%</span>
                    </div>
                </div>

                {{-- Unpaid --}}
                <div class="flex items-center justify-between rounded-xl bg-slate-950/40 border border-slate-800/80 p-2.5">
                    <div class="flex items-center gap-2">
                        <span class="h-2.5 w-2.5 rounded-full bg-red-400"></span>
                        <span class="text-slate-300 font-medium">غير مدفوع</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-red-400 font-mono">{{ $unpaidCount }}</span>
                        <span class="rounded bg-red-500/10 px-1.5 py-0.5 text-[10px] font-bold text-red-400 font-mono">{{ number_format(($unpaidCount / $totalStatusCount) * 100, 0) }}%</span>
                    </div>
                </div>
            </div>

            <div class="mt-4 flex h-2 overflow-hidden rounded-full bg-slate-800">
                <div class="bg-emerald-500 transition-all" style="width: {{ ($paidCount / $totalStatusCount) * 100 }}%"></div>
                <div class="bg-amber-500 transition-all" style="width: {{ ($partialCount / $totalStatusCount) * 100 }}%"></div>
                <div class="bg-red-500 transition-all" style="width: {{ ($unpaidCount / $totalStatusCount) * 100 }}%"></div>
            </div>
        </div>

        {{-- Monthly Chart --}}
        <div class="lg:col-span-2 rounded-2xl border border-slate-800 bg-slate-900/50 backdrop-blur-md p-5 shadow-xl">
            <div class="flex items-center justify-between mb-4 border-b border-slate-800 pb-3">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <svg class="h-4 w-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" /></svg>
                    <span>الإيرادات والمصروفات — حركة آخر 12 شهر</span>
                </h3>
                <div class="flex items-center gap-3 text-[11px]">
                    <span class="inline-flex items-center gap-1 text-emerald-400 font-semibold"><span class="h-2 w-2 rounded-full bg-emerald-500"></span> الإيرادات</span>
                    <span class="inline-flex items-center gap-1 text-blue-400 font-semibold"><span class="h-2 w-2 rounded-full bg-blue-500"></span> المحصل</span>
                    <span class="inline-flex items-center gap-1 text-red-400 font-semibold"><span class="h-2 w-2 rounded-full bg-red-500"></span> المصروفات</span>
                </div>
            </div>
            <div class="relative w-full h-[220px]">
                <script type="application/json" id="monthly-chart-data">@json($monthlyData)</script>
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Recent Contracts Table Card --}}
    <div class="rounded-2xl border border-slate-800 bg-slate-900/50 backdrop-blur-md overflow-hidden shadow-xl">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-800/80 bg-slate-900/80">
            <div>
                <h3 class="text-sm sm:text-base font-bold text-white">آخر العقود المضافة</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">أحدث 5 عقود مسجلة في النظام</p>
            </div>
            <a href="{{ route('admin.houses.index') }}" class="inline-flex items-center gap-1 rounded-xl bg-slate-800 px-3 py-1.5 text-xs font-bold text-emerald-400 hover:bg-slate-700 hover:text-emerald-300 transition-colors">
                <span>عرض جميع العقود</span>
                <svg class="h-3.5 w-3.5 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-800 text-right text-xs sm:text-sm">
                <thead class="bg-slate-950/60 font-semibold text-slate-300">
                    <tr>
                        <th class="px-5 py-3 whitespace-nowrap">رقم العقد</th>
                        <th class="px-5 py-3 whitespace-nowrap">العميل</th>
                        <th class="px-5 py-3 whitespace-nowrap text-left">قيمة العقد</th>
                        <th class="px-5 py-3 whitespace-nowrap text-center">حالة العقد</th>
                        <th class="px-5 py-3 whitespace-nowrap text-center">حالة الدفع</th>
                        <th class="px-5 py-3 whitespace-nowrap text-center">تاريخ العقد</th>
                        <th class="px-5 py-3 whitespace-nowrap text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse ($recentContracts as $contract)
                        @php
                            $statusColors = [
                                'draft' => 'bg-slate-500/15 text-slate-400 border border-slate-500/30',
                                'active' => 'bg-blue-500/15 text-blue-400 border border-blue-500/30',
                                'completed' => 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30',
                                'cancelled' => 'bg-red-500/15 text-red-400 border border-red-500/30'
                            ];
                            $paymentColors = [
                                'paid' => 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30',
                                'partial' => 'bg-amber-500/15 text-amber-400 border border-amber-500/30',
                                'unpaid' => 'bg-red-500/15 text-red-400 border border-red-500/30'
                            ];
                        @endphp
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="px-5 py-3 whitespace-nowrap font-mono text-emerald-400 font-bold text-xs">
                                <a href="{{ route('admin.houses.show', $contract) }}" class="hover:underline">
                                    {{ $contract->contract_number ?? '—' }}
                                </a>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-white font-medium">
                                {{ $contract->buyer_name ?? $contract->client_name ?? '—' }}
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-left font-mono font-bold text-white">
                                {{ $contract->price ? number_format($contract->price, 2) : '0.00' }} <span class="text-[10px] font-normal text-slate-400">د.ب</span>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-center">
                                <span class="inline-flex items-center rounded-lg px-2.5 py-0.5 text-xs font-bold {{ $statusColors[$contract->contract_status] ?? $statusColors['draft'] }}">
                                    {{ $contract->contract_status_label }}
                                </span>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-center">
                                <span class="inline-flex items-center rounded-lg px-2.5 py-0.5 text-xs font-bold {{ $paymentColors[$contract->payment_status] ?? $paymentColors['unpaid'] }}">
                                    {{ $contract->payment_status_label }}
                                </span>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-center text-xs text-slate-400 font-mono">
                                {{ $contract->contract_date?->format('Y-m-d') ?? '—' }}
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('admin.houses.show', $contract) }}"
                                       class="rounded-lg bg-emerald-500/15 border border-emerald-500/30 px-2.5 py-1 text-xs font-bold text-emerald-400 hover:bg-emerald-500/25 transition-colors">
                                        فتح
                                    </a>
                                    <a href="{{ route('admin.houses.contract.pdf', $contract) }}"
                                       target="_blank"
                                       class="rounded-lg bg-red-600/15 border border-red-500/30 px-2 py-1 text-xs font-bold text-red-400 hover:bg-red-600/25 transition-colors"
                                       title="تحميل PDF">
                                        PDF
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-slate-500 text-xs">
                                لا توجد عقود بعد. <a href="{{ route('admin.houses.create') }}" class="text-emerald-400 hover:underline">إنشاء عقد جديد</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

