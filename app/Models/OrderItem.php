<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model {
    protected $fillable = ['sales_order_id', 'product_id', 'product_variant_id', 'quantity', 'unit_price', 'subtotal', 'tax', 'discount', 'total'];
    public function salesOrder() { return $this->belongsTo(SalesOrder::class); }
    public function product() { return $this->belongsTo(Product::class); }
}
