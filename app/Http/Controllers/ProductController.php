<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Unit;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $selectedWarehouseId = $request->query('warehouse_id');

        $query = Product::with(['category', 'brand', 'unit', 'inventories.warehouse']);

        if ($selectedWarehouseId) {
            $query->whereHas('inventories', function ($q) use ($selectedWarehouseId) {
                $q->where('warehouse_id', $selectedWarehouseId);
            });
        }

        $products = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::all();
        $brands = Brand::all();
        $units = Unit::all();
        $warehouses = Warehouse::where('is_active', true)->get();

        return view('products.index', compact('products', 'categories', 'brands', 'units', 'warehouses', 'selectedWarehouseId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products,sku',
            'barcode' => 'nullable|string|max:100',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'unit_id' => 'required|exists:units,id',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0',
            'alert_quantity' => 'nullable|integer|min:0',
            'initial_stock' => 'nullable|integer|min:0',
            'warehouse_id' => 'nullable|exists:warehouses,id',
        ]);

        $product = Product::create([
            'name' => $validated['name'],
            'sku' => strtoupper($validated['sku']),
            'barcode' => $validated['barcode'] ?? null,
            'category_id' => $validated['category_id'],
            'brand_id' => $validated['brand_id'] ?? null,
            'unit_id' => $validated['unit_id'],
            'cost_price' => $validated['cost_price'],
            'selling_price' => $validated['selling_price'],
            'tax_rate' => $validated['tax_rate'] ?? 0,
            'alert_quantity' => $validated['alert_quantity'] ?? 5,
            'is_active' => true,
        ]);

        if (!empty($validated['initial_stock']) && !empty($validated['warehouse_id'])) {
            Inventory::create([
                'warehouse_id' => $validated['warehouse_id'],
                'product_id' => $product->id,
                'quantity' => $validated['initial_stock'],
            ]);
        }

        return redirect()->route('products.index')->with('status', 'Product created successfully!');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('status', 'Product deleted successfully!');
    }

    public function updateStock(Request $request, Product $product)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity' => 'required|integer|min:0',
        ]);

        Inventory::updateOrCreate(
            ['product_id' => $product->id, 'warehouse_id' => $validated['warehouse_id']],
            ['quantity' => $validated['quantity']]
        );

        return redirect()->back()->with('status', 'Store branch stock updated successfully!');
    }
}
