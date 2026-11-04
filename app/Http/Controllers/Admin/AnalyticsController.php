<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * Dashboard Analytics
     */
    public function dashboard(Request $request)
    {
        $period = $request->query('period', 'daily'); // daily, monthly, yearly
        $date = $request->query('date', now()->format('Y-m-d'));

        // Parse date for queries
        $selectedDate = Carbon::parse($date);

        // Today's Sales Summary (kept for backwards compatibility)
        $todaySales = $this->getTodaysSales();

        // Period-based transaction summary (for rekap transaksi)
        $periodTransactions = $this->getPeriodTransactions($period, $selectedDate);

        // Product Sales Analytics
        $productAnalytics = $this->getProductAnalytics($period, $selectedDate);

        // Top selling products
        $topProducts = $this->getTopProducts($period, $selectedDate);

        // Sales trend chart data
        $chartData = $this->getSalesTrendData($period, $selectedDate);

        // Payment method summary
        $paymentMethodSummary = $this->getPaymentMethodSummary($period, $selectedDate);

        return view('admin.analytics.dashboard', compact(
            'todaySales',
            'periodTransactions',
            'productAnalytics',
            'topProducts',
            'chartData',
            'paymentMethodSummary',
            'period',
            'date'
        ));
    }

    /**
     * Get today's sales summary
     */
    private function getTodaysSales()
    {
        $today = now()->toDateString();

        $orders = Order::whereDate('tanggal', $today)
            ->where('status_pembayaran', 'paid')
            ->get();

        $totalTransactions = $orders->count();
        $totalRevenue = $orders->sum('final_total');
        $totalProfit = $orders->sum('profit_margin');
        $totalCharge = $orders->sum('service_charge');

        // Purchase history
        $purchaseHistory = Order::with(['user', 'items.menu'])
            ->whereDate('tanggal', $today)
            ->where('status_pembayaran', 'paid')
            ->latest('tanggal')
            ->get();

        return [
            'date' => now()->format('d F Y'),
            'total_transactions' => $totalTransactions,
            'total_revenue' => $totalRevenue,
            'total_profit' => $totalProfit,
            'total_charge' => $totalCharge,
            'avg_transaction' => $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0,
            'purchase_history' => $purchaseHistory,
        ];
    }

    /**
     * Get period date range from filter
     */
    private function getPeriodDateRange($period, $date)
    {
        switch ($period) {
            case 'daily':
                return [
                    'start' => $date->copy()->startOfDay(),
                    'end' => $date->copy()->endOfDay(),
                ];
            case 'monthly':
                return [
                    'start' => $date->copy()->startOfMonth(),
                    'end' => $date->copy()->endOfMonth(),
                ];
            case 'yearly':
            default:
                return [
                    'start' => $date->copy()->startOfYear(),
                    'end' => $date->copy()->endOfYear(),
                ];
        }
    }

    /**
     * Get transaction summary for selected period
     */
    private function getPeriodTransactions($period, $date)
    {
        $range = $this->getPeriodDateRange($period, $date);

        $orders = Order::with(['user', 'items.menu'])
            ->whereBetween('tanggal', [$range['start'], $range['end']])
            ->where('status_pembayaran', 'paid')
            ->latest('tanggal')
            ->get();

        $totalTransactions = $orders->count();
        $totalRevenue = $orders->sum('final_total');
        $totalProfit = $orders->sum('profit_margin');
        $totalCharge = $orders->sum('service_charge');

        return [
            'date_range' => [
                'start' => $range['start']->format('Y-m-d'),
                'end' => $range['end']->format('Y-m-d'),
                'label' => match ($period) {
                    'daily' => $date->format('d F Y'),
                    'monthly' => $date->format('F Y'),
                    'yearly' => $date->format('Y'),
                },
            ],
            'total_transactions' => $totalTransactions,
            'total_revenue' => $totalRevenue,
            'total_profit' => $totalProfit,
            'total_charge' => $totalCharge,
            'avg_transaction' => $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0,
            'purchase_history' => $orders,
        ];
    }

    /**
     * Get payment method summary for selected period
     */
    private function getPaymentMethodSummary($period, $date)
    {
        $range = $this->getPeriodDateRange($period, $date);

        $paymentMethods = Order::selectRaw('
                payment_method,
                COUNT(*) as transaction_count,
                SUM(final_total) as total_revenue
            ')
            ->whereBetween('tanggal', [$range['start'], $range['end']])
            ->where('status_pembayaran', 'paid')
            ->groupBy('payment_method')
            ->get();

        return $paymentMethods->map(function ($item) {
            $item->payment_method_label = match ($item->payment_method) {
                'cash' => 'Tunai',
                'credit' => 'Kredit',
                'debit' => 'Debit',
                'card' => 'Kartu',
                'e_wallet' => 'E-Wallet',
                'bank_transfer' => 'Transfer Bank',
                'qris' => 'QRIS',
                default => $item->payment_method,
            };
            return $item;
        })->values();
    }

    /**
     * Get product sales analytics for period
     */
    private function getProductAnalytics($period, $date)
    {
        $query = OrderItem::with('menu')
            ->whereHas('order', function ($q) {
                $q->where('status_pembayaran', 'paid');
            });

        // Filter by period
        switch ($period) {
            case 'daily':
                $query->whereDate('order_items.created_at', $date->toDateString());
                break;
            case 'monthly':
                $query->whereYear('order_items.created_at', $date->year)
                    ->whereMonth('order_items.created_at', $date->month);
                break;
            case 'yearly':
                $query->whereYear('order_items.created_at', $date->year);
                break;
        }

        $products = $query->groupBy('id_menu')
            ->selectRaw('
                id_menu,
                SUM(quantity) as total_quantity,
                SUM(subtotal) as total_revenue,
                AVG(subtotal / NULLIF(quantity, 0)) as avg_price,
                COUNT(DISTINCT order_items.id_order) as number_of_orders
            ')
            ->orderByDesc('total_quantity')
            ->get();

        return $products;
    }

    /**
     * Get top selling products with rankings
     */
    private function getTopProducts($period, $date, $limit = 10)
    {
        $query = OrderItem::with('menu')
            ->whereHas('order', function ($q) {
                $q->where('status_pembayaran', 'paid');
            });

        // Filter by period
        switch ($period) {
            case 'daily':
                $query->whereDate('order_items.created_at', $date->toDateString());
                break;
            case 'monthly':
                $query->whereYear('order_items.created_at', $date->year)
                    ->whereMonth('order_items.created_at', $date->month);
                break;
            case 'yearly':
                $query->whereYear('order_items.created_at', $date->year);
                break;
        }

        $topProducts = $query->selectRaw('
                id_menu,
                SUM(quantity) as total_sold,
                SUM(subtotal) as total_revenue,
                SUM(COALESCE(hpp, 0) * quantity) as total_cost,
                COUNT(DISTINCT order_items.id_order) as times_ordered
            ')
            ->groupBy('id_menu')
            ->orderByDesc('total_sold')
            ->limit($limit)
            ->get();

        // Attach menu details and calculate margin based on HPP
        return $topProducts->map(function ($item, $index) {
            $menu = Menu::find($item->id_menu);
            $item->rank = $index + 1;
            $item->menu_name = $menu->nama_menu ?? 'Unknown';
            $item->menu_price = $menu->harga ?? 0;

            // Calculate margin: (harga_jual - hpp) x qty = total_revenue - total_cost
            $item->total_margin = $item->total_revenue - $item->getAttributes()['total_cost'];

            return $item;
        });
    }

    /**
     * Get sales trend data for chart
     */
    private function getSalesTrendData($period, $date)
    {
        if ($period === 'daily') {
            return $this->getDailyTrendData($date);
        } elseif ($period === 'monthly') {
            return $this->getMonthlyTrendData($date);
        } else {
            return $this->getYearlyTrendData($date);
        }
    }

    /**
     * Get a SQL expression to extract a date part, compatible with SQLite and MySQL.
     *
     * @param  string  $column  The column name (e.g. 'tanggal')
     * @param  string  $part    One of 'hour', 'day', 'month'
     * @return string  Raw SQL expression
     */
    private function datePartExpression($column, $part)
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            return match ($part) {
                'hour'  => "HOUR($column)",
                'day'   => "DAY($column)",
                'month' => "MONTH($column)",
            };
        }

        // SQLite (default / testing)
        $format = match ($part) {
            'hour'  => '%H',
            'day'   => '%d',
            'month' => '%m',
        };

        return "CAST(strftime(\"$format\", $column) AS INTEGER)";
    }

    /**
     * Daily trend (hour by hour)
     */
    private function getDailyTrendData($date)
    {
        $hourExpr = $this->datePartExpression('tanggal', 'hour');

        $orders = Order::selectRaw("$hourExpr as hour")
            ->selectRaw('COUNT(*) as transactions')
            ->selectRaw('SUM(final_total) as revenue')
            ->selectRaw('SUM(COALESCE(profit_margin, 0)) as profit')
            ->whereDate('tanggal', $date->toDateString())
            ->where('status_pembayaran', 'paid')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        $labels = [];
        $revenues = [];
        $profits = [];
        $transactions = [];

        for ($i = 0; $i < 24; $i++) {
            $labels[] = sprintf('%02d:00', $i);

            $order = $orders->where('hour', $i)->first();
            $revenues[] = $order ? (float) $order->revenue : 0;
            $profits[] = $order ? (float) $order->profit : 0;
            $transactions[] = $order ? (int) $order->transactions : 0;
        }

        return [
            'labels' => $labels,
            'revenue' => $revenues,
            'profit' => $profits,
            'transactions' => $transactions,
        ];
    }

    /**
     * Monthly trend (day by day)
     */
    private function getMonthlyTrendData($date)
    {
        $daysInMonth = $date->daysInMonth;
        $dayExpr = $this->datePartExpression('tanggal', 'day');

        $orders = Order::selectRaw("$dayExpr as day")
            ->selectRaw('COUNT(*) as transactions')
            ->selectRaw('SUM(final_total) as revenue')
            ->selectRaw('SUM(COALESCE(profit_margin, 0)) as profit')
            ->whereYear('tanggal', $date->year)
            ->whereMonth('tanggal', $date->month)
            ->where('status_pembayaran', 'paid')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $labels = [];
        $revenues = [];
        $profits = [];
        $transactions = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $labels[] = $day;

            $order = $orders->where('day', $day)->first();
            $revenues[] = $order ? (float) $order->revenue : 0;
            $profits[] = $order ? (float) $order->profit : 0;
            $transactions[] = $order ? (int) $order->transactions : 0;
        }

        return [
            'labels' => $labels,
            'revenue' => $revenues,
            'profit' => $profits,
            'transactions' => $transactions,
        ];
    }

    /**
     * Yearly trend (month by month)
     */
    private function getYearlyTrendData($date)
    {
        $monthExpr = $this->datePartExpression('tanggal', 'month');

        $orders = Order::selectRaw("$monthExpr as month")
            ->selectRaw('COUNT(*) as transactions')
            ->selectRaw('SUM(final_total) as revenue')
            ->selectRaw('SUM(COALESCE(profit_margin, 0)) as profit')
            ->whereYear('tanggal', $date->year)
            ->where('status_pembayaran', 'paid')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $labels = [];
        $revenues = [];
        $profits = [];
        $transactions = [];

        for ($month = 1; $month <= 12; $month++) {
            $labels[] = $months[$month - 1];

            $order = $orders->where('month', $month)->first();
            $revenues[] = $order ? (float) $order->revenue : 0;
            $profits[] = $order ? (float) $order->profit : 0;
            $transactions[] = $order ? (int) $order->transactions : 0;
        }

        return [
            'labels' => $labels,
            'revenue' => $revenues,
            'profit' => $profits,
            'transactions' => $transactions,
        ];
    }

    /**
     * Get detailed product report
     */
    public function productReport(Request $request)
    {
        $period = $request->query('period', 'daily');
        $date = $request->query('date', now()->format('Y-m-d'));
        $selectedDate = Carbon::parse($date);

        $products = $this->getProductAnalyticsWithHpp($period, $selectedDate);

        return view('admin.analytics.product-report', compact('products', 'period', 'date'));
    }

    /**
     * Get product sales analytics with HPP for margin calculation
     */
    private function getProductAnalyticsWithHpp($period, $date)
    {
        $query = OrderItem::with('menu')
            ->whereHas('order', function ($q) {
                $q->where('status_pembayaran', 'paid');
            });

        // Filter by period
        switch ($period) {
            case 'daily':
                $query->whereDate('order_items.created_at', $date->toDateString());
                break;
            case 'monthly':
                $query->whereYear('order_items.created_at', $date->year)
                    ->whereMonth('order_items.created_at', $date->month);
                break;
            case 'yearly':
                $query->whereYear('order_items.created_at', $date->year);
                break;
        }

        $products = $query->groupBy('id_menu')
            ->selectRaw('
                id_menu,
                SUM(quantity) as total_quantity,
                SUM(subtotal) as total_revenue,
                SUM(COALESCE(hpp, 0) * quantity) as total_cost,
                AVG(subtotal / NULLIF(quantity, 0)) as avg_price,
                COUNT(DISTINCT order_items.id_order) as number_of_orders
            ')
            ->orderByDesc('total_quantity')
            ->get();

        return $products->map(function ($p) {
            $p->menu = Menu::find($p->id_menu);
            // Calculate margin: total_revenue - total_cost
            $p->margin = $p->total_revenue - $p->getAttributes()['total_cost'];

            return $p;
        });
    }

    /**
     * Export to CSV
     */
    public function exportCsv(Request $request)
    {
        $period = $request->query('period', 'daily');
        $date = $request->query('date', now()->format('Y-m-d'));
        $selectedDate = Carbon::parse($date);

        $products = $this->getProductAnalytics($period, $selectedDate);

        $fileName = 'sales-report-'.$period.'-'.$date.'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
        ];

        $callback = function () use ($products) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Menu', 'Total Quantity', 'Total Revenue', 'Avg Price', 'Number of Orders']);

            foreach ($products as $product) {
                fputcsv($file, [
                    $product->menu->nama_menu ?? 'Unknown',
                    $product->total_quantity,
                    $product->total_revenue,
                    $product->avg_price,
                    $product->number_of_orders,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
