<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Menu;
use Illuminate\Http\Request;
use Carbon\Carbon;
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
        
        // Today's Sales Summary
        $todaySales = $this->getTodaysSales();
        
        // Product Sales Analytics
        $productAnalytics = $this->getProductAnalytics($period, $selectedDate);
        
        // Top selling products
        $topProducts = $this->getTopProducts($period, $selectedDate);
        
        // Sales trend chart data
        $chartData = $this->getSalesTrendData($period, $selectedDate);

        return view('admin.analytics.dashboard', compact(
            'todaySales',
            'productAnalytics',
            'topProducts',
            'chartData',
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
        $totalTax = $orders->sum('tax_amount');

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
            'total_tax' => $totalTax,
            'avg_transaction' => $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0,
            'purchase_history' => $purchaseHistory,
        ];
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
                COUNT(DISTINCT order_items.id_order) as times_ordered
            ')
            ->groupBy('id_menu')
            ->orderByDesc('total_sold')
            ->limit($limit)
            ->get();

        // Attach menu details and calculate margin
        return $topProducts->map(function ($item, $index) {
            $menu = Menu::find($item->id_menu);
            $item->rank = $index + 1;
            $item->menu_name = $menu->nama_menu ?? 'Unknown';
            $item->menu_price = $menu->harga ?? 0;
            
            // Calculate simple margin (assuming cost is 40% of price)
            $item->total_margin = ($item->total_revenue * 0.6);
            
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
     * Daily trend (hour by hour)
     */
    private function getDailyTrendData($date)
    {
        $orders = Order::selectRaw('
                CAST(strftime("%H", tanggal) AS INTEGER) as hour,
                COUNT(*) as transactions,
                SUM(final_total) as revenue,
                SUM(profit_margin) as profit
            ')
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
            $revenues[] = $order->revenue ?? 0;
            $profits[] = $order->profit ?? 0;
            $transactions[] = $order->transactions ?? 0;
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
        $orders = Order::selectRaw('
                DAY(tanggal) as day,
                COUNT(*) as transactions,
                SUM(final_total) as revenue,
                SUM(profit_margin) as profit
            ')
            ->whereYear('tanggal', $date->year)
            ->whereMonth('tanggal', $date->month)
            ->where('status_pembayaran', 'paid')
            ->groupBy('day')
            ->get();

        $labels = [];
        $revenues = [];
        $profits = [];
        $transactions = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $labels[] = $day;
            
            $order = $orders->where('day', $day)->first();
            $revenues[] = $order->revenue ?? 0;
            $profits[] = $order->profit ?? 0;
            $transactions[] = $order->transactions ?? 0;
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
        $orders = Order::selectRaw('
                MONTH(tanggal) as month,
                COUNT(*) as transactions,
                SUM(final_total) as revenue,
                SUM(profit_margin) as profit
            ')
            ->whereYear('tanggal', $date->year)
            ->where('status_pembayaran', 'paid')
            ->groupBy('month')
            ->get();

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $labels = [];
        $revenues = [];
        $profits = [];
        $transactions = [];

        for ($month = 1; $month <= 12; $month++) {
            $labels[] = $months[$month - 1];
            
            $order = $orders->where('month', $month)->first();
            $revenues[] = $order->revenue ?? 0;
            $profits[] = $order->profit ?? 0;
            $transactions[] = $order->transactions ?? 0;
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

        $products = $this->getProductAnalytics($period, $selectedDate);
        $products = $products->map(function ($p) {
            $p->menu = Menu::find($p->id_menu);
            $p->margin = ($p->total_revenue * 0.6); // 60% margin
            return $p;
        });

        return view('admin.analytics.product-report', compact('products', 'period', 'date'));
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

        $fileName = 'sales-report-' . $period . '-' . $date . '.csv';
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
        );

        $callback = function() use ($products) {
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
