<?php

use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->staffRole = Role::firstOrCreate(['role_name' => 'Staff']);

    $this->staffUser = User::factory()->create([
        'id_role' => $this->staffRole->id_role,
        'status' => 'active',
    ]);

    $this->customerUser = User::factory()->create(['is_guest' => true]);
});

function cashierOrder(array $overrides = []): Order
{
    return Order::create(array_merge([
        'tanggal' => now(),
        'nama_pelanggan' => 'Pelanggan Kasir',
        'customer_name' => 'Pelanggan Kasir',
        'total_harga' => 50000,
        'subtotal' => 50000,
        'final_total' => 50000,
        'status_pembayaran' => 'pending',
        'status_order' => 'pending',
        'service_type' => 'dine_in',
        'payment_method' => 'qris',
        'id_user' => User::factory()->create(['is_guest' => true])->id_user,
    ], $overrides));
}

test('the cashier log lists incoming qris payments', function () {
    $order = cashierOrder();
    $log = qrisPaymentLogFor($order);

    Livewire::actingAs($this->staffUser)
        ->test(CashierPaymentLog::class)
        ->assertSee('Log Transfer QRIS')
        ->assertSee($order->public_code)
        ->assertSee('Rp 50.000')
        ->assertSee($log->transaction_id);
});

test('a settled payment is shown as paid in the cashier log', function () {
    $order = cashierOrder();
    $log = qrisPaymentLogFor($order);
    $log->update([
        'transaction_status' => 'settlement',
        'paid_at' => now(),
    ]);

    Livewire::actingAs($this->staffUser)
        ->test(CashierPaymentLog::class)
        ->assertSee('Lunas (Settlement)');
});

test('only payments that arrive after the page was opened are highlighted', function () {
    $order = cashierOrder();
    qrisPaymentLogFor($order);

    $component = Livewire::actingAs($this->staffUser)->test(CashierPaymentLog::class);

    // Log yang sudah ada sebelum halaman dibuka tidak boleh ikut disorot.
    expect($component->get('highlightedIds'))->toBe([]);

    $newLog = qrisPaymentLogFor(cashierOrder());

    $component->call('poll')
        ->assertSet('highlightedIds', [$newLog->id]);

    // Poll berikutnya tidak menyorot baris yang sama lagi.
    $component->call('poll')
        ->assertSet('highlightedIds', []);
});

test('the cashier log can be filtered by status', function () {
    $pending = qrisPaymentLogFor(cashierOrder());
    $settled = qrisPaymentLogFor(cashierOrder());
    $settled->update([
        'transaction_status' => 'settlement',
        'paid_at' => now(),
    ]);

    Livewire::actingAs($this->staffUser)
        ->test(CashierPaymentLog::class)
        ->set('status', 'settlement')
        ->assertSee($settled->transaction_id)
        ->assertDontSee($pending->transaction_id);
});

test('the cashier log totals the payments received today', function () {
    qrisPaymentLogFor(cashierOrder(['total_harga' => 50000, 'final_total' => 50000]));
    qrisPaymentLogFor(cashierOrder(['total_harga' => 25000, 'final_total' => 25000]));

    Livewire::actingAs($this->staffUser)
        ->test(CashierPaymentLog::class)
        ->assertSee('Rp 0');
});

test('the customer qr page shows the qr image and the total', function () {
    $order = cashierOrder([
        'id_user' => $this->customerUser->id_user,
        'total_harga' => 75000,
        'final_total' => 75000,
    ]);
    $log = qrisPaymentLogFor($order, ['gross_amount' => 75000]);

    Livewire::actingAs($this->customerUser)
        ->test(CheckoutQrisPage::class, ['order' => $order])
        ->assertOk()
        ->assertSee($log->qr_code_url)
        ->assertSee('Rp 75.000')
        ->assertSee('Menunggu Pembayaran');
});

test('the customer qr page sends the customer away once the order is paid', function () {
    $order = cashierOrder(['id_user' => $this->customerUser->id_user]);
    qrisPaymentLogFor($order);

    $order->update([
        'status_pembayaran' => 'paid',
        'payment_status' => 'paid',
        'paid_at' => now(),
    ]);

    Livewire::actingAs($this->customerUser)
        ->test(CheckoutQrisPage::class, ['order' => $order])
        ->assertRedirect(route('order.show', $order->id_order));
});
