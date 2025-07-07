<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Customer;
use App\Models\Pharmacy;
use App\Models\Mask;
use App\Models\Order;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_success_get_top_spenders()
    {
        $customer1 = Customer::factory()->create();
        $customer2 = Customer::factory()->create();

        Order::factory()->create(['customer_id' => $customer1->id, 'total_amount' => 100, 'purchased_at' => now()]);
        Order::factory()->create(['customer_id' => $customer2->id, 'total_amount' => 200, 'purchased_at' => now()]);
        Order::factory()->create(['customer_id' => $customer1->id, 'total_amount' => 150, 'purchased_at' => now()]);

        $response = $this->getJson('/api/orders/top-spenders?start_date=2023-01-01&end_date=2025-12-31&limit=2');

        $response->assertStatus(200)
                 ->assertJson(['metadata' => ['status' => '0000']])
                 ->assertJsonCount(2, 'data')
                 ->assertJsonPath('data.0.total_spent', "250.00")
                 ->assertJsonPath('data.1.total_spent', "200.00");
    }

    public function test_success_create_order()
    {
        $customer = Customer::factory()->create();
        $pharmacy1 = Pharmacy::factory()->create();
        $pharmacy2 = Pharmacy::factory()->create();
        $mask1 = Mask::factory()->create(['pharmacy_id' => $pharmacy1->id, 'stock_quantity' => 50, 'price' => 10]);
        $mask2 = Mask::factory()->create(['pharmacy_id' => $pharmacy2->id, 'stock_quantity' => 30, 'price' => 20]);

        $orderData = [
            'customer_id' => $customer->id,
            'items' => [
                ['mask_id' => $mask1->id, 'pharmacy_id' => $pharmacy1->id, 'quantity' => 5],
                ['mask_id' => $mask2->id, 'pharmacy_id' => $pharmacy2->id, 'quantity' => 3],
            ],
        ];

        $response = $this->postJson('/api/orders', $orderData);

        $response->assertStatus(200)
                 ->assertJson(['metadata' => ['status' => '0000', 'message' => 'Success.']]);

        $this->assertEquals(45, $mask1->fresh()->stock_quantity);
        $this->assertEquals(27, $mask2->fresh()->stock_quantity);

        $this->assertDatabaseHas('orders', [
            'customer_id' => $customer->id,
            'total_amount' => ($mask1->price * 5) + ($mask2->price * 3),
        ]);

        $this->assertDatabaseHas('order_details', [
            'mask_id' => $mask1->id,
            'quantity' => 5,
            'unit_price' => $mask1->price,
        ]);
    }

    public function test_insufficient_stock_should_return_422()
    {
        $customer = Customer::factory()->create();
        $pharmacy = Pharmacy::factory()->create();
        $mask = Mask::factory()->create(['pharmacy_id' => $pharmacy->id, 'stock_quantity' => 5, 'price' => 10]);

        $orderData = [
            'customer_id' => $customer->id,
            'items' => [
                ['mask_id' => $mask->id, 'pharmacy_id' => $pharmacy->id, 'quantity' => 8],
            ],
        ];

        $response = $this->postJson('/api/orders', $orderData);

        $response->assertStatus(422)
                 ->assertJson(['metadata' => ['status' => '4221', 'message' => 'Insufficient stock for mask ID '. $mask->id]]);
    }
}
