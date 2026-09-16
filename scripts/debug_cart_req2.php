<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Menu;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;

$summer = Menu::where('nama_menu', 'Summer')->first();

$session = app('session.store');
$session->start();
$csrfToken = $session->token();

$req2 = Request::create(
    '/cart/add',
    'POST',
    [],
    [],
    [],
    [
        'CONTENT_TYPE' => 'application/json',
        'HTTP_X_CSRF_TOKEN' => $csrfToken,
        'HTTP_ACCEPT' => 'application/json',
    ],
    json_encode([
        '_token' => $csrfToken,
        'menu_id' => $summer->id_menu,
        'quantity' => 2,
    ])
);
$req2->setLaravelSession($session);
$resp2 = $app->handle($req2);

echo 'Status: '.$resp2->getStatusCode()."\n";
echo 'Content: '.$resp2->getContent()."\n";
