<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$pdo = Illuminate\Support\Facades\DB::getPdo();
$stm = $pdo->query('SHOW TABLES');
$rows = $stm->fetchAll(PDO::FETCH_NUM);
foreach ($rows as $r) echo $r[0].PHP_EOL;
