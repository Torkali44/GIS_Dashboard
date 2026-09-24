<?php

namespace App\Services;

use App\Models\PropertyHouse;
use App\Support\TcpdfFonts;
use TCPDF;
use Throwable;

/**
 * Generates the House Inspection CONTRACT as a PDF document.
 * 6 balanced pages matching the reference document with clean black typography,
 * large clear 11.5pt font, zero blank spaces, and proper stamp/signature placement.
 */
class ContractPdfGenerator
{
    private string $font = 'arialbd';

    public function renderBinary(PropertyHouse $house): string
    {
        TcpdfFonts::registerPath();

        $prev = ini_get('memory_limit');
        ini_set('memory_limit', '512M');

        set_error_handler(function ($severity, $message, $file, $line) {
            if (strpos($file, 'tcpdf') !== false) {
                return true;
            }
            return false;
        });

        try {
            $contractNo = $this->eHtml($house->contract_number ?: $house->reference_code ?: ('H-' . $house->id));
            $contractDate = $house->contract_date
                ? $house->contract_date->format('d') . '-' . $house->contract_date->format('m') . '-' . $house->contract_date->format('Y')
                : now()->format('d-m-Y');

            $inspectionDate = $house->inspection_date
                ? $house->inspection_date->format('d-m-Y')
                : $contractDate;

            $inspectionDayAr = $this->arabicDayName(
                $house->inspection_date ?? $house->contract_date ?? now()
            );

            $logoPath = $this->resolveLogoPath();

            $pdf = new ContractTCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
            $pdf->contractNo   = $contractNo;
            $pdf->contractDate = $contractDate;
            $pdf->logoPath     = $logoPath;
            $pdf->reportFont   = $this->font;

            $pdf->SetCreator(config('app.name'));
            $pdf->SetAuthor('GIS VALUATION AND EVALUATION');
            $pdf->SetTitle('عقد فحص — ' . ($house->buyer_name ?? $house->client_name ?? $house->title));

            $pdf->setPrintHeader(true);
            $pdf->setPrintFooter(true);
            $pdf->SetMargins(14, 32, 14);
            $pdf->SetHeaderMargin(8);
            $pdf->SetFooterMargin(14);
            $pdf->SetAutoPageBreak(true, 36);
            $pdf->setRTL(true);
            $pdf->SetFont($this->font, '', 11);

            $pdf->AddPage();

            // ─── Build dynamic fields ───────────────────────────────────────
            $clientName   = $this->eHtml(trim((string)($house->buyer_name ?? $house->client_name ?? '')) ?: '---');
            $nationality  = $this->eHtml(trim((string)($house->nationality ?? '')) ?: '---');
            $idNumber     = $this->eHtml(trim((string)($house->id_number ?? '')) ?: '---');
            $clientEmail  = $this->eHtml(trim((string)($house->client_email ?? '')) ?: '---');
            $clientPhone  = $this->eHtml(trim((string)($house->phone ?? '')) ?: '---');

            // Location
            $areaName     = $this->eHtml(trim((string)($house->area ?? '')) ?: '---');
            $villaNo      = $this->eHtml(trim((string)($house->villa_number ?? '')) ?: '---');
            $road         = $this->eHtml(trim((string)($house->road ?? '')) ?: '---');
            $compound     = $this->eHtml(trim((string)($house->compound ?? '')) ?: '---');
            $introNo      = $this->eHtml(trim((string)($house->intro_number ?? '')) ?: '0000/0000');
            $docNo        = $this->eHtml(trim((string)($house->document_number ?? '')) ?: '00000');
            $locationFull = $areaName;

            // Price
            $price      = $house->price ? number_format($house->price, 0) : '---';
            $priceWords = $this->eHtml($this->numberToArabicWords((float)($house->price ?? 0)));

            $this->renderContract(
                $pdf,
                $contractNo,
                $contractDate,
                $inspectionDate,
                $inspectionDayAr,
                $clientName,
                $nationality,
                $idNumber,
                $clientEmail,
                $clientPhone,
                $areaName,
                $villaNo,
                $road,
                $compound,
                $introNo,
                $docNo,
                $locationFull,
                $price,
                $priceWords,
            );

            return $pdf->Output('contract-' . $house->id . '.pdf', 'S');
        } finally {
            restore_error_handler();
            ini_set('memory_limit', (string)$prev);
        }
    }

