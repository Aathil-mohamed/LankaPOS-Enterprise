<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model {
    protected $fillable = ['warehouse_id', 'product_id', 'product_variant_id', 'quantity', 'batch_no', 'expiry_date'];
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function product() { return $this->belongsTo(Product::class); }
    public function variant() { return $this->belongsTo(ProductVariant::class, 'product_variant_id'); }
}
