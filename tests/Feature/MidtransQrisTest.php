<?php

use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\PaymentLog;
use App\Models\User;
use App\Services\QrisPaymentService;

function qrisMidtransOrder(array $overrides = []): Order
{
    useMidtransSandboxConfig();

    $user = User::factory()->create(['is_guest' => true]);

    return Order::create(array_merge([
        'tanggal' => now(),
        'nama_pelanggan' => 'Pelanggan QRIS',
        'customer_name' => 'Pelanggan QRIS',
        'total_harga' => 50000,
        'subtotal' => 50000,
        'final_total' => 50000,
        'status_pembayaran' => 'pending',
        'status_order' => 'pending',
        'service_type' => 'dine_in',
        'payment_method' => 'qris',
        'id_user' => $user->id_user,
    ], $overrides));
}

beforeEach(function () {
    useMidtransSandboxConfig();
});

test('signature verification follows the midtrans sha512 formula', function () {
    $log = qrisPaymentLogFor(qrisMidtransOrder());
    $service = app(QrisPaymentService::class);

    $valid = midtransNotificationFor($log);
    expect($service->verifySignature($valid))->toBeTrue();

    $tampered = $valid;
    $tampered['gross_amount'] = '99999.00';
    expect($service->verifySignature($tampered))->toBeFalse();

    $missing = $valid;
    unset($missing['signature_key']);
    expect($service->verifySignature($missing))->toBeFalse();
});

test('a valid settlement notification marks the order as paid', function () {
    $order = qrisMidtransOrder();
    $log = qrisPaymentLogFor($order);

    $this->postJson(route('midtrans.webhook'), midtransNotificationFor($log))
        ->assertOk()
        ->assertJson(['status' => 'OK']);

    $log->refresh();
    $order->refresh();

    expect($log->transaction_status)->toBe('settlement')
        ->and($log->paid_at)->not->toBeNull()
        ->and($log->signature_key)->not->toBeNull()
        ->and($log->raw_response['order_id'])->toBe($log->midtrans_order_id)
        ->and($order->isPaid())->toBeTrue()
        ->and($order->payment_status)->toBe('paid')
        ->and($order->status_pembayaran)->toBe('paid')
        ->and($order->status_order)->toBe('processing')
        ->and($order->paid_at)->not->toBeNull();
});

test('repeated notifications for the same payment are idempotent', function () {
    $order = qrisMidtransOrder();
    $log = qrisPaymentLogFor($order);
    $payload = midtransNotificationFor($log);

    $this->postJson(route('midtrans.webhook'), $payload)->assertOk();
    $paidAt = $log->refresh()->paid_at;

    $this->travel(5)->seconds();
    $this->postJson(route('midtrans.webhook'), $payload)->assertOk();

    expect($log->refresh()->paid_at->equalTo($paidAt))->toBeTrue()
        ->and($order->refresh()->isPaid())->toBeTrue();
});

test('a notification with an invalid signature is rejected', function () {
    $order = qrisMidtransOrder();
    $log = qrisPaymentLogFor($order);

    $payload = midtransNotificationFor($log);
    $payload['signature_key'] = str_repeat('a', 128);

    $this->postJson(route('midtrans.webhook'), $payload)->assertStatus(403);

    expect($log->refresh()->paid_at)->toBeNull()
        ->and($order->refresh()->isPaid())->toBeFalse();
});

test('a notification for an unknown transaction is not found', function () {
    qrisMidtransOrder();

    $log = qrisPaymentLogFor(qrisMidtransOrder());
    $payload = midtransNotificationFor($log);
    $payload['order_id'] = 'ORDER-999-20261021120000';
    $payload['signature_key'] = hash(
        'sha512',
        $payload['order_id'].$payload['status_code'].$payload['gross_amount'].MIDTRANS_TEST_SERVER_KEY
    );

    $this->postJson(route('midtrans.webhook'), $payload)->assertStatus(404);
});

test('a notification with a mismatched amount never settles the order', function () {
    $order = qrisMidtransOrder();
    $log = qrisPaymentLogFor($order);

    $payload = midtransNotificationFor($log, ['gross_amount' => '1.00']);

    $this->postJson(route('midtrans.webhook'), $payload)->assertStatus(400);

    $log->refresh();
    expect($log->paid_at)->toBeNull()
        ->and($log->transaction_status)->toBe('pending')
        ->and($order->refresh()->isPaid())->toBeFalse();
});

test('an expire notification flags the payment as expired without paying the order', function () {
    $order = qrisMidtransOrder();
    $log = qrisPaymentLogFor($order);

    $payload = midtransNotificationFor($log, [
        'transaction_status' => 'expire',
        'fraud_status' => null,
        'status_message' => 'Midtrans payment is expired',
    ]);
    unset($payload['fraud_status']);

    $this->postJson(route('midtrans.webhook'), $payload)->assertOk();

    $log->refresh();
    $order->refresh();

    expect($log->transaction_status)->toBe('expire')
        ->and($log->paid_at)->toBeNull()
        ->and($order->isPaid())->toBeFalse()
        ->and($order->payment_status)->toBe('expired')
        ->and($order->status_order)->toBe('pending');
});

test('a cancelled order is never resurrected by a late settlement', function () {
    $order = qrisMidtransOrder(['status_order' => 'cancelled']);
    $log = qrisPaymentLogFor($order);

    $this->postJson(route('midtrans.webhook'), midtransNotificationFor($log))->assertOk();

    $order->refresh();
    expect($order->isPaid())->toBeFalse()
        ->and($order->status_order)->toBe('cancelled');
});

test('qris checkout redirects the customer to the qr payment page', function () {
    $menu = Menu::create([
        'nama_menu' => 'Kopi Susu Uji',
        'harga' => 20000,
        'stok' => 10,
        'status_tersedia' => 1,
        'id_kategori' => Category::firstOrCreate(['nama_kategori' => 'Uji Uji'])->id,
    ]);

    $this->postJson(route('cart.add'), ['product_id' => $menu->id_menu, 'quantity' => 1])->assertOk();

    $response = $this->postJson(route('order.store'), [
        'service_type' => 'dine_in',
        'payment_method' => 'qris',
        'customer_name' => 'Pelanggan QRIS',
    ])->assertOk();

    $order = Order::latest('id_order')->firstOrFail();

    expect($order->payment_method)->toBe('qris')
        ->and($response->json('redirect'))->toBe(route('order.qris', $order->id_order));
});

test('the qr payment page reports an inline error when midtrans is not configured', function () {
    config(['services.midtrans.server_key' => null]);

    $order = qrisMidtransOrder();
    $user = User::find($order->id_user);

    $this->actingAs($user)
        ->get(route('order.qris', $order->id_order))
        ->assertOk()
        ->assertSee('QRIS gagal dibuat');

    expect(PaymentLog::count())->toBe(0);
});

test('another customer cannot open the qr payment page', function () {
    $order = qrisMidtransOrder();
    $intruder = User::factory()->create(['is_guest' => true]);

    $this->actingAs($intruder)
        ->get(route('order.qris', $order->id_order))
        ->assertStatus(403);
});
