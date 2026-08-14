<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model {
    protected $fillable = ['sales_order_id', 'purchase_order_id', 'customer_id', 'supplier_id', 'payment_method', 'amount', 'reference_no', 'user_id'];
    public function salesOrder() { return $this->belongsTo(SalesOrder::class); }
    public function user() { return $this->belongsTo(User::class); }
}
