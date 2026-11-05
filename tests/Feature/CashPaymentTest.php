<?php

use App\Models\Order;
use App\Models\Role;
use App\Models\User;

function cashPaymentOrder(array $overrides = []): Order
{
    $user = User::factory()->create(['is_guest' => true]);

    return Order::create(array_merge([
        'tanggal' => now(),
        'nama_pelanggan' => 'Pelanggan Tunai',
        'customer_name' => 'Pelanggan Tunai',
        'total_harga' => 25000,
        'subtotal' => 25000,
        'final_total' => 25000,
        'status_pembayaran' => 'pending',
        'status_order' => 'pending',
        'service_type' => 'dine_in',
        'payment_method' => 'cash',
        'id_user' => $user->id_user,
    ], $overrides));
}

function cashPaymentStaff(): User
{
    $role = Role::firstOrCreate(['role_name' => 'Staff']);

    return User::factory()->create(['id_role' => $role->id_role, 'status' => 'active']);
}

function cashPaymentCustomer(): User
{
    $role = Role::firstOrCreate(['role_name' => 'Customer']);

    return User::factory()->create(['id_role' => $role->id_role, 'status' => 'active']);
}

test('cash payment with exact money settles the order and opens the receipt', function () {
    $staff = cashPaymentStaff();
    $order = cashPaymentOrder(['total_harga' => 25000]);

    $this->actingAs($staff)
        ->post(route('admin.orders.confirm', $order->id_order), ['uang_diterima' => 25000])
        ->assertRedirect(route('admin.receipt.print', $order->id_order));

    $order->refresh();
    expect($order->isPaid())->toBeTrue()
        ->and($order->status_order)->toBe('processing')
        ->and((int) $order->uang_diterima)->toBe(25000)
        ->and((int) $order->kembalian)->toBe(0)
        ->and($order->paid_at)->not->toBeNull()
        ->and($order->cashier_name)->toBe($staff->name);
});

test('cash payment with overpayment succeeds and records the change', function () {
    $staff = cashPaymentStaff();
    $order = cashPaymentOrder(['total_harga' => 25000]);

    $this->actingAs($staff)
        ->post(route('admin.orders.confirm', $order->id_order), ['uang_diterima' => 50000])
        ->assertRedirect(route('admin.receipt.print', $order->id_order));

    $order->refresh();
    expect($order->isPaid())->toBeTrue()
        ->and((int) $order->uang_diterima)->toBe(50000)
        ->and((int) $order->kembalian)->toBe(25000);
});

test('cash payment with insufficient money is rejected and the order stays unpaid', function () {
    $staff = cashPaymentStaff();
    $order = cashPaymentOrder(['total_harga' => 25000]);

    $this->actingAs($staff)
        ->from(route('admin.orders'))
        ->post(route('admin.orders.confirm', $order->id_order), ['uang_diterima' => 20000])
        ->assertRedirect(route('admin.orders'))
        ->assertSessionHasErrors('uang_diterima');

    $order->refresh();
    expect($order->isPaid())->toBeFalse()
        ->and($order->status_pembayaran)->toBe('pending')
        ->and($order->status_order)->toBe('pending')
        ->and($order->uang_diterima)->toBeNull();
});

test('change is calculated on the server and cannot be forged by the browser', function () {
    $staff = cashPaymentStaff();
    $order = cashPaymentOrder(['total_harga' => 25000]);

    $this->actingAs($staff)
        ->post(route('admin.orders.confirm', $order->id_order), [
            'uang_diterima' => 30000,
            'kembalian' => 999999,
            'amount_received' => 999999,
        ])
        ->assertRedirect(route('admin.receipt.print', $order->id_order));

    $order->refresh();
    expect((int) $order->uang_diterima)->toBe(30000)
        ->and((int) $order->kembalian)->toBe(5000);
});

test('a qris order cannot be settled through the cash confirmation endpoint', function () {
    $staff = cashPaymentStaff();
    $order = cashPaymentOrder(['payment_method' => 'qris', 'total_harga' => 25000]);

    $this->actingAs($staff)
        ->from(route('admin.orders'))
        ->post(route('admin.orders.confirm', $order->id_order), ['uang_diterima' => 25000])
        ->assertRedirect(route('admin.orders'))
        ->assertSessionHasErrors('payment_method');

    $order->refresh();
    expect($order->isPaid())->toBeFalse()
        ->and($order->status_order)->toBe('pending')
        ->and($order->uang_diterima)->toBeNull();
});

test('a user without cashier or admin rights cannot confirm payment', function () {
    $customer = cashPaymentCustomer();
    $order = cashPaymentOrder();

    $this->actingAs($customer)
        ->post(route('admin.orders.confirm', $order->id_order), ['uang_diterima' => 25000])
        ->assertRedirect('/home');

    expect($order->fresh()->isPaid())->toBeFalse();
});

test('the cashier orders page renders the cash confirmation modal', function () {
    $staff = cashPaymentStaff();
    cashPaymentOrder();

    $this->actingAs($staff)
        ->get(route('admin.orders'))
        ->assertOk()
        ->assertSee('data-confirm-cash', false)
        ->assertSee('cashModal', false)
        ->assertSee('name="uang_diterima"', false);
});

test('a midtrans notification with a mismatched amount does not settle the order', function () {
    $order = cashPaymentOrder(['payment_method' => 'qris', 'total_harga' => 25000]);
    $log = qrisPaymentLogFor($order);

    $this->postJson(route('midtrans.webhook'), midtransNotificationFor($log, [
        'gross_amount' => '10000.00',
    ]))->assertStatus(400);

    $order->refresh();
    expect($order->isPaid())->toBeFalse()
        ->and($order->status_order)->toBe('pending')
        ->and($order->status_pembayaran)->toBe('pending')
        ->and($order->payment_status)->toBeNull()
        ->and($log->refresh()->paid_at)->toBeNull();
});

test('a verified midtrans settlement pushes the order to the kitchen', function () {
    $order = cashPaymentOrder(['payment_method' => 'qris', 'total_harga' => 25000]);
    $log = qrisPaymentLogFor($order);

    $this->postJson(route('midtrans.webhook'), midtransNotificationFor($log))
        ->assertOk()
        ->assertJson(['status' => 'OK']);

    $order->refresh();
    expect($order->isPaid())->toBeTrue()
        ->and($order->status_order)->toBe('processing')
        ->and($order->payment_status)->toBe('paid')
        ->and($order->paid_at)->not->toBeNull()
        ->and($log->refresh()->paid_at)->not->toBeNull();
});
