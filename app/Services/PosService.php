<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\StockMovement;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PosService
{
    /**
     * Complete a checkout transaction atomically.
     */
    public function processCheckout(array $data, int $userId): SalesOrder
    {
        return DB::transaction(function () use ($data, $userId) {
            $warehouseId = $data['warehouse_id'];
            $items = $data['items']; // array of ['product_id', 'quantity', 'unit_price', 'discount', 'tax']
            $customer = $data['customer_id'] ?? null;
            $paymentMethod = $data['payment_method'] ?? 'cash';
            $paidAmount = (float) ($data['paid_amount'] ?? 0.00);

            $subtotal = 0;
            $totalTax = 0;
            $totalDiscount = 0;

            foreach ($items as $item) {
                $lineSubtotal = $item['quantity'] * $item['unit_price'];
                $subtotal += $lineSubtotal;
                $totalTax += $item['tax'] ?? 0;
                $totalDiscount += $item['discount'] ?? 0;
            }

            $grandTotal = ($subtotal + $totalTax) - $totalDiscount;
            $dueAmount = max(0, $grandTotal - $paidAmount);
            $paymentStatus = $paidAmount >= $grandTotal ? 'paid' : ($paidAmount > 0 ? 'partial' : 'unpaid');

            // 1. Create Sales Order
            $order = SalesOrder::create([
                'invoice_number' => 'INV-' . strtoupper(Str::random(8)),
                'warehouse_id' => $warehouseId,
                'customer_id' => $customer,
                'user_id' => $userId,
                'subtotal' => $subtotal,
                'discount_amount' => $totalDiscount,
                'tax_amount' => $totalTax,
                'grand_total' => $grandTotal,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'status' => 'completed',
                'payment_status' => $paymentStatus,
                'notes' => $data['notes'] ?? 'POS Terminal Order',
            ]);

            // 2. Add Order Items & Deduct Stock
            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                OrderItem::create([
                    'sales_order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['quantity'] * $item['unit_price'],
                    'tax' => $item['tax'] ?? 0,
                    'discount' => $item['discount'] ?? 0,
                    'total' => ($item['quantity'] * $item['unit_price']) + ($item['tax'] ?? 0) - ($item['discount'] ?? 0),
                ]);

                // Atomic inventory check and deduction
                $inventory = Inventory::where('warehouse_id', $warehouseId)
                    ->where('product_id', $product->id)
                    ->first();

                if (!$inventory || $inventory->quantity < $item['quantity']) {
                    throw new Exception("Insufficient inventory for product: {$product->name}");
                }

                $inventory->decrement('quantity', $item['quantity']);

                // Record stock movement ledger
                StockMovement::create([
                    'warehouse_id' => $warehouseId,
                    'product_id' => $product->id,
                    'type' => 'out',
                    'quantity' => $item['quantity'],
                    'reference_type' => SalesOrder::class,
                    'reference_id' => $order->id,
                    'user_id' => $userId,
                    'notes' => "POS Order Invoice #{$order->invoice_number}",
                ]);
            }

            // 3. Record Payment transaction
            if ($paidAmount > 0) {
                Payment::create([
                    'sales_order_id' => $order->id,
                    'customer_id' => $customer,
                    'payment_method' => $paymentMethod,
                    'amount' => $paidAmount,
                    'reference_no' => 'PAY-' . time(),
                    'user_id' => $userId,
                ]);
            }

            activity()
                ->performedOn($order)
                ->causedBy(auth()->user())
                ->log("Processed POS checkout order {$order->invoice_number}");

            return $order;
        });
    }
}
