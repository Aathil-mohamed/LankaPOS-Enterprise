<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model {
    protected $fillable = ['warehouse_id', 'product_id', 'product_variant_id', 'type', 'quantity', 'reference_type', 'reference_id', 'user_id', 'notes'];
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function product() { return $this->belongsTo(Product::class); }
    public function user() { return $this->belongsTo(User::class); }
}
