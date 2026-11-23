<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class EnterpriseSeeder extends Seeder
{
    public function run(): void
    {
        $warehouse = Warehouse::firstOrCreate(['code' => 'WH-MAIN-001'], [
            'name' => 'Main Supermarket Warehouse',
            'code' => 'WH-MAIN-001',
            'phone' => '+1234567890',
            'email' => 'warehouse@pos.com',
            'address' => '100 Commercial Avenue, Logistics City',
            'is_active' => true,
        ]);

        $categoryFood = Category::firstOrCreate(['slug' => 'beverages-snacks'], ['name' => 'Beverages & Snacks', 'slug' => 'beverages-snacks']);
        $categoryElec = Category::firstOrCreate(['slug' => 'electronics'], ['name' => 'Electronics & Accessories', 'slug' => 'electronics']);

        $brandSony = Brand::firstOrCreate(['slug' => 'sony'], ['name' => 'Sony', 'slug' => 'sony']);
        $brandNestle = Brand::firstOrCreate(['slug' => 'nestle'], ['name' => 'Nestle', 'slug' => 'nestle']);

        $unitPcs = Unit::firstOrCreate(['short_name' => 'pcs'], ['name' => 'Pieces', 'short_name' => 'pcs']);
        $unitBox = Unit::firstOrCreate(['short_name' => 'box'], ['name' => 'Box', 'short_name' => 'box']);

        $p1 = Product::firstOrCreate(['sku' => 'ELEC-SONY-001'], [
            'name' => 'Wireless Noise Canceling Headphones',
            'sku' => 'ELEC-SONY-001',
            'barcode' => '8901234567890',
            'category_id' => $categoryElec->id,
            'brand_id' => $brandSony->id,
            'unit_id' => $unitPcs->id,
            'cost_price' => 150.00,
            'selling_price' => 249.99,
            'tax_rate' => 10.00,
            'alert_quantity' => 5,
            'is_active' => true,
        ]);

        $p2 = Product::firstOrCreate(['sku' => 'FOOD-NEST-002'], [
            'name' => 'Premium Instant Coffee 200g',
            'sku' => 'FOOD-NEST-002',
            'barcode' => '8909876543210',
            'category_id' => $categoryFood->id,
            'brand_id' => $brandNestle->id,
            'unit_id' => $unitPcs->id,
            'cost_price' => 6.50,
            'selling_price' => 11.99,
            'tax_rate' => 5.00,
            'alert_quantity' => 15,
            'is_active' => true,
        ]);

        Inventory::firstOrCreate(
            ['warehouse_id' => $warehouse->id, 'product_id' => $p1->id],
            ['quantity' => 50]
        );

        Inventory::firstOrCreate(
            ['warehouse_id' => $warehouse->id, 'product_id' => $p2->id],
            ['quantity' => 120]
        );

        Customer::firstOrCreate(['email' => 'walkin@pos.com'], [
            'name' => 'Walk-in Customer',
            'email' => 'walkin@pos.com',
            'phone' => '0000000000',
            'credit_limit' => 0.00,
        ]);

        Customer::firstOrCreate(['email' => 'contact@acme.corp'], [
            'name' => 'Acme Corporation',
            'email' => 'contact@acme.corp',
            'phone' => '+1987654321',
            'credit_limit' => 5000.00,
        ]);

        Supplier::firstOrCreate(['email' => 'sales@globalelec.com'], [
            'name' => 'Global Electronics Wholesale',
            'company_name' => 'Global Elec Inc',
            'email' => 'sales@globalelec.com',
            'phone' => '+1122334455',
        ]);
    }
}
