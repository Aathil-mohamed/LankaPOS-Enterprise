<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model {
    protected $fillable = ['expense_category_id', 'warehouse_id', 'user_id', 'amount', 'reference_no', 'description', 'attachment', 'expense_date'];
    public function category() { return $this->belongsTo(ExpenseCategory::class, 'expense_category_id'); }
}
