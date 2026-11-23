<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $todaySales = SalesOrder::whereDate('created_at', now()->today())
            ->where('status', 'completed')
            ->sum('grand_total');

        $monthlySales = SalesOrder::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', 'completed')
            ->sum('grand_total');

        $totalExpenses = Expense::whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');

        $totalProducts = Product::count();

        $lowStockProducts = Inventory::with(['product', 'warehouse'])
            ->whereHas('product', function ($q) {
                $q->whereColumn('inventories.quantity', '<=', 'products.alert_quantity');
            })->get();

        $recentSales = SalesOrder::with(['customer', 'user', 'warehouse'])
            ->latest()
            ->take(10)
            ->get();

        $topSelling = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('products.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'todaySales',
            'monthlySales',
            'totalExpenses',
            'totalProducts',
            'lowStockProducts',
            'recentSales',
            'topSelling'
        ));
    }
}
