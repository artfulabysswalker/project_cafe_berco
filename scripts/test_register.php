<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

echo "====================================================\n";
echo "TESTING /login AND /register ROUTES\n";
echo "====================================================\n";

// 1. Test GET /login
$reqLogin = Request::create('/login', 'GET');
$resLogin = $kernel->handle($reqLogin);
echo '1. GET /login: HTTP '.$resLogin->getStatusCode()."\n";

// 2. Test GET /register
$reqReg = Request::create('/register', 'GET');
$resReg = $kernel->handle($reqReg);
echo '2. GET /register: HTTP '.$resReg->getStatusCode()."\n";
if ($resReg->getStatusCode() === 200) {
    echo "   -> /register loaded successfully without redirect! ✅\n";
} else {
    echo '   -> /register status: '.$resReg->getStatusCode().' (Location: '.$resReg->headers->get('Location').")\n";
}
