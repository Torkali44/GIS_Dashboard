<?php

namespace Tests\Feature;

use App\Models\PropertyHouse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_contract_word_download_returns_valid_docx_with_content_length(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $house = PropertyHouse::create([
            'user_id' => $admin->id,
            'title' => 'Word download test',
            'inspection_date' => '2026-08-19',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.houses.contract.word', $house));

        $response->assertOk()
            ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document')
            ->assertHeader('X-Content-Type-Options', 'nosniff');

        $binary = $response->getContent();
        $this->assertNotEmpty($binary);
        $this->assertSame((string) strlen($binary), $response->headers->get('Content-Length'));

        // Verify it's a valid ZIP (DOCX is a ZIP archive)
        $zip = new \ZipArchive;
        $tempFile = tempnam(sys_get_temp_dir(), 'word-http-test-');
        file_put_contents($tempFile, $binary);
        try {
            $this->assertTrue($zip->open($tempFile) === true);
            $documentXml = $zip->getFromName('word/document.xml');
            $this->assertIsString($documentXml);
        } finally {
            $zip->close();
            @unlink($tempFile);
        }
    }

    public function test_contract_pdf_download_returns_valid_pdf(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $house = PropertyHouse::create([
            'user_id' => $admin->id,
            'title' => 'PDF download test',
            'price' => 350,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.houses.contract.pdf', $house));

        $response->assertOk()
            ->assertHeader('Content-Type', 'application/pdf')
            ->assertHeader('X-Content-Type-Options', 'nosniff');

        $binary = $response->getContent();
        $this->assertNotEmpty($binary);
        $this->assertStringStartsWith('%PDF-', $binary);
        $this->assertSame((string) strlen($binary), $response->headers->get('Content-Length'));
    }

    public function test_contract_word_is_valid_zip_with_rtl_support(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $house = PropertyHouse::create([
            'user_id' => $admin->id,
            'title' => 'RTL test contract',
            'buyer_name' => 'عميل اختبار',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.houses.contract.word', $house));
        $response->assertOk();

        $binary = $response->getContent();
        $tempFile = tempnam(sys_get_temp_dir(), 'word-rtl-test-');
        file_put_contents($tempFile, $binary);

        $zip = new \ZipArchive;
        try {
            $this->assertTrue($zip->open($tempFile) === true);
            $this->assertNotFalse($zip->locateName('word/document.xml'));
            $this->assertNotFalse($zip->locateName('[Content_Types].xml'));
            $this->assertNotFalse($zip->locateName('_rels/.rels'));

            $documentXml = $zip->getFromName('word/document.xml');
            $this->assertIsString($documentXml);
            $this->assertStringContainsString('<w:bidi/>', $documentXml);
        } finally {
            $zip->close();
            @unlink($tempFile);
        }
    }
}
