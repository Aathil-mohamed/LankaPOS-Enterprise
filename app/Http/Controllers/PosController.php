<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Warehouse;
use App\Services\PosService;
use Exception;
use Illuminate\Http\Request;

class PosController extends Controller
{
    protected PosService $posService;

    public function __construct(PosService $posService)
    {
        $this->posService = $posService;
    }

    public function index()
    {
        $warehouses = Warehouse::where('is_active', true)->get();
        $categories = Category::where('is_active', true)->get();
        $customers = Customer::all();
        $products = Product::with(['category', 'unit', 'inventories'])
            ->where('is_active', true)
            ->get();

        return view('pos.index', compact('warehouses', 'categories', 'customers', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'customer_id' => 'nullable|exists:customers,id',
            'payment_method' => 'required|in:cash,card,digital,bank_transfer,credit',
            'paid_amount' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.tax' => 'nullable|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        try {
            $order = $this->posService->processCheckout($validated, auth()->id());
            return response()->json([
                'success' => true,
                'message' => 'Sale completed successfully!',
                'invoice_number' => $order->invoice_number,
                'order_id' => $order->id,
                'receipt_url' => route('pos.receipt', $order->id),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function printReceipt($id)
    {
        $order = \App\Models\SalesOrder::with(['customer', 'user', 'warehouse', 'items.product', 'payments'])
            ->findOrFail($id);

        return view('pos.receipt', compact('order'));
    }
}
