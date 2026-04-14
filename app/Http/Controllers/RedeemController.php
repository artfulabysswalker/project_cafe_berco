<?php

namespace App\Http\Controllers;

use App\Models\Redemption;
use App\Models\Reward;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RedeemController extends Controller
{
    public function index()
    {
        $rewards = Reward::all(); // Tampilkan semua rewards, bukan hanya available
        return view('redeem', compact('rewards'));
    }

    public function redeem(Request $request, Reward $reward)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        if (!$reward->available) {
            return back()->withErrors(['reward' => 'Reward ini sedang tidak tersedia.']);
        }

        $user = Auth::user();
        $totalExpNeeded = $reward->exp_cost * $request->quantity;

        if ($user->exp < $totalExpNeeded) {
            return back()->withErrors(['exp' => 'EXP tidak cukup untuk penukaran ini.']);
        }

        $redemption = DB::transaction(function () use ($user, $reward, $totalExpNeeded) {
            // Kurangi EXP user
            $user->decrement('exp', $totalExpNeeded);

            // Buat record redemption
            return Redemption::create([
                'user_id' => $user->id,
                'reward_id' => $reward->id,
                'exp_used' => $totalExpNeeded,
                'status' => 'completed',
            ]);
        ]);

        return redirect()->route('redeem.receipt', $redemption);
    }

    public function receipt(Redemption $redemption)
    {
        // Pastikan user hanya bisa lihat redemption sendiri
        if ($redemption->user_id !== Auth::id()) {
            abort(403);
        }

        return view('receipt', compact('redemption'));
    }

    public function history()
    {
        $redemptions = Auth::user()->redemptions()->with('reward')->latest()->get();
        return view('history', compact('redemptions'));
    }

    public function claimDaily()
    {
        $user = Auth::user();
        
        // Cek apakah user sudah klaim hari ini (menggunakan timestamp)
        if ($user->last_daily_claim && $user->last_daily_claim->isToday()) {
            return back()->withErrors(['daily' => 'Anda sudah mengambil reward hari ini!']);
        }

        DB::transaction(function () use ($user) {
            $user->increment('exp', 50); // Gunakan increment agar atomic
            $user->update(['last_daily_claim' => now()]);
        });

        return back()->with('success', 'Selamat! 50 EXP telah ditambahkan ke akun Anda.');
    }

    public function leaderboard()
    {
        $topUsers = User::orderBy('exp', 'desc')
            ->limit(10)
            ->get(['name', 'exp']);
            
        return view('leaderboard', compact('topUsers'));
    }
}