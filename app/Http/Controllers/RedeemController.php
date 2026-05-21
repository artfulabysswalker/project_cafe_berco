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
        $rewards = Reward::where('available', true)->get();
        return view('Customerviews.redeem', compact('rewards'));
    }

    public function redeem(Request $request, Reward $reward)
    {
        $user = Auth::user();

        if (!$reward->available) {
            return back()->withErrors(['reward' => 'Voucher tidak tersedia.']);
        }

        if ($user->exp < $reward->exp_cost) {
            return back()->withErrors(['reward' => 'EXP Anda belum cukup untuk menukar voucher ini.']);
        }

        $redemption = DB::transaction(function () use ($user, $reward) {
            $user->decrement('exp', $reward->exp_cost);

            return Redemption::create([
                'user_id' => $user->id_user, // Menggunakan id_user sebagai primary key User
                'reward_id' => $reward->id,
                'exp_used' => $reward->exp_cost,
                'status' => 'completed',
            ]);
        });

        return redirect()->route('redeem.receipt', $redemption)->with('success', 'Voucher berhasil ditukar.');
    }

    public function receipt(Redemption $redemption)
    {
        // Pastikan user hanya bisa lihat redemption sendiri
        if ($redemption->user_id !== Auth::id()) {
            abort(403);
        }

        return view('redeem-receipt', compact('redemption'));
    }

    public function history()
    {
        $redemptions = Auth::user()->redemptions()->with('reward')->latest()->get();
        return view('redeem-history', compact('redemptions'));
    }

    public function claimDaily()
    {
        $user = Auth::user();
        
        // Cek apakah user sudah klaim hari ini (menggunakan timestamp)
        if ($user->last_daily_claim && $user->last_daily_claim->isToday()) {
            return back()->withErrors(['daily' => 'Anda sudah mengambil reward hari ini!']);
        }

        // Optimasi: Melakukan increment EXP dan update timestamp dalam satu query database
        DB::transaction(function () use ($user) {
            $user->increment('exp', 50, ['last_daily_claim' => now()]);
        });

        return back()->with('success', 'Selamat! 50 EXP telah ditambahkan ke akun Anda.');
    }

    public function leaderboard()
    {
        $topUsers = User::orderBy('exp', 'desc')
            ->where('exp', '>', 0) // Saran: hanya tampilkan yang punya EXP
            ->limit(10)
            ->get(['name', 'exp']);
            
        return view('leaderboard', compact('topUsers'));
    }
}