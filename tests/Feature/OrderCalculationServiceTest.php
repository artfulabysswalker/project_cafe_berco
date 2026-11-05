<?php

use App\Models\DiscountScheme;
use App\Models\TaxConfiguration;
use App\Models\User;
use App\Services\OrderCalculationService;

test('it returns the subtotal when no tax or discount is applied', function () {
    $result = (new OrderCalculationService())->calculateOrderTotal(40000, null, null, 12000);

    expect($result)->toBe([
        'subtotal' => 40000,
        'tax_amount' => 0,
        'discount_amount' => 0,
        'final_total' => 40000,
        'profit_margin' => 28000,
    ]);
});

test('it applies an active tax and a valid percentage discount to the taxed subtotal', function () {
    $user = User::factory()->create();
    $tax = TaxConfiguration::create([
        'name' => 'Pajak uji',
        'tax_percentage' => 10,
        'is_active' => true,
        'id_user' => $user->id_user,
    ]);
    $discount = DiscountScheme::create([
        'code' => 'UNIT10',
        'name' => 'Diskon uji',
        'discount_type' => 'percentage',
        'discount_value' => 10,
        'min_purchase' => 0,
        'is_active' => true,
        'valid_from' => now()->subDay(),
        'valid_until' => now()->addDay(),
        'id_user' => $user->id_user,
    ]);

    $result = (new OrderCalculationService())->calculateOrderTotal(
        100000,
        $tax->id_tax_config,
        $discount->id_discount_scheme,
        60000,
    );

    expect($result)->toBe([
        'subtotal' => 100000,
        'tax_amount' => 10000,
        'discount_amount' => 11000,
        'final_total' => 99000,
        'profit_margin' => 39000,
    ]);
});

test('it ignores tax and discount ids that do not exist', function () {
    $result = (new OrderCalculationService())->calculateOrderTotal(25000, 999999, 999999);

    expect($result)->toBe([
        'subtotal' => 25000,
        'tax_amount' => 0,
        'discount_amount' => 0,
        'final_total' => 25000,
        'profit_margin' => 25000,
    ]);
});

test('percentage discounts respect the minimum purchase boundary', function (int $subtotal, int $expectedDiscount) {
    $user = User::factory()->create();
    $discount = DiscountScheme::create([
        'code' => 'MINIMUM'.uniqid(),
        'name' => 'Diskon minimum uji',
        'discount_type' => 'percentage',
        'discount_value' => 10,
        'min_purchase' => 100000,
        'is_active' => true,
        'valid_from' => now()->subDay(),
        'valid_until' => now()->addDay(),
        'id_user' => $user->id_user,
    ]);

    $result = (new OrderCalculationService())->calculateOrderTotal(
        $subtotal,
        null,
        $discount->id_discount_scheme,
    );

    expect($result['discount_amount'])->toBe($expectedDiscount)
        ->and($result['final_total'])->toBe($subtotal - $expectedDiscount);
})->with([
    'one rupiah below minimum' => [99999, 0],
    'exactly at minimum' => [100000, 10000],
]);