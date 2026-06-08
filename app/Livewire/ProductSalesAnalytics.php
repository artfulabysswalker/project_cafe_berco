<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\OrderItem;
use App\Models\Menu;
use Carbon\Carbon;

class ProductSalesAnalytics extends Component
{
    public $period = 'daily';
    public $date;
    public $products = [];
    public $topProducts = [];
    public $summary = [];

    public function mount()
    {
        $this->date = now()->format('Y-m-d');
        $this->loadAnalytics();
    }

    public function loadAnalytics()
    {
        $selectedDate = Carbon::parse($this->date);

        // Product Sales Analytics
        $this->products = $this->getProductAnalytics($this->period, $selectedDate);
        
        // Top selling products
        $this->topProducts = $this->getTopProducts($this->period, $selectedDate);
        
        // Summary
        $this->summary = $this->getSummary($this->period, $selectedDate);
    }

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

        return $query->groupBy('id_menu')
            ->selectRaw('
                id_menu,
                SUM(quantity) as total_quantity,
                SUM(subtotal) as total_revenue,
                COUNT(DISTINCT order_items.id_order) as number_of_orders,
                AVG(subtotal / NULLIF(quantity, 0)) as avg_price
            ')
            ->orderByDesc('total_quantity')
            ->get()
            ->map(function ($item) {
                $menu = Menu::find($item->id_menu);
                return [
                    'menu_id' => $item->id_menu,
                    'menu_name' => $menu?->nama_menu ?? 'Unknown',
                    'quantity' => $item->total_quantity,
                    'revenue' => $item->total_revenue,
                    'orders' => $item->number_of_orders,
                    'avg_price' => $item->avg_price,
                ];
            })
            ->toArray();
    }

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

        $products = $query->selectRaw('
                id_menu,
                SUM(quantity) as total_sold,
                SUM(subtotal) as total_revenue,
                COUNT(DISTINCT order_items.id_order) as times_ordered
            ')
            ->groupBy('id_menu')
            ->orderByDesc('total_sold')
            ->limit($limit)
            ->get();

        return $products->map(function ($product, $index) {
            $menu = Menu::find($product->id_menu);
            return [
                'rank' => $index + 1,
                'menu_name' => $menu?->nama_menu ?? 'Unknown',
                'total_sold' => $product->total_sold,
                'total_revenue' => $product->total_revenue,
                'times_ordered' => $product->times_ordered,
                'avg_sold_per_order' => $product->times_ordered > 0 ? round($product->total_sold / $product->times_ordered, 2) : 0,
            ];
        })->toArray();
    }

    private function getSummary($period, $date)
    {
        $query = OrderItem::whereHas('order', function ($q) {
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

        $totalQuantity = $query->sum('quantity');
        $totalRevenue = $query->sum('subtotal');
        $totalOrders = $query->distinct('id_order')->count('id_order');

        return [
            'total_quantity' => $totalQuantity,
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'avg_items_per_order' => $totalOrders > 0 ? round($totalQuantity / $totalOrders, 2) : 0,
            'avg_revenue_per_order' => $totalOrders > 0 ? round($totalRevenue / $totalOrders, 2) : 0,
        ];
    }

    public function updatePeriod($period)
    {
        $this->period = $period;
        $this->loadAnalytics();
    }

    public function changeDate($date)
    {
        $this->date = $date;
        $this->loadAnalytics();
    }

    public function render()
    {
        return view('livewire.product-sales-analytics', [
            'products' => $this->products,
            'topProducts' => $this->topProducts,
            'summary' => $this->summary,
            'period' => $this->period,
            'date' => $this->date,
        ]);
    }
}
