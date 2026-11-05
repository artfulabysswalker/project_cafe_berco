<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
<<<<<<< HEAD
    public function index()
=======
    /**
     * Display the Admin Dashboard with Analytics & Metrics
     */
    public function index(Request $request)
>>>>>>> c30337b78add6001f0cb0fbdc0f7627f0d348f54
    {
        $user = auth()->user();
        $isAdmin = $user->isAdmin();

        $data = array_merge(
            $this->getKeyMetrics($user, $isAdmin),
            $this->getSalesTrend($user, $isAdmin),
            $this->getPaymentBreakdown($user, $isAdmin),
            $this->getTopProducts($user, $isAdmin),
            $this->getMenuMonitoring(),
            $this->getRecentOrders($user, $isAdmin)
        );

        return view('admin.dashboard', $data);
    }

    private function getKeyMetrics(User $user, bool $isAdmin): array
    {
        $completedQuery = Order::where(function ($q) {
            $q->whereIn('status_pembayaran', ['paid', 'Sudah'])
                ->orWhere('status_order', 'completed');
        });

        if (!$isAdmin) {
            $completedQuery->where('id_user', $user->id_user);
        }

        $totalRevenue = (float) $completedQuery->sum('total_harga');

        $totalOrdersQuery = Order::query();

        if (!$isAdmin) {
            $totalOrdersQuery->where('id_user', $user->id_user);
        }

        $totalOrders = $totalOrdersQuery->count();

        $completedCountQuery = Order::where(function ($q) {
            $q->whereIn('status_pembayaran', ['paid', 'Sudah'])
                ->orWhere('status_order', 'completed');
        });

        if (!$isAdmin) {
            $completedCountQuery->where('id_user', $user->id_user);
        }

        $completedOrdersCount = $completedCountQuery->count();

        $cashQuery = Order::where(function ($q) {
            $q->where('payment_method', 'cash')
                ->orWhereNull('payment_method');
        })->where(function ($q) {
            $q->whereIn('status_pembayaran', ['paid', 'Sudah'])
                ->orWhere('status_order', 'completed');
        });

        if (!$isAdmin) {
            $cashQuery->where('id_user', $user->id_user);
        }

        $cashRevenue = (float) $cashQuery->sum('total_harga');
        $cashCount = $cashQuery->count();

        $qrisQuery = Order::where('payment_method', 'qris')
            ->where(function ($q) {
                $q->whereIn('status_pembayaran', ['paid', 'Sudah'])
                    ->orWhere('status_order', 'completed');
            });

        if (!$isAdmin) {
            $qrisQuery->where('id_user', $user->id_user);
        }

        $qrisRevenue = (float) $qrisQuery->sum('total_harga');
        $qrisCount = $qrisQuery->count();

        $pendingQuery = Order::where(function ($q) {
            $q->where('status_pembayaran', 'pending')
                ->orWhere('status_order', 'pending');
        });

        if (!$isAdmin) {
            $pendingQuery->where('id_user', $user->id_user);
        }

        $pendingOrdersCount = $pendingQuery->count();

        $completedIdsQuery = Order::where(function ($q) {
            $q->whereIn('status_pembayaran', ['paid', 'Sudah'])
                ->orWhere('status_order', 'completed');
        });

        if (!$isAdmin) {
            $completedIdsQuery->where('id_user', $user->id_user);
        }

        $completedOrderIds = $completedIdsQuery->pluck('id_order');

        $cogsTotal = (float) OrderItem::whereIn('id_order', $completedOrderIds)
            ->join('menus', 'order_items.id_menu', '=', 'menus.id_menu')
            ->sum(DB::raw('order_items.quantity * CASE
                WHEN order_items.hpp_at_sale > 0 THEN order_items.hpp_at_sale
                WHEN order_items.hpp > 0 THEN order_items.hpp
                ELSE COALESCE(menus.hpp, 0)
            END'));

        $grossProfit = max(0, $totalRevenue - $cogsTotal);

        $profitMarginPct = $totalRevenue > 0
            ? round(($grossProfit / $totalRevenue) * 100, 1)
            : 0;

        $operationalExpenses = round($totalRevenue * 0.15);
        $netProfit = max(0, $grossProfit - $operationalExpenses);

        $averageOrderValue = $totalOrders > 0
            ? round($totalRevenue / max(1, $completedOrdersCount ?: $totalOrders))
            : 0;

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
            ->groupBy(
                'menus.id_menu',
                'menus.nama_menu',
                'menus.foto',
                'menus.harga',
                'menus.hpp'
            )
            ->orderByDesc('total_profit')
            ->limit(5)
            ->get();

        return compact(
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
            'averageOrderValue'
        );
    }

<<<<<<< HEAD
    private function getSalesTrend(User $user, bool $isAdmin): array
    {
=======
        // 2. Sales Trend Chart Data (Supports 7 days, 30 days, recent active period)
        $trendRange = $request->query('range', '7_days');

        $completedCondition = function ($q) {
            $q->whereIn('status_pembayaran', ['paid', 'Sudah'])
                ->orWhere('status_order', 'completed');
        };

        $latestOrderQuery = Order::where($completedCondition);
        if (! $isAdmin) {
            $latestOrderQuery->where('id_user', $user->id_user);
        }
        $latestOrder = $latestOrderQuery->latest('tanggal')->first();
        $latestOrderDate = $latestOrder?->tanggal;

        $anchorDate = Carbon::today();
        if ($trendRange === 'recent' && $latestOrderDate) {
            $anchorDate = Carbon::parse($latestOrderDate)->startOfDay();
            $daysCount = 7;
            $trendChartTitle = 'Tren Penjualan (7 Hari Aktif Terakhir: ' . $anchorDate->copy()->subDays(6)->locale('id')->isoFormat('D MMM') . ' - ' . $anchorDate->locale('id')->isoFormat('D MMM Y') . ')';
        } elseif ($trendRange === '30_days') {
            $daysCount = 30;
            $trendChartTitle = 'Tren Penjualan 30 Hari Terakhir';
        } else {
            $trendRange = '7_days';
            $daysCount = 7;
            $trendChartTitle = 'Tren Penjualan 7 Hari Terakhir';
        }

>>>>>>> c30337b78add6001f0cb0fbdc0f7627f0d348f54
        $sevenDaysDates = collect();
        $sevenDaysRevenue = collect();
        $sevenDaysOrders = collect();

<<<<<<< HEAD
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);

            $sevenDaysDates->push(
                $date->locale('id')->isoFormat('ddd, D MMM')
            );
=======
        for ($i = $daysCount - 1; $i >= 0; $i--) {
            $date = $anchorDate->copy()->subDays($i);
            $dayLabel = $daysCount <= 7
                ? $date->locale('id')->isoFormat('ddd, D MMM')
                : $date->locale('id')->isoFormat('D MMM');
            $sevenDaysDates->push($dayLabel);

            $dayRevenueQuery = Order::whereDate('tanggal', $date->toDateString())
                ->where($completedCondition);
            $dayOrdersQuery = Order::whereDate('tanggal', $date->toDateString())
                ->where($completedCondition);
>>>>>>> c30337b78add6001f0cb0fbdc0f7627f0d348f54

            $revenueQuery = Order::whereDate(
                'tanggal',
                $date->toDateString()
            );

            $ordersQuery = Order::whereDate(
                'tanggal',
                $date->toDateString()
            );

            if (!$isAdmin) {
                $revenueQuery->where('id_user', $user->id_user);
                $ordersQuery->where('id_user', $user->id_user);
            }

<<<<<<< HEAD
            $sevenDaysRevenue->push(
                (int) $revenueQuery->sum('total_harga')
            );
=======
            $dayRevenue = $dayRevenueQuery->sum(DB::raw('COALESCE(final_total, total_harga)'));
            $dayOrders = $dayOrdersQuery->count();
>>>>>>> c30337b78add6001f0cb0fbdc0f7627f0d348f54

            $sevenDaysOrders->push(
                (int) $ordersQuery->count()
            );
        }

<<<<<<< HEAD
        return compact(
            'sevenDaysDates',
            'sevenDaysRevenue',
            'sevenDaysOrders'
        );
    }
=======
        $trendTotalRevenue = $sevenDaysRevenue->sum();
        $trendTotalOrders = $sevenDaysOrders->sum();

        // 3. Payment Method & Service Type Breakdown
        $paymentMethodsQuery = Order::select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(total_harga) as total'))
            ->groupBy('payment_method');
>>>>>>> c30337b78add6001f0cb0fbdc0f7627f0d348f54

    private function getPaymentBreakdown(User $user, bool $isAdmin): array
    {
        $paymentQuery = Order::select(
            'payment_method',
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(total_harga) as total')
        )->groupBy('payment_method');

        if (!$isAdmin) {
            $paymentQuery->where('id_user', $user->id_user);
        }

        $paymentMethods = $paymentQuery->get();

        $paymentLabels = $paymentMethods->pluck('payment_method')
            ->map(function ($method) {
                return match (strtolower($method ?? '')) {
                    'cash' => 'Tunai (Cash)',
                    'qris' => 'QRIS',
                    default => ucfirst($method ?: 'Lainnya'),
                };
            })
            ->toArray();

        $paymentCounts = $paymentMethods->pluck('count')->toArray();

        if (empty($paymentLabels)) {
            $paymentLabels = ['Tunai (Cash)', 'QRIS'];
            $paymentCounts = [1, 0];
        }

        $serviceQuery = Order::select(
            'service_type',
            DB::raw('COUNT(*) as count')
        )->groupBy('service_type');

        if (!$isAdmin) {
            $serviceQuery->where('id_user', $user->id_user);
        }

        $serviceTypes = $serviceQuery->get();

        return compact(
            'paymentLabels',
            'paymentCounts',
            'serviceTypes'
        );
    }

    private function getTopProducts(User $user, bool $isAdmin): array
    {
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
            ->groupBy(
                'menus.id_menu',
                'menus.nama_menu',
                'menus.harga',
                'menus.foto'
            )
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

        return compact('topProducts');
    }

    private function getMenuMonitoring(): array
    {
        $totalMenus = Menu::count();

        $availableMenus = Menu::where('status_tersedia', 1)
            ->count();

<<<<<<< HEAD
        $outOfStockMenus = Menu::where('status_tersedia', 0)
            ->get();

        return compact(
=======
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
            'trendRange',
            'trendChartTitle',
            'trendTotalRevenue',
            'trendTotalOrders',
            'latestOrderDate',
            'paymentLabels',
            'paymentCounts',
            'serviceTypes',
            'topProducts',
>>>>>>> c30337b78add6001f0cb0fbdc0f7627f0d348f54
            'totalMenus',
            'availableMenus',
            'outOfStockMenus'
        );
    }

    private function getRecentOrders(User $user, bool $isAdmin): array
    {
        $recentQuery = Order::with('user')
            ->latest('tanggal')
            ->limit(5);

        if (!$isAdmin) {
            $recentQuery->where('id_user', $user->id_user);
        }

        $recentOrders = $recentQuery->get();

        return compact('recentOrders');
    }
}
