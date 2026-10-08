<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Stasiun Dapur (Kitchen Display Station)
    |--------------------------------------------------------------------------
    | Pemetaan kata kunci kategori menu -> stasiun produksi. Pesanan masuk ke
    | tiket/KDS per stasiun berdasarkan nama kategori menu (case-insensitive).
    | Bila tidak cocok, masuk stasiun default di bawah.
    */
    'stations' => [
        'Dapur Mie' => ['mie', 'noodle', 'bakmi', 'pasta'],
        'Dapur Dimsum' => ['dimsum', 'siomay', 'bao', 'xiao long'],
        'Bar Minuman' => ['minuman', 'kopi', 'coffee', 'teh', 'tea', 'jus', 'juice', 'mocktail', 'smoothie', 'es', 'lemon'],
        'Dapur Utama' => ['makanan', 'food', 'snack', 'nasi', 'rice', 'ayam', 'chicken', 'burger', 'gelato', 'dessert'],
    ],

    'default' => 'Dapur Utama',
];
