<?php
session_start();
require_once __DIR__ . '/../app/helpers.php';

header('Content-Type: application/json');
$action = $_GET['action'] ?? '';

// ── US 2.1 + 2.2: Browse & Filter Menu ───────────────────
if ($action === 'menu') {
    $cat    = $_GET['category'] ?? 'all';
    $search = strtolower($_GET['search'] ?? '');
    $menu   = getMenu();
    $result = [];
    foreach ($menu as $key => $items) {
        if ($cat !== 'all' && $cat !== $key) continue;
        $filtered = $search
            ? array_values(array_filter($items, fn($i) =>
                str_contains(strtolower($i['name']), $search) ||
                str_contains(strtolower($i['desc']), $search)))
            : $items;
        if ($filtered) $result[$key] = $filtered;
    }
    echo json_encode($result);
    exit;
}

// ── US 2.3: Get Cart ──────────────────────────────────────
if ($action === 'cart') {
    echo json_encode(getCartSummary());
    exit;
}

// ── US 2.3: Add to Cart ───────────────────────────────────
if ($action === 'add') {
    $body = json_decode(file_get_contents('php://input'), true);
    $id   = $body['item_id'] ?? '';
    $qty  = (int)($body['qty'] ?? 1);
    if (!getItemById($id)) { http_response_code(404); echo json_encode(['error'=>'Not found']); exit; }
    $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + $qty;
    echo json_encode(['message'=>'Added','cart_count'=>array_sum($_SESSION['cart'])]);
    exit;
}

// ── US 2.3: Update Cart ───────────────────────────────────
if ($action === 'update') {
    $body = json_decode(file_get_contents('php://input'), true);
    $id   = $body['item_id'] ?? '';
    $qty  = (int)($body['qty'] ?? 0);
    if ($qty <= 0) unset($_SESSION['cart'][$id]);
    else $_SESSION['cart'][$id] = $qty;
    echo json_encode(['message'=>'Updated']);
    exit;
}

// ── US 2.3: Remove from Cart ──────────────────────────────
if ($action === 'remove') {
    $body = json_decode(file_get_contents('php://input'), true);
    $id   = $body['item_id'] ?? '';
    unset($_SESSION['cart'][$id]);
    echo json_encode(['message'=>'Removed']);
    exit;
}

// ── US 2.3: Clear Cart ────────────────────────────────────
if ($action === 'clear') {
    $_SESSION['cart'] = [];
    echo json_encode(['message'=>'Cleared']);
    exit;
}

// ── US 2.4: Checkout & Receipt ────────────────────────────
if ($action === 'checkout') {
    $body   = json_decode(file_get_contents('php://input'), true);
    $name   = htmlspecialchars($body['name'] ?? 'Tamu');
    $table  = htmlspecialchars($body['table'] ?? '-');
    $pay    = htmlspecialchars($body['payment'] ?? 'Cash');
    $cart   = getCart();

    if (empty($cart)) { http_response_code(400); echo json_encode(['error'=>'Cart kosong']); exit; }

    $summary  = getCartSummary();
    $tax      = (int)($summary['total'] * 0.1);
    $grand    = $summary['total'] + $tax;
    $order_id = 'BCO-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

    $receipt = [
        'order_id'  => $order_id,
        'name'      => $name,
        'table'     => $table,
        'payment'   => $pay,
        'items'     => $summary['items'],
        'subtotal'  => $summary['total'],
        'tax'       => $tax,
        'total'     => $grand,
        'timestamp' => date('d F Y, H:i'),
        'status'    => 'Dikonfirmasi ✓',
    ];

    // Simpan ke session untuk referensi
    $_SESSION['last_order']   = $receipt;
    $_SESSION['cart']         = [];

    echo json_encode($receipt);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Unknown action']);