<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContractExpense;
use App\Models\PropertyHouse;
use App\Services\ContractPdfGenerator;
use App\Services\ContractWordGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\HeaderUtils;

class PropertyHouseController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $statusFilter = $request->query('status');
        $paymentFilter = $request->query('payment');
        $monthFilter = $request->query('month');
        $yearFilter = $request->query('year');

        $query = PropertyHouse::query()
            ->withSum('payments', 'amount')
            ->withSum('expenses', 'amount')
            ->when($search, function ($q) use ($search) {
                $term = '%'.addcslashes((string) $search, '%_\\').'%';
                $q->where(function ($sub) use ($term) {
                    $sub->where('title', 'like', $term)
                        ->orWhere('client_name', 'like', $term)
                        ->orWhere('buyer_name', 'like', $term)
                        ->orWhere('phone', 'like', $term)
                        ->orWhere('address', 'like', $term)
                        ->orWhere('contract_number', 'like', $term)
                        ->orWhere('reference_code', 'like', $term)
                        ->orWhere('villa_number', 'like', $term)
                        ->orWhere('compound', 'like', $term)
                        ->orWhere('area', 'like', $term)
                        ->orWhere('document_number', 'like', $term);
                });
            })
            ->when($statusFilter, fn ($q) => $q->where('contract_status', $statusFilter))
            ->when($monthFilter && $yearFilter, function ($q) use ($monthFilter, $yearFilter) {
                $q->whereMonth('created_at', $monthFilter)
                    ->whereYear('created_at', $yearFilter);
            });

        if ($paymentFilter === 'paid') {
            $query->whereRaw('(SELECT COALESCE(SUM(amount), 0) FROM contract_payments WHERE contract_payments.property_house_id = property_houses.id) >= property_houses.price AND property_houses.price > 0');
        } elseif ($paymentFilter === 'partial') {
            $query->whereRaw('(SELECT COALESCE(SUM(amount), 0) FROM contract_payments WHERE contract_payments.property_house_id = property_houses.id) > 0 AND (SELECT COALESCE(SUM(amount), 0) FROM contract_payments WHERE contract_payments.property_house_id = property_houses.id) < property_houses.price');
        } elseif ($paymentFilter === 'unpaid') {
            $query->whereRaw('((SELECT COALESCE(SUM(amount), 0) FROM contract_payments WHERE contract_payments.property_house_id = property_houses.id) = 0 OR property_houses.price IS NULL OR property_houses.price = 0)');
        }

        $houses = $query->latest()->paginate(15)->withQueryString();

        // Top KPIs
        $totalContractsCount = PropertyHouse::count();
        $activeContractsCount = PropertyHouse::where('contract_status', 'active')->count();
        $completedContractsCount = PropertyHouse::where('contract_status', 'completed')->count();
        $totalContractsValue = (float) PropertyHouse::sum('price');
        $totalCollectedPayments = (float) \App\Models\ContractPayment::sum('amount');
        $totalRemainingPayments = max(0, $totalContractsValue - $totalCollectedPayments);

        return view('admin.houses.index', compact(
            'houses',
            'search',
            'statusFilter',
            'paymentFilter',
            'monthFilter',
            'yearFilter',
            'totalContractsCount',
            'activeContractsCount',
            'completedContractsCount',
            'totalContractsValue',
            'totalCollectedPayments',
            'totalRemainingPayments'
        ));
    }

    public function create(): View
    {
        return view('admin.houses.create');
    }

    private function validationRules(?PropertyHouse $house = null): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'client_name' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'reference_code' => ['nullable', 'string', 'max:120'],
            'inspection_date' => ['nullable', 'date'],
            'contract_date' => ['nullable', 'date'],
            'contract_status' => ['nullable', 'string', 'in:draft,active,completed,cancelled'],
            'notes' => ['nullable', 'string', 'max:8000'],
            'activity' => ['nullable', 'string', 'max:255'],
            'property_type' => ['nullable', 'string', 'max:255'],
            'building_status' => ['nullable', 'string', 'max:255'],
            'document_number' => ['nullable', 'string', 'max:255'],
            'intro_number' => ['nullable', 'string', 'max:255'],
            'villa_number' => ['nullable', 'string', 'max:255'],
            'road' => ['nullable', 'string', 'max:255'],
            'compound' => ['nullable', 'string', 'max:255'],
            'area' => ['nullable', 'string', 'max:255'],
            'buyer_name' => ['nullable', 'string', 'max:255'],
            'nationality' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'client_email' => ['nullable', 'email', 'max:255'],
            'contract_number' => [
                'nullable',
                'string',
                'max:120',
                Rule::unique('property_houses', 'contract_number')->ignore($house?->id),
            ],
            'payment_method' => ['nullable', 'string', 'in:cash,benefit,bank_transfer,other'],
            'id_number' => ['nullable', 'string', 'max:255'],
            'developer_name' => ['nullable', 'string', 'max:255'],
            'engineering_supervisor' => ['nullable', 'string', 'max:255'],
            'main_contractor' => ['nullable', 'string', 'max:255'],
            'property_age' => ['nullable', 'string', 'max:255'],
            'land_area' => ['nullable', 'string', 'max:255'],
            'building_area' => ['nullable', 'string', 'max:255'],
            'floors_count' => ['nullable', 'string', 'max:255'],
            'rooms_count' => ['nullable', 'string', 'max:255'],
            'bathrooms_count' => ['nullable', 'string', 'max:255'],
            'halls_count' => ['nullable', 'string', 'max:255'],
            'parking_count' => ['nullable', 'string', 'max:255'],
            'kitchens_count' => ['nullable', 'string', 'max:255'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'contract_notes' => ['nullable', 'string', 'max:8000'],
        ];
    }

    private function validationMessages(): array
    {
        return [
            'title.required' => 'حقل عنوان أو اسم العقار مطلوب.',
            'price.numeric' => 'يجب أن تكون قيمة العقد رقماً.',
            'price.min' => 'يجب ألا تقل قيمة العقد عن صفر.',
            'inspection_date.date' => 'صيغة تاريخ الفحص غير صحيحة.',
            'contract_date.date' => 'صيغة تاريخ العقد غير صحيحة.',
            'contract_status.in' => 'حالة العقد المختارة غير صالحة.',
            'payment_method.in' => 'طريقة الدفع المختارة غير صالحة.',
            'client_email.email' => 'صيغة البريد الإلكتروني غير صحيحة.',
            'contract_number.unique' => 'رقم العقد مستخدم مسبقاً.',
        ];
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->validationRules(), $this->validationMessages());

        $house = DB::transaction(function () use ($data, $request) {
            $house = new PropertyHouse($data);
            $house->user_id = $request->user()->id;
            $house->save();

            return $house;
        });

        return redirect()
            ->route('admin.houses.show', $house)
            ->with('status', 'تم إنشاء العقد بنجاح — رقم العقد: '.$house->contract_number);
    }

    public function edit(PropertyHouse $house): View
    {
        return view('admin.houses.edit', compact('house'));
    }

    public function update(Request $request, PropertyHouse $house): RedirectResponse
    {
        $data = $request->validate($this->validationRules($house), $this->validationMessages());

        $house->update($data);

        return redirect()
            ->route('admin.houses.show', $house)
            ->with('status', 'تم تحديث بيانات العقد.');
    }

    public function show(PropertyHouse $house): View
    {
        $house->load(['payments', 'expenses']);

        $reportNo = $house->contract_number ?: $house->reference_code ?: ('H-'.$house->id);
        $clientName = trim((string) ($house->buyer_name ?? $house->client_name ?? '')) ?: '---';
        $propertyAddress = collect([
            $house->villa_number ? 'فيلا '.$house->villa_number : null,
            $house->road ? 'طريق '.$house->road : null,
            $house->compound ? 'مجمع '.$house->compound : null,
            $house->area ?: null,
        ])->filter()->implode('  ') ?: ($house->address ?: $house->title);

        $expenseTypes = ContractExpense::expenseTypes();

        return view('admin.houses.show', compact(
            'house',
            'reportNo',
            'clientName',
            'propertyAddress',
            'expenseTypes',
        ));
    }

    public function destroy(PropertyHouse $house): RedirectResponse
    {
        $house->delete();

        return redirect()
            ->route('admin.houses.index')
            ->with('status', 'تم حذف العقد وجميع بياناته.');
    }

    public function downloadContractPdf(PropertyHouse $house): HttpResponse
    {
        $house->load(['payments', 'expenses']);
        $pdf = (new ContractPdfGenerator())->renderBinary($house);

        $filename = $house->downloadBasename().'.pdf';

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => HeaderUtils::makeDisposition(HeaderUtils::DISPOSITION_ATTACHMENT, $filename),
            'Content-Length' => strlen($pdf),
        ]);
    }

    public function downloadContractWord(PropertyHouse $house): HttpResponse
    {
        $house->load(['payments', 'expenses']);

        $docx = (new ContractWordGenerator())->renderBinary($house);

        $filename = $house->downloadBasename().'.docx';

        return response($docx, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Content-Disposition' => HeaderUtils::makeDisposition(HeaderUtils::DISPOSITION_ATTACHMENT, $filename),
            'Content-Length' => strlen($docx),
        ]);
    }
}
