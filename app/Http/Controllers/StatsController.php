<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\OrderItem;
use App\Models\Expense;
use App\Models\User;
use App\Models\Shift;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    /**
     * Statistics & Financial Analytics Page
     */
    public function index(Request $request)
    {
        $currentUser = auth()->user();
        $isAdmin = $currentUser ? $currentUser->isAdmin() : true;

        // Unique Staff & Cashier list for dropdown switcher
        $staffList = User::whereHas('role', function ($q) {
            $q->whereIn('role_name', ['Admin', 'Staff', 'Cashier', 'kasir', 'pegawai']);
        })->orWhereIn('username', ['admin', 'robin', 'nikita', 'dery'])
          ->with('role')
          ->get()
          ->unique('name');

        // Determine staff filter
        $staffId = $request->query('staff_id');
        if (!$isAdmin && !$staffId) {
            $staffId = $currentUser ? $currentUser->id_user : 'all';
        }
        $staffId = $staffId ?: 'all';

        $shift = $request->query('shift', 'all');
        $range = $request->query('range', 'today');
        $customStart = $request->query('start_date');
        $customEnd = $request->query('end_date');

        [$startDate, $endDate, $periodeLabel] = $this->resolveDateRange($range, $customStart, $customEnd);

        $selectedStaff = ($staffId !== 'all') ? User::find($staffId) : null;
        $reportData = $this->calculateFinancialReport($startDate, $endDate, $staffId, $shift);

        return view('admin.stats_page', array_merge($reportData, [
            'staffList' => $staffList,
            'selectedStaffId' => $staffId,
            'selectedStaff' => $selectedStaff,
            'selectedShift' => $shift,
            'range' => $range,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
            'periodeLabel' => $periodeLabel,
            'isAdmin' => $isAdmin,
        ]));
    }

    /**
     * Download Financial Report as PDF
     */
    public function downloadPdf(Request $request)
    {
        $staffId = $request->query('staff_id', 'all');
        $shiftId = $request->query('shift_id'); // If filtering by specific shift
        $range = $request->query('range', 'today');
        $customStart = $request->query('start_date');
        $customEnd = $request->query('end_date');

        [$startDate, $endDate, $periodeLabel] = $this->resolveDateRange($range, $customStart, $customEnd);

        $reportData = $this->calculateFinancialReport($startDate, $endDate, $staffId, 'all', $shiftId);

        // Determine Display Staff Name
        $staffLabel = 'Semua Staff';
        if ($staffId !== 'all') {
            $staffLabel = User::find($staffId)?->name ?? 'Staff';
        } elseif ($shiftId) {
            $staffLabel = Shift::find($shiftId)?->nama_pegawai ?? 'Staff';
        }

        // Payment Breakdown Aggregation
        $allOrders = $reportData['cashOrders']->concat($reportData['qrisOrders']);
        $totalOrdersCount = $allOrders->count();

        $paymentBreakdown = $allOrders->groupBy('payment_method')->map(function ($orders, $method) use ($totalOrdersCount) {
            $totalAmount = $orders->sum('total_harga');
            $count = $orders->count();
            return [
                'method' => strtoupper($method ?: 'CASH'),
                'count' => $count,
                'total' => $totalAmount,
                'percentage' => $totalOrdersCount > 0 ? round(($count / $totalOrdersCount) * 100, 1) : 0
            ];
        })->values();

        $pdfData = array_merge($reportData, [
            'startDate' => $startDate->format('d/m/Y'),
            'endDate' => $endDate->format('d/m/Y'),
            'periodeLabel' => $periodeLabel,
            'selectedStaff' => $staffLabel,
            'paymentBreakdown' => $paymentBreakdown,
            'printedAt' => Carbon::now()->locale('id')->isoFormat('D MMMM Y, HH:mm'),
            'allOrders' => $allOrders->sortByDesc('tanggal')
        ]);

        $pdf = Pdf::loadView('admin.reports.financial_pdf', $pdfData);
        $pdf->setPaper('A4', 'portrait');

        $cleanStaffName = str_replace(' ', '-', $staffLabel);
        $fileName = 'laporan-penjualan-' . $cleanStaffName . '-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Print View Financial Report
     */
    public function printReport(Request $request)
    {
        $staffId = $request->query('staff_id', 'all');
        $shiftId = $request->query('shift_id');
        $range = $request->query('range', 'today');
        $customStart = $request->query('start_date');
        $customEnd = $request->query('end_date');

        [$startDate, $endDate, $periodeLabel] = $this->resolveDateRange($range, $customStart, $customEnd);

        $reportData = $this->calculateFinancialReport($startDate, $endDate, $staffId, 'all', $shiftId);

        $staffLabel = 'Semua Staff';
        if ($staffId !== 'all') {
            $staffLabel = User::find($staffId)?->name ?? 'Staff';
        } elseif ($shiftId) {
            $staffLabel = Shift::find($shiftId)?->nama_pegawai ?? 'Staff';
        }

        $allOrders = $reportData['cashOrders']->concat($reportData['qrisOrders']);
        $totalOrdersCount = $allOrders->count();

        $paymentBreakdown = $allOrders->groupBy('payment_method')->map(function ($orders, $method) use ($totalOrdersCount) {
            $totalAmount = $orders->sum('total_harga');
            $count = $orders->count();
            return [
                'method' => strtoupper($method ?: 'CASH'),
                'count' => $count,
                'total' => $totalAmount,
                'percentage' => $totalOrdersCount > 0 ? round(($count / $totalOrdersCount) * 100, 1) : 0
            ];
        })->values();

        $printData = array_merge($reportData, [
            'startDate' => $startDate->format('d/m/Y'),
            'endDate' => $endDate->format('d/m/Y'),
            'periodeLabel' => $periodeLabel,
            'selectedStaff' => $staffLabel,
            'paymentBreakdown' => $paymentBreakdown,
            'printedAt' => Carbon::now()->locale('id')->isoFormat('D MMMM Y, HH:mm'),
            'allOrders' => $allOrders->sortByDesc('tanggal')
        ]);

        return view('admin.reports.financial_pdf', $printData);
    }

    /**
     * Helper: Resolve Start and End Dates
     */
    private function resolveDateRange($range, $customStart = null, $customEnd = null)
    {
        if ($range === 'custom' && $customStart && $customEnd) {
            $start = Carbon::parse($customStart)->startOfDay();
            $end = Carbon::parse($customEnd)->endOfDay();
            $label = $start->format('d/m/Y') . ' - ' . $end->format('d/m/Y');
            return [$start, $end, $label];
        }

        switch ($range) {
            case 'yesterday':
                $start = Carbon::yesterday()->startOfDay();
                $end = Carbon::yesterday()->endOfDay();
                $label = 'Kemarin (' . $start->format('d/m/Y') . ')';
                break;
            case 'month':
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
                $label = 'Bulan Ini';
                break;
            default:
                $start = Carbon::today()->startOfDay();
                $end = Carbon::today()->endOfDay();
                $label = 'Hari Ini (' . $start->format('d/m/Y') . ')';
                break;
        }

        return [$start, $end, $label];
    }

    /**
     * Helper: Calculate complete financial numbers
     */
    private function calculateFinancialReport($startDate, $endDate, $staffId = 'all', $shift = 'all', $shiftId = null)
    {
        $query = Order::with(['items.menu', 'user'])
            ->where('status_order', 'completed')
            ->whereBetween('tanggal', [$startDate, $endDate]);

        if ($staffId !== 'all') {
            $query->where('id_user', $staffId);
        }

        if ($shiftId) {
            $query->where('id_shift', $shiftId);
        }

        $allOrders = $query->orderBy('tanggal', 'asc')->get();

        $cashOrders = $allOrders->filter(fn($o) => in_array(strtolower($o->payment_method), ['cash', 'tunai', '']));
        $qrisOrders = $allOrders->filter(fn($o) => in_array(strtolower($o->payment_method), ['qris', 'transfer', 'debit', 'credit']));

        $totalCash = $cashOrders->sum('total_harga');
        $totalQris = $qrisOrders->sum('total_harga');
        $totalOmzet = $allOrders->sum('total_harga');

        $totalHpp = 0;
        foreach ($allOrders as $ord) {
            foreach ($ord->items as $item) {
                $itemHpp = ($item->hpp_at_sale > 0) ? $item->hpp_at_sale : (($item->hpp > 0) ? $item->hpp : ($item->menu?->hpp ?? 0));
                $totalHpp += ($itemHpp * $item->quantity);
            }
        }

        $labaKotor = max(0, $totalOmzet - $totalHpp);
        $marginLabaKotor = ($totalOmzet > 0) ? round(($labaKotor / $totalOmzet) * 100, 1) : 0;

        $expenseQuery = Expense::whereBetween('tanggal', [$startDate, $endDate]);
        if ($staffId !== 'all') $expenseQuery->where('id_user', $staffId);

        $expenses = $expenseQuery->orderBy('tanggal', 'asc')->get();
        $totalExpenses = $expenses->sum('nominal');

        // Top 5 Most Profitable Menus in the selected period
        $completedOrderIds = $allOrders->pluck('id_order');
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

        return [
            'cashOrders' => $cashOrders,
            'qrisOrders' => $qrisOrders,
            'expenses' => $expenses,
            'totalCash' => $totalCash,
            'countCash' => $cashOrders->count(),
            'totalQris' => $totalQris,
            'countQris' => $qrisOrders->count(),
            'totalOmzet' => $totalOmzet,
            'countTotal' => $allOrders->count(),
            'totalHpp' => $totalHpp,
            'labaKotor' => $labaKotor,
            'marginLabaKotor' => $marginLabaKotor,
            'topProfitableMenus' => $topProfitableMenus,
            'totalExpenses' => $totalExpenses,
            'labaBersih' => $labaKotor - $totalExpenses,
        ];
    }
}
