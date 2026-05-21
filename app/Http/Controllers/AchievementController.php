<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\User;
use App\Models\UserAchievement;
use Illuminate\Http\Request;

class AchievementController extends Controller
{
    /**
     * Show user's achievements
     */
    public function index()
    {
        $user = auth()->user();
        
        $achievements = Achievement::where('is_active', true)
            ->get()
            ->map(function ($achievement) use ($user) {
                $userAchievement = $user->achievements()
                    ->where('achievement_id', $achievement->id)
                    ->first();

                return [
                    'achievement' => $achievement,
                    'earned' => $userAchievement !== null,
                    'earned_at' => $userAchievement?->earned_at,
                ];
            });

        $earnedCount = $user->achievements()->count();
        $totalCount = Achievement::where('is_active', true)->count();

        return view('achievement.dashboard', compact(
            'achievements',
            'earnedCount',
            'totalCount'
        ));
    }

    /**
     * Check and award achievements for user
     */
    public function checkAndAward(int $userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $achievements = Achievement::where('is_active', true)->get();
        $newAchievements = [];

        foreach ($achievements as $achievement) {
            // Skip if already earned
            if ($user->achievements()->where('achievement_id', $achievement->id)->exists()) {
                continue;
            }

            $isEarned = match ($achievement->type) {
                'orders_count' => $user->getCompletedOrdersCount() >= $achievement->threshold,
                'total_spent' => $user->getTotalSpent() >= $achievement->threshold,
                'referrals_count' => $user->referralsMade()
                    ->where('status', 'completed')
                    ->count() >= $achievement->threshold,
                default => false,
            };

            if ($isEarned) {
                // Award achievement
                UserAchievement::create([
                    'user_id' => $user->id_user, // Menggunakan id_user sebagai primary key User
                    'achievement_id' => $achievement->id,
                    'earned_at' => now(),
                ]);

                // Add reward to user's referral balance
                $user->increment('referral_balance', $achievement->reward_amount);

                $newAchievements[] = $achievement->name;
            }
        }

        return response()->json([
            'success' => true,
            'new_achievements' => $newAchievements,
        ]);
    }

    /**
     * Get achievement details
     */
    public function show(Achievement $achievement)
    {
        $user = auth()->user();
        $isEarned = $user->achievements()
            ->where('achievement_id', $achievement->id)
            ->exists();

        return response()->json([
            'achievement' => $achievement,
            'earned' => $isEarned,
        ]);
    }

    /**
     * List all achievements
     */
    public function list()
    {
        $achievements = Achievement::where('is_active', true)
            ->orderBy('threshold')
            ->get();

        return response()->json($achievements);
    }
}
