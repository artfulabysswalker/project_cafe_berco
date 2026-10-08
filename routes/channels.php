<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('cashier.orders', function (User $user) {
    // Only active Staff / Cashier / Admin are authorized to listen
    return $user->isActive() && ($user->isAdmin() || $user->isStaff());
});
