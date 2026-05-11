<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use App\Models\User;
use App\Notifications\ComebackVoucherNotification;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    /**
     * Show vouchers list
     */
    public function index()
    {
        $vouchers = Voucher::paginate(15);
        return view('vouchers.index', compact('vouchers'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('vouchers.create');
    }

    /**
     * Store new voucher
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:vouchers,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'max_discount' => 'nullable|numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'type' => 'required|in:welcome,comeback,promotion,referral',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after:valid_from',
        ]);

        Voucher::create($validated);

        return redirect()->route('voucher.index')
            ->with('success', 'Voucher berhasil dibuat!');
    }

    /**
     * Show edit form
     */
    public function edit(Voucher $voucher)
    {
        return view('vouchers.edit', compact('voucher'));
    }

    /**
     * Update voucher
     */
    public function update(Request $request, Voucher $voucher)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:vouchers,code,' . $voucher->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'max_discount' => 'nullable|numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'type' => 'required|in:welcome,comeback,promotion,referral',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after:valid_from',
            'is_active' => 'boolean',
        ]);

        $voucher->update($validated);

        return redirect()->route('voucher.index')
            ->with('success', 'Voucher berhasil diperbarui!');
    }

    /**
     * Delete voucher
     */
    public function destroy(Voucher $voucher)
    {
        $voucher->delete();
        
        return redirect()->route('voucher.index')
            ->with('success', 'Voucher berhasil dihapus!');
    }

    /**
     * Show distribute voucher form
     */
    public function distributeForm(Voucher $voucher)
    {
        $inactiveUsersCount = User::where(function ($query) {
            $query->whereNull('last_activity_at')
                ->orWhere('last_activity_at', '<=', now()->subDays(30));
        })->where('email_verified_at', '!=', null)->count();

        return view('vouchers.distribute', compact('voucher', 'inactiveUsersCount'));
    }

    /**
     * Distribute voucher to inactive users
     */
    public function distribute(Request $request, Voucher $voucher)
    {
        $validated = $request->validate([
            'days' => 'required|integer|min:1|max:365',
            'send_notification' => 'boolean',
        ]);

        // Get inactive users
        $inactiveUsers = User::where(function ($query) use ($validated) {
            $query->whereNull('last_activity_at')
                ->orWhere('last_activity_at', '<=', now()->subDays($validated['days']));
        })
        ->where('email_verified_at', '!=', null)
        ->get();

        $sentCount = 0;

        foreach ($inactiveUsers as $user) {
            // Check if user already has this voucher
            $hasVoucher = $user->vouchers()
                ->where('voucher_id', $voucher->id)
                ->exists();

            if (!$hasVoucher) {
                // Attach voucher to user
                $user->vouchers()->attach($voucher->id, [
                    'status' => 'active',
                    'notified_at' => now(),
                ]);

                // Send notification if enabled
                if ($validated['send_notification']) {
                    $user->notify(new ComebackVoucherNotification($voucher));
                }

                $sentCount++;
            }
        }

        return redirect()->route('voucher.index')
            ->with('success', "Voucher berhasil didistribusikan ke {$sentCount} pengguna!");
    }

    /**
     * Get user vouchers
     */
    public function myVouchers()
    {
        $user = auth()->user();
        $vouchers = $user->getActiveVouchers();
        
        return view('vouchers.my-vouchers', compact('vouchers'));
    }
}
