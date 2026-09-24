<?php

namespace Tests\Feature;

use App\Models\PropertyHouse;
use App\Models\User;
use App\Support\TcpdfFonts;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TcpdfFontsTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_font_definitions_exist(): void
    {
        $dir = resource_path('fonts');

        foreach ([
            'arial.php', 'arial.z', 'arial.ctg.z',
            'arialbd.php', 'arialbd.z', 'arialbd.ctg.z',
            'helvetica.php',
        ] as $file) {
            $this->assertFileExists($dir.DIRECTORY_SEPARATOR.$file, "Missing {$file}");
        }
    }

    public function test_tcpdf_fonts_path_points_to_resources_fonts(): void
    {
        TcpdfFonts::registerPath();

        $this->assertTrue(defined('K_PATH_FONTS'));
        $this->assertSame(
            rtrim(str_replace('\\', '/', resource_path('fonts')), '/').'/',
            K_PATH_FONTS
        );
    }

    public function test_contract_pdf_loads_arialbd_without_vendor_font_copy(): void
    {
        // Simulate a fresh deploy where vendor does not contain custom Arial defs.
        $vendorArial = base_path('vendor/tecnickcom/tcpdf/fonts/arialbd.php');
        $backup = null;
        if (is_file($vendorArial)) {
            $backup = $vendorArial.'.bak-test';
            rename($vendorArial, $backup);
        }

        try {
            $admin = User::factory()->create(['is_admin' => true]);
            $house = PropertyHouse::create([
                'user_id' => $admin->id,
                'title' => 'اختبار خط عربي',
                'buyer_name' => 'محمد أحمد',
                'nationality' => 'بحريني',
                'price' => 250,
                'area' => 'المنامة',
            ]);

            $response = $this->actingAs($admin)->get(route('admin.houses.contract.pdf', $house));

            $response->assertOk();
            $response->assertHeader('Content-Type', 'application/pdf');
            $this->assertStringStartsWith('%PDF-', $response->getContent());
            $this->assertStringNotContainsString(
                'Could not include font definition file',
                $response->getContent()
            );
        } finally {
            if ($backup !== null && is_file($backup)) {
                rename($backup, $vendorArial);
            }
        }
    }

    public function test_ensure_fonts_command_succeeds(): void
    {
        $this->artisan('tcpdf:ensure-fonts')->assertSuccessful();
    }
}
