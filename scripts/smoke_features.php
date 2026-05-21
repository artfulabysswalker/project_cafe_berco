<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Menu;

$results = [];
$user = User::first();
if (!$user) { echo "No user available for feature tests.\n"; exit(1); }

// Reviews
if (Schema::hasTable('reviews')) {
    try {
        $product = Menu::first();
        $reviewId = DB::table('reviews')->insertGetId([
            'user_id' => $user->id,
            'menu_id' => $product ? $product->id : 1,
            'rating' => 5,
            'comment' => 'Smoke test review',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('reviews')->where('id', $reviewId)->delete();
        $results['reviews'] = 'ok';
    } catch (\Exception $e) { $results['reviews'] = 'error: '.$e->getMessage(); }
} else { $results['reviews'] = 'missing'; }

// Vouchers
if (Schema::hasTable('vouchers')) {
    try {
        $vid = DB::table('vouchers')->insertGetId([
            'code' => 'SMOKE'.uniqid(),
            'name' => 'Smoke Voucher',
            'type' => 'promotion',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $uid = DB::table('user_vouchers')->insertGetId([
            'user_id' => $user->id,
            'voucher_id' => $vid,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('user_vouchers')->where('id', $uid)->delete();
        DB::table('vouchers')->where('id', $vid)->delete();
        $results['vouchers'] = 'ok';
    } catch (\Exception $e) { $results['vouchers'] = 'error: '.$e->getMessage(); }
} else { $results['vouchers'] = 'missing'; }

// Referrals
if (Schema::hasTable('referrals')) {
    try {
        $rid = DB::table('referrals')->insertGetId([
            'referral_code' => 'REF'.uniqid(),
            'referrer_id' => $user->id,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('referrals')->where('id', $rid)->delete();
        $results['referrals'] = 'ok';
    } catch (\Exception $e) { $results['referrals'] = 'error: '.$e->getMessage(); }
} else { $results['referrals'] = 'missing'; }

// Playlists
if (Schema::hasTable('playlists')) {
    try {
        $pid = DB::table('playlists')->insertGetId([
            'title' => 'Smoke Song',
            'artist' => 'Smoke Artist',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $vid = Schema::hasTable('playlist_votes') ? DB::table('playlist_votes')->insertGetId([
            'user_id' => $user->id,
            'playlist_id' => $pid,
            'vote_type' => 'upvote',
            'created_at' => now(),
            'updated_at' => now(),
        ]) : null;
        if ($vid) DB::table('playlist_votes')->where('id', $vid)->delete();
        DB::table('playlists')->where('id', $pid)->delete();
        $results['playlists'] = 'ok';
    } catch (\Exception $e) { $results['playlists'] = 'error: '.$e->getMessage(); }
} else { $results['playlists'] = 'missing'; }

// Rewards/Redemptions
if (Schema::hasTable('rewards') && Schema::hasTable('redemptions')) {
    try {
        $rid = DB::table('rewards')->insertGetId([
            'name' => 'Smoke Reward',
            'exp_cost' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $did = DB::table('redemptions')->insertGetId([
            'user_id' => $user->id,
            'reward_id' => $rid,
            'exp_used' => 10,
            'status' => 'completed',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('redemptions')->where('id', $did)->delete();
        DB::table('rewards')->where('id', $rid)->delete();
        $results['rewards_redemptions'] = 'ok';
    } catch (\Exception $e) { $results['rewards_redemptions'] = 'error: '.$e->getMessage(); }
} else { $results['rewards_redemptions'] = 'missing'; }

// Report
foreach ($results as $k => $v) {
    echo "$k: $v\n";
}
