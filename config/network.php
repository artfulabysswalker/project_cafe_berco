<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Jaringan Lokal
    |--------------------------------------------------------------------------
    |
    | Daftar CIDR tambahan yang dianggap "jaringan lokal" oleh middleware
    | RestrictToLocalNetwork, selain rentang standar yang sudah hardcoded
    | (loopback, RFC 1918, CGNAT, dan IPv6 unique/link-local).
    |
    | Contoh kalau router kafe memakai subnet tidak lazim:
    | 'local.network.extra_cidrs' => ['192.168.50.0/24'],
    |
    */

    'extra_local_cidrs' => array_filter(explode(',', (string) env('NETWORK_EXTRA_CIDRS', ''))),
];
