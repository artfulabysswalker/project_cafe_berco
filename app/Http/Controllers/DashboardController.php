<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Menu;
use App\Models\User;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the Admin Dashboard with Analytics & Metrics
     */
    public function index()
    {
        $user = auth()->user();
        $isAdmin = $user->isAdmin();

        // 1. Key Metrics (Revenue, Orders, Profit)
        $completedOrdersQuery = Order::where(function ($q) {
            $q->whereIn('status_pembayaran', ['paid', 'Sudah'])
              ->orWhere('status_order', 'completed');
        });

        if (!$isAdmin) {
            $completedOrdersQuery->where('id_user', $user->id_user);
        }

        $totalRevenue = (float) $completedOrdersQuery->sum('total_harga');

        $totalOrdersQuery = Order::query();
        if (!$isAdmin) {
            $totalOrdersQuery->where('id_user', $user->id_user);
        }
        $totalOrders = $totalOrdersQuery->count();

        $completedOrdersCountQuery = Order::where(function ($q) {
            $q->whereIn('status_pembayaran', ['paid', 'Sudah'])
              ->orWhere('status_order', 'completed');
        });
        if (!$isAdmin) {
            $completedOrdersCountQuery->where('id_user', $user->id_user);
        }
        $completedOrdersCount = $completedOrdersCountQuery->count();

        // Cash Breakdown
        $cashOrdersQuery = Order::where(function ($q) {
            $q->where('payment_method', 'cash')
              ->orWhere('payment_method', 'tunai')
              ->orWhereNull('payment_method');
        })->where(function ($q) {
            $q->whereIn('status_pembayaran', ['paid', 'Sudah'])
              ->orWhere('status_order', 'completed');
        });
        if (!$isAdmin) {
            $cashOrdersQuery->where('id_user', $user->id_user);
        }
        $cashRevenue = (float) $cashOrdersQuery->sum('total_harga');
        $cashCount = $cashOrdersQuery->count();

        // QRIS Breakdown
        $qrisOrdersQuery = Order::where(function ($q) {
            $q->where('payment_method', 'qris')
              ->orWhere('payment_method', 'QRIS')
              ->orWhere('payment_method', 'transfer');
        })->where(function ($q) {
            $q->whereIn('status_pembayaran', ['paid', 'Sudah'])
              ->orWhere('status_order', 'completed');
        });
        if (!$isAdmin) {
            $qrisOrdersQuery->where('id_user', $user->id_user);
        }
        $qrisRevenue = (float) $qrisOrdersQuery->sum('total_harga');
        $qrisCount = $qrisOrdersQuery->count();

        $pendingOrdersQuery = Order::where('status_pembayaran', 'pending')
            ->orWhere('status_order', 'pending');
        if (!$isAdmin) {
            $pendingOrdersQuery->where('id_user', $user->id_user);
        }
        $pendingOrdersCount = $pendingOrdersQuery->count();

        // Estimated Cost of Goods Sold (HPP) & Gross Profit Calculation
        $completedOrderIdsQuery = Order::where(function ($q) {
            $q->whereIn('status_pembayaran', ['paid', 'Sudah'])
              ->orWhere('status_order', 'completed');
        });
        if (!$isAdmin) {
            $completedOrderIdsQuery->where('id_user', $user->id_user);
        }
        $completedOrderIds = $completedOrderIdsQuery->pluck('id_order');

        $cogsTotal = (float) OrderItem::whereIn('id_order', $completedOrderIds)
            ->join('menus', 'order_items.id_menu', '=', 'menus.id_menu')
            ->sum(DB::raw('order_items.quantity * CASE 
                WHEN order_items.hpp_at_sale > 0 THEN order_items.hpp_at_sale 
                WHEN order_items.hpp > 0 THEN order_items.hpp 
                ELSE COALESCE(menus.hpp, 0) 
            END'));

        $grossProfit = max(0, $totalRevenue - $cogsTotal);
        $profitMarginPct = $totalRevenue > 0 ? round(($grossProfit / $totalRevenue) * 100, 1) : 0;
        $operationalExpenses = round($totalRevenue * 0.15); // 15% operational expenses
        $netProfit = max(0, $grossProfit - $operationalExpenses);

        // Top 5 Most Profitable Menus
        $topProfitableMenus = DB::table('order_items')
            ->join('menus', 'order_items.id_menu', '=', 'menus.id_menu')
            ->whereIn('order_items.id_order', $completedOrderIds)
            ->select(
                'menus.id_menu',
                'menus.nama_menu',
                'menus.foto',
                'menus.harga',
                'menus.hpp',
                DB::raw('SUM(order_items.quantity) as total_qty'),
                DB::raw('SUM(order_items.subtotal) as total_revenue'),
                DB::raw('SUM(order_items.quantity * CASE 
                    WHEN order_items.hpp_at_sale > 0 THEN order_items.hpp_at_sale 
                    WHEN order_items.hpp > 0 THEN order_items.hpp 
                    ELSE COALESCE(menus.hpp, 0) 
                END) as total_cogs'),
                DB::raw('SUM(order_items.subtotal - (order_items.quantity * CASE 
                    WHEN order_items.hpp_at_sale > 0 THEN order_items.hpp_at_sale 
                    WHEN order_items.hpp > 0 THEN order_items.hpp 
                    ELSE COALESCE(menus.hpp, 0) 
                END)) as total_profit')
            )
            ->groupBy('menus.id_menu', 'menus.nama_menu', 'menus.foto', 'menus.harga', 'menus.hpp')
            ->orderByDesc('total_profit')
            ->limit(5)
            ->get();

        $averageOrderValue = $totalOrders > 0
            ? round($totalRevenue / max(1, $completedOrdersCount ?: $totalOrders))
            : 0;

        // 2. Sales Trend for Last 7 Days (Chart Data)
        $sevenDaysDates = collect();
        $sevenDaysRevenue = collect();
        $sevenDaysOrders = collect();

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dayLabel = $date->locale('id')->isoFormat('ddd, D MMM');
            $sevenDaysDates->push($dayLabel);

            $dayRevenueQuery = Order::whereDate('tanggal', $date->toDateString());
            $dayOrdersQuery = Order::whereDate('tanggal', $date->toDateString());

            if (!$isAdmin) {
                $dayRevenueQuery->where('id_user', $user->id_user);
                $dayOrdersQuery->where('id_user', $user->id_user);
            }

            $dayRevenue = $dayRevenueQuery->sum('total_harga');
            $dayOrders = $dayOrdersQuery->count();

            $sevenDaysRevenue->push((int) $dayRevenue);
            $sevenDaysOrders->push((int) $dayOrders);
        }

        // 3. Payment Method & Service Type Breakdown
        $paymentMethodsQuery = Order::select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(total_harga) as total'))
            ->groupBy('payment_method');

        if (!$isAdmin) {
            $paymentMethodsQuery->where('id_user', $user->id_user);
        }
        $paymentMethods = $paymentMethodsQuery->get();

        $paymentLabels = $paymentMethods->pluck('payment_method')->map(function ($method) {
            return match (strtolower($method ?? '')) {
                'cash' => 'Tunai (Cash)',
                'qris' => 'QRIS',
                'debit' => 'Debit Card',
                'credit' => 'Credit Card',
                default => ucfirst($method ?: 'Lainnya'),
            };
        })->toArray();

        $paymentCounts = $paymentMethods->pluck('count')->toArray();

        if (empty($paymentLabels)) {
            $paymentLabels = ['Tunai (Cash)', 'QRIS'];
            $paymentCounts = [1, 0];
        }

        $serviceTypesQuery = Order::select('service_type', DB::raw('count(*) as count'))
            ->groupBy('service_type');
        if (!$isAdmin) {
            $serviceTypesQuery->where('id_user', $user->id_user);
        }
        $serviceTypes = $serviceTypesQuery->get();

        // 4. Top Selling Products (Top 5)
        $topSoldQuery = DB::table('order_items')
            ->join('menus', 'order_items.id_menu', '=', 'menus.id_menu')
            ->join('orders', 'order_items.id_order', '=', 'orders.id_order')
            ->select(
                'menus.id_menu',
                'menus.nama_menu',
                'menus.harga',
                'menus.foto',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->groupBy('menus.id_menu', 'menus.nama_menu', 'menus.harga', 'menus.foto')
            ->orderByDesc('total_sold')
            ->limit(5);

        if (!$isAdmin) {
            $topSoldQuery->where('orders.id_user', $user->id_user);
        }
        $topSold = $topSoldQuery->get();

        $existingIds = $topSold->pluck('id_menu')->toArray();
        $remainingNeeded = 5 - $topSold->count();

        if ($remainingNeeded > 0) {
            $otherMenus = Menu::whereNotIn('id_menu', $existingIds)
                ->limit($remainingNeeded)
                ->get()
                ->map(function ($menu) {
                    return (object) [
                        'id_menu' => $menu->id_menu,
                        'nama_menu' => $menu->nama_menu,
                        'harga' => $menu->harga,
                        'foto' => $menu->foto,
                        'total_sold' => 0,
                        'total_revenue' => 0,
                    ];
                });

            $topProducts = $topSold->concat($otherMenus);
        } else {
            $topProducts = $topSold;
        }

        // 5. Stock & Menu Monitoring
        $totalMenus = Menu::count();
        $availableMenus = Menu::where('status_tersedia', 1)->count();
        $outOfStockMenus = Menu::where('status_tersedia', 0)->get();

        // 6. Recent 5 Orders
        $recentOrdersQuery = Order::with('user')
            ->latest('tanggal')
            ->limit(5);
        if (!$isAdmin) {
            $recentOrdersQuery->where('id_user', $user->id_user);
        }
        $recentOrders = $recentOrdersQuery->get();

        return view('admin.dashboard', compact(
            'totalRevenue',
            'cashRevenue',
            'cashCount',
            'qrisRevenue',
            'qrisCount',
            'cogsTotal',
            'grossProfit',
            'profitMarginPct',
            'netProfit',
            'topProfitableMenus',
            'totalOrders',
            'completedOrdersCount',
            'pendingOrdersCount',
            'averageOrderValue',
            'sevenDaysDates',
            'sevenDaysRevenue',
            'sevenDaysOrders',
            'paymentLabels',
            'paymentCounts',
            'serviceTypes',
            'topProducts',
            'totalMenus',
            'availableMenus',
            'outOfStockMenus',
            'recentOrders'
        ));
    }
}
