<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model {
    use SoftDeletes;
    protected $fillable = [
        'name', 'sku', 'barcode', 'category_id', 'brand_id', 'unit_id',
        'cost_price', 'selling_price', 'tax_rate', 'alert_quantity', 'image',
        'description', 'has_variants', 'is_active'
    ];

    public function category() { return $this->belongsTo(Category::class); }
    public function brand() { return $this->belongsTo(Brand::class); }
    public function unit() { return $this->belongsTo(Unit::class); }
    public function inventories() { return $this->hasMany(Inventory::class); }
}
