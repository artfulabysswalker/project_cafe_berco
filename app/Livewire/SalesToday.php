<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use Carbon\Carbon;

class SalesToday extends Component
{
    public $date;
    public $todaySales = [];
    public $purchaseHistory = [];

    public function mount()
    {
        $this->date = now()->format('Y-m-d');
        $this->loadTodaysSales();
    }

    public function loadTodaysSales()
    {
        $today = Carbon::parse($this->date)->toDateString();

        $orders = Order::whereDate('tanggal', $today)
            ->where('status_pembayaran', 'paid')
            ->get();

        $totalTransactions = $orders->count();
        $totalRevenue = $orders->sum('final_total');
        $totalProfit = $orders->sum('profit_margin');
        $totalTax = $orders->sum('tax_amount');

        $this->todaySales = [
            'date_display' => Carbon::parse($this->date)->format('d F Y'),
            'date_raw' => $this->date,
            'total_transactions' => $totalTransactions,
            'total_revenue' => $totalRevenue,
            'total_profit' => $totalProfit,
            'total_tax' => $totalTax,
            'avg_transaction' => $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0,
            'total_charge' => $totalTax, // Charge = Tax
        ];

        // Purchase history
        $this->purchaseHistory = Order::with(['user', 'items.menu'])
            ->whereDate('tanggal', $today)
            ->where('status_pembayaran', 'paid')
            ->latest('tanggal')
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id_order,
                    'time' => $order->tanggal->format('H:i:s'),
                    'customer' => $order->nama_pelanggan ?? 'Guest',
                    'items' => $order->items->map(fn ($item) => $item->menu->nama_menu)->implode(', '),
                    'subtotal' => $order->subtotal,
                    'tax' => $order->tax_amount,
                    'discount' => $order->discount_amount,
                    'total' => $order->final_total,
                    'profit' => $order->profit_margin,
                ];
            })
            ->toArray();
    }

    public function changeDate($date)
    {
        $this->date = $date;
        $this->loadTodaysSales();
    }

    public function render()
    {
        return view('livewire.sales-today', [
            'todaySales' => $this->todaySales,
            'purchaseHistory' => $this->purchaseHistory,
        ]);
    }
}
