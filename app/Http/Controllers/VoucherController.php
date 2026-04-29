<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use App\Models\User;
use App\Services\VoucherService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class VoucherController extends Controller
{
    protected VoucherService $voucherService;

    public function __construct(VoucherService $voucherService)
    {
        $this->voucherService = $voucherService;
    }

    /**
     * Display a listing of vouchers
     */
    public function index(): View
    {
        $vouchers = Voucher::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.vouchers.index', compact('vouchers'));
    }

    /**
     * Show the form for creating a new voucher
     */
    public function create(): View
    {
        return view('admin.vouchers.create');
    }

    /**
     * Store a newly created voucher in database
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:vouchers|max:50',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'quantity' => 'nullable|integer|min:1',
            'minimum_purchase' => 'nullable|numeric|min:0',
            'valid_from' => 'required|date|before:valid_until',
            'valid_until' => 'required|date',
            'voucher_type' => 'required|in:automatic,manual',
        ]);

        Voucher::create($validated);

        return redirect()->route('admin.vouchers.index')
            ->with('success', 'Voucher berhasil dibuat');
    }

    /**
     * Show the form for editing a voucher
     */
    public function edit(Voucher $voucher): View
    {
        return view('admin.vouchers.edit', compact('voucher'));
    }

    /**
     * Update the specified voucher in database
     */
    public function update(Request $request, Voucher $voucher): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:vouchers,code,' . $voucher->id,
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'quantity' => 'nullable|integer|min:1',
            'minimum_purchase' => 'nullable|numeric|min:0',
            'valid_from' => 'required|date|before:valid_until',
            'valid_until' => 'required|date',
            'voucher_type' => 'required|in:automatic,manual',
            'is_active' => 'boolean',
        ]);

        $voucher->update($validated);

        return redirect()->route('admin.vouchers.index')
            ->with('success', 'Voucher berhasil diperbarui');
    }

    /**
     * Delete the specified voucher
     */
    public function destroy(Voucher $voucher): RedirectResponse
    {
        $voucher->delete();
        return redirect()->route('admin.vouchers.index')
            ->with('success', 'Voucher berhasil dihapus');
    }

    /**
     * Send voucher to inactive customers
     */
    public function sendToInactiveCustomers(Request $request): RedirectResponse
    {
        $inactiveUsers = User::whereNotNull('last_visit_at')
            ->where('notification_enabled', true)
            ->where('is_active', true)
            ->where('last_visit_at', '<=', now()->subDays(30))
            ->get();

        $voucher = Voucher::findOrFail($request->voucher_id);
        $count = 0;

        foreach ($inactiveUsers as $user) {
            if ($this->voucherService->assignVoucherToUser($voucher, $user)) {
                $count++;
            }
        }

        return back()->with('success', "Voucher berhasil dikirim ke {$count} pelanggan tidak aktif");
    }

    /**
     * Toggle voucher active status
     */
    public function toggleActive(Voucher $voucher): RedirectResponse
    {
        $voucher->update(['is_active' => !$voucher->is_active]);

        $status = $voucher->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Voucher berhasil {$status}");
    }
}