    // ─── Contract body renderer ────────────────────────────────────────────

    private function renderContract(
        TCPDF $pdf,
        string $contractNo,
        string $contractDate,
        string $inspectionDate,
        string $inspectionDayAr,
        string $clientName,
        string $nationality,
        string $idNumber,
        string $clientEmail,
        string $clientPhone,
        string $areaName,
        string $villaNo,
        string $road,
        string $compound,
        string $introNo,
        string $docNo,
        string $locationFull,
        string $price,
        string $priceWords,
    ): void {
        $pdf->setRTL(true);
        $pdf->setCellHeightRatio(1.58);

        // ═════════════════════════════════════════════════════════════════════
        // PAGE 1: Title, Date, Parties, Inspection Clause 1, Preamble
        // ═════════════════════════════════════════════════════════════════════
        $html1 = <<<HTML
<div dir="rtl" style="font-family:arialbd; font-size:11.5pt; text-align:right; line-height:1.58; color:#000000;">

<p style="text-align:center; font-size:12pt; font-weight:bold; color:#C00000; margin-bottom:4px;">رقم الطلب {$contractNo}</p>

<h2 style="text-align:center; font-size:15.5pt; font-weight:bold; text-decoration:underline; color:#000; margin-top:2px; margin-bottom:8px;">عقد فحص المبنى</h2>

<p>تحرر هذه الاتفاقية في المنامة بمملكة البحرين بتاريخ : <b>{$contractDate}</b></p>

<p style="margin-top:6px; margin-bottom:4px;"><b>بين كلا من :-</b></p>

<p style="margin-bottom:6px;"><b>اولاً :-</b><br>
جي أي إس لتجارة والمقاولات والتقييم والتثمين مسجلة لدى وزارة الصناعة والتجارة والسياحة بموجب القيد رقم
<b>160528-1</b> العنوان مبنى 915 مكتب 14 طريق 6015 مجمع 760
بريد اكتروني <b><span dir="ltr">infogisguif@gmail.com</span></b> هاتف رقم <b>36698895</b></p>

<p style="margin-bottom:6px;"><b>ثانياً :-</b><br>
الاسم :- <b>{$clientName}</b>
&nbsp;&nbsp;&nbsp;&nbsp; الجنسية: <b>{$nationality}</b>
&nbsp;&nbsp;&nbsp;&nbsp; رقم الهوية / الجواز: <b>{$idNumber}</b><br>
بريد الكتروني: <span dir="ltr">{$clientEmail}</span>
&nbsp;&nbsp;&nbsp;&nbsp; هاتف : <b>{$clientPhone}</b></p>

<p style="margin-bottom:6px;">1- اتفق الطرف الأول على ان يقوم لطرف الثاني بفحص المنزل الكائن في منطقة <b>{$areaName}</b>
فيلا رقم <b>{$villaNo}</b> طريق <b>{$road}</b> مجمع <b>{$compound}</b>
الموافق <b>{$inspectionDate}</b> يوم <b>{$inspectionDayAr}</b></p>

<p style="margin-bottom:6px;">أقر الشريكين بأهليتهما للتصرف وطلبا منا إثبات الاتي :-</p>

<h3 style="font-size:12.5pt; text-decoration:underline; margin-top:8px; margin-bottom:4px;">تمهيـــــد :</h3>
<p style="margin-bottom:6px;">(لمؤسسة الفردية جي أي إس) هي مؤسسة فردية تمارس أنشطة التقييم والتثمين والفحص الشامل للمباني الجاهزة
قبل الشراء، وحيث أن الطرف الثاني قد رغب في تعيين الطرف الأول كفاحص للعقار المقيد بموجب المقدمة رقم
<b>{$introNo}</b> والوثيقة رقم <b>{$docNo}</b> والكائن في <b>{$locationFull}</b> .</p>

<p style="margin-top:6px;">لذلك فقد تم الاتفاق بين أطراف هذا العقد على البنود والشروط الآتية :-</p>

</div>
HTML;
        $pdf->writeHTMLCell(0, 0, '', '', $html1, 0, 1, false, true, 'R', true);

        // ═════════════════════════════════════════════════════════════════════
        // PAGE 2: Clause 1, Clause 2 (All 14 Inspection Items)
        // ═════════════════════════════════════════════════════════════════════
        $pdf->AddPage();
        $html2 = <<<HTML
<div dir="rtl" style="font-family:arialbd; font-size:11.5pt; text-align:right; line-height:1.62; color:#000000;">

<p style="margin-bottom:10px;"><b>أولاً:</b> يعتبر التمهيد أعلاه جزءْ لا يتجزأ من هذه الاتفاقية .</p>

<h3 style="font-size:12.5pt; text-decoration:underline; margin-top:10px; margin-bottom:6px;">ثانياً: التزامات الطرف الأول:</h3>
<p style="margin-bottom:8px;">1. يلتزم الفاحص بالفحص الفني بفحص كل من التالي:</p>

<table cellpadding="3.5" style="font-size:11.5pt;">
  <tr><td>1- معاينة الموقع العام .</td></tr>
  <tr><td>2- فحص ومعاينة مواقف السيارات .</td></tr>
  <tr><td>3- فحص ومعاينة واجهه الفيلا .</td></tr>
  <tr><td>4- فحص ومعاينة أنابيب التكييف و التهوية .</td></tr>
  <tr><td>5- فحص ومعاينة الاسقف والديكور (الجبس ).</td></tr>
  <tr><td>6- فحص ومعاينة الالمنيوم النوافذ والابواب و الزجاج .</td></tr>
  <tr><td>7- فحص ومعاينة الجدران و الدهانات .</td></tr>
  <tr><td>8- فحص ومعاينة الارضيات وعمال السراميك .</td></tr>
  <tr><td>9- فحص ومعاينة انظمة الحماية و السلامة .</td></tr>
  <tr><td>10- فحص ومعاينة السباكة وتسريبات المياه و الخزانات .</td></tr>
  <tr><td>11- فحص ومعاينة الكهرباء وسخانات المياه و التأريض .</td></tr>
  <tr><td>12- فحص ومعاينة وحدات الصرف الصحي .</td></tr>
  <tr><td>13- فحص ومعاينة الابواب الخشبية .</td></tr>
  <tr><td>14- فحص ومعاينة عازل المياه .</td></tr>
</table>

</div>
HTML;
        $pdf->writeHTMLCell(0, 0, '', '', $html2, 0, 1, false, true, 'R', true);

        // ═════════════════════════════════════════════════════════════════════
        // PAGE 3: Obligations items 2 & 3, Clause 3, Clause 4, Clause 5 (1 to 3)
        // ═════════════════════════════════════════════════════════════════════
        $pdf->AddPage();
        $html3 = <<<HTML
<div dir="rtl" style="font-family:arialbd; font-size:11.5pt; text-align:right; line-height:1.60; color:#000000;">

<p style="margin-bottom:8px;">2. تلتزم المؤسسة بتسليم التقرير الفني للعميل خلال 5 أيام عمل.</p>
<p style="margin-bottom:12px;">3. تلتزم المؤسسة بتقديم تقرير شامل التلفيات و الاضرار الموجودة بالمبنى والتوصيات التي يجب على العميل القيام بها.</p>

<h3 style="font-size:12.5pt; text-decoration:underline; margin-top:10px; margin-bottom:6px;">ثالثاً: التزامات الطرف الثاني :</h3>
<p style="margin-bottom:6px;">1. يلتزم العميل بتمكين المؤسسة من فحص و معاينة العقار في التاريخ والوقت المتفق عليهما.</p>
<p style="margin-bottom:6px;">2. يلتزم العميل بكافة المصاريف المترتبة على إزالة بعض المنقولات في العقار لغرض الفحص.</p>
<p style="margin-bottom:12px;">3. يلتزم العميل بتمكين المؤسسة من الاطلاع على شهادات ضمان الأدوات الكهربائية و الخرائط الهندسية وتقرير فحص التربة وشهادة العازل المائي ضمان اعمال الالمنيوم والزجاج إن طلبها الفاحص .</p>

<h3 style="font-size:12.5pt; text-decoration:underline; margin-top:10px; margin-bottom:6px;">رابعاً : الأتعاب المهنية :</h3>
<p style="margin-bottom:12px;">اتفق الطرفان أن يكون إجمالي أتعاب المؤسسة هي مبلغ وقدره <b>{$price}</b> دينار بحريني (<b>{$priceWords}</b>) يتم دفع المبلغ قبل البدء في إجراءات فحص المنزل.</p>

<h3 style="font-size:12.5pt; text-decoration:underline; margin-top:10px; margin-bottom:6px;">خامساً: المسؤولية :</h3>
<p style="margin-bottom:6px;">1. في حالة اغفال الطرف الأول لأية خطأ فني في التقرير فإنه لا يلتزم بتعويض العملي عن أية أضرار قد تحدث مستقبلاً.</p>
<p style="margin-bottom:6px;">2. يقر الطرفان بصحة أيه مراسلات أو مخاطبات تتم على العنوان المذكور بصدر هذا العقد، مالم يخطر أحد الطرفين الآخر كتابةً بتغيير عنوانه، ويعد الإشعار صحيحاً بمجرد وصوله بالبريد أو تسلمه باليد.</p>
<p style="margin-bottom:6px;">3. يحق للطرف الثاني إلغاء عملية الفحص بعد التوقيع على هذه الاتفاقية على أن يتحمل مبلغ وقدره 30 دينار بحريني (ثلاثون دينار بحريني )</p>

</div>
HTML;
        $pdf->writeHTMLCell(0, 0, '', '', $html3, 0, 1, false, true, 'R', true);

        // ═════════════════════════════════════════════════════════════════════
        // PAGE 4: Clause 5 (Items 4 to 8), Clause 6, Clause 7
        // ═════════════════════════════════════════════════════════════════════
        $pdf->AddPage();
        $html4 = <<<HTML
<div dir="rtl" style="font-family:arialbd; font-size:11.5pt; text-align:right; line-height:1.60; color:#000000;">

<p style="margin-bottom:10px;">4- اتفق الاطراف على عدم تحمل الشركة والفاحصين اي مسؤولية قانونية في حالة حدوث اي عطل او تسريبات او اي اضرار كهربائية وصحية اثناء عمليه الفحص والمعاينة في المنزل او انشاء أي نزاع معا المقاول والمطور كما لا تتحمل اي مسؤولية نتيجة الاضرار الحالية و لاحقه.</p>
<p style="margin-bottom:10px;">5- اتفق الطرف الثاني بسماح الى الفاحصين بمعاينة وفحص المبنى وعدم تحمل الفاحصين اي مسؤولية اثناء عمليه الفحص .</p>
<p style="margin-bottom:10px;">6- اتفق الطرف الاول على ارسال التقرير لطرف الثاني قبل اعتماد التقرير على ان يتم الرد والتعديل بمده لأتزيد عن 3 ايام من استلام نسخه التعديل .</p>
<p style="margin-bottom:10px;">7- اتفق الاطراف في حالة وقوع اي ضرر اثناء عميله الفحص لا يتم تحمل الشركة والفاحصين اي مسؤولية او تعويضات ماليه .</p>
<p style="margin-bottom:14px;">8- اتفق الاطراف على تسليم نسخه التقرير بالغة العربية ويتم تسليم التقرير بعد سداد قيمة مبلغ الفحص بكامل .</p>

<h3 style="font-size:12.5pt; text-decoration:underline; margin-top:10px; margin-bottom:6px;">سادساً : الاختصاص القضائي :</h3>
<p style="margin-bottom:14px;">اتفق الأطراف على إحالة أي نزاع ينشأ عن هذا العقد ليحل عبر التحكيم بما فيه بطلانه أو فسخة أو إنهائه.</p>

<h3 style="font-size:12.5pt; text-decoration:underline; margin-top:10px; margin-bottom:6px;">سابعاً : عدد النسخ :</h3>
<p style="margin-bottom:6px;">تحررت هذه الاتفاقية من نسختين بيد كل طرف نسخة للعمل بموجبها .</p>

</div>
HTML;
        $pdf->writeHTMLCell(0, 0, '', '', $html4, 0, 1, false, true, 'R', true);

        // ═════════════════════════════════════════════════════════════════════
        // PAGE 5: Clause 8, Clause 9, Clause 10 (Item 1)
        // ═════════════════════════════════════════════════════════════════════
        $pdf->AddPage();
        $html5 = <<<HTML
<div dir="rtl" style="font-family:arialbd; font-size:11.5pt; text-align:right; line-height:1.60; color:#000000;">

<h3 style="font-size:12.5pt; text-decoration:underline; margin-top:4px; margin-bottom:6px;">ثامناً: حدود الفحص الفني والتقرير</h3>
<p style="margin-bottom:8px;">-1 يقتصر دور المؤسسة على وصف الحالة الحالية للمكونات الظاهرة والظروف الخارجية للعقار فقط، ولا يشمل الفحص التشخيص العميق أو التحاليل المختبرية أو فحص المواد تحت السطح إلا إذا تم الاتفاق عليها كتابيًا بشكل منفصل.</p>
<p style="margin-bottom:8px;">-2 لا يعتبر التقرير الفني ضمانًا لأي التزامات فنية مستقبلية، ولا يترتب عليه أي التزامات قانونية تتعلق بعيوب لم تكن مرئية أو لم تظهر وقت الفحص.</p>
<p style="margin-bottom:14px;">-3 فور مغادرة فريق الفحص الموقع، تعتبر مسؤولية المؤسسة منتهية بالكامل، ولا تُحمّل الشركة أي مسؤولية عن تغيّرات لاحقة في حالة العقار، إلا في حال الاتفاق المسبق على خدمة متابعة إضافية مدفوعة.</p>

<h3 style="font-size:12.5pt; text-decoration:underline; margin-top:10px; margin-bottom:6px;">تاسعاً: طبيعة التقرير الفني</h3>
<p style="margin-bottom:8px;">-1 التقرير الفني يعد مستندًا استشاريًا يعتمد على الفحص البصري والمعاينة السطحية للمكونات الظاهرة فقط، مع إجراء بعض الاختبارات الأولية دون التدخل أو الإتلاف أو التفكيك.</p>
<p style="margin-bottom:8px;">-2 التقرير لا يُعتَبر شهادة صلاحية أو جودة، ولا يرقى إلى مستوى تقييم هندسي شامل أو شهادة إشراف هندسي أو تصميم هندسي.</p>
<p style="margin-bottom:14px;">-3 ضمن التقرير توصيفًا فنيًا للحالة الراهنة للعقار، إلى جانب توصيات تهدف لتقليل المخاطر أو إصلاح العيوب إن وُجدت.</p>

<h3 style="font-size:12.5pt; text-decoration:underline; margin-top:10px; margin-bottom:6px;">عاشراً: السرية وحقوق الاستخدام</h3>
<p style="margin-bottom:6px;">-1 يُعد التقرير الفني معدًا حصريًا لاستخدام الطرف الثاني، ولا يجوز نسخه أو تداوله أو مشاركته مع أي طرف ثالث، باستثناء الجهات المختصة بأعمال صيانة العقار نفسه، وذلك دون إذن كتابي مسبق من الشركة.</p>

</div>
HTML;
        $pdf->writeHTMLCell(0, 0, '', '', $html5, 0, 1, false, true, 'R', true);

        // ═════════════════════════════════════════════════════════════════════
        // PAGE 6: Clause 10 (Items 2 & 3), Clause 11, Clause 12 & Signature Block
        // ═════════════════════════════════════════════════════════════════════
        $pdf->AddPage();
        $html6 = <<<HTML
<div dir="rtl" style="font-family:arialbd; font-size:11.5pt; text-align:right; line-height:1.60; color:#000000;">

<p style="margin-bottom:8px;">-2 تحتفظ الشركة بحق استخدام المعلومات الفنية العامة أو الصور غير المرتبطة بهوية العقار أو العميل، لأغراض التطوير المهني والتدريب الداخلي، ما لم يتقدم الطرف الثاني بطلب كتابي رسمي بعدم الاستخدام.</p>
<p style="margin-bottom:12px;">3- جميع الحقوق الفكرية المتعلقة بالتقرير الفني محفوظة للشركة، ويُمنع إعادة إنتاجه أو نسخه أو تعديله أو إعادة نشره كليًا أو جزئيًا بأي وسيلة كانت، دون موافقة كتابية صريحة من الشركة. يُعد أي استخدام غير مصرح به انتهاكًا لحقوق الملكية الفكرية ويُعرّض المخالف للمساءلة القانونية.</p>

<h3 style="font-size:12.5pt; text-decoration:underline; margin-top:8px; margin-bottom:6px;">حادي عشر: القوة القاهرة</h3>
<p style="margin-bottom:12px;">1- لا تتحمل الشركة مسؤولية التأخير في تسليم التقرير أو إتمام الفحص في حال وقوع ظروف خارجة عن إرادتها مثل الكوارث الطبيعية أو الأعطال الأمنية أو عدم تعاون الطرف الثاني.</p>

<h3 style="font-size:12.5pt; text-decoration:underline; margin-top:8px; margin-bottom:6px;">الثاني عشر: التنويه القانوني</h3>
<p style="margin-bottom:16px; text-decoration:underline;"><b>لا يجوز استخدام التقرير لأي أغراض قانونية أو قضائية (مثل الدعاوى أو النزاعات) إلا بموجب عقد منفصل يحدد مسؤوليات الشركة والتزاماتها.</b></p>

</div>
HTML;
        $pdf->writeHTMLCell(0, 0, '', '', $html6, 0, 1, false, true, 'R', true);

        // ─── Signature block on Page 6 ─────────────────────────────────────
        $pdf->Ln(4);
        $pdf->SetFont($this->font, '', 11);

        $pageW = $pdf->getPageWidth();
        $colW  = ($pageW - 28) / 2;

        $pdf->setRTL(false);

        // Party headers
        $startY = $pdf->GetY();
        $pdf->SetXY(14, $startY);
        $pdf->SetFont($this->font, 'B', 12);
        $pdf->Cell($colW, 6, 'الطرف الثاني', 0, 0, 'C');
        $pdf->Cell($colW, 6, 'الطرف الاول', 0, 1, 'C');

        // Party names in clean black
        $pdf->SetXY(14, $pdf->GetY() + 1);
        $pdf->SetFont($this->font, 'B', 11);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell($colW, 6, htmlspecialchars_decode($clientName, ENT_QUOTES), 0, 0, 'C');
        $pdf->Cell($colW, 6, 'جي أي إس للتقييم والتثمين العقاري', 0, 1, 'C');

        // Party 1 (Company): Stamp & Signature without any intersecting line
        $sigY = $pdf->GetY() + 14;
        $signPath = public_path('images/GIS SIGN.png');
        if (is_file($signPath)) {
            try {
                $stampW = 34;
                $stampH = 26;
                $stampX = 14 + $colW + (($colW - $stampW) / 2);
                $stampY = $sigY - 14;
                $pdf->Image($signPath, $stampX, $stampY, $stampW, $stampH, '', '', '', false, 300);
            } catch (Throwable $e) {}
        }

        $pdf->setRTL(true);
        $pdf->SetTextColor(0, 0, 0);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────

    private function eHtml(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

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

    private function resolveLogoPath(): ?string
    {
        $primary = public_path('images/company-logo.png');
        if (is_file($primary) && is_readable($primary)) {
            return $primary;
        }
        return null;
    }
}

/**
 * Custom TCPDF class for House Inspection Contract.
 */
class ContractTCPDF extends TCPDF
{
    public string $contractNo = '';
    public string $contractDate = '';
    public ?string $logoPath = null;
    public string $reportFont = 'arialbd';

    public function Header()
    {
        $this->setRTL(false);

        // Company Logo on the left
        if ($this->logoPath && is_readable($this->logoPath)) {
            try {
                $this->Image($this->logoPath, 14, 8, 30, 20, '', '', '', false, 300);
            } catch (Throwable $e) {}
        }

        // Contract number + date on the right in #C00000
        $this->SetFont($this->reportFont, 'B', 10);
        $this->SetTextColor(192, 0, 0);
        $this->SetXY(135, 11);
        $this->Cell(60, 5, 'رقم الطلب ' . $this->contractNo, 0, 0, 'R');
        $this->SetFont($this->reportFont, 'B', 9);
        $this->SetXY(135, 17);
        $this->Cell(60, 5, 'التاريخ ' . $this->contractDate, 0, 0, 'R');

        $this->setRTL(true);
        $this->SetTextColor(0, 0, 0);
    }

    public function Footer()
    {
        $pageW = $this->getPageWidth();
        $pageH = $this->getPageHeight();

        $this->setRTL(false);
        $maroon = [192, 0, 0];
        $rowY = $pageH - 17;
        $iconSize = 3.4;
        $iconY = $rowY + 0.55;

        $whatsappIcon = public_path('images/whatsapp_icon.png');
        $emailIcon = public_path('images/email_icon.png');
        $instagramIcon = public_path('images/instagram.png');

        $x = 14.0;
        if (is_file($whatsappIcon)) {
            try {
                $this->Image($whatsappIcon, $x, $iconY, $iconSize, $iconSize, '', '', '', false, 300);
            } catch (Throwable $e) {}
        }
        $x += $iconSize + 1.0;
        $this->SetFont($this->reportFont, '', 8);
        $this->SetTextColor($maroon[0], $maroon[1], $maroon[2]);
        $this->SetXY($x, $rowY);
        $this->Cell(17, 4.2, '36698895', 0, 0, 'L');

        $x = 36.5;
        if (is_file($emailIcon)) {
            try {
                $this->Image($emailIcon, $x, $iconY, $iconSize, $iconSize, '', '', '', false, 300);
            } catch (Throwable $e) {}
        }
        $x += $iconSize + 1.0;
        $this->SetFont($this->reportFont, '', 7.5);
        $this->SetXY($x, $rowY);
        $this->Cell(38, 4.2, 'infogisgulf@gmail.com', 0, 0, 'L');

        $x = 79.0;
        if (is_file($instagramIcon)) {
            try {
                $this->Image($instagramIcon, $x, $iconY, $iconSize, $iconSize, '', '', '', false, 300);
            } catch (Throwable $e) {}
        }
        $x += $iconSize + 1.0;
        $this->SetFont($this->reportFont, '', 8);
        $this->SetXY($x, $rowY);
        $this->Cell(22, 4.2, 'gis.Bahrain', 0, 0, 'L');

        // Address
        $this->SetFont('helvetica', '', 7);
        $this->SetTextColor($maroon[0], $maroon[1], $maroon[2]);
        $this->SetXY(14, $pageH - 11.8);
        $this->Cell(90, 3.2, 'Seef District - Kingdom of Bahrain', 0, 0, 'L');

        // Company name right
        $rightW = 86.0;
        $rightX = $pageW - 14 - $rightW;
        $this->SetFont($this->reportFont, '', 8);
        $this->SetTextColor($maroon[0], $maroon[1], $maroon[2]);
        $this->SetXY($rightX, $pageH - 18);
        $this->Cell($rightW, 3.6, 'GIS VALUATION AND EVALUATION', 0, 2, 'R');
        $this->Cell($rightW, 3.6, 'جي إي إس للتقييم والتثمين العقاري', 0, 2, 'R');
        $this->SetFont('helvetica', '', 7);
        $this->Cell($rightW, 3.0, 'C.R. 160528-1', 0, 0, 'R');

        // Official GIS stamp in footer of every page (above the company text on the right)
        $signPath = public_path('images/GIS SIGN.png');
        if (is_file($signPath)) {
            try {
                $stampW = 32.0;
                $stampH = 24.0;
                $stampX = $pageW - 14.0 - 38.0;
                $stampY = $pageH - 43.0;
                $this->Image($signPath, $stampX, $stampY, $stampW, $stampH, '', '', '', false, 300);
            } catch (Throwable $e) {}
        }

        $this->setRTL(true);
        $this->SetTextColor(0, 0, 0);
    }
}
