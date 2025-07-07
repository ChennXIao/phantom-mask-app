<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Pharmacy;
use App\Models\Mask;
use App\Models\PharmacyHour;

class PharmacyControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_success_list_pharmacies_with_time_and_day_filter()
    {
        $pharmacy = Pharmacy::factory()->create();
        PharmacyHour::factory()->create([
            'pharmacy_id' => $pharmacy->id,
            'weekday' => '1',
            'open_time' => '09:00:00',
            'close_time' => '17:00:00',
        ]);

        $response = $this->getJson('/api/pharmacies?day=Mon&time=14:00:00');

        $response->assertStatus(200)
                 ->assertJson(['metadata' => ['status' => '0000']])
                 ->assertJsonCount(1, 'data');
    }

    public function test_success_list_masks_for_a_pharmacy_and_sort_by_price()
    {
        $pharmacy = Pharmacy::factory()->create();
        Mask::factory()->create(['pharmacy_id' => $pharmacy->id, 'price' => 100]);
        Mask::factory()->create(['pharmacy_id' => $pharmacy->id, 'price' => 50]);

        $response = $this->getJson("/api/pharmacies/{$pharmacy->id}/masks?sort=price&order=desc");

        $response->assertStatus(200)
                 ->assertJson(['metadata' => ['status' => '0000']])
                 ->assertJsonPath('data.0.price', "100.00")
                 ->assertJsonPath('data.1.price', "50.00");
    }

    public function test_success_list_masks_for_a_pharmacy_and_sort_by_name()
    {
        $pharmacy = Pharmacy::factory()->create();
        Mask::factory()->create(['pharmacy_id' => $pharmacy->id, 'name' => "Test mask 1"]);
        Mask::factory()->create(['pharmacy_id' => $pharmacy->id, 'name' => "Mask 2"]);

        $response = $this->getJson("/api/pharmacies/{$pharmacy->id}/masks?sort=name");

        $response->assertStatus(200)
                 ->assertJson(['metadata' => ['status' => '0000']])
                 ->assertJsonPath('data.0.name', "Mask 2")
                 ->assertJsonPath('data.1.name', "Test mask 1");
    }

    public function test_success_filter_pharmacies_by_mask_count()
    {
        $pharmacy = Pharmacy::factory()->create();
        Mask::factory()->count(3)->create(['pharmacy_id' => $pharmacy->id, 'price' => 10]);

        $response = $this->getJson('/api/pharmacies/filter-by-mask-count?min_price=5&max_price=15&operator=>&count=2');


        $response->assertStatus(200)
                 ->assertJson(['metadata' => ['status' => '0000']])
                 ->assertJsonCount(1, 'data');
    }

    public function test_success_batch_upsert_masks_for_a_pharmacy()
    {
        $pharmacy = Pharmacy::factory()->create();
        $maskData = [
            ['name' => 'N95 Mask', 'price' => 150, 'stock_quantity' => 100],
            ['name' => 'Surgical Mask', 'price' => 50, 'stock_quantity' => 200],
        ];

        $response = $this->postJson("/api/pharmacies/{$pharmacy->id}/masks/batch", ['masks' => $maskData]);

        $response->assertStatus(200)
                 ->assertJson(['metadata' => ['status' => '0000', 'message' => 'Success.']]);

        $this->assertDatabaseHas('masks', ['pharmacy_id' => $pharmacy->id, 'name' => 'N95 Mask', 'stock_quantity' => 100]);
        $this->assertDatabaseHas('masks', ['pharmacy_id' => $pharmacy->id, 'name' => 'Surgical Mask', 'stock_quantity' => 200]);
    }

    public function test_success_update_mask_stock()
    {
        $pharmacy = Pharmacy::factory()->create();
        $mask = Mask::factory()->create(['pharmacy_id' => $pharmacy->id, 'stock_quantity' => 100]);

        $response = $this->patchJson("/api/pharmacies/{$pharmacy->id}/masks/{$mask->id}", ['stock_delta' => -10]);

        $response->assertStatus(200)
                 ->assertJson(['metadata' => ['status' => '0000', 'message' => 'Success.']]);

        $this->assertEquals(90, $mask->fresh()->stock_quantity);
    }

    public function test_fail_update_mask_stock_and_return_404()
    {
       $pharmacy = Pharmacy::factory()->create();
        $otherPharmacy = Pharmacy::factory()->create();
        $mask = Mask::factory()->create(['pharmacy_id' => $otherPharmacy->id, 'stock_quantity' => 10]);

        $response = $this->patchJson("/api/pharmacies/{$pharmacy->id}/masks/{$mask->id}", ['stock_delta' => -11]);

        $response->assertStatus(404)
                 ->assertJson(['metadata' => ['status' => '4041', 'message' => 'Mask id ' . $mask->id . ' not found in the specified pharmacy']]);
    }

    public function test_returns_error_if_stock_becomes_negative()
    {
        $pharmacy = Pharmacy::factory()->create();
        $mask = Mask::factory()->create(['pharmacy_id' => $pharmacy->id, 'stock_quantity' => 5]);

        $response = $this->patchJson("/api/pharmacies/{$pharmacy->id}/masks/{$mask->id}", ['stock_delta' => -10]);

        $response->assertStatus(422)
                 ->assertJson(['metadata' => ['status' => '4221', 'message' => 'Insufficient stock for the requested mask.']]);
    }

    public function test_success_search_pharmacies_and_masks()
    {
        Pharmacy::factory()->create(['name' => 'Test Pharmacy A']);
        Mask::factory()->create(['name' => 'Test Mask B']);

        $response = $this->getJson('/api/search?q=Test');

        $response->assertStatus(200)
                 ->assertJson(['metadata' => ['status' => '0000']])
                 ->assertJsonPath('data.pharmacies.0.name', 'Test Pharmacy A')
                 ->assertJsonPath('data.masks.0.name', 'Test Mask B');
    }
}
