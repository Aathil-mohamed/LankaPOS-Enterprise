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
            $items = $data['items']; // array of ['product_id', 'quantity', 'discount']
            $customer = $data['customer_id'] ?? null;
            $paymentMethod = $data['payment_method'] ?? 'cash';
            $paidAmount = (float) ($data['paid_amount'] ?? 0.00);

            // Price and tax always come from the database, never from the client
            $products = Product::whereIn('id', array_column($items, 'product_id'))
                ->where('is_active', true)
                ->get()
                ->keyBy('id');

            $lines = [];
            foreach ($items as $item) {
                $product = $products->get($item['product_id']);
                if (!$product) {
                    throw new Exception("Product #{$item['product_id']} is not available for sale.");
                }

                $lineSubtotal = round($item['quantity'] * $product->selling_price, 2);
                $lineTax = round($lineSubtotal * ($product->tax_rate / 100), 2);
                $lineDiscount = min((float) ($item['discount'] ?? 0), $lineSubtotal);

                $lines[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->selling_price,
                    'subtotal' => $lineSubtotal,
                    'tax' => $lineTax,
                    'discount' => $lineDiscount,
                    'total' => $lineSubtotal + $lineTax - $lineDiscount,
                ];
            }

            $subtotal = array_sum(array_column($lines, 'subtotal'));
            $totalTax = array_sum(array_column($lines, 'tax'));
            $totalDiscount = array_sum(array_column($lines, 'discount'));

            $grandTotal = round(($subtotal + $totalTax) - $totalDiscount, 2);
            $appliedAmount = min($paidAmount, $grandTotal);
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
                'paid_amount' => $appliedAmount,
                'due_amount' => $dueAmount,
                'status' => 'completed',
                'payment_status' => $paymentStatus,
                'notes' => $data['notes'] ?? 'POS Terminal Order',
            ]);

            // 2. Add Order Items & Deduct Stock
            foreach ($lines as $line) {
                $product = $line['product'];

                OrderItem::create([
                    'sales_order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'subtotal' => $line['subtotal'],
                    'tax' => $line['tax'],
                    'discount' => $line['discount'],
                    'total' => $line['total'],
                ]);

                // Lock the row so concurrent checkouts cannot oversell
                $inventory = Inventory::where('warehouse_id', $warehouseId)
                    ->where('product_id', $product->id)
                    ->lockForUpdate()
                    ->first();

                if (!$inventory || $inventory->quantity < $line['quantity']) {
                    throw new Exception("Insufficient inventory for product: {$product->name}");
                }

                $inventory->decrement('quantity', $line['quantity']);

                // Record stock movement ledger
                StockMovement::create([
                    'warehouse_id' => $warehouseId,
                    'product_id' => $product->id,
                    'type' => 'out',
                    'quantity' => $line['quantity'],
                    'reference_type' => SalesOrder::class,
                    'reference_id' => $order->id,
                    'user_id' => $userId,
                    'notes' => "POS Order Invoice #{$order->invoice_number}",
                ]);
            }

            // 3. Record Payment transaction
            if ($appliedAmount > 0) {
                Payment::create([
                    'sales_order_id' => $order->id,
                    'customer_id' => $customer,
                    'payment_method' => $paymentMethod,
                    'amount' => $appliedAmount,
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
