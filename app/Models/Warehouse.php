<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model {
    protected $fillable = ['name', 'code', 'phone', 'email', 'address', 'is_active'];
}
