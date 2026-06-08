<?php

namespace App\Services;

use App\Models\Order;
use App\Models\TaxConfiguration;
use App\Models\DiscountScheme;

class OrderCalculationService
{
    /**
     * Calculate order total with tax and discount
     */
    public function calculateOrderTotal($subtotal, $taxConfigId = null, $discountSchemeId = null, $costOfGoods = 0)
    {
        // Get active tax configuration if not provided
        if (!$taxConfigId) {
            $taxConfig = TaxConfiguration::getActiveConfiguration();
            $taxConfigId = $taxConfig?->id_tax_config;
        }

        $taxAmount = 0;
        if ($taxConfigId) {
            $taxConfig = TaxConfiguration::find($taxConfigId);
            if ($taxConfig) {
                $taxAmount = $taxConfig->calculateTax($subtotal);
            }
        }

        // Calculate discount if scheme provided
        $discountAmount = 0;
        if ($discountSchemeId) {
            $discountScheme = DiscountScheme::find($discountSchemeId);
            if ($discountScheme) {
                $isValid = $discountScheme->isValid($subtotal);
                if ($isValid['valid']) {
                    $discountAmount = $discountScheme->calculateDiscount($subtotal + $taxAmount);
                }
            }
        }

        $finalTotal = $subtotal + $taxAmount - $discountAmount;

        // Calculate profit margin
        $profitMargin = $finalTotal - $costOfGoods;

        return [
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'final_total' => $finalTotal,
            'profit_margin' => $profitMargin,
        ];
    }

    /**
     * Update order with tax and discount
     */
    public function applyTaxAndDiscount(Order $order, $taxConfigId = null, $discountSchemeId = null, $costOfGoods = 0)
    {
        // Calculate subtotal from items
        $subtotal = $order->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $calculation = $this->calculateOrderTotal($subtotal, $taxConfigId, $discountSchemeId, $costOfGoods);

        // Update order
        $order->update([
            'subtotal' => $calculation['subtotal'],
            'tax_amount' => $calculation['tax_amount'],
            'discount_amount' => $calculation['discount_amount'],
            'final_total' => $calculation['final_total'],
            'profit_margin' => $calculation['profit_margin'],
            'id_tax_config' => $taxConfigId,
            'id_discount_scheme' => $discountSchemeId,
            'cost_of_goods' => $costOfGoods,
            'total_harga' => $calculation['final_total'], // Keep for backward compatibility
        ]);

        // Increment discount usage if applicable
        if ($discountSchemeId) {
            $discountScheme = DiscountScheme::find($discountSchemeId);
            if ($discountScheme) {
                $discountScheme->increment('times_used');
            }
        }

        return $order;
    }

    /**
     * Get checkout summary
     */
    public function getCheckoutSummary($cartItems)
    {
        $subtotal = $cartItems->sum(function ($item) {
            return $item->menu->harga * $item->quantity;
        });

        $activeTax = TaxConfiguration::getActiveConfiguration();
        $taxAmount = $activeTax?->calculateTax($subtotal) ?? 0;

        // Get available discount schemes
        $availableDiscounts = DiscountScheme::getValidSchemes($subtotal);

        return [
            'subtotal' => $subtotal,
            'tax_config' => $activeTax,
            'tax_amount' => $taxAmount,
            'available_discounts' => $availableDiscounts,
            'items_count' => $cartItems->count(),
        ];
    }
}
