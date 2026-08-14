<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model {
    protected $fillable = [
        'invoice_number', 'warehouse_id', 'customer_id', 'user_id',
        'subtotal', 'discount_amount', 'tax_amount', 'grand_total',
        'paid_amount', 'due_amount', 'status', 'payment_status', 'notes'
    ];

    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function items() { return $this->hasMany(OrderItem::class); }
    public function payments() { return $this->hasMany(Payment::class); }
}
