<?php

namespace App\Http\Controllers;

use App\Models\Referral;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReferralController extends Controller
{
    /**
     * Show referral dashboard
     */
    public function index()
    {
        $user = auth()->user();
        $referralCode = $user->referral_code;
        $referralsMade = $user->referralsMade()->with('referee')->get();
        $referralBalance = $user->referral_balance;
        $completedReferrals = $user->referralsMade()
            ->where('status', 'completed')
            ->count();

        return view('referral.dashboard', compact(
            'referralCode',
            'referralsMade',
            'referralBalance',
            'completedReferrals'
        ));
    }

    /**
     * Apply referral code
     */
    public function apply(Request $request)
    {
        $request->validate([
            'referral_code' => 'required|string|exists:referrals,referral_code',
        ]);

        $user = auth()->user();

        // Check if user already has a referrer
        if ($user->referred_by) {
            return back()->with('error', 'Anda sudah memiliki referrer sebelumnya.');
        }

        // Find the referral
        $referral = Referral::where('referral_code', $request->referral_code)->first();

        // Check if referral is still valid
        if ($referral->status !== 'pending') {
            return back()->with('error', 'Kode referral tidak valid atau sudah digunakan.');
        }

        if ($referral->expires_at && $referral->expires_at->isPast()) {
            return back()->with('error', 'Kode referral sudah expired.');
        }

        // Update user with referrer info
        $user->update([
            'referred_by' => $referral->referrer_id,
        ]);

        // Update referral with referee info
        $referral->update([
            'referee_id' => $user->id_user, // Menggunakan id_user sebagai primary key User
        ]);

        return back()->with('success', 'Kode referral berhasil diterapkan!');
    }

    /**
     * Generate new referral code for user
     */
    public function generateCode()
    {
        $user = auth()->user();

        // Create or update referral code
        if (!$user->referral_code) {
            $user->update([
                'referral_code' => Referral::generateReferralCode(),
            ]);
        }

        return back()->with('success', 'Kode referral berhasil dibuat!');
    }

    /**
     * Get referral statistics
     */
    public function stats()
    {
        $user = auth()->user();

        return response()->json([
            'referral_code' => $user->referral_code,
            'referral_balance' => $user->referral_balance,
            'completed_referrals' => $user->referralsMade()
                ->where('status', 'completed')
                ->count(),
            'pending_referrals' => $user->referralsMade()
                ->where('status', 'pending')
                ->count(),
        ]);
    }
}
