<?php

namespace Tests\Feature;

use App\Models\PropertyHouse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContractManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create([
            'is_admin' => true,
        ]);
    }

    public function test_admin_can_view_contract_edit_page(): void
    {
        $house = PropertyHouse::create([
            'user_id'         => $this->admin->id,
            'title'           => 'فيلا اختبار ديار المحرق',
            'contract_number' => 'GIS-2026-0001',
            'contract_date'   => '2026-09-18',
            'price'           => 200,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.houses.edit', $house));

        $response->assertOk();
        $response->assertSee('GIS-2026-0001');
        $response->assertSee('فيلا اختبار ديار المحرق');
        $response->assertSee('تعديل بيانات العقد');
        $response->assertSee('بيانات الاتفاقية والعقد الأساسية');
        $response->assertSee('بيانات الطرف الثاني (العميل / المشتري)');
        $response->assertSee('المواصفات الفنية والهندسية للعقار');
    }

    public function test_admin_can_update_all_contract_fields(): void
    {
        $house = PropertyHouse::create([
            'user_id'         => $this->admin->id,
            'title'           => 'فيلا أولية',
            'contract_number' => 'GIS-2026-0001',
            'price'           => 100,
        ]);

        $updateData = [
            'title'                  => 'فيلا فاخرة - سار',
            'contract_number'        => 'GIS-2026-0099',
            'contract_date'          => '2026-09-20',
            'inspection_date'        => '2026-09-22',
            'contract_status'        => 'active',
            'payment_method'         => 'benefit',
            'price'                  => 350.00,
            'reference_code'         => 'REF-SAR-99',
            'buyer_name'             => 'محمد عبد الله الشمري',
            'nationality'            => 'بحريني',
            'id_number'              => '900123456',
            'phone'                  => '36698895',
            'client_email'           => 'mohammed@example.com',
            'client_name'            => 'وسيط عقاري سار',
            'area'                   => 'سار',
            'villa_number'           => '55',
            'road'                   => '1234',
            'compound'               => '567',
            'intro_number'           => '4321/2026',
            'document_number'        => 'DOC-998877',
            'property_type'          => 'فيلا خاصة',
            'building_status'        => 'جاهز للسكن',
            'activity'               => 'سكني استثماري',
            'address'                => 'فيلا 55، طريق 1234، مجمع 567، سار',
            'land_area'              => '400 م²',
            'building_area'          => '550 م²',
            'property_age'           => 'سنة واحدة',
            'floors_count'           => '3',
            'rooms_count'            => '5',
            'bathrooms_count'        => '6',
            'halls_count'            => '2',
            'parking_count'          => '2',
            'kitchens_count'         => '2',
            'developer_name'         => 'المطور المعتمد',
            'engineering_supervisor' => 'المكتب الهندسي للاستشارات',
            'main_contractor'        => 'شركة المقاولات الحديثة',
            'contract_notes'         => 'شروط خاصة بفحص العوازل والكهرباء',
            'notes'                  => 'ملاحظات عامة حول الفحص',
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.houses.update', $house), $updateData);

        $response->assertRedirect(route('admin.houses.show', $house));
        $response->assertSessionHas('status', 'تم تحديث بيانات العقد.');

        $house->refresh();
        $this->assertEquals('GIS-2026-0099', $house->contract_number);
        $this->assertEquals('محمد عبد الله الشمري', $house->buyer_name);
        $this->assertEquals('بحريني', $house->nationality);
        $this->assertEquals('mohammed@example.com', $house->client_email);
        $this->assertEquals('4321/2026', $house->intro_number);
        $this->assertEquals('DOC-998877', $house->document_number);
        $this->assertEquals(350.00, (float)$house->price);
        $this->assertEquals('المطور المعتمد', $house->developer_name);
    }

    public function test_admin_can_download_contract_pdf(): void
    {
        $house = PropertyHouse::create([
            'user_id'         => $this->admin->id,
            'title'           => 'فيلا عقد PDF',
            'contract_number' => 'GIS-2026-0055',
            'buyer_name'      => 'علي حسين',
            'nationality'     => 'بحريني',
            'client_email'    => 'ali@example.com',
            'price'           => 180,
            'intro_number'    => '1111/2026',
            'document_number' => '22222',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.houses.contract.pdf', $house));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertNotEmpty($response->getContent());
    }

    public function test_admin_can_download_contract_word(): void
    {
        $house = PropertyHouse::create([
            'user_id'         => $this->admin->id,
            'title'           => 'فيلا عقد Word',
            'contract_number' => 'GIS-2026-0077',
            'buyer_name'      => 'سعيد المعلم',
            'nationality'     => 'سعودي',
            'client_email'    => 'saeed@example.com',
            'phone'           => '36698895',
            'price'           => 250,
            'intro_number'    => '3333/2026',
            'document_number' => '44444',
            'area'            => 'ديار المحرق',
            'villa_number'    => '10',
            'road'            => '20',
            'compound'        => '30',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.houses.contract.word', $house));

        $response->assertOk();
        $this->assertStringContainsString('application/vnd.openxmlformats-officedocument.wordprocessingml.document', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('.docx', $response->headers->get('Content-Disposition'));
        
        $content = $response->getContent();
        // Verify it's a valid ZIP / DOCX file (PK header)
        $this->assertStringStartsWith('PK', $content);

        // Read word/document.xml from the generated docx
        $tmp = tempnam(sys_get_temp_dir(), 'test_docx_') . '.docx';
        file_put_contents($tmp, $content);
        $zip = new \ZipArchive;
        $this->assertTrue($zip->open($tmp));
        $docXml = $zip->getFromName('word/document.xml');
        $zip->close();
        @unlink($tmp);

        $this->assertNotEmpty($docXml);
        $this->assertStringContainsString('عقد فحص المبنى', $docXml);
        $this->assertStringContainsString('GIS-2026-0077', $docXml);
        $this->assertStringContainsString('سعيد المعلم', $docXml);
        $this->assertStringContainsString('سعودي', $docXml);
        $this->assertStringContainsString('saeed@example.com', $docXml);
        $this->assertStringContainsString('3333/2026', $docXml);
        $this->assertStringContainsString('44444', $docXml);
        $this->assertStringContainsString('ديار المحرق', $docXml);
        $this->assertStringContainsString('250', $docXml);
        $this->assertStringContainsString('الثاني عشر: التنويه القانوني', $docXml);
        $this->assertStringContainsString('<w:bidi', $docXml);
    }
}
