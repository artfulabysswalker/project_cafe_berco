<?php

use App\Models\Order;
use App\Models\PaymentLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

/*
|--------------------------------------------------------------------------
| Midtrans QRIS helpers
|--------------------------------------------------------------------------
|
| Server key sandbox palsu dipakai untuk menandatangani notifikasi webhook
| dengan formula resmi SHA512(order_id + status_code + gross_amount + key),
| sehingga tes bisa memverifikasi signature tanpa kredensial asli.
|
*/

const MIDTRANS_TEST_SERVER_KEY = 'SB-TestServerKey123';

function useMidtransSandboxConfig(): void
{
    config([
        'services.midtrans.server_key' => MIDTRANS_TEST_SERVER_KEY,
        'services.midtrans.client_key' => 'SB-TestClientKey123',
        'services.midtrans.is_production' => false,
    ]);
}

function qrisPaymentLogFor(Order $order, array $overrides = []): PaymentLog
{
    useMidtransSandboxConfig();

    return PaymentLog::create(array_merge([
        'order_id' => $order->id_order,
        'midtrans_order_id' => 'ORDER-'.$order->id_order.'-20261021120000',
        'transaction_id' => 'TXN-'.uniqid(),
        'qr_code_url' => 'https://api.sandbox.midtrans.com/v2/qris/TXN/qr-code',
        'gross_amount' => (int) $order->total_harga,
        'transaction_status' => 'pending',
        'fraud_status' => 'accept',
        'payment_type' => 'qris',
        'expired_at' => now()->addMinutes(15),
    ], $overrides));
}

function midtransNotificationFor(PaymentLog $log, array $overrides = []): array
{
    useMidtransSandboxConfig();

    $payload = array_merge([
        'transaction_time' => now()->format('Y-m-d H:i:s'),
        'transaction_status' => 'settlement',
        'transaction_id' => $log->transaction_id,
        'status_message' => 'midtrans payment notification',
        'status_code' => '200',
        'payment_type' => 'qris',
        'order_id' => $log->midtrans_order_id,
        'merchant_id' => 'M000001',
        'gross_amount' => number_format($log->gross_amount, 2, '.', ''),
        'fraud_status' => 'accept',
        'currency' => 'IDR',
    ], $overrides);

    $payload['signature_key'] = hash(
        'sha512',
        $payload['order_id'].$payload['status_code'].$payload['gross_amount'].MIDTRANS_TEST_SERVER_KEY
    );

    return $payload;
}
