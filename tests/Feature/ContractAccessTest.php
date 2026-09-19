<?php

namespace Tests\Feature;

use App\Models\ContractPayment;
use App\Models\PropertyHouse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContractAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the contract show page loads correctly with payments and expenses.
     */
    public function test_contract_show_page_loads_with_financial_data(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $house = PropertyHouse::create([
            'user_id' => $admin->id,
            'title' => 'فيلا تجريبية',
            'price' => 500,
        ]);

        ContractPayment::create([
            'property_house_id' => $house->id,
            'amount' => 200,
            'payment_date' => now(),
            'payment_method' => 'cash',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.houses.show', $house));

        $response->assertOk();
        $response->assertSee('فيلا تجريبية');
        $response->assertSee('200');
    }

    /**
     * Test that a contract belonging to the system can be viewed.
     */
    public function test_admin_can_view_contract_details(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $house = PropertyHouse::create([
            'user_id' => $admin->id,
            'title' => 'فيلا اختبار',
            'contract_number' => 'GIS-2026-0001',
            'price' => 1000,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.houses.show', $house));

        $response->assertOk();
        $response->assertSee('GIS-2026-0001');
    }

    /**
     * Test that non-admin users cannot access contract pages.
     */
    public function test_non_admin_cannot_access_contract_pages(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $house = PropertyHouse::create([
            'user_id' => $user->id,
            'title' => 'Restricted',
        ]);

        $response = $this->actingAs($user)->get(route('admin.houses.show', $house));

        $response->assertForbidden();
    }
}
