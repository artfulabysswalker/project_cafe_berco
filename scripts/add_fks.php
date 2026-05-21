<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$statements = [
    // reviews -> users(id), products(id)
    "ALTER TABLE reviews ADD CONSTRAINT reviews_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE",
    "ALTER TABLE reviews ADD CONSTRAINT reviews_menu_id_foreign FOREIGN KEY (menu_id) REFERENCES products(id) ON DELETE CASCADE",

    // user_vouchers -> users(id), vouchers(id)
    "ALTER TABLE user_vouchers ADD CONSTRAINT user_vouchers_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE",
    "ALTER TABLE user_vouchers ADD CONSTRAINT user_vouchers_voucher_id_foreign FOREIGN KEY (voucher_id) REFERENCES vouchers(id) ON DELETE CASCADE",

    // playlist_votes -> users(id), playlists(id) and unique
    "ALTER TABLE playlist_votes ADD CONSTRAINT playlist_votes_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE",
    "ALTER TABLE playlist_votes ADD CONSTRAINT playlist_votes_playlist_id_foreign FOREIGN KEY (playlist_id) REFERENCES playlists(id) ON DELETE CASCADE",
    "ALTER TABLE playlist_votes ADD UNIQUE KEY playlist_votes_user_id_playlist_id_unique (user_id, playlist_id)",

    // referrals -> users(id)
    "ALTER TABLE referrals ADD CONSTRAINT referrals_referrer_id_foreign FOREIGN KEY (referrer_id) REFERENCES users(id) ON DELETE CASCADE",
    "ALTER TABLE referrals ADD CONSTRAINT referrals_referee_id_foreign FOREIGN KEY (referee_id) REFERENCES users(id) ON DELETE SET NULL",
];

foreach ($statements as $s) {
    try {
        DB::statement($s);
        echo "OK: $s\n";
    } catch (\Exception $e) {
        echo "ERR: " . $e->getMessage() . " for statement: $s\n";
    }
}
