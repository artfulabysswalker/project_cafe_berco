<?php

// ── Menu Data ─────────────────────────────────────────────
function getMenu() {
    return [
        'food' => [
            ['id'=>'f1','name'=>'Nasi Goreng Berco',  'price'=>35000,'desc'=>'Nasi goreng spesial dengan telur dan ayam suwir',        'tag'=>'Bestseller'],
            ['id'=>'f2','name'=>'Sandwich Panggang',   'price'=>28000,'desc'=>'Roti gandum dengan isian keju, sayur segar & saus mustard','tag'=>''],
            ['id'=>'f3','name'=>'Pasta Carbonara',     'price'=>42000,'desc'=>'Pasta creamy dengan bacon, telur, dan parmesan',           'tag'=>'New'],
            ['id'=>'f4','name'=>'Salad Bowl',           'price'=>32000,'desc'=>'Mixed greens, cherry tomato, alpukat & vinaigrette',      'tag'=>'Healthy'],
            ['id'=>'f5','name'=>'Burger Berco',         'price'=>45000,'desc'=>'Double patty, cheddar melt, caramelized onion',           'tag'=>'Bestseller'],
            ['id'=>'f6','name'=>'Waffle Klasik',        'price'=>30000,'desc'=>'Waffle renyah dengan butter, madu & buah segar',          'tag'=>''],
        ],
        'drinks' => [
            ['id'=>'d1','name'=>'Kopi Susu Berco', 'price'=>22000,'desc'=>'Espresso dengan susu segar pilihan',            'tag'=>'Favorite'],
            ['id'=>'d2','name'=>'Matcha Latte',    'price'=>25000,'desc'=>'Matcha premium Jepang dengan oat milk',         'tag'=>''],
            ['id'=>'d3','name'=>'Es Coklat',       'price'=>20000,'desc'=>'Coklat belgia premium dengan es batu',          'tag'=>''],
            ['id'=>'d4','name'=>'Jus Alpukat',     'price'=>22000,'desc'=>'Alpukat segar, susu, dan sedikit madu',         'tag'=>'Healthy'],
            ['id'=>'d5','name'=>'Lemon Tea Segar', 'price'=>18000,'desc'=>'Teh hitam, perasan lemon & mint segar',         'tag'=>''],
            ['id'=>'d6','name'=>'Sparkling Berry', 'price'=>23000,'desc'=>'Minuman berkarbonasi dengan mixed berry',       'tag'=>'New'],
        ],
    ];
}

function getItemById($id) {
    foreach (getMenu() as $items) {
        foreach ($items as $item) {
            if ($item['id'] === $id) return $item;
        }
    }
    return null;
}

// ── Cart Helper ─-
function getCart() {
    return $_SESSION['cart'] ?? [];
}

function getCartSummary() {
    $cart  = getCart();
    $items = [];
    $total = 0;
    foreach ($cart as $id => $qty) {
        $item = getItemById($id);
        if ($item) {
            $sub     = $item['price'] * $qty;
            $items[] = array_merge($item, ['qty' => $qty, 'subtotal' => $sub]);
            $total  += $sub;
        }
    }
    $count = array_sum($cart);
    return compact('items','total','count');
}

function fmtRp($n) {
    return 'Rp ' . number_format($n, 0, ',', '.');
}