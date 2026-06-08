<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatsController extends Controller
{
    public function index()
    {
        // get selected tab (default = today)
        $range = request('range', 'today');

        $startDate = match ($range) {
            'today' => Carbon::today(),
            'week' => Carbon::now()->startOfWeek(),
            'month' => Carbon::now()->startOfMonth(),
            'all' => Carbon::now()->subMonths(6), // ⚠️ limit "all"
            default => Carbon::today(),
        };

        $stats = $this->getStats($startDate);

        return view('admin.stats.index', [
            'stats' => $stats,
            'range' => $range,
        ]);
    }

    private function getStats($startDate)
    {
        $orderQuery = Order::where('status_order', 'completed');

        if ($startDate) {
            $orderQuery->where('tanggal', '>=', $startDate);
        }

        // SAFE queries
        $totalSales = (clone $orderQuery)->sum('total_harga');
        $totalOrders = (clone $orderQuery)->count();
        $avgOrder = $totalOrders ? $totalSales / $totalOrders : 0;

        // ✅ Best selling (with menu to avoid N+1)
        $bestSelling = OrderItem::with('menu')
            ->select('id_menu', DB::raw('SUM(quantity) as total'))
            ->whereHas('order', function ($q) use ($startDate) {
                $q->where('status_order', 'completed');
                if ($startDate) {
                    $q->where('tanggal', '>=', $startDate);
                }
            })
            ->groupBy('id_menu')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // ✅ Worst selling
        $worstSelling = OrderItem::with('menu')
            ->select('id_menu', DB::raw('SUM(quantity) as total'))
            ->whereHas('order', function ($q) use ($startDate) {
                $q->where('status_order', 'completed');
                if ($startDate) {
                    $q->where('tanggal', '>=', $startDate);
                }
            })
            ->groupBy('id_menu')
            ->orderBy('total')
            ->limit(5)
            ->get();

        // ✅ Peak hours (LIMITED)
        $peakHours = Order::select(
                DB::raw('CAST(strftime("%H", tanggal) AS INTEGER) as hour'),
                DB::raw('COUNT(*) as total')
            )
            ->where('status_order', 'completed')
            ->when($startDate, fn($q) => $q->where('tanggal', '>=', $startDate))
            ->groupBy('hour')
            ->orderByDesc('total')
            ->limit(24)
            ->get();

        // ✅ Customers per day (LIMITED - VERY IMPORTANT)
        $customersPerDay = Order::select(
                DB::raw('DATE(tanggal) as date'),
                DB::raw('COUNT(DISTINCT nama_pelanggan) as total')
            )
            ->where('status_order', 'completed')
            ->when($startDate, fn($q) => $q->where('tanggal', '>=', $startDate))
            ->groupBy('date')
            ->orderByDesc('date')
            ->limit(30) // 🚨 prevents memory crash
            ->get();

        // ✅ Payment method statistics (CASH and QRIS only)
        $paymentStats = Order::select('payment_method', DB::raw('COUNT(*) as total'))
            ->where('status_order', 'completed')
            ->when($startDate, fn($q) => $q->where('tanggal', '>=', $startDate))
            ->groupBy('payment_method')
            ->get()
            ->pluck('total', 'payment_method');

        $totalPayments = $paymentStats->sum();
        $cashPayments = $paymentStats->get('cash', 0);
        $qrisPayments = $paymentStats->get('qris', 0);

        // ✅ Daily sales data for chart (last 7 days)
        $dailySales = Order::select(
                DB::raw('DATE(tanggal) as date'),
                DB::raw('SUM(total_harga) as total')
            )
            ->where('status_order', 'completed')
            ->where('tanggal', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'total_revenue' => $totalSales,
            'total_orders' => $totalOrders,
            'completed_orders' => $totalOrders,
            'avg_order' => round($avgOrder),

            'best_selling' => $bestSelling,
            'worst_selling' => $worstSelling,

            'peak_hours' => $peakHours,
            'customers_per_day' => $customersPerDay,
            
            // Payment methods
            'cash_payments' => $cashPayments,
            'qris_payments' => $qrisPayments,
            'total_payments' => $totalPayments,
            
            // Daily sales for chart
            'daily_sales' => $dailySales,
        ];
    }
}