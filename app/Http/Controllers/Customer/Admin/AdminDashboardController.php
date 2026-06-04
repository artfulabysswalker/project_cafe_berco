<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Total orders
        $totalOrders = Order::count();

        // Total revenue (only from paid orders)
        $totalRevenue = Order::where('status_pembayaran', 'Paid')->sum('total_harga');

        // Total products sold
        $totalProductsSold = OrderItem::sum('quantity');

        // Top 5 products
        $topProducts = OrderItem::with('product')
            ->selectRaw('product_id, SUM(quantity) as total_quantity')
            ->groupBy('product_id')
            ->orderBy('total_quantity', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('totalOrders', 'totalRevenue', 'totalProductsSold', 'topProducts'));
    }
}