<?php

namespace App\Services;

use App\Models\PropertyHouse;
use PhpOffice\PhpWord\Element\Footer;
use PhpOffice\PhpWord\Element\Header;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\Style\Language;

/**
 * Generates the House Inspection CONTRACT as a proper .docx file using PHPWord.
 * Mirrors the exact 6-page structure, headers, footers, clauses, clean black typography,
 * and stamps of ContractPdfGenerator and the reference document.
 */
class ContractWordGenerator
{
    private string $stampPath;
    private string $logoPath;

    public function __construct()
    {
        $this->stampPath = public_path('images/GIS SIGN.png');
        $this->logoPath  = public_path('images/company-logo.png');
    }

    public function renderBinary(PropertyHouse $house): string
    {
        // ─── Build dynamic fields (matching ContractPdfGenerator) ──────────────
        $contractNo   = $house->contract_number ?: $house->reference_code ?: ('H-' . $house->id);
        $contractDate = $house->contract_date
            ? $house->contract_date->format('d-m-Y')
            : now()->format('d-m-Y');
        $inspectionDate = $house->inspection_date
            ? $house->inspection_date->format('d-m-Y')
            : $contractDate;
        $inspectionDay = $this->arabicDayName(
            $house->inspection_date ?? $house->contract_date ?? now()
        );
        $clientName  = trim((string)($house->buyer_name ?? $house->client_name ?? '')) ?: '---';
        $nationality = trim((string)($house->nationality ?? '')) ?: '---';
        $idNumber    = trim((string)($house->id_number ?? '')) ?: '---';
        $clientEmail = trim((string)($house->client_email ?? '')) ?: '---';
        $clientPhone = trim((string)($house->phone ?? '')) ?: '---';
        $areaName    = trim((string)($house->area ?? '')) ?: '---';
        $villaNo     = trim((string)($house->villa_number ?? '')) ?: '---';
        $road        = trim((string)($house->road ?? '')) ?: '---';
        $compound    = trim((string)($house->compound ?? '')) ?: '---';
        $introNo     = trim((string)($house->intro_number ?? '')) ?: '0000/0000';
        $docNo       = trim((string)($house->document_number ?? '')) ?: '00000';
        $locationFull = $areaName;
        $price       = $house->price ? number_format($house->price, 0) : '---';
        $priceWords  = $this->numberToArabicWords((float)($house->price ?? 0));

        // ─── PHPWord Setup ─────────────────────────────────────────────────────
        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(11.5);

        $lang = new Language('ar-SA', null, 'ar-SA');
        $phpWord->getSettings()->setThemeFontLang($lang);

        $phpWord->addParagraphStyle('Normal', [
            'alignment' => Jc::RIGHT,
            'bidi'      => true,
            'spaceAfter' => 60,
        ]);
        $phpWord->setDefaultParagraphStyle([
            'alignment' => Jc::RIGHT,
            'bidi'      => true,
            'spaceAfter' => 60,
        ]);

        // ─── Section (A4 Portrait, ~20mm margins) ─────────────────────────────
        $section = $phpWord->addSection([
            'marginTop'    => 1134,
            'marginBottom' => 1134,
            'marginLeft'   => 1134,
            'marginRight'  => 1134,
        ]);

        // ─── Paragraph Styles ─────────────────────────────────────────────────
        $pRight   = ['alignment' => Jc::RIGHT,  'bidi' => true, 'spaceAfter' => 70, 'lineHeight' => 1.25];
        $pCenter  = ['alignment' => Jc::CENTER, 'bidi' => true, 'spaceAfter' => 70, 'lineHeight' => 1.25];
        $pHeading = ['alignment' => Jc::RIGHT,  'bidi' => true, 'spaceBefore' => 100, 'spaceAfter' => 60, 'lineHeight' => 1.25];
        $pItem    = ['alignment' => Jc::RIGHT,  'bidi' => true, 'spaceAfter' => 40, 'lineHeight' => 1.22];
        $ltrLeft  = ['alignment' => Jc::LEFT,   'bidi' => false, 'spaceAfter' => 20];
        $rtlRight = ['alignment' => Jc::RIGHT,  'bidi' => true, 'spaceAfter' => 20];

        // ─── Font Styles (Clean Black Typography) ─────────────────────────────
        $fNormal        = ['name' => 'Arial', 'size' => 11.5, 'rtl' => true];
        $fBold          = ['name' => 'Arial', 'size' => 11.5, 'bold' => true, 'rtl' => true];
        $fTitle         = ['name' => 'Arial', 'size' => 15.5, 'bold' => true, 'rtl' => true, 'underline' => 'single'];
        $fHeading       = ['name' => 'Arial', 'size' => 12.5, 'bold' => true, 'rtl' => true, 'underline' => 'single'];
        $fBoldUnderline = ['name' => 'Arial', 'size' => 11.5, 'bold' => true, 'rtl' => true, 'underline' => 'single'];
        $fCenterBold    = ['name' => 'Arial', 'size' => 12,   'bold' => true, 'color' => 'C00000', 'rtl' => true];
        $fFooterEn      = ['name' => 'Arial', 'size' => 8,    'rtl' => false, 'color' => 'C00000'];
        $fFooterAr      = ['name' => 'Arial', 'size' => 8,    'rtl' => true,  'color' => 'C00000'];
        $fFooterCr      = ['name' => 'Arial', 'size' => 7.5,  'rtl' => false, 'color' => '555555'];
        $fSmall         = ['name' => 'Arial', 'size' => 8,    'rtl' => false, 'color' => 'C00000'];

        // ─── Table Styles (9638 twips = printable width of A4 portrait) ────────
        $phpWord->addTableStyle('NoBorderTable', [
            'width'       => 9638, 'unit' => TblWidth::TWIP, 'layout' => 'fixed',
            'borderColor' => 'FFFFFF', 'borderSize' => 0, 'cellMargin' => 40, 'alignment' => Jc::CENTER,
        ]);
        $phpWord->addTableStyle('FooterTable', [
            'width'       => 9638, 'unit' => TblWidth::TWIP, 'layout' => 'fixed',
            'borderColor' => 'FFFFFF', 'borderSize' => 0, 'cellMargin' => 20, 'alignment' => Jc::CENTER,
        ]);

        // ─── HEADER (On Every Page) ───────────────────────────────────────────
        // In Word table layout: Cell 1 is on the Left, Cell 2 is on the Right
        $header = $section->addHeader();
        $hTable = $header->addTable('NoBorderTable');
        $hTable->addRow();

        // Cell 1 (Left): Company Logo
        $hLeft = $hTable->addCell(4819);
        if (file_exists($this->logoPath)) {
            $hLeft->addImage($this->logoPath, [
                'width'         => 80,
                'height'        => 48,
                'alignment'     => Jc::LEFT,
                'wrappingStyle' => 'inline',
            ]);
        }

        // Cell 2 (Right): Order No & Date in #C00000 Bold
        $hRight = $hTable->addCell(4819);
        $hRight->addText("رقم الطلب {$contractNo}", ['name' => 'Arial', 'size' => 11, 'bold' => true, 'color' => 'C00000', 'rtl' => true], $rtlRight);
        $hRight->addText("التاريخ {$contractDate}", ['name' => 'Arial', 'size' => 10, 'bold' => true, 'color' => 'C00000', 'rtl' => true], $rtlRight);

        // ─── FOOTER (On Every Page) ───────────────────────────────────────────
        $footer = $section->addFooter();
        $fTable = $footer->addTable('FooterTable');
        $fTable->addRow();

        // Cell 1 (Left): Contact details & address
        $fLeft = $fTable->addCell(4819);
        $fLeft->addText('36698895 | infogisguif@gmail.com | gis.Bahrain', $fSmall, $ltrLeft);
        $fLeft->addText('Seef District - Kingdom of Bahrain', ['name' => 'Arial', 'size' => 7.5, 'color' => 'C00000', 'rtl' => false], $ltrLeft);

        // Cell 2 (Right): Stamp image right above company text
        $fRight = $fTable->addCell(4819);
        if (file_exists($this->stampPath)) {
            $fRight->addImage($this->stampPath, [
                'width'         => 85,
                'height'        => 65,
                'alignment'     => Jc::RIGHT,
                'wrappingStyle' => 'inline',
            ]);
        }
        $fRight->addText('GIS VALUATION AND EVALUATION', $fFooterEn, ['alignment' => Jc::RIGHT, 'bidi' => false, 'spaceAfter' => 10]);
        $fRight->addText('جي إي إس للتقييم والتثمين العقاري', $fFooterAr, $rtlRight);
        $fRight->addText('C.R. 160528-1', $fFooterCr, ['alignment' => Jc::RIGHT, 'bidi' => false, 'spaceAfter' => 10]);

        // ═════════════════════════════════════════════════════════════════════
        // PAGE 1: Title, Date, Parties, Inspection Clause 1, Preamble
        // ═════════════════════════════════════════════════════════════════════
        $section->addText("رقم الطلب {$contractNo}", $fCenterBold, $pCenter);
        $section->addText('عقد فحص المبنى', $fTitle, $pCenter);
        $section->addTextBreak(1);

        $rDate = $section->addTextRun($pRight);
        $rDate->addText('تحرر هذه الاتفاقية في المنامة بمملكة البحرين بتاريخ : ', $fNormal);
        $rDate->addText($contractDate, $fBold);

        $section->addText('بين كلا من :-', $fBold, $pRight);

        // Party 1: Company
        $section->addText('اولاً :-', $fBold, $pRight);
        $rP1 = $section->addTextRun($pRight);
        $rP1->addText('جي أي إس لتجارة والمقاولات والتقييم والتثمين مسجلة لدى وزارة الصناعة والتجارة والسياحة بموجب القيد رقم ', $fNormal);
        $rP1->addText('160528-1', $fBold);
        $rP1->addText(' العنوان مبنى 915 مكتب 14 طريق 6015 مجمع 760 بريد اكتروني ', $fNormal);
        $rP1->addText('infogisguif@gmail.com', $fBold);
        $rP1->addText(' هاتف رقم ', $fNormal);
        $rP1->addText('36698895', $fBold);

        // Party 2: Client
        $section->addText('ثانياً :-', $fBold, $pRight);
        $rP2 = $section->addTextRun($pRight);
        $rP2->addText('الاسم :- ', $fBold);
        $rP2->addText($clientName, $fBold);
        $rP2->addText('    الجنسية: ', $fBold);
        $rP2->addText($nationality, $fBold);
        $rP2->addText('    رقم الهوية / الجواز: ', $fBold);
        $rP2->addText($idNumber, $fBold);

        $rP2b = $section->addTextRun($pRight);
        $rP2b->addText('بريد الكتروني: ', $fNormal);
        $rP2b->addText($clientEmail, $fNormal);
        $rP2b->addText('    هاتف : ', $fNormal);
        $rP2b->addText($clientPhone, $fBold);

        // Clause 1
        $rC1 = $section->addTextRun($pRight);
        $rC1->addText('1- اتفق الطرف الأول على ان يقوم لطرف الثاني بفحص المنزل الكائن في منطقة ', $fNormal);
        $rC1->addText($areaName, $fBold);
        $rC1->addText(' فيلا رقم ', $fNormal);
        $rC1->addText($villaNo, $fBold);
        $rC1->addText(' طريق ', $fNormal);
        $rC1->addText($road, $fBold);
        $rC1->addText(' مجمع ', $fNormal);
        $rC1->addText($compound, $fBold);
        $rC1->addText(' الموافق ', $fNormal);
        $rC1->addText($inspectionDate, $fBold);
        $rC1->addText(' يوم ', $fNormal);
        $rC1->addText($inspectionDay, $fBold);

        $section->addText('أقر الشريكين بأهليتهما للتصرف وطلبا منا إثبات الاتي :-', $fNormal, $pRight);

        // Preamble
        $section->addText('تمهيـــــد :', $fHeading, $pHeading);
        $rPrem = $section->addTextRun($pRight);
        $rPrem->addText('(لمؤسسة الفردية جي أي إس) هي مؤسسة فردية تمارس أنشطة التقييم والتثمين والفحص الشامل للمباني الجاهزة قبل الشراء، وحيث أن الطرف الثاني قد رغب في تعيين الطرف الأول كفاحص للعقار المقيد بموجب المقدمة رقم ', $fNormal);
        $rPrem->addText($introNo, $fBold);
        $rPrem->addText(' والوثيقة رقم ', $fNormal);
        $rPrem->addText($docNo, $fBold);
        $rPrem->addText(' والكائن في ', $fNormal);
        $rPrem->addText($locationFull, $fBold);
        $rPrem->addText(' .', $fNormal);

        $section->addText('لذلك فقد تم الاتفاق بين أطراف هذا العقد على البنود والشروط الآتية :-', $fNormal, $pRight);

        $section->addPageBreak();

        // ═════════════════════════════════════════════════════════════════════
        // PAGE 2: Clause 1, Clause 2 (All 14 Inspection Items)
        // ═════════════════════════════════════════════════════════════════════
        $section->addText('أولاً: يعتبر التمهيد أعلاه جزءْ لا يتجزأ من هذه الاتفاقية .', $fBold, $pRight);

        $section->addText('ثانياً: التزامات الطرف الأول:', $fHeading, $pHeading);
        $section->addText('1. يلتزم الفاحص بالفحص الفني بفحص كل من التالي:', $fBold, $pRight);

        $inspectionItems = [
            'معاينة الموقع العام .',
            'فحص ومعاينة مواقف السيارات .',
            'فحص ومعاينة واجهه الفيلا .',
            'فحص ومعاينة أنابيب التكييف و التهوية .',
            'فحص ومعاينة الاسقف والديكور (الجبس ).',
            'فحص ومعاينة الالمنيوم النوافذ والابواب و الزجاج .',
            'فحص ومعاينة الجدران و الدهانات .',
            'فحص ومعاينة الارضيات وعمال السراميك .',
            'فحص ومعاينة انظمة الحماية و السلامة .',
            'فحص ومعاينة السباكة وتسريبات المياه و الخزانات .',
            'فحص ومعاينة الكهرباء وسخانات المياه و التأريض .',
            'فحص ومعاينة وحدات الصرف الصحي .',
            'فحص ومعاينة الابواب الخشبية .',
            'فحص ومعاينة عازل المياه .',
        ];

        foreach ($inspectionItems as $i => $item) {
            $section->addText(($i + 1) . '- ' . $item, $fNormal, $pItem);
        }

        $section->addPageBreak();

        // ═════════════════════════════════════════════════════════════════════
        // PAGE 3: Obligations items 2 & 3, Clause 3, Clause 4, Clause 5 (1 to 3)
        // ═════════════════════════════════════════════════════════════════════
        $section->addText('2. تلتزم المؤسسة بتسليم التقرير الفني للعميل خلال 5 أيام عمل.', $fNormal, $pRight);
        $section->addText('3. تلتزم المؤسسة بتقديم تقرير شامل التلفيات و الاضرار الموجودة بالمبنى والتوصيات التي يجب على العميل القيام بها.', $fNormal, $pRight);

        $section->addText('ثالثاً: التزامات الطرف الثاني :', $fHeading, $pHeading);
        $section->addText('1. يلتزم العميل بتمكين المؤسسة من فحص و معاينة العقار في التاريخ والوقت المتفق عليهما.', $fNormal, $pRight);
        $section->addText('2. يلتزم العميل بكافة المصاريف المترتبة على إزالة بعض المنقولات في العقار لغرض الفحص.', $fNormal, $pRight);
        $section->addText('3. يلتزم العميل بتمكين المؤسسة من الاطلاع على شهادات ضمان الأدوات الكهربائية و الخرائط الهندسية وتقرير فحص التربة وشهادة العازل المائي ضمان اعمال الالمنيوم والزجاج إن طلبها الفاحص .', $fNormal, $pRight);

        $section->addText('رابعاً : الأتعاب المهنية :', $fHeading, $pHeading);
        $rFee = $section->addTextRun($pRight);
        $rFee->addText('اتفق الطرفان أن يكون إجمالي أتعاب المؤسسة هي مبلغ وقدره ', $fNormal);
        $rFee->addText($price, $fBold);
        $rFee->addText(' دينار بحريني (', $fNormal);
        $rFee->addText($priceWords, $fBold);
        $rFee->addText(') يتم دفع المبلغ قبل البدء في إجراءات فحص المنزل.', $fNormal);

        $section->addText('خامساً: المسؤولية :', $fHeading, $pHeading);
        $section->addText('1. في حالة اغفال الطرف الأول لأية خطأ فني في التقرير فإنه لا يلتزم بتعويض العملي عن أية أضرار قد تحدث مستقبلاً.', $fNormal, $pRight);
        $section->addText('2. يقر الطرفان بصحة أيه مراسلات أو مخاطبات تتم على العنوان المذكور بصدر هذا العقد، مالم يخطر أحد الطرفين الآخر كتابةً بتغيير عنوانه، ويعد الإشعار صحيحاً بمجرد وصوله بالبريد أو تسلمه باليد.', $fNormal, $pRight);
        $section->addText('3. يحق للطرف الثاني إلغاء عملية الفحص بعد التوقيع على هذه الاتفاقية على أن يتحمل مبلغ وقدره 30 دينار بحريني (ثلاثون دينار بحريني )', $fNormal, $pRight);

        $section->addPageBreak();

        // ═════════════════════════════════════════════════════════════════════
        // PAGE 4: Clause 5 (Items 4 to 8), Clause 6, Clause 7
        // ═════════════════════════════════════════════════════════════════════
        $respItems4to8 = [
            'اتفق الاطراف على عدم تحمل الشركة والفاحصين اي مسؤولية قانونية في حالة حدوث اي عطل او تسريبات او اي اضرار كهربائية وصحية اثناء عمليه الفحص والمعاينة في المنزل او انشاء أي نزاع معا المقاول والمطور كما لا تتحمل اي مسؤولية نتيجة الاضرار الحالية و لاحقه.',
            'اتفق الطرف الثاني بسماح الى الفاحصين بمعاينة وفحص المبنى وعدم تحمل الفاحصين اي مسؤولية اثناء عمليه الفحص .',
            'اتفق الطرف الاول على ارسال التقرير لطرف الثاني قبل اعتماد التقرير على ان يتم الرد والتعديل بمده لأتزيد عن 3 ايام من استلام نسخه التعديل .',
            'اتفق الاطراف في حالة وقوع اي ضرر اثناء عميله الفحص لا يتم تحمل الشركة والفاحصين اي مسؤولية او تعويضات ماليه .',
            'اتفق الاطراف على تسليم نسخه التقرير بالغة العربية ويتم تسليم التقرير بعد سداد قيمة مبلغ الفحص بكامل .',
        ];
        foreach ($respItems4to8 as $i => $item) {
            $section->addText(($i + 4) . '- ' . $item, $fNormal, $pRight);
        }

        $section->addText('سادساً : الاختصاص القضائي :', $fHeading, $pHeading);
        $section->addText('اتفق الأطراف على إحالة أي نزاع ينشأ عن هذا العقد ليحل عبر التحكيم بما فيه بطلانه أو فسخة أو إنهائه.', $fNormal, $pRight);

        $section->addText('سابعاً : عدد النسخ :', $fHeading, $pHeading);
        $section->addText('تحررت هذه الاتفاقية من نسختين بيد كل طرف نسخة للعمل بموجبها .', $fNormal, $pRight);

        $section->addPageBreak();

        // ═════════════════════════════════════════════════════════════════════
        // PAGE 5: Clause 8, Clause 9, Clause 10 (Item 1)
        // ═════════════════════════════════════════════════════════════════════
        $section->addText('ثامناً: حدود الفحص الفني والتقرير', $fHeading, $pHeading);
        $section->addText('-1 يقتصر دور المؤسسة على وصف الحالة الحالية للمكونات الظاهرة والظروف الخارجية للعقار فقط، ولا يشمل الفحص التشخيص العميق أو التحاليل المختبرية أو فحص المواد تحت السطح إلا إذا تم الاتفاق عليها كتابيًا بشكل منفصل.', $fNormal, $pRight);
        $section->addText('-2 لا يعتبر التقرير الفني ضمانًا لأي التزامات فنية مستقبلية، ولا يترتب عليه أي التزامات قانونية تتعلق بعيوب لم تكن مرئية أو لم تظهر وقت الفحص.', $fNormal, $pRight);
        $section->addText('-3 فور مغادرة فريق الفحص الموقع، تعتبر مسؤولية المؤسسة منتهية بالكامل، ولا تُحمّل الشركة أي مسؤولية عن تغيّرات لاحقة في حالة العقار، إلا في حال الاتفاق المسبق على خدمة متابعة إضافية مدفوعة.', $fNormal, $pRight);

        $section->addText('تاسعاً: طبيعة التقرير الفني', $fHeading, $pHeading);
        $section->addText('-1 التقرير الفني يعد مستندًا استشاريًا يعتمد على الفحص البصري والمعاينة السطحية للمكونات الظاهرة فقط، مع إجراء بعض الاختبارات الأولية دون التدخل أو الإتلاف أو التفكيك.', $fNormal, $pRight);
        $section->addText('-2 التقرير لا يُعتَبر شهادة صلاحية أو جودة، ولا يرقى إلى مستوى تقييم هندسي شامل أو شهادة إشراف هندسي أو تصميم هندسي.', $fNormal, $pRight);
        $section->addText('-3 ضمن التقرير توصيفًا فنيًا للحالة الراهنة للعقار، إلى جانب توصيات تهدف لتقليل المخاطر أو إصلاح العيوب إن وُجدت.', $fNormal, $pRight);

        $section->addText('عاشراً: السرية وحقوق الاستخدام', $fHeading, $pHeading);
        $section->addText('-1 يُعد التقرير الفني معدًا حصريًا لاستخدام الطرف الثاني، ولا يجوز نسخه أو تداوله أو مشاركته مع أي طرف ثالث، باستثناء الجهات المختصة بأعمال صيانة العقار نفسه، وذلك دون إذن كتابي مسبق من الشركة.', $fNormal, $pRight);

        $section->addPageBreak();

        // ═════════════════════════════════════════════════════════════════════
        // PAGE 6: Clause 10 (Items 2 & 3), Clause 11, Clause 12 & Signature Block
        // ═════════════════════════════════════════════════════════════════════
        $section->addText('-2 تحتفظ الشركة بحق استخدام المعلومات الفنية العامة أو الصور غير المرتبطة بهوية العقار أو العميل، لأغراض التطوير المهني والتدريب الداخلي، ما لم يتقدم الطرف الثاني بطلب كتابي رسمي بعدم الاستخدام.', $fNormal, $pRight);
        $section->addText('3- جميع الحقوق الفكرية المتعلقة بالتقرير الفني محفوظة للشركة، ويُمنع إعادة إنتاجه أو نسخه أو تعديله أو إعادة نشره كليًا أو جزئيًا بأي وسيلة كانت، دون موافقة كتابية صريحة من الشركة. يُعد أي استخدام غير مصرح به انتهاكًا لحقوق الملكية الفكرية ويُعرّض المخالف للمساءلة القانونية.', $fNormal, $pRight);

        $section->addText('حادي عشر: القوة القاهرة', $fHeading, $pHeading);
        $section->addText('1- لا تتحمل الشركة مسؤولية التأخير في تسليم التقرير أو إتمام الفحص في حال وقوع ظروف خارجة عن إرادتها مثل الكوارث الطبيعية أو الأعطال الأمنية أو عدم تعاون الطرف الثاني.', $fNormal, $pRight);

        $section->addText('الثاني عشر: التنويه القانوني', $fHeading, $pHeading);
        $section->addText('لا يجوز استخدام التقرير لأي أغراض قانونية أو قضائية (مثل الدعاوى أو النزاعات) إلا بموجب عقد منفصل يحدد مسؤوليات الشركة والتزاماتها.', $fBoldUnderline, $pRight);

        // Signature block
        $section->addTextBreak(1);
        $sigTable = $section->addTable('NoBorderTable');
        $sigTable->addRow();

        // Cell 1 (Left): Party 2 (Client)
        $sig2 = $sigTable->addCell(4819);
        $sig2->addText('الطرف الثاني', $fBold, $pCenter);
        $sig2->addText($clientName, $fBold, $pCenter);
        $sig2->addTextBreak(3);
        $sig2->addText('التوقيع', $fNormal, $pCenter);

        // Cell 2 (Right): Party 1 (Company)
        $sig1 = $sigTable->addCell(4819);
        $sig1->addText('الطرف الاول', $fBold, $pCenter);
        $sig1->addText('جي أي إس للتقييم والتثمين العقاري', $fNormal, $pCenter);
        if (file_exists($this->stampPath)) {
            $sig1->addImage($this->stampPath, [
                'width'         => 80,
                'height'        => 65,
                'alignment'     => Jc::CENTER,
                'wrappingStyle' => 'inline',
            ]);
        } else {
            $sig1->addTextBreak(2);
        }
        $sig1->addText('التوقيع والختم', $fNormal, $pCenter);

        // ─── SAVE & RETURN ────────────────────────────────────────────────────
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $tmpFile = tempnam(sys_get_temp_dir(), 'contract_') . '.docx';
        $writer->save($tmpFile);

        $binary = file_get_contents($tmpFile);
        @unlink($tmpFile);

        // Force RTL on all sections
        $binary = $this->forceSectionRtl($binary);

        return $binary;
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function arabicDayName(\DateTimeInterface $date): string
    {
        $days = ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
        return $days[(int) $date->format('w')];
    }

    private function numberToArabicWords(float $number): string
    {
        $n = (int) $number;
        $map = [
            0   => 'صفر',       1   => 'واحد',      2   => 'اثنان',
            3   => 'ثلاثة',     4   => 'أربعة',     5   => 'خمسة',
            6   => 'ستة',       7   => 'سبعة',      8   => 'ثمانية',
            9   => 'تسعة',      10  => 'عشرة',      11  => 'أحد عشر',
            12  => 'اثنا عشر', 13  => 'ثلاثة عشر', 14  => 'أربعة عشر',
            15  => 'خمسة عشر', 16  => 'ستة عشر',   17  => 'سبعة عشر',
            18  => 'ثمانية عشر', 19 => 'تسعة عشر', 20  => 'عشرون',
            30  => 'ثلاثون',   40  => 'أربعون',    50  => 'خمسون',
            60  => 'ستون',     70  => 'سبعون',     80  => 'ثمانون',
            90  => 'تسعون',    100 => 'مائة',      200 => 'مئتان',
            300 => 'ثلاثمائة', 400 => 'أربعمائة', 500 => 'خمسمائة',
            600 => 'ستمائة',   700 => 'سبعمائة',  800 => 'ثمانمائة',
            900 => 'تسعمائة',
        ];
        if (isset($map[$n])) return $map[$n] . ' دينار فقط';
        if ($n > 20 && $n < 100) {
            $tens  = (int)($n / 10) * 10;
            $units = $n % 10;
            if ($units === 0) return ($map[$tens] ?? $n) . ' دينار فقط';
            return ($map[$units] ?? '') . ' و' . ($map[$tens] ?? '') . ' دينار فقط';
        }
        if ($n >= 100 && $n < 1000) {
            $hundreds = (int)($n / 100) * 100;
            $rest     = $n % 100;
            $hw       = $map[$hundreds] ?? '';
            if ($rest === 0) return $hw . ' دينار فقط';
            $rw = str_replace(' دينار فقط', '', $this->numberToArabicWords($rest));
            return $hw . ' و' . $rw . ' دينار فقط';
        }
        return $n . ' دينار فقط';
    }

    /**
     * Patch word/document.xml inside the .docx ZIP to add <w:bidi/> to every
     * <w:sectPr> element so that Microsoft Word renders the document RTL.
     */
    private function forceSectionRtl(string $docxBinary): string
    {
        $tmpZip = tempnam(sys_get_temp_dir(), 'rtl_fix_') . '.docx';
        file_put_contents($tmpZip, $docxBinary);

        $zip = new \ZipArchive();
        if ($zip->open($tmpZip) !== true) {
            @unlink($tmpZip);
            return $docxBinary;
        }

        $xml = $zip->getFromName('word/document.xml');
        if ($xml !== false) {
            // Add <w:bidi/> to each <w:sectPr> if not already there
            $xml = preg_replace_callback(
                '/<w:sectPr\b([^>]*)>(.*?)<\/w:sectPr>/s',
                function ($m) {
                    $inner = $m[2];
                    if (strpos($inner, '<w:bidi') === false) {
                        $inner = '<w:bidi/>' . $inner;
                    }
                    return '<w:sectPr' . $m[1] . '>' . $inner . '</w:sectPr>';
                },
                $xml
            );
            $zip->addFromString('word/document.xml', $xml);
        }

        $zip->close();
        $result = file_get_contents($tmpZip);
        @unlink($tmpZip);

        return $result ?: $docxBinary;
    }
}
