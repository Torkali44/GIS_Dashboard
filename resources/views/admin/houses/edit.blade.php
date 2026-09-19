@extends('layouts.admin')

@section('title', 'تعديل عقد: ' . ($house->contract_number ?? $house->id))

@section('content')
<div class="space-y-8 max-w-6xl mx-auto pb-16">

    {{-- Breadcrumb & Page Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-800/80 pb-6">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-400 transition-colors">لوحة التحكم</a>
                <span>/</span>
                <a href="{{ route('admin.houses.index') }}" class="hover:text-emerald-400 transition-colors">العقود والحسابات</a>
                <span>/</span>
                <a href="{{ route('admin.houses.show', $house) }}" class="hover:text-emerald-400 transition-colors font-mono">{{ $house->contract_number ?? ('#'.$house->id) }}</a>
                <span>/</span>
                <span class="text-emerald-400">تعديل العقد</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-emerald-500/20 to-teal-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white tracking-tight flex items-center gap-3">
                        تعديل بيانات العقد
                        <span class="px-3 py-1 text-xs rounded-full bg-slate-800 text-emerald-400 border border-emerald-500/20 font-mono">
                            {{ $house->contract_number ?? ('ID: ' . $house->id) }}
                        </span>
                    </h1>
                    <p class="text-xs text-slate-400 mt-0.5">
                        جميع البيانات المدخلة هنا تنعكس تلقائياً في تقرير العقد (PDF و Word) وصفحة التفاصيل.
                    </p>
                </div>
            </div>
        </div>

        {{-- Quick action links --}}
        <div class="flex items-center flex-wrap gap-2.5">
            <a href="{{ route('admin.houses.show', $house) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-300 bg-slate-800/80 hover:bg-slate-700 border border-slate-700/60 transition-all">
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                معاينة العقد
            </a>
            <a href="{{ route('admin.houses.contract.pdf', $house) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold text-rose-300 bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/30 transition-all">
                <svg class="w-4 h-4 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                تحميل PDF
            </a>
            <a href="{{ route('admin.houses.contract.word', $house) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold text-blue-300 bg-blue-500/10 hover:bg-blue-500/20 border border-blue-500/30 transition-all">
                <svg class="w-4 h-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                تحميل Word
            </a>
        </div>
    </div>

    {{-- Main Form --}}
    <form method="post" action="{{ route('admin.houses.update', $house) }}" class="space-y-8">
        @csrf
        @method('PUT')

        {{-- SECTION 1: بيانات الاتفاقية والعقد الأساسية --}}
        <div class="bg-slate-900/90 rounded-2xl border border-slate-800/80 p-6 md:p-7 shadow-xl space-y-6 relative overflow-hidden">
            <div class="absolute top-0 right-0 left-0 h-1 bg-gradient-to-r from-emerald-500 via-teal-500 to-emerald-600"></div>

            <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-white">بيانات الاتفاقية والعقد الأساسية</h2>
                        <p class="text-xs text-slate-400">معلومات رقم العقد وتواريخ الفحص وقيمة الأتعاب المالية</p>
                    </div>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-emerald-500/10 text-emerald-300 border border-emerald-500/20">
                    القسم 1
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                {{-- رقم العقد --}}
                <div>
                    <label for="contract_number" class="block text-xs font-semibold text-slate-300 mb-1.5 flex items-center justify-between">
                        <span>رقم العقد / رقم الطلب</span>
                        <span class="text-[10px] text-emerald-400 font-normal">ترويسة العقد والطلب</span>
                    </label>
                    <input id="contract_number" name="contract_number" type="text"
                           value="{{ old('contract_number', $house->contract_number) }}"
                           placeholder="مثال: GIS-2026-0001"
                           class="w-full font-mono rounded-xl border border-slate-700/80 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 focus:outline-none transition-all">
                    @error('contract_number')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- تاريخ العقد --}}
                <div>
                    <label for="contract_date" class="block text-xs font-semibold text-slate-300 mb-1.5 flex items-center justify-between">
                        <span>تاريخ تحرير العقد</span>
                        <span class="text-[10px] text-emerald-400 font-normal">صدر الاتفاقية</span>
                    </label>
                    <input id="contract_date" name="contract_date" type="date"
                           value="{{ old('contract_date', $house->contract_date?->format('Y-m-d')) }}"
                           class="w-full rounded-xl border border-slate-700/80 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 focus:outline-none transition-all">
                    @error('contract_date')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- تاريخ الفحص --}}
                <div>
                    <label for="inspection_date" class="block text-xs font-semibold text-slate-300 mb-1.5 flex items-center justify-between">
                        <span>تاريخ الفحص والمعاينة</span>
                        <span class="text-[10px] text-emerald-400 font-normal">البند الأول</span>
                    </label>
                    <input id="inspection_date" name="inspection_date" type="date"
                           value="{{ old('inspection_date', $house->inspection_date?->format('Y-m-d')) }}"
                           class="w-full rounded-xl border border-slate-700/80 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 focus:outline-none transition-all">
                    @error('inspection_date')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- حالة العقد --}}
                <div>
                    <label for="contract_status" class="block text-xs font-semibold text-slate-300 mb-1.5">
                        حالة العقد
                    </label>
                    <select id="contract_status" name="contract_status"
                            class="w-full rounded-xl border border-slate-700/80 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 focus:outline-none transition-all">
                        <option value="draft" {{ old('contract_status', $house->contract_status) === 'draft' ? 'selected' : '' }}>مسودة</option>
                        <option value="active" {{ old('contract_status', $house->contract_status) === 'active' ? 'selected' : '' }}>ساري / نشط</option>
                        <option value="completed" {{ old('contract_status', $house->contract_status) === 'completed' ? 'selected' : '' }}>مكتمل ومسلّم</option>
                        <option value="cancelled" {{ old('contract_status', $house->contract_status) === 'cancelled' ? 'selected' : '' }}>ملغي</option>
                    </select>
                    @error('contract_status')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- قيمة العقد (د.ب) --}}
                <div>
                    <label for="price" class="block text-xs font-semibold text-amber-300 mb-1.5 flex items-center justify-between">
                        <span>إجمالي أتعاب الفحص (د.ب)</span>
                        <span class="text-[10px] text-amber-400 font-normal">البند الرابع (بالأرقام والكلمات)</span>
                    </label>
                    <div class="relative">
                        <input id="price" name="price" type="number" step="0.01" min="0"
                               value="{{ old('price', $house->price) }}"
                               placeholder="مثال: 180"
                               class="w-full rounded-xl border border-amber-500/40 bg-slate-950/70 px-3.5 py-2.5 text-sm font-bold text-amber-300 focus:border-amber-400 focus:ring-1 focus:ring-amber-400 focus:outline-none transition-all pl-12">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-amber-500/70">د.ب</span>
                    </div>
                    @error('price')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- طريقة الدفع --}}
                <div>
                    <label for="payment_method" class="block text-xs font-semibold text-slate-300 mb-1.5">
                        طريقة الدفع المتفق عليها
                    </label>
                    <select id="payment_method" name="payment_method"
                            class="w-full rounded-xl border border-slate-700/80 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 focus:outline-none transition-all">
                        <option value="cash" {{ old('payment_method', $house->payment_method) === 'cash' ? 'selected' : '' }}>كاش (نقدي)</option>
                        <option value="benefit" {{ old('payment_method', $house->payment_method) === 'benefit' ? 'selected' : '' }}>Benefit / بنفت</option>
                        <option value="bank_transfer" {{ old('payment_method', $house->payment_method) === 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                        <option value="other" {{ old('payment_method', $house->payment_method) === 'other' ? 'selected' : '' }}>أخرى</option>
                    </select>
                    @error('payment_method')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- رقم المرجع --}}
                <div class="md:col-span-3">
                    <label for="reference_code" class="block text-xs font-semibold text-slate-300 mb-1.5">
                        رقم المرجع / الكود الداخلي (اختياري)
                    </label>
                    <input id="reference_code" name="reference_code" type="text"
                           value="{{ old('reference_code', $house->reference_code) }}"
                           placeholder="مثال: REF-2026-X"
                           class="w-full rounded-xl border border-slate-700/80 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 focus:outline-none transition-all">
                    @error('reference_code')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- SECTION 2: بيانات الطرف الثاني — العميل / المشتري --}}
        <div class="bg-slate-900/90 rounded-2xl border border-slate-800/80 p-6 md:p-7 shadow-xl space-y-6 relative overflow-hidden">
            <div class="absolute top-0 right-0 left-0 h-1 bg-gradient-to-r from-blue-500 via-indigo-500 to-blue-600"></div>

            <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-white">بيانات الطرف الثاني (العميل / المشتري)</h2>
                        <p class="text-xs text-slate-400">تظهر هذه البيانات في ديباجة العقد كطرف ثانٍ وفي جدول التوقيعات</p>
                    </div>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-blue-500/10 text-blue-300 border border-blue-500/20">
                    القسم 2
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                {{-- اسم العميل / المشتري --}}
                <div class="md:col-span-2">
                    <label for="buyer_name" class="block text-xs font-semibold text-slate-300 mb-1.5 flex items-center justify-between">
                        <span>اسم العميل / المشتري <span class="text-rose-400">*</span></span>
                        <span class="text-[10px] text-blue-400 font-normal">الطرف الثاني + التوقيع</span>
                    </label>
                    <input id="buyer_name" name="buyer_name" type="text" required
                           value="{{ old('buyer_name', $house->buyer_name ?? $house->client_name) }}"
                           placeholder="مثال: أحمد عبد الله الهاجري"
                           class="w-full rounded-xl border border-slate-700/80 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition-all">
                    @error('buyer_name')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- الجنسية --}}
                <div>
                    <label for="nationality" class="block text-xs font-semibold text-slate-300 mb-1.5 flex items-center justify-between">
                        <span>الجنسية</span>
                        <span class="text-[10px] text-blue-400 font-normal">ديباجة العقد</span>
                    </label>
                    <input id="nationality" name="nationality" type="text"
                           value="{{ old('nationality', $house->nationality) }}"
                           placeholder="مثال: بحريني / سعودي / كويتي"
                           class="w-full rounded-xl border border-slate-700/80 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition-all">
                    @error('nationality')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- رقم الهوية / الجواز --}}
                <div>
                    <label for="id_number" class="block text-xs font-semibold text-slate-300 mb-1.5 flex items-center justify-between">
                        <span>رقم الهوية / الجواز / السجل</span>
                        <span class="text-[10px] text-blue-400 font-normal">ديباجة العقد</span>
                    </label>
                    <input id="id_number" name="id_number" type="text"
                           value="{{ old('id_number', $house->id_number) }}"
                           placeholder="مثال: 850123456"
                           class="w-full rounded-xl border border-slate-700/80 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition-all">
                    @error('id_number')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- رقم الهاتف --}}
                <div>
                    <label for="phone" class="block text-xs font-semibold text-slate-300 mb-1.5 flex items-center justify-between">
                        <span>رقم الهاتف / الواتساب</span>
                        <span class="text-[10px] text-blue-400 font-normal">ديباجة العقد</span>
                    </label>
                    <input id="phone" name="phone" type="text"
                           value="{{ old('phone', $house->phone) }}"
                           placeholder="مثال: 36698895 أو +973 36698895"
                           class="w-full rounded-xl border border-slate-700/80 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition-all">
                    @error('phone')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- البريد الإلكتروني --}}
                <div>
                    <label for="client_email" class="block text-xs font-semibold text-slate-300 mb-1.5 flex items-center justify-between">
                        <span>البريد الإلكتروني للعميل</span>
                        <span class="text-[10px] text-blue-400 font-normal">ديباجة العقد والمراسلات</span>
                    </label>
                    <input id="client_email" name="client_email" type="email"
                           value="{{ old('client_email', $house->client_email) }}"
                           placeholder="client@example.com"
                           class="w-full rounded-xl border border-slate-700/80 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition-all">
                    @error('client_email')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- جهة التواصل / اسم إضافي --}}
                <div class="md:col-span-3">
                    <label for="client_name" class="block text-xs font-semibold text-slate-300 mb-1.5">
                        اسم جهة التواصل أو وسيط إضافي (اختياري)
                    </label>
                    <input id="client_name" name="client_name" type="text"
                           value="{{ old('client_name', $house->client_name) }}"
                           placeholder="مثال: شركة عقارات أو وسيط العميل"
                           class="w-full rounded-xl border border-slate-700/80 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition-all">
                    @error('client_name')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- SECTION 3: بيانات العقار وموقع المعاينة --}}
        <div class="bg-slate-900/90 rounded-2xl border border-slate-800/80 p-6 md:p-7 shadow-xl space-y-6 relative overflow-hidden">
            <div class="absolute top-0 right-0 left-0 h-1 bg-gradient-to-r from-teal-500 via-emerald-500 to-cyan-500"></div>

            <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-teal-500/10 border border-teal-500/20 flex items-center justify-center text-teal-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-white">بيانات العقار وموقع المعاينة</h2>
                        <p class="text-xs text-slate-400">تظهر في التمهيد والبند الأول (المنطقة، الفيلا، الطريق، المجمع، والمقدمة والوثيقة)</p>
                    </div>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-teal-500/10 text-teal-300 border border-teal-500/20">
                    القسم 3
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                {{-- عنوان العقار --}}
                <div class="md:col-span-2">
                    <label for="title" class="block text-xs font-semibold text-slate-300 mb-1.5 flex items-center justify-between">
                        <span>عنوان / مسمى العقار <span class="text-rose-400">*</span></span>
                        <span class="text-[10px] text-teal-400 font-normal">عنوان التقرير واللوحة</span>
                    </label>
                    <input id="title" name="title" type="text" required
                           value="{{ old('title', $house->title) }}"
                           placeholder="مثال: فيلا سكنية فاخرة - ديار المحرق"
                           class="w-full rounded-xl border border-slate-700/80 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white focus:border-teal-500 focus:ring-1 focus:ring-teal-500 focus:outline-none transition-all">
                    @error('title')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- المنطقة --}}
                <div>
                    <label for="area" class="block text-xs font-semibold text-slate-300 mb-1.5 flex items-center justify-between">
                        <span>المنطقة</span>
                        <span class="text-[10px] text-teal-400 font-normal">التمهيد + البند الأول</span>
                    </label>
                    <input id="area" name="area" type="text"
                           value="{{ old('area', $house->area) }}"
                           placeholder="مثال: ديار المحرق"
                           class="w-full rounded-xl border border-slate-700/80 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white focus:border-teal-500 focus:ring-1 focus:ring-teal-500 focus:outline-none transition-all">
                    @error('area')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- رقم الفيلا --}}
                <div>
                    <label for="villa_number" class="block text-xs font-semibold text-slate-300 mb-1.5 flex items-center justify-between">
                        <span>رقم الفيلا / المبنى</span>
                        <span class="text-[10px] text-teal-400 font-normal">البند الأول</span>
                    </label>
                    <input id="villa_number" name="villa_number" type="text"
                           value="{{ old('villa_number', $house->villa_number) }}"
                           placeholder="مثال: 142"
                           class="w-full rounded-xl border border-slate-700/80 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white focus:border-teal-500 focus:ring-1 focus:ring-teal-500 focus:outline-none transition-all">
                    @error('villa_number')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- الطريق --}}
                <div>
                    <label for="road" class="block text-xs font-semibold text-slate-300 mb-1.5 flex items-center justify-between">
                        <span>الطريق</span>
                        <span class="text-[10px] text-teal-400 font-normal">البند الأول</span>
                    </label>
                    <input id="road" name="road" type="text"
                           value="{{ old('road', $house->road) }}"
                           placeholder="مثال: 2314"
                           class="w-full rounded-xl border border-slate-700/80 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white focus:border-teal-500 focus:ring-1 focus:ring-teal-500 focus:outline-none transition-all">
                    @error('road')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- المجمع --}}
                <div>
                    <label for="compound" class="block text-xs font-semibold text-slate-300 mb-1.5 flex items-center justify-between">
                        <span>المجمع</span>
                        <span class="text-[10px] text-teal-400 font-normal">البند الأول</span>
                    </label>
                    <input id="compound" name="compound" type="text"
                           value="{{ old('compound', $house->compound) }}"
                           placeholder="مثال: 263"
                           class="w-full rounded-xl border border-slate-700/80 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white focus:border-teal-500 focus:ring-1 focus:ring-teal-500 focus:outline-none transition-all">
                    @error('compound')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- رقم المقدمة --}}
                <div>
                    <label for="intro_number" class="block text-xs font-semibold text-slate-300 mb-1.5 flex items-center justify-between">
                        <span>رقم المقدمة</span>
                        <span class="text-[10px] text-teal-400 font-normal">التمهيد (المقيد بموجب)</span>
                    </label>
                    <input id="intro_number" name="intro_number" type="text"
                           value="{{ old('intro_number', $house->intro_number) }}"
                           placeholder="مثال: 1234/2026"
                           class="w-full rounded-xl border border-slate-700/80 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white focus:border-teal-500 focus:ring-1 focus:ring-teal-500 focus:outline-none transition-all">
                    @error('intro_number')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- رقم الوثيقة --}}
                <div>
                    <label for="document_number" class="block text-xs font-semibold text-slate-300 mb-1.5 flex items-center justify-between">
                        <span>رقم الوثيقة</span>
                        <span class="text-[10px] text-teal-400 font-normal">التمهيد (والوثيقة رقم)</span>
                    </label>
                    <input id="document_number" name="document_number" type="text"
                           value="{{ old('document_number', $house->document_number) }}"
                           placeholder="مثال: 98765"
                           class="w-full rounded-xl border border-slate-700/80 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white focus:border-teal-500 focus:ring-1 focus:ring-teal-500 focus:outline-none transition-all">
                    @error('document_number')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- نوع العقار --}}
                <div>
                    <label for="property_type" class="block text-xs font-semibold text-slate-300 mb-1.5">
                        نوع العقار
                    </label>
                    <input id="property_type" name="property_type" type="text"
                           value="{{ old('property_type', $house->property_type ?? 'فيلا') }}"
                           placeholder="مثال: فيلا / شقة / مبنى سكني"
                           class="w-full rounded-xl border border-slate-700/80 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white focus:border-teal-500 focus:ring-1 focus:ring-teal-500 focus:outline-none transition-all">
                    @error('property_type')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- حالة المبنى --}}
                <div>
                    <label for="building_status" class="block text-xs font-semibold text-slate-300 mb-1.5">
                        حالة المبنى
                    </label>
                    <input id="building_status" name="building_status" type="text"
                           value="{{ old('building_status', $house->building_status ?? 'مشطب (غير مؤثث)') }}"
                           placeholder="مثال: مشطب (غير مؤثث) / جاهز"
                           class="w-full rounded-xl border border-slate-700/80 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white focus:border-teal-500 focus:ring-1 focus:ring-teal-500 focus:outline-none transition-all">
                    @error('building_status')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- النشاط --}}
                <div>
                    <label for="activity" class="block text-xs font-semibold text-slate-300 mb-1.5">
                        النشاط بحسب رخصة البناء
                    </label>
                    <input id="activity" name="activity" type="text"
                           value="{{ old('activity', $house->activity ?? 'سكني') }}"
                           placeholder="مثال: سكني / تجاري / استثماري"
                           class="w-full rounded-xl border border-slate-700/80 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white focus:border-teal-500 focus:ring-1 focus:ring-teal-500 focus:outline-none transition-all">
                    @error('activity')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- العنوان الكامل التفصيلي --}}
                <div class="md:col-span-3">
                    <label for="address" class="block text-xs font-semibold text-slate-300 mb-1.5">
                        العنوان التفصيلي للعقار
                    </label>
                    <input id="address" name="address" type="text"
                           value="{{ old('address', $house->address) }}"
                           placeholder="مثال: فيلا 142، طريق 2314، مجمع 263، ديار المحرق، مملكة البحرين"
                           class="w-full rounded-xl border border-slate-700/80 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white focus:border-teal-500 focus:ring-1 focus:ring-teal-500 focus:outline-none transition-all">
                    @error('address')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- SECTION 4: المواصفات الفنية والمساحات --}}
        <div class="bg-slate-900/90 rounded-2xl border border-slate-800/80 p-6 md:p-7 shadow-xl space-y-6 relative overflow-hidden">
            <div class="absolute top-0 right-0 left-0 h-1 bg-gradient-to-r from-amber-500 via-orange-500 to-amber-600"></div>

            <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-white">المواصفات الفنية والهندسية للعقار</h2>
                        <p class="text-xs text-slate-400">تظهر في تقرير الفحص ومواصفات العقار الفنية</p>
                    </div>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-amber-500/10 text-amber-300 border border-amber-500/20">
                    القسم 4
                </span>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                {{-- مساحة الأرض --}}
                <div>
                    <label for="land_area" class="block text-xs font-semibold text-slate-300 mb-1.5">مساحة الأرض (م²)</label>
                    <input id="land_area" name="land_area" type="text" value="{{ old('land_area', $house->land_area) }}"
                           placeholder="مثال: 320 م²"
                           class="w-full rounded-xl border border-slate-700/80 bg-slate-950/70 px-3 py-2 text-sm text-white focus:border-amber-500 focus:outline-none transition-all">
                </div>

                {{-- مساحة البناء --}}
                <div>
                    <label for="building_area" class="block text-xs font-semibold text-slate-300 mb-1.5">مساحة البناء (م²)</label>
                    <input id="building_area" name="building_area" type="text" value="{{ old('building_area', $house->building_area) }}"
                           placeholder="مثال: 450 م²"
                           class="w-full rounded-xl border border-slate-700/80 bg-slate-950/70 px-3 py-2 text-sm text-white focus:border-amber-500 focus:outline-none transition-all">
                </div>

                {{-- عمر العقار --}}
                <div>
                    <label for="property_age" class="block text-xs font-semibold text-slate-300 mb-1.5">عمر العقار</label>
                    <input id="property_age" name="property_age" type="text" value="{{ old('property_age', $house->property_age) }}"
                           placeholder="مثال: جديد / سنتين"
                           class="w-full rounded-xl border border-slate-700/80 bg-slate-950/70 px-3 py-2 text-sm text-white focus:border-amber-500 focus:outline-none transition-all">
                </div>

                {{-- عدد الطوابق --}}
                <div>
                    <label for="floors_count" class="block text-xs font-semibold text-slate-300 mb-1.5">عدد الطوابق</label>
                    <input id="floors_count" name="floors_count" type="text" value="{{ old('floors_count', $house->floors_count) }}"
                           placeholder="مثال: طابقين + ملحق"
                           class="w-full rounded-xl border border-slate-700/80 bg-slate-950/70 px-3 py-2 text-sm text-white focus:border-amber-500 focus:outline-none transition-all">
                </div>

                {{-- عدد الغرف --}}
                <div>
                    <label for="rooms_count" class="block text-xs font-semibold text-slate-300 mb-1.5">عدد الغرف</label>
                    <input id="rooms_count" name="rooms_count" type="text" value="{{ old('rooms_count', $house->rooms_count) }}"
                           placeholder="مثال: 4 غرف نوم"
                           class="w-full rounded-xl border border-slate-700/80 bg-slate-950/70 px-3 py-2 text-sm text-white focus:border-amber-500 focus:outline-none transition-all">
                </div>

                {{-- عدد دورات المياه --}}
                <div>
                    <label for="bathrooms_count" class="block text-xs font-semibold text-slate-300 mb-1.5">دورات المياه</label>
                    <input id="bathrooms_count" name="bathrooms_count" type="text" value="{{ old('bathrooms_count', $house->bathrooms_count) }}"
                           placeholder="مثال: 5"
                           class="w-full rounded-xl border border-slate-700/80 bg-slate-950/70 px-3 py-2 text-sm text-white focus:border-amber-500 focus:outline-none transition-all">
                </div>

                {{-- عدد الصالات --}}
                <div>
                    <label for="halls_count" class="block text-xs font-semibold text-slate-300 mb-1.5">عدد الصالات</label>
                    <input id="halls_count" name="halls_count" type="text" value="{{ old('halls_count', $house->halls_count) }}"
                           placeholder="مثال: صالتين"
                           class="w-full rounded-xl border border-slate-700/80 bg-slate-950/70 px-3 py-2 text-sm text-white focus:border-amber-500 focus:outline-none transition-all">
                </div>

                {{-- مواقف السيارات --}}
                <div>
                    <label for="parking_count" class="block text-xs font-semibold text-slate-300 mb-1.5">مواقف السيارات</label>
                    <input id="parking_count" name="parking_count" type="text" value="{{ old('parking_count', $house->parking_count) }}"
                           placeholder="مثال: سيارتين"
                           class="w-full rounded-xl border border-slate-700/80 bg-slate-950/70 px-3 py-2 text-sm text-white focus:border-amber-500 focus:outline-none transition-all">
                </div>

                {{-- المطابخ --}}
                <div>
                    <label for="kitchens_count" class="block text-xs font-semibold text-slate-300 mb-1.5">عدد المطابخ</label>
                    <input id="kitchens_count" name="kitchens_count" type="text" value="{{ old('kitchens_count', $house->kitchens_count) }}"
                           placeholder="مثال: مطبخين داخلي وخارجي"
                           class="w-full rounded-xl border border-slate-700/80 bg-slate-950/70 px-3 py-2 text-sm text-white focus:border-amber-500 focus:outline-none transition-all">
                </div>

                {{-- اسم المطور --}}
                <div>
                    <label for="developer_name" class="block text-xs font-semibold text-slate-300 mb-1.5">اسم المطور العقاري</label>
                    <input id="developer_name" name="developer_name" type="text" value="{{ old('developer_name', $house->developer_name) }}"
                           placeholder="مثال: ديار المحرق"
                           class="w-full rounded-xl border border-slate-700/80 bg-slate-950/70 px-3 py-2 text-sm text-white focus:border-amber-500 focus:outline-none transition-all">
                </div>

                {{-- المشرف الهندسي --}}
                <div>
                    <label for="engineering_supervisor" class="block text-xs font-semibold text-slate-300 mb-1.5">المشرف الهندسي</label>
                    <input id="engineering_supervisor" name="engineering_supervisor" type="text" value="{{ old('engineering_supervisor', $house->engineering_supervisor) }}"
                           placeholder="مثال: مكتب استشارات هندسية"
                           class="w-full rounded-xl border border-slate-700/80 bg-slate-950/70 px-3 py-2 text-sm text-white focus:border-amber-500 focus:outline-none transition-all">
                </div>

                {{-- المقاول الرئيسي --}}
                <div>
                    <label for="main_contractor" class="block text-xs font-semibold text-slate-300 mb-1.5">المقاول الرئيسي</label>
                    <input id="main_contractor" name="main_contractor" type="text" value="{{ old('main_contractor', $house->main_contractor) }}"
                           placeholder="مثال: شركة المقاولات"
                           class="w-full rounded-xl border border-slate-700/80 bg-slate-950/70 px-3 py-2 text-sm text-white focus:border-amber-500 focus:outline-none transition-all">
                </div>
            </div>
        </div>

        {{-- SECTION 5: الملاحظات والشروط الإضافية --}}
        <div class="bg-slate-900/90 rounded-2xl border border-slate-800/80 p-6 md:p-7 shadow-xl space-y-6 relative overflow-hidden">
            <div class="absolute top-0 right-0 left-0 h-1 bg-gradient-to-r from-purple-500 via-pink-500 to-purple-600"></div>

            <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-white">الملاحظات والشروط الإضافية</h2>
                        <p class="text-xs text-slate-400">ملاحظات خاصة بالعقد أو شروط إضافية بين الطرفين</p>
                    </div>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-purple-500/10 text-purple-300 border border-purple-500/20">
                    القسم 5
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="contract_notes" class="block text-xs font-semibold text-slate-300 mb-1.5">
                        ملاحظات العقد والشروط الإضافية
                    </label>
                    <textarea id="contract_notes" name="contract_notes" rows="4"
                              placeholder="أي بنود أو ملاحظات إضافية تم الاتفاق عليها مع العميل..."
                              class="w-full rounded-xl border border-slate-700/80 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white focus:border-purple-500 focus:ring-1 focus:ring-purple-500 focus:outline-none transition-all leading-relaxed">{{ old('contract_notes', $house->contract_notes) }}</textarea>
                    @error('contract_notes')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="notes" class="block text-xs font-semibold text-slate-300 mb-1.5">
                        ملاحظات عامة عن العقار أو الزيارة
                    </label>
                    <textarea id="notes" name="notes" rows="4"
                              placeholder="ملاحظات داخلية لفريق الفحص أو معلومات تفصيلية عن العقار..."
                              class="w-full rounded-xl border border-slate-700/80 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white focus:border-purple-500 focus:ring-1 focus:ring-purple-500 focus:outline-none transition-all leading-relaxed">{{ old('notes', $house->notes) }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Action Bar --}}
        <div class="sticky bottom-4 z-20 bg-slate-900/95 backdrop-blur-md rounded-2xl border border-slate-700/70 p-4 shadow-2xl flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-2 text-xs text-slate-400">
                <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>تأكد من صحة رقم العقد وأسماء الأطراف قبل الحفظ لضمان سلامة المستندات القانونية.</span>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.houses.show', $house) }}"
                   class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-medium text-xs transition-colors border border-slate-700/60">
                    إلغاء
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-8 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-slate-950 font-bold text-xs shadow-lg shadow-emerald-500/25 transition-all transform hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                    حفظ وتحديث بيانات العقد
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
