<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\User;
use App\Models\Warehouse;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    private function admin(): User
    {
        return User::where('email', 'admin@pos.com')->first();
    }

    private function cashier(): User
    {
        return User::where('email', 'cashier@pos.com')->first();
    }

    public function test_admin_can_view_all_pages(): void
    {
        foreach (['dashboard', 'products.index', 'stores.index', 'pos.index'] as $route) {
            $this->actingAs($this->admin())->get(route($route))->assertOk();
        }
    }

    public function test_cashier_is_restricted_to_pos(): void
    {
        $this->actingAs($this->cashier())->get(route('pos.index'))->assertOk();
        $this->actingAs($this->cashier())->get(route('products.index'))->assertForbidden();
        $this->actingAs($this->cashier())->get(route('stores.index'))->assertForbidden();
    }

    public function test_checkout_uses_server_side_prices_and_deducts_stock(): void
    {
        $product = Product::where('sku', 'FOOD-NEST-002')->first();
        $warehouse = Warehouse::first();

        $response = $this->actingAs($this->cashier())->postJson(route('pos.store'), [
            'warehouse_id' => $warehouse->id,
            'payment_method' => 'cash',
            'paid_amount' => 100,
            'items' => [
                // Tampered client price/tax must be ignored
                ['product_id' => $product->id, 'quantity' => 2, 'unit_price' => 0.01, 'tax' => 0],
            ],
        ]);

        $response->assertOk()->assertJson(['success' => true]);

        $order = SalesOrder::with('payments')->find($response->json('order_id'));
        $this->assertEquals(23.98, (float) $order->subtotal);
        $this->assertEquals(1.20, (float) $order->tax_amount);
        $this->assertEquals(25.18, (float) $order->grand_total);
        $this->assertEquals('paid', $order->payment_status);
        // Payment records the amount applied to the sale, not the cash tendered
        $this->assertEquals(25.18, (float) $order->payments->sum('amount'));

        $this->assertEquals(118, Inventory::where('product_id', $product->id)->value('quantity'));

        $this->actingAs($this->cashier())->get($response->json('receipt_url'))->assertOk();
    }

    public function test_checkout_fails_and_rolls_back_on_insufficient_stock(): void
    {
        $product = Product::where('sku', 'ELEC-SONY-001')->first();

        $this->actingAs($this->cashier())->postJson(route('pos.store'), [
            'warehouse_id' => Warehouse::first()->id,
            'payment_method' => 'cash',
            'paid_amount' => 0,
            'items' => [['product_id' => $product->id, 'quantity' => 999, 'unit_price' => 249.99]],
        ])->assertStatus(422);

        $this->assertEquals(0, SalesOrder::count());
        $this->assertEquals(50, Inventory::where('product_id', $product->id)->value('quantity'));
    }

    public function test_admin_can_create_product_and_store(): void
    {
        $warehouse = Warehouse::first();

        $this->actingAs($this->admin())->post(route('products.store'), [
            'name' => 'Test Item',
            'sku' => 'test-001',
            'barcode' => '8909876543210', // duplicate of seeded product
            'category_id' => 1,
            'unit_id' => 1,
            'cost_price' => 1,
            'selling_price' => 2,
        ])->assertSessionHasErrors('barcode');

        $this->actingAs($this->admin())->post(route('products.store'), [
            'name' => 'Test Item',
            'sku' => 'test-001',
            'category_id' => 1,
            'unit_id' => 1,
            'cost_price' => 1,
            'selling_price' => 2,
            'initial_stock' => 10,
            'warehouse_id' => $warehouse->id,
        ])->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('products', ['sku' => 'TEST-001']);

        $this->actingAs($this->admin())->post(route('stores.store'), [
            'name' => 'Branch 2',
            'code' => 'wh-002',
        ])->assertRedirect(route('stores.index'));

        $this->assertDatabaseHas('warehouses', ['code' => 'WH-002']);
    }

    public function test_seeders_can_be_run_twice(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->assertEquals(2, Product::count());
    }
}
