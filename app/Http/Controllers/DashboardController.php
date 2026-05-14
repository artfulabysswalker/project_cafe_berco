<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status_pembayaran', 'Belum')->count();
        $completedOrders = Order::where('status_pembayaran', 'Sudah')->count();
        $recentOrders = Order::latest('tanggal')->limit(5)->get();

        // Staff count (optional)
        $staffCount = User::whereHas('role', function ($query) {
            $query->where('role_name', 'staff'); // adjust column if your roles table uses 'role' instead of 'role_name'
        })->count();

        // Staff list
        $staffs = User::with('role')->get(); // 🔥 add this here

        // History orders
        $historyOrders = Order::where('status_pembayaran', 'Sudah')
            ->orderBy('tanggal', 'desc')
            ->get();

        // ✅ Merge everything in one return
        return view('dashboard', [
            'totalOrders'     => $totalOrders,
            'pendingOrders'   => $pendingOrders,
            'completedOrders' => $completedOrders,
            'recentOrders'    => $recentOrders,
            'historyOrders'   => $historyOrders,
            'staffCount'      => $staffCount,
            'staffs'          => $staffs, // 🔥 now $staffs exists in dashboard
        ]);
    }
    
}