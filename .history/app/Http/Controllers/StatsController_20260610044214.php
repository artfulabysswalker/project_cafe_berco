<?php

namespace App\Http\Controllers;

use App\Models\OrderHistory;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class StatsController extends Controller
{
    public function index()
    {
        $range = request('range', 'today');

        $startDate = match ($range) {
            'today' => Carbon::today(),
            'week' => Carbon::now()->startOfWeek(),
            'month' => Carbon::now()->startOfMonth(),
            'all' => Carbon::now()->subMonths(6),
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
        // BASE QUERY (History only)
        $orderQuery = OrderHistory::query()
            ->where('status_order', 'completed');

        if ($startDate) {
            $orderQuery->where('created_at', '>=', $startDate);
        }

        // -------------------------
        // TOTALS
        // -------------------------
        $totalSales = (clone $orderQuery)->sum('total_harga');
        $totalOrders = (clone $orderQuery)->count();
        $avgOrder = $totalOrders ? $totalSales / $totalOrders : 0;

        // -------------------------
        // BEST SELLING PRODUCTS
        // -------------------------
        $bestSelling = OrderItem::with('menu')
            ->select('id_menu', DB::raw('SUM(quantity) as total'))
            ->whereHas('order', function ($q) use ($startDate) {
                $q->where('status_order', 'completed');

                if ($startDate) {
                    $q->where('created_at', '>=', $startDate);
                }
            })
            ->groupBy('id_menu')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // -------------------------
        // WORST SELLING PRODUCTS
        // -------------------------
        $worstSelling = OrderItem::with('menu')
            ->select('id_menu', DB::raw('SUM(quantity) as total'))
            ->whereHas('order', function ($q) use ($startDate) {
                $q->where('status_order', 'completed');

                if ($startDate) {
                    $q->where('created_at', '>=', $startDate);
                }
            })
            ->groupBy('id_menu')
            ->orderBy('total')
            ->limit(5)
            ->get();

        // -------------------------
        // PEAK HOURS (MYSQL FIXED)
        // -------------------------
        $peakHours = OrderHistory::select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as total')
            )
            ->where('status_order', 'completed')
            ->when($startDate, fn($q) => $q->where('created_at', '>=', $startDate))
            ->groupBy('hour')
            ->orderByDesc('total')
            ->limit(24)
            ->get();

        // -------------------------
        // CUSTOMERS PER DAY
        // -------------------------
        $customersPerDay = OrderHistory::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(DISTINCT nama_pelanggan) as total')
            )
            ->where('status_order', 'completed')
            ->when($startDate, fn($q) => $q->where('created_at', '>=', $startDate))
            ->groupBy('date')
            ->orderByDesc('date')
            ->limit(30)
            ->get();

        // -------------------------
        // PAYMENT STATS
        // -------------------------
        $paymentStats = OrderHistory::select('payment_method', DB::raw('COUNT(*) as total'))
            ->where('status_order', 'completed')
            ->when($startDate, fn($q) => $q->where('created_at', '>=', $startDate))
            ->groupBy('payment_method')
            ->pluck('total', 'payment_method');

        $totalPayments = $paymentStats->sum();
        $cashPayments = $paymentStats->get('cash', 0);
        $qrisPayments = $paymentStats->get('qris', 0);

        // -------------------------
        // DAILY SALES (CHART)
        // -------------------------
        $dailySales = OrderHistory::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_harga) as total')
            )
            ->where('status_order', 'completed')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // -------------------------
        // RETURN
        // -------------------------
        return [
            'total_revenue' => $totalSales,
            'total_orders' => $totalOrders,
            'completed_orders' => $totalOrders,
            'avg_order' => round($avgOrder),

            'best_selling' => $bestSelling,
            'worst_selling' => $worstSelling,

            'peak_hours' => $peakHours,
            'customers_per_day' => $customersPerDay,

            'cash_payments' => $cashPayments,
            'qris_payments' => $qrisPayments,
            'total_payments' => $totalPayments,

            'daily_sales' => $dailySales,
        ];
    }

 
}