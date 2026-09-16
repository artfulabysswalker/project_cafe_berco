<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * Expense Management & Detail Reports
     */
    public function index(Request $request)
    {
        $currentUser = auth()->user();
        $isAdmin = $currentUser && $currentUser->role && $currentUser->role->role_name === 'Admin';

        $staffList = User::whereHas('role', function ($q) {
            $q->whereIn('role_name', ['Admin', 'Staff']);
        })->get()->unique('name');

        $staffId = $request->query('staff_id');
        if (! $isAdmin && ! $staffId) {
            $staffId = $currentUser ? $currentUser->id_user : 'all';
        }
        $staffId = $staffId ?: 'all';

        $shift = $request->query('shift', 'all');
        $range = $request->query('range', 'all');
        $customStart = $request->query('start_date');
        $customEnd = $request->query('end_date');

        [$startDate, $endDate, $periodeLabel] = $this->resolveDateRange($range, $customStart, $customEnd);

        $query = Expense::with('user');

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        if ($staffId !== 'all') {
            $query->where('id_user', $staffId);
        }

        if ($shift === 'shift1') {
            $query->whereTime('tanggal', '>=', '10:00:00')->whereTime('tanggal', '<', '16:00:00');
        } elseif ($shift === 'shift2' || $shift === 'shift_izin') {
            $query->whereTime('tanggal', '>=', '16:00:00')->whereTime('tanggal', '<=', '22:30:00');
        }

        $expenses = $query->orderBy('tanggal', 'desc')->get();

        // Metrics
        $todayExpenses = Expense::whereDate('tanggal', Carbon::today())->sum('nominal');
        $thisWeekExpenses = Expense::whereBetween('tanggal', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('nominal');
        $thisMonthExpenses = Expense::whereBetween('tanggal', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->sum('nominal');
        $filteredTotal = $expenses->sum('nominal');

        $selectedStaffId = $staffId;
        $selectedShift = $shift;
        $selectedStaff = ($staffId !== 'all') ? User::find($staffId) : null;

        return view('admin.expenses.index', compact(
            'expenses',
            'staffList',
            'selectedStaffId',
            'selectedStaff',
            'selectedShift',
            'range',
            'startDate',
            'endDate',
            'periodeLabel',
            'todayExpenses',
            'thisWeekExpenses',
            'thisMonthExpenses',
            'filteredTotal',
            'isAdmin'
        ));
    }

    /**
     * Store new expense
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'deskripsi' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:0',
            'operator' => 'nullable|string|max:100',
        ]);

        $defaultOperator = auth()->user()?->name ?: 'Kasir';

        Expense::create([
            'tanggal' => Carbon::parse($request->tanggal),
            'deskripsi' => trim($request->deskripsi),
            'nominal' => $request->nominal,
            'operator' => $request->operator ?: $defaultOperator,
            'id_user' => auth()->id(),
        ]);

        return redirect()->route('admin.expenses.index')->with('success', 'Catatan pengeluaran berhasil disimpan!');
    }

    /**
     * Update an expense
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'deskripsi' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:0',
            'operator' => 'nullable|string|max:100',
        ]);

        $expense = Expense::findOrFail($id);
        $expense->update([
            'tanggal' => Carbon::parse($request->tanggal),
            'deskripsi' => trim($request->deskripsi),
            'nominal' => $request->nominal,
            'operator' => $request->operator ?: $expense->operator,
        ]);

        return redirect()->route('admin.expenses.index')->with('success', 'Catatan pengeluaran berhasil diperbarui!');
    }

    /**
     * Delete an expense
     */
    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
        $desc = $expense->deskripsi;
        $expense->delete();

        return redirect()->route('admin.expenses.index')->with('success', 'Pengeluaran "'.$desc.'" berhasil dihapus!');
    }

    /**
     * Download Expense Report as PDF
     */
    public function downloadPdf(Request $request)
    {
        $staffId = $request->query('staff_id', 'all');
        $shift = $request->query('shift', 'all');
        $range = $request->query('range', 'all');
        $customStart = $request->query('start_date');
        $customEnd = $request->query('end_date');

        [$startDate, $endDate, $periodeLabel] = $this->resolveDateRange($range, $customStart, $customEnd);

        $query = Expense::with('user');

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        if ($staffId !== 'all') {
            $query->where('id_user', $staffId);
        }

        if ($shift === 'shift1') {
            $query->whereTime('tanggal', '>=', '10:00:00')->whereTime('tanggal', '<', '16:00:00');
        } elseif ($shift === 'shift2' || $shift === 'shift_izin') {
            $query->whereTime('tanggal', '>=', '16:00:00')->whereTime('tanggal', '<=', '22:30:00');
        }

        $expenses = $query->orderBy('tanggal', 'desc')->get();
        $totalExpenses = $expenses->sum('nominal');

        $selectedStaff = ($staffId !== 'all') ? User::find($staffId) : null;
        $staffLabel = $selectedStaff ? $selectedStaff->name : 'Semua Staff & Admin';

        $shiftLabel = match ($shift) {
            'shift1' => 'Shift 1 Siang (10:00 - 16:00)',
            'shift2' => 'Shift 2 Sore (16:00 - 22:30)',
            'shift_izin' => 'Shift Khusus 1 Staff Izin (16:00 - 22:30)',
            default => 'Semua Jam Buka (10:00 - 22:30)',
        };

        $pdf = Pdf::loadView('admin.reports.expense_pdf', [
            'expenses' => $expenses,
            'totalExpenses' => $totalExpenses,
            'periodeLabel' => $periodeLabel,
            'staffLabel' => $staffLabel,
            'shiftLabel' => $shiftLabel,
            'printedAt' => Carbon::now()->locale('id')->isoFormat('D MMMM Y, HH:mm'),
        ]);

        $pdf->setPaper('A4', 'portrait');
        $fileName = 'Laporan_Pengeluaran_'.date('Ymd').'.pdf';

        return $pdf->download($fileName);
    }

    private function resolveDateRange($range, $customStart = null, $customEnd = null)
    {
        if ($range === 'custom' && $customStart && $customEnd) {
            $start = Carbon::parse($customStart)->startOfDay();
            $end = Carbon::parse($customEnd)->endOfDay();
            $label = $start->format('d/m/Y').' - '.$end->format('d/m/Y');

            return [$start, $end, $label];
        }

        switch ($range) {
            case 'today':
                $start = Carbon::today()->startOfDay();
                $end = Carbon::today()->endOfDay();
                $label = 'Hari Ini ('.$start->format('d/m/Y').')';
                break;
            case 'yesterday':
                $start = Carbon::yesterday()->startOfDay();
                $end = Carbon::yesterday()->endOfDay();
                $label = 'Kemarin ('.$start->format('d/m/Y').')';
                break;
            case 'week':
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek();
                $label = 'Minggu Ini ('.$start->format('d/m/Y').' - '.$end->format('d/m/Y').')';
                break;
            case 'month':
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
                $label = 'Bulan Ini ('.$start->locale('id')->isoFormat('MMMM Y').')';
                break;
            case 'all':
            default:
                $start = null;
                $end = null;
                $label = 'Semua Periode';
                break;
        }

        return [$start, $end, $label];
    }
}
