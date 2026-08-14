<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model {
    protected $fillable = ['purchase_order_id', 'product_id', 'product_variant_id', 'quantity', 'cost_price', 'total', 'batch_no', 'expiry_date'];
    public function purchaseOrder() { return $this->belongsTo(PurchaseOrder::class); }
    public function product() { return $this->belongsTo(Product::class); }
}
