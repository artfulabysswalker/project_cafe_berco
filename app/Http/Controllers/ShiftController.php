<?php

namespace App\Http\Controllers;

use App\Models\CashierShift;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShiftController extends Controller
{
    /**
     * Display a listing of shifts for Admin Web
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user ? $user->isAdmin() : true;
        $query = CashierShift::with(['user', 'orders']);

        if (! $isAdmin && $user) {
            $query->where('user_id', $user->id_user);
        }

        if ($request->filled('nama_pegawai') && $request->nama_pegawai != 'Semua') {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', $request->nama_pegawai);
            });
        }

        if ($request->filled('shift_type') && $request->shift_type != 'all') {
            $query->where('shift_type', $request->shift_type);
        }

        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('tanggal') && $request->filled('tanggal')) {
            $query->whereDate('opened_at', $request->tanggal);
        }

        $shifts = $query->orderBy('opened_at', 'desc')->paginate(12)->withQueryString();

        // 1. Kasir-Kasir yang Sedang Aktif Sekarang (Multi-Active Shift Monitor)
        $activeShifts = CashierShift::with(['user', 'orders'])
            ->where('status', 'open')
            ->orderBy('opened_at', 'desc')
            ->get();

        // 2. Daftar Pegawai untuk Filter dan Modal Buka Shift
        $employees = User::whereHas('role', function ($q) {
            $q->whereIn('role_name', ['Cashier', 'Staff', 'Admin', 'kasir']);
        })->get(['id_user', 'name', 'username']);

        // 3. Ringkasan Real-Time Hari Ini per Shift Type
        $today = Carbon::today();
        $todayShift1 = CashierShift::whereDate('opened_at', $today)->where('shift_type', 'shift_1')->get();
        $todayShift2 = CashierShift::whereDate('opened_at', $today)->where('shift_type', 'shift_2')->get();

        $shift1Stats = [
            'count' => $todayShift1->count(),
            'total_omzet' => $todayShift1->sum('cash_sales') + $todayShift1->sum('non_cash_sales'),
            'total_cash' => $todayShift1->sum('cash_sales'),
            'total_qris' => $todayShift1->sum('non_cash_sales'),
            'status' => $todayShift1->where('status', 'open')->count() > 0 ? 'Sedang Berjalan' : ($todayShift1->count() > 0 ? 'Selesai' : 'Belum Ada'),
        ];

        $shift2Stats = [
            'count' => $todayShift2->count(),
            'active_count' => $todayShift2->where('status', 'open')->count(),
            'total_omzet' => $todayShift2->sum('cash_sales') + $todayShift2->sum('non_cash_sales'),
            'total_cash' => $todayShift2->sum('cash_sales'),
            'total_qris' => $todayShift2->sum('non_cash_sales'),
            'status' => $todayShift2->where('status', 'open')->count() > 0 ? 'Multi-Kasir Aktif' : ($todayShift2->count() > 0 ? 'Selesai' : 'Belum Ada'),
        ];

        // 4. Leaderboard / Performa Kasir
        $leaderboard = CashierShift::join('users', 'cashier_shifts.user_id', '=', 'users.id_user')
            ->select(
                'users.name as nama_pegawai',
                DB::raw('SUM(cash_sales + non_cash_sales) as total_omzet'),
                DB::raw('COUNT(*) as total_shift')
            )
            ->where('cashier_shifts.status', 'closed')
            ->groupBy('users.id_user', 'users.name')
            ->orderBy('total_omzet', 'desc')
            ->take(5)
            ->get();

        // 5. Shift Kasir yang Sedang Login (Jika kasir)
        $myActiveShift = $user ? $user->activeShift : null;

        return view('admin.shifts', compact(
            'shifts', 'employees', 'leaderboard', 'activeShifts',
            'shift1Stats', 'shift2Stats', 'myActiveShift'
        ));
    }

    /**
     * Get detail of orders in a specific shift (for modal inspection)
     */
    public function getShiftOrders($id)
    {
        $shift = CashierShift::with(['user', 'orders.items.menu'])->findOrFail($id);

        // Security / Policy check
        $user = auth()->user();
        if ($user && ! $user->isAdmin() && $shift->user_id !== $user->id_user) {
            return response()->json(['error' => 'Akses ditolak. Anda tidak memiliki izin melihat transaksi shift ini.'], 403);
        }

        $orders = $shift->orders->map(function ($order) {
            return [
                'id_order' => $order->id_order,
                'nama_pelanggan' => $order->nama_pelanggan,
                'tanggal' => $order->tanggal ? $order->tanggal->format('H:i:s') : '-',
                'payment_method' => strtoupper($order->payment_method),
                'status_pembayaran' => $order->status_pembayaran,
                'status_order' => $order->status_order,
                'final_total' => $order->final_total,
                'formatted_total' => 'Rp '.number_format($order->final_total, 0, ',', '.'),
                'items_count' => $order->items->count(),
                'items_detail' => $order->items->map(function ($item) {
                    return ($item->menu ? $item->menu->nama_menu : 'Menu').' x'.$item->quantity;
                })->implode(', '),
            ];
        });

        return response()->json([
            'shift' => [
                'id_shift' => $shift->id_shift,
                'kasir' => $shift->user->name,
                'shift_type' => $shift->shift_type == 'shift_1' ? 'Shift 1 (Pagi: 07:00 - 15:00)' : 'Shift 2 (Sore: 15:00 - 23:00)',
                'status' => $shift->status,
                'starting_cash' => $shift->starting_cash,
                'formatted_starting_cash' => 'Rp '.number_format($shift->starting_cash, 0, ',', '.'),
                'cash_sales' => $shift->cash_sales,
                'formatted_cash_sales' => 'Rp '.number_format($shift->cash_sales, 0, ',', '.'),
                'non_cash_sales' => $shift->non_cash_sales,
                'formatted_non_cash_sales' => 'Rp '.number_format($shift->non_cash_sales, 0, ',', '.'),
                'expected_cash' => $shift->starting_cash + $shift->cash_sales,
                'formatted_expected_cash' => 'Rp '.number_format($shift->starting_cash + $shift->cash_sales, 0, ',', '.'),
                'actual_cash' => $shift->actual_cash,
                'formatted_actual_cash' => $shift->actual_cash !== null ? 'Rp '.number_format($shift->actual_cash, 0, ',', '.') : '-',
                'difference' => $shift->difference,
                'formatted_difference' => $shift->difference !== null ? 'Rp '.number_format($shift->difference, 0, ',', '.') : '-',
                'opened_at' => $shift->opened_at ? $shift->opened_at->format('d M Y, H:i') : '-',
                'closed_at' => $shift->closed_at ? $shift->closed_at->format('d M Y, H:i') : 'Masih Berjalan',
                'notes' => $shift->notes ?? '-',
            ],
            'orders' => $orders,
            'total_orders' => $orders->count(),
            'total_omzet' => 'Rp '.number_format($shift->cash_sales + $shift->non_cash_sales, 0, ',', '.'),
        ]);
    }

    /**
     * Open a new shift (Web & API)
     */
    public function openShift(Request $request)
    {
        $request->validate([
            'id_user' => 'required|exists:users,id_user',
            'modal_awal' => 'required|numeric|min:0',
            'shift_type' => 'nullable|in:shift_1,shift_2',
            'notes' => 'nullable|string',
        ]);

        $userId = $request->id_user;

        // Cek apakah kasir ini masih memiliki shift yang berstatus 'open'
        $existingOpenShift = CashierShift::where('user_id', $userId)
            ->where('status', 'open')
            ->first();

        if ($existingOpenShift) {
            $message = 'Kasir ini masih memiliki shift aktif yang belum ditutup (Shift #'.$existingOpenShift->id_shift.'). Harap tutup shift terlebih dahulu.';
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $message], 422);
            }

            return back()->with('error', $message);
        }

        // Tentukan shift_type otomatis jika kosong
        $currentHour = now()->hour;
        $shiftType = $request->shift_type ?? (($currentHour >= 7 && $currentHour < 15) ? 'shift_1' : 'shift_2');

        $shift = CashierShift::create([
            'user_id' => $userId,
            'shift_type' => $shiftType,
            'starting_cash' => $request->modal_awal,
            'cash_sales' => 0,
            'non_cash_sales' => 0,
            'status' => 'open',
            'opened_at' => now(),
            'notes' => $request->notes,
        ]);

        $successMsg = 'Shift '.($shiftType == 'shift_1' ? '1 (Pagi)' : '2 (Sore)').' berhasil dibuka untuk '.$shift->user->name.' dengan modal kas awal Rp '.number_format($request->modal_awal, 0, ',', '.').'.';

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => $successMsg,
                'shift' => $shift,
            ]);
        }

        return back()->with('success', $successMsg);
    }

    /**
     * Close an existing shift (Web & API)
     */
    public function closeShift(Request $request)
    {
        $request->validate([
            'id_shift' => 'required|exists:cashier_shifts,id_shift',
            'kas_akhir_aktual' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $shift = CashierShift::with('user')->findOrFail($request->id_shift);

        if ($shift->status === 'closed') {
            $message = 'Shift ini sudah ditutup sebelumnya.';
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $message], 400);
            }

            return back()->with('error', $message);
        }

        // Calculate totals from orders in this shift
        $totals = Order::where('id_shift', $shift->id_shift)
            ->where('status_order', 'completed')
            ->select(
                DB::raw("SUM(CASE WHEN payment_method = 'cash' THEN final_total ELSE 0 END) as tunai"),
                DB::raw("SUM(CASE WHEN payment_method != 'cash' THEN final_total ELSE 0 END) as nontunai")
            )->first();

        $tunai = (float) ($totals->tunai ?? 0);
        $nontunai = (float) ($totals->nontunai ?? 0);
        $totalDiharapkan = (float) ($shift->starting_cash + $tunai);
        $kasAkhirAktual = (float) $request->kas_akhir_aktual;
        $selisih = $kasAkhirAktual - $totalDiharapkan;

        $shift->update([
            'cash_sales' => $tunai,
            'non_cash_sales' => $nontunai,
            'expected_cash' => $totalDiharapkan,
            'actual_cash' => $kasAkhirAktual,
            'difference' => $selisih,
            'status' => 'closed',
            'closed_at' => now(),
            'notes' => $request->notes ?? $shift->notes,
        ]);

        $statusSelisih = $selisih == 0 ? 'Kas Pas / Balance' : ($selisih < 0 ? 'Selisih Kurang Rp '.number_format(abs($selisih), 0, ',', '.') : 'Surplus Rp '.number_format($selisih, 0, ',', '.'));
        $successMsg = 'Shift #'.$shift->id_shift.' ('.$shift->user->name.') berhasil ditutup. Total Kas Masuk: Rp '.number_format($tunai, 0, ',', '.').' | Hasil: '.$statusSelisih.'.';

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => $successMsg,
                'shift' => $shift,
            ]);
        }

        return back()->with('success', $successMsg);
    }

    /**
     * Verify Outlet PIN (Mock implementation for now)
     */
    public function verifyPin(Request $request)
    {
        $request->validate(['pin' => 'required|string']);

        if ($request->pin === '1234' || $request->pin === '123456') {
            return response()->json(['status' => 'success', 'message' => 'PIN Valid']);
        }

        return response()->json(['status' => 'error', 'message' => 'PIN Salah'], 401);
    }

    /**
     * Get list of cashiers
     */
    public function getEmployees()
    {
        $employees = User::whereHas('role', function ($query) {
            $query->whereIn('role_name', ['Cashier', 'Staff', 'Admin', 'kasir']);
        })->with('role')->get(['id_user', 'name', 'id_role']);

        $formatted = $employees->map(function ($user) {
            return [
                'id_user' => $user->id_user,
                'name' => $user->name,
                'role' => $user->role ? $user->role->role_name : 'Staff',
            ];
        });

        return response()->json($formatted);
    }
}
