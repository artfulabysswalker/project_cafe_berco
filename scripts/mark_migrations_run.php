<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
$migrations = [
    '2026_05_14_112258_create_reviews_table',
    '2026_05_14_112648_create_user_vouchers_table',
    '2026_05_14_115039_create_playlist_votes_table',
    '2026_05_14_115457_create_referrals_table',
];
$batch = 8;
foreach ($migrations as $m) {
    try {
        DB::table('migrations')->insert(['migration' => $m, 'batch' => $batch]);
        echo "Marked: $m\n";
    } catch (\Exception $e) {
        echo "Skip/Err: $m => " . $e->getMessage() . "\n";
    }
}
