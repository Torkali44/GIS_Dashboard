@extends('layouts.admin')

@section('title', 'العقود والحسابات')

@section('content')
    {{-- Header with Title and Create Button --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-8">
        <div>
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2.5">
                        <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">عقود الفحص والحسابات</h1>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 px-3 py-0.5 text-xs font-bold text-emerald-400">
                            {{ number_format($totalContractsCount) }} عقد
                        </span>
                    </div>
                    <p class="mt-0.5 text-xs sm:text-sm text-slate-400">إدارة العقود ومتابعة تقارير الفحص والبيانات المالية والتحصيل</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ route('admin.houses.create') }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 px-5 py-2.5 text-sm font-bold text-slate-950 shadow-lg shadow-emerald-900/30 hover:from-emerald-400 hover:to-teal-400 transition-all duration-200 hover:scale-[1.02] active:scale-[0.98]">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                <span>إنشاء عقد جديد</span>
            </a>
            <a href="{{ route('admin.reports.monthly') }}"
               class="inline-flex items-center gap-1.5 rounded-xl border border-slate-700 bg-slate-800/80 px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-300 hover:bg-slate-700 hover:text-white transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                <span>تقرير شهري</span>
            </a>
            <a href="{{ route('admin.reports.annual') }}"
               class="inline-flex items-center gap-1.5 rounded-xl border border-slate-700 bg-slate-800/80 px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-300 hover:bg-slate-700 hover:text-white transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                <span>تقرير سنوي</span>
            </a>
        </div>
    </div>

    {{-- Top Overview Stats: 4 Side-by-Side Cards (Guaranteed Grid Layout) --}}
    @php
        $avgContractValue = $totalContractsCount > 0 ? $totalContractsValue / $totalContractsCount : 0;
        $collectionRate = $totalContractsValue > 0 ? ($totalCollectedPayments / $totalContractsValue) * 100 : 0;
    @endphp
    <div class="overflow-x-auto pb-2 mb-8">
        <div class="financial-metrics-grid" style="display: grid !important; grid-template-columns: repeat(4, minmax(0, 1fr)) !important; gap: 1rem !important; width: 100% !important; min-width: 680px !important;">
            {{-- Card 1: Contracts Count --}}
            <div class="stat-card rounded-2xl border border-slate-800 bg-gradient-to-br from-slate-900/90 to-slate-900/50 backdrop-blur-md p-4 sm:p-5" style="display: flex; flex-direction: column; justify-content: space-between; min-width: 0;">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-slate-400">إجمالي العقود</span>
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-800 text-slate-300 border border-slate-700/60">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    </div>
                </div>
                <div>
                    <p class="text-2xl sm:text-3xl font-black text-white tracking-tight font-mono">{{ number_format($totalContractsCount) }}</p>
                    <div class="mt-2.5 flex items-center gap-1.5 flex-wrap text-[11px]">
                        <span class="inline-flex items-center gap-1 rounded-md bg-blue-500/15 border border-blue-500/25 px-2 py-0.5 font-bold text-blue-400">
                            نشط: {{ $activeContractsCount }}
                        </span>
                        <span class="inline-flex items-center gap-1 rounded-md bg-emerald-500/15 border border-emerald-500/25 px-2 py-0.5 font-bold text-emerald-400">
                            مكتمل: {{ $completedContractsCount }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Card 2: Total Contract Value --}}
            <div class="stat-card rounded-2xl border border-blue-500/20 bg-gradient-to-br from-blue-950/25 to-slate-900/60 backdrop-blur-md p-4 sm:p-5" style="display: flex; flex-direction: column; justify-content: space-between; min-width: 0;">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-blue-300">قيمة العقود الإجمالية</span>
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-500/10 text-blue-400 border border-blue-500/30">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                </div>
                <div>
                    <p class="text-xl sm:text-2xl font-black text-blue-400 tracking-tight font-mono">{{ number_format($totalContractsValue, 2) }} <span class="text-xs font-normal text-blue-300">د.ب</span></p>
                    <p class="mt-2.5 text-[11px] text-slate-400">متوسط قيمة العقد: <span class="font-bold text-slate-300 font-mono">{{ number_format($avgContractValue, 2) }} د.ب</span></p>
                </div>
            </div>

            {{-- Card 3: Total Collected --}}
            <div class="stat-card rounded-2xl border border-emerald-500/20 bg-gradient-to-br from-emerald-950/25 to-slate-900/60 backdrop-blur-md p-4 sm:p-5" style="display: flex; flex-direction: column; justify-content: space-between; min-width: 0;">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-emerald-300">المحصل فعلياً</span>
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                </div>
                <div>
                    <div class="flex items-baseline justify-between">
                        <p class="text-xl sm:text-2xl font-black text-emerald-400 tracking-tight font-mono">{{ number_format($totalCollectedPayments, 2) }} <span class="text-xs font-normal text-emerald-300">د.ب</span></p>
                        <span class="text-[11px] font-bold text-emerald-400 font-mono">{{ number_format($collectionRate, 1) }}%</span>
                    </div>
                    <div class="w-full bg-slate-800 rounded-full h-1.5 mt-2.5 overflow-hidden">
                        <div class="bg-emerald-500 h-1.5 rounded-full transition-all" style="width: {{ min(100, $collectionRate) }}%"></div>
                    </div>
                </div>
            </div>

            {{-- Card 4: Remaining to Collect --}}
            <div class="stat-card rounded-2xl border border-amber-500/20 bg-gradient-to-br from-amber-950/25 to-slate-900/60 backdrop-blur-md p-4 sm:p-5" style="display: flex; flex-direction: column; justify-content: space-between; min-width: 0;">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-amber-300">المتبقي للتحصيل</span>
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-500/10 text-amber-400 border border-amber-500/30">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                </div>
                <div>
                    <p class="text-xl sm:text-2xl font-black text-amber-400 tracking-tight font-mono">{{ number_format($totalRemainingPayments, 2) }} <span class="text-xs font-normal text-amber-300">د.ب</span></p>
                    <p class="mt-2.5 text-[11px] text-slate-400">مستحقات آجلة قيد التحصيل</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Search & Filters Bar --}}
    <div class="mb-6 rounded-2xl border border-slate-800 bg-slate-900/60 backdrop-blur-md p-4 sm:p-5 shadow-xl">
        <form action="{{ route('admin.houses.index') }}" method="GET" class="space-y-3 sm:space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 sm:gap-4">
                {{-- Search Box --}}
                <div class="sm:col-span-2 lg:col-span-6 relative">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none text-slate-500">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="ابحث برقم العقد، اسم العميل، الهاتف، العنوان، رقم الوثيقة..."
                        class="w-full rounded-xl border border-slate-700 bg-slate-950 pr-10 pl-4 py-2.5 text-xs sm:text-sm text-white placeholder-slate-500 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                    >
                </div>

                {{-- Contract Status Filter --}}
                <div class="lg:col-span-2">
                    <select name="status" class="w-full rounded-xl border border-slate-700 bg-slate-950 py-2.5 px-3 text-xs sm:text-sm text-white focus:border-emerald-500 focus:outline-none">
                        <option value="">حالة العقد (الكل)</option>
                        <option value="draft" {{ ($statusFilter ?? '') === 'draft' ? 'selected' : '' }}>مسودة</option>
                        <option value="active" {{ ($statusFilter ?? '') === 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="completed" {{ ($statusFilter ?? '') === 'completed' ? 'selected' : '' }}>مكتمل</option>
                        <option value="cancelled" {{ ($statusFilter ?? '') === 'cancelled' ? 'selected' : '' }}>ملغي</option>
                    </select>
                </div>

                {{-- Payment Status Filter --}}
                <div class="lg:col-span-2">
                    <select name="payment" class="w-full rounded-xl border border-slate-700 bg-slate-950 py-2.5 px-3 text-xs sm:text-sm text-white focus:border-emerald-500 focus:outline-none">
                        <option value="">حالة الدفع (الكل)</option>
                        <option value="paid" {{ ($paymentFilter ?? '') === 'paid' ? 'selected' : '' }}>مدفوع بالكامل</option>
                        <option value="partial" {{ ($paymentFilter ?? '') === 'partial' ? 'selected' : '' }}>مدفوع جزئي</option>
                        <option value="unpaid" {{ ($paymentFilter ?? '') === 'unpaid' ? 'selected' : '' }}>غير مدفوع</option>
                    </select>
                </div>

                {{-- Submit & Reset Buttons --}}
                <div class="sm:col-span-2 lg:col-span-2 flex items-center gap-2">
                    <button type="submit" class="flex-1 rounded-xl bg-emerald-500 py-2.5 px-4 text-xs sm:text-sm font-bold text-slate-950 hover:bg-emerald-400 transition-colors shadow-md">
                        تطبيق الفلتر
                    </button>
                    @if($search || $statusFilter || $paymentFilter || $monthFilter || $yearFilter)
                        <a href="{{ route('admin.houses.index') }}" class="rounded-xl border border-slate-700 bg-slate-800 px-3 py-2.5 text-xs sm:text-sm font-bold text-slate-300 hover:bg-slate-700 hover:text-white transition-colors" title="مسح الفلاتر">
                            مسح
                        </a>
                    @endif
                </div>
            </div>

            {{-- Quick Filter Pills --}}
            <div class="flex items-center gap-2 pt-2 border-t border-slate-800/80 overflow-x-auto text-xs">
                <span class="text-slate-500 shrink-0 font-medium">تصفية سريعة:</span>
                <a href="{{ route('admin.houses.index') }}"
                   class="px-2.5 py-1 rounded-lg shrink-0 font-semibold transition-colors {{ empty($statusFilter) && empty($paymentFilter) ? 'bg-slate-700 text-white font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                    الكل ({{ $totalContractsCount }})
                </a>
                <a href="{{ route('admin.houses.index', ['status' => 'active']) }}"
                   class="px-2.5 py-1 rounded-lg shrink-0 font-semibold transition-colors {{ ($statusFilter ?? '') === 'active' ? 'bg-blue-500/20 text-blue-400 border border-blue-500/30' : 'text-slate-400 hover:text-blue-400 hover:bg-slate-800' }}">
                    عقود نشطة ({{ $activeContractsCount }})
                </a>
                <a href="{{ route('admin.houses.index', ['status' => 'completed']) }}"
                   class="px-2.5 py-1 rounded-lg shrink-0 font-semibold transition-colors {{ ($statusFilter ?? '') === 'completed' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'text-slate-400 hover:text-emerald-400 hover:bg-slate-800' }}">
                    عقود مكتملة ({{ $completedContractsCount }})
                </a>
                <a href="{{ route('admin.houses.index', ['payment' => 'paid']) }}"
                   class="px-2.5 py-1 rounded-lg shrink-0 font-semibold transition-colors {{ ($paymentFilter ?? '') === 'paid' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'text-slate-400 hover:text-emerald-400 hover:bg-slate-800' }}">
                    مدفوع بالكامل
                </a>
                <a href="{{ route('admin.houses.index', ['payment' => 'partial']) }}"
                   class="px-2.5 py-1 rounded-lg shrink-0 font-semibold transition-colors {{ ($paymentFilter ?? '') === 'partial' ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : 'text-slate-400 hover:text-amber-400 hover:bg-slate-800' }}">
                    مدفوع جزئي
                </a>
                <a href="{{ route('admin.houses.index', ['payment' => 'unpaid']) }}"
                   class="px-2.5 py-1 rounded-lg shrink-0 font-semibold transition-colors {{ ($paymentFilter ?? '') === 'unpaid' ? 'bg-red-500/20 text-red-400 border border-red-500/30' : 'text-slate-400 hover:text-red-400 hover:bg-slate-800' }}">
                    غير مدفوع
                </a>
            </div>
        </form>
    </div>

    {{-- Contracts Table Card --}}
    <div class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/50 backdrop-blur-md shadow-2xl">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-800/80 bg-slate-900/80">
            <div class="flex items-center gap-2">
                <svg class="h-5 w-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <h3 class="text-base font-bold text-white">قائمة العقود والتقارير</h3>
                <span class="text-xs text-slate-400">({{ $houses->total() }} عقد)</span>
            </div>
            <span class="text-xs text-slate-400">
                عرض {{ $houses->firstItem() ?? 0 }} إلى {{ $houses->lastItem() ?? 0 }} من أصل {{ $houses->total() }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-800 text-right text-xs sm:text-sm">
                <thead class="bg-slate-950/70 font-semibold text-slate-300">
                    <tr>
                        <th class="px-4 py-3.5 whitespace-nowrap">رقم العقد</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">العميل والتواصل</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">العقار / المنطقة</th>
                        <th class="px-4 py-3.5 whitespace-nowrap text-left">قيمة العقد</th>
                        <th class="px-4 py-3.5 whitespace-nowrap text-left">المدفوع</th>
                        <th class="px-4 py-3.5 whitespace-nowrap text-left">المتبقي</th>
                        <th class="px-4 py-3.5 whitespace-nowrap text-center">حالة العقد</th>
                        <th class="px-4 py-3.5 whitespace-nowrap text-center">حالة الدفع</th>
                        <th class="px-4 py-3.5 whitespace-nowrap text-center">تاريخ العقد</th>
                        <th class="px-4 py-3.5 whitespace-nowrap text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse ($houses as $house)
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
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            {{-- Contract No --}}
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <a href="{{ route('admin.houses.show', $house) }}"
                                   class="font-mono text-xs font-bold text-emerald-400 hover:text-emerald-300 hover:underline inline-flex items-center gap-1">
                                    <span>{{ $house->contract_number ?? '—' }}</span>
                                </a>
                            </td>

                            {{-- Client & Phone --}}
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <div class="font-bold text-white">{{ $house->buyer_name ?? $house->client_name ?? '—' }}</div>
                                @if($house->phone)
                                    <div class="text-[11px] text-slate-400 font-mono mt-1 flex items-center gap-2">
                                        <span>{{ $house->phone }}</span>
                                        @php
                                            $cleanPhone = preg_replace('/[^0-9]/', '', $house->phone);
                                            if (!str_starts_with($cleanPhone, '973') && strlen($cleanPhone) == 8) {
                                                $cleanPhone = '973' . $cleanPhone;
                                            }
                                        @endphp
                                        <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" class="text-emerald-400 hover:text-emerald-300" title="محادثة واتساب">
                                            <svg class="h-3.5 w-3.5 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                        </a>
                                        <a href="tel:{{ $house->phone }}" class="text-slate-400 hover:text-white" title="اتصال">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                        </a>
                                    </div>
                                @endif
                            </td>

                            {{-- Property Title / Area --}}
                            <td class="px-4 py-3.5 whitespace-nowrap text-slate-300">
                                <div class="max-w-[200px] truncate font-medium text-white" title="{{ $house->title }}">
                                    {{ $house->title }}
                                </div>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    @if($house->property_type)
                                        <span class="rounded bg-blue-500/10 border border-blue-500/20 px-1.5 py-0.2 text-[10px] font-semibold text-blue-300">{{ $house->property_type }}</span>
                                    @endif
                                    @if($house->area)
                                        <span class="text-[11px] text-slate-400">{{ $house->area }}</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Price --}}
                            <td class="px-4 py-3.5 whitespace-nowrap text-left font-mono font-bold text-white">
                                {{ $house->price ? number_format($house->price, 2) : '0.00' }} <span class="text-[11px] font-normal text-slate-400">د.ب</span>
                            </td>

                            {{-- Paid --}}
                            <td class="px-4 py-3.5 whitespace-nowrap text-left font-mono font-bold text-emerald-400">
                                {{ number_format($house->total_paid, 2) }}
                            </td>

                            {{-- Remaining --}}
                            <td class="px-4 py-3.5 whitespace-nowrap text-left font-mono font-bold {{ $house->remaining_amount > 0 ? 'text-amber-400' : 'text-slate-500' }}">
                                {{ number_format($house->remaining_amount, 2) }}
                            </td>

                            {{-- Contract Status --}}
                            <td class="px-4 py-3.5 whitespace-nowrap text-center">
                                <span class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-xs font-bold {{ $statusColors[$house->contract_status] ?? $statusColors['draft'] }}">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                    {{ $house->contract_status_label }}
                                </span>
                            </td>

                            {{-- Payment Status --}}
                            <td class="px-4 py-3.5 whitespace-nowrap text-center">
                                <span class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-xs font-bold {{ $paymentColors[$house->payment_status] ?? $paymentColors['unpaid'] }}">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                    {{ $house->payment_status_label }}
                                </span>
                            </td>

                            {{-- Date --}}
                            <td class="px-4 py-3.5 whitespace-nowrap text-center text-xs text-slate-400 font-mono">
                                {{ $house->contract_date?->format('Y-m-d') ?? '—' }}
                            </td>

                            {{-- Actions --}}
                            <td class="px-4 py-3.5 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-1">
                                    {{-- View --}}
                                    <a href="{{ route('admin.houses.show', $house) }}"
                                       class="rounded-lg bg-emerald-500/15 border border-emerald-500/30 px-2.5 py-1 text-xs font-bold text-emerald-400 hover:bg-emerald-500/25 transition-colors"
                                       title="فتح العقد والحسابات">
                                        فتح
                                    </a>

                                    {{-- PDF --}}
                                    <a href="{{ route('admin.houses.contract.pdf', $house) }}"
                                       target="_blank"
                                       class="rounded-lg bg-red-600/15 border border-red-500/30 px-2 py-1 text-xs font-bold text-red-400 hover:bg-red-600/25 transition-colors"
                                       title="تحميل العقد PDF">
                                        PDF
                                    </a>

                                    {{-- Word --}}
                                    <a href="{{ route('admin.houses.contract.word', $house) }}"
                                       class="rounded-lg bg-blue-600/15 border border-blue-500/30 px-2 py-1 text-xs font-bold text-blue-400 hover:bg-blue-600/25 transition-colors"
                                       title="تحميل العقد Word">
                                        Word
                                    </a>

                                    {{-- Edit --}}
                                    <a href="{{ route('admin.houses.edit', $house) }}"
                                       class="rounded-lg bg-slate-800 border border-slate-700 px-2 py-1 text-xs font-semibold text-slate-300 hover:bg-slate-700 hover:text-white transition-colors"
                                       title="تعديل بيانات العقد">
                                        تعديل
                                    </a>

                                    {{-- Delete --}}
                                    <form id="delete-house-{{ $house->id }}" action="{{ route('admin.houses.destroy', $house) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            onclick="confirmAction('هل أنت متأكد من حذف هذا العقد وجميع بياناته المالية والفحص؟', function() { document.getElementById('delete-house-{{ $house->id }}').submit(); })"
                                            class="rounded-lg bg-red-950/25 border border-red-800/40 px-2 py-1 text-xs font-semibold text-red-400 hover:bg-red-900/40 hover:text-red-300 transition-colors"
                                            title="حذف العقد">
                                            حذف
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-800/80 text-slate-500 mb-3 border border-slate-700/60">
                                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                    </div>
                                    <h4 class="text-base font-bold text-white mb-1">لا توجد عقود مطابقة</h4>
                                    <p class="text-xs text-slate-400 mb-4">جرّب تغيير كلمات البحث أو الفلاتر أو أنشئ عقداً جديداً</p>
                                    <a href="{{ route('admin.houses.create') }}" class="rounded-xl bg-emerald-500 px-4 py-2 text-xs font-bold text-slate-950 hover:bg-emerald-400 transition-colors">
                                        + إنشاء أول عقد فحص
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($houses->hasPages())
        <div class="mt-6">
            {{ $houses->links() }}
        </div>
    @endif
@endsection
