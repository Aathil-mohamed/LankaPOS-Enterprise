<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model {
    protected $fillable = ['po_number', 'supplier_id', 'warehouse_id', 'user_id', 'grand_total', 'paid_amount', 'due_amount', 'status', 'notes'];
    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function items() { return $this->hasMany(PurchaseItem::class); }
}
