<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\DiscountScheme;
use App\Models\Menu;
use App\Models\TaxConfiguration;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartSessionService
{
    const SESSION_KEY = 'cart';

    const PROMO_KEY = 'cart_promo';

    const ORDER_NOTE_KEY = 'cart_order_note';

    /**
     * Generate unique cart item key
     * Example: "8_Hot", "8_Ice", "3_default"
     */
    public function generateItemKey(int $menuId, ?string $temperature = null): string
    {
        $tempKey = $temperature ? ucfirst(strtolower($temperature)) : 'default';

        return $menuId.'_'.$tempKey;
    }

    /**
     * Get raw cart array from session
     */
    public function getRawCart(): array
    {
        $cart = Session::get(self::SESSION_KEY, []);

        // If session cart is empty but user is authenticated, hydrate from DB
        if (empty($cart) && Auth::check()) {
            $user = Auth::user();
            $dbItems = CartItem::where('user_id', $user->id_user ?? $user->id)->with('menu')->get();
            if ($dbItems->isNotEmpty()) {
                foreach ($dbItems as $dbItem) {
                    if ($dbItem->menu) {
                        $temp = $dbItem->temperature ? ucfirst(strtolower($dbItem->temperature)) : null;
                        if ($temp === 'Cold') {
                            $temp = 'Ice';
                        }
                        $key = $this->generateItemKey($dbItem->menu_id, $temp);
                        $price = $dbItem->menu->getPriceForTemperature($temp);
                        $cart[$key] = [
                            'id' => $key,
                            'cart_item_key' => $key,
                            'db_id' => $dbItem->id,
                            'menu_id' => $dbItem->menu_id,
                            'name' => $dbItem->menu->nama_menu,
                            'price' => (float) $price,
                            'quantity' => (int) $dbItem->quantity,
                            'temperature' => $temp,
                            'note' => $dbItem->note,
                            'subtotal' => (float) ($price * $dbItem->quantity),
                            'image' => $dbItem->menu->image_url ?? ($dbItem->menu->foto ? asset('storage/'.$dbItem->menu->foto) : null),
                            'category' => $dbItem->menu->categoryRelation?->nama_kategori ?? $dbItem->menu->category ?? 'Menu',
                            'series' => $dbItem->menu->series,
                            'stok' => (int) ($dbItem->menu->stok ?? 50),
                        ];
                    }
                }
                Session::put(self::SESSION_KEY, $cart);
            }
        }

        return $cart;
    }

    /**
     * Get collection of cart items with full Menu object attached
     */
    public function getCartItems(): Collection
    {
        $rawCart = $this->getRawCart();
        $menuIds = array_column($rawCart, 'menu_id');
        $menus = Menu::whereIn('id_menu', $menuIds)->with('categoryRelation')->get()->keyBy('id_menu');

        $items = collect();

        foreach ($rawCart as $key => $item) {
            $menu = $menus->get($item['menu_id']);
            if (! $menu) {
                continue;
            }

            $temp = isset($item['temperature']) && $item['temperature'] ? ucfirst(strtolower($item['temperature'])) : null;
            if ($temp === 'Cold') {
                $temp = 'Ice';
            }
            $unitPrice = (float) $menu->getPriceForTemperature($temp);
            $qty = (int) ($item['quantity'] ?? 1);
            $subtotal = $unitPrice * $qty;

            $items->push((object) [
                'id' => $key,
                'cart_item_key' => $key,
                'db_id' => $item['db_id'] ?? null,
                'menu_id' => $menu->id_menu,
                'menu' => $menu,
                'name' => $menu->nama_menu,
                'price' => $unitPrice,
                'price_formatted' => 'Rp '.number_format($unitPrice, 0, ',', '.'),
                'quantity' => $qty,
                'temperature' => $temp,
                'note' => $item['note'] ?? null,
                'subtotal' => $subtotal,
                'subtotal_formatted' => 'Rp '.number_format($subtotal, 0, ',', '.'),
                'image' => $item['image'] ?? ($menu->foto ? asset('storage/'.$menu->foto) : null),
                'category' => $menu->categoryRelation?->nama_kategori ?? $menu->category ?? 'Menu',
                'series' => $menu->series,
                'stok' => (int) ($menu->stok ?? 50),
            ]);
        }

        return $items;
    }

    /**
     * Add menu item with specific temperature variant to cart session
     */
    public function addItem(int $menuId, int $quantity = 1, ?string $temperature = null, ?string $note = null): array
    {
        $menu = Menu::findOrFail($menuId);
        $quantity = max(1, $quantity);

        // Normalize temperature variant
        $normTemp = null;
        if ($menu->is_beverage) {
            if ($temperature) {
                $t = ucfirst(strtolower(trim($temperature)));
                $normTemp = ($t === 'Cold' || $t === 'Ice') ? 'Ice' : (($t === 'Hot') ? 'Hot' : null);
            }
            // If still null and menu has temperature options, default to first option or Ice
            if (! $normTemp) {
                if (! empty($menu->temperature_options) && is_array($menu->temperature_options)) {
                    $first = $menu->temperature_options[0]['type'] ?? 'Ice';
                    $normTemp = strcasecmp($first, 'hot') === 0 ? 'Hot' : 'Ice';
                } else {
                    $normTemp = 'Ice';
                }
            }
        }

        $itemKey = $this->generateItemKey($menu->id_menu, $normTemp);
        $cart = $this->getRawCart();

        $currentQty = isset($cart[$itemKey]) ? (int) $cart[$itemKey]['quantity'] : 0;
        $newQty = $currentQty + $quantity;

        // Stock check
        $availableStock = $menu->stok ?? 999;
        if ($availableStock < $newQty) {
            return [
                'success' => false,
                'message' => 'Stok menu "'.$menu->nama_menu.'" tidak mencukupi (Sisa stok: '.$availableStock.' porsi).',
            ];
        }

        $unitPrice = (float) $menu->getPriceForTemperature($normTemp);

        $cart[$itemKey] = [
            'id' => $itemKey,
            'cart_item_key' => $itemKey,
            'db_id' => $cart[$itemKey]['db_id'] ?? null,
            'menu_id' => $menu->id_menu,
            'name' => $menu->nama_menu,
            'price' => $unitPrice,
            'quantity' => $newQty,
            'temperature' => $normTemp,
            'note' => $note ?: ($cart[$itemKey]['note'] ?? null),
            'subtotal' => $unitPrice * $newQty,
            'image' => $menu->image_url ?? ($menu->foto ? asset('storage/'.$menu->foto) : null),
            'category' => $menu->categoryRelation?->nama_kategori ?? $menu->category ?? 'Menu',
            'series' => $menu->series,
            'stok' => $availableStock,
        ];

        Session::put(self::SESSION_KEY, $cart);

        // Sync with Database for Authenticated/Guest user
        if (Auth::check()) {
            $user = Auth::user();
            $cartItem = CartItem::where('user_id', $user->id_user ?? $user->id)
                ->where('menu_id', $menu->id_menu)
                ->where(function ($q) use ($normTemp) {
                    if ($normTemp) {
                        $q->where('temperature', $normTemp);
                    } else {
                        $q->whereNull('temperature')->orWhere('temperature', '');
                    }
                })
                ->first();

            if ($cartItem) {
                $cartItem->quantity = $newQty;
                if ($note) {
                    $cartItem->note = $note;
                }
                $cartItem->save();
                $cart[$itemKey]['db_id'] = $cartItem->id;
            } else {
                $created = CartItem::create([
                    'user_id' => $user->id_user ?? $user->id,
                    'menu_id' => $menu->id_menu,
                    'quantity' => $newQty,
                    'temperature' => $normTemp,
                    'note' => $note,
                ]);
                $cart[$itemKey]['db_id'] = $created->id;
            }
            Session::put(self::SESSION_KEY, $cart);
        }

        $totals = $this->getTotals();

        $variantLabel = $normTemp ? " ($normTemp)" : '';

        return [
            'success' => true,
            'message' => 'Menu "'.$menu->nama_menu.$variantLabel.'" berhasil ditambahkan ke keranjang!',
            'product_name' => $menu->nama_menu,
            'variant' => $normTemp,
            'cart_count' => $totals['total_quantity'],
            'cart_items_count' => $totals['items_count'],
            'subtotal' => $totals['subtotal'],
            'grand_total' => $totals['grand_total'],
            'item_key' => $itemKey,
        ];
    }

    /**
     * Update quantity of a cart item
     */
    public function updateQuantity(string $itemKeyOrId, ?int $targetQuantity = null, ?string $action = null): array
    {
        $cart = $this->getRawCart();
        $matchedKey = $this->resolveItemKey($itemKeyOrId, $cart);

        if (! $matchedKey || ! isset($cart[$matchedKey])) {
            return [
                'success' => false,
                'message' => 'Item keranjang tidak ditemukan.',
            ];
        }

        $item = $cart[$matchedKey];
        $menu = Menu::find($item['menu_id']);
        $currentQty = (int) $item['quantity'];

        if ($action === 'increase') {
            $newQty = $currentQty + 1;
        } elseif ($action === 'decrease') {
            $newQty = $currentQty - 1;
        } elseif ($targetQuantity !== null) {
            $newQty = (int) $targetQuantity;
        } else {
            $newQty = $currentQty;
        }

        // If quantity reaches 0 or less, remove item
        if ($newQty <= 0) {
            return $this->removeItem($matchedKey);
        }

        // Stock validation
        $availableStock = $menu ? ($menu->stok ?? 999) : 999;
        if ($newQty > $availableStock) {
            return [
                'success' => false,
                'message' => 'Maksimal pesanan menu ini adalah '.$availableStock.' porsi.',
                'current_quantity' => $currentQty,
            ];
        }

        $unitPrice = $menu ? (float) $menu->getPriceForTemperature($item['temperature'] ?? null) : (float) $item['price'];
        $itemSubtotal = $unitPrice * $newQty;

        $cart[$matchedKey]['quantity'] = $newQty;
        $cart[$matchedKey]['price'] = $unitPrice;
        $cart[$matchedKey]['subtotal'] = $itemSubtotal;

        Session::put(self::SESSION_KEY, $cart);

        // Sync with DB
        if (Auth::check()) {
            $user = Auth::user();
            $dbItem = null;
            if (! empty($item['db_id'])) {
                $dbItem = CartItem::find($item['db_id']);
            }
            if (! $dbItem) {
                $dbItem = CartItem::where('user_id', $user->id_user ?? $user->id)
                    ->where('menu_id', $item['menu_id'])
                    ->where('temperature', $item['temperature'] ?? null)
                    ->first();
            }
            if ($dbItem) {
                $dbItem->quantity = $newQty;
                $dbItem->save();
            }
        }

        $totals = $this->getTotals();

        return [
            'success' => true,
            'message' => 'Jumlah pesanan berhasil diperbarui.',
            'item_key' => $matchedKey,
            'quantity' => $newQty,
            'item_subtotal' => $itemSubtotal,
            'item_subtotal_formatted' => 'Rp '.number_format($itemSubtotal, 0, ',', '.'),
            'cart_count' => $totals['total_quantity'],
            'cart_items_count' => $totals['items_count'],
            'subtotal' => $totals['subtotal'],
            'subtotal_formatted' => $totals['subtotal_formatted'],
            'discount' => $totals['discount'],
            'discount_formatted' => $totals['discount_formatted'],
            'service_charge' => $totals['service_charge'],
            'service_charge_formatted' => $totals['service_charge_formatted'],
            'tax' => $totals['tax'],
            'tax_formatted' => $totals['tax_formatted'],
            'grand_total' => $totals['grand_total'],
            'grand_total_formatted' => $totals['grand_total_formatted'],
        ];
    }

    /**
     * Update item note
     */
    public function updateItemNote(string $itemKeyOrId, ?string $note): array
    {
        $cart = $this->getRawCart();
        $matchedKey = $this->resolveItemKey($itemKeyOrId, $cart);

        if (! $matchedKey || ! isset($cart[$matchedKey])) {
            return ['success' => false, 'message' => 'Item tidak ditemukan'];
        }

        $cart[$matchedKey]['note'] = $note ? trim($note) : null;
        Session::put(self::SESSION_KEY, $cart);

        if (Auth::check()) {
            $user = Auth::user();
            CartItem::where('user_id', $user->id_user ?? $user->id)
                ->where('menu_id', $cart[$matchedKey]['menu_id'])
                ->where('temperature', $cart[$matchedKey]['temperature'] ?? null)
                ->update(['note' => $cart[$matchedKey]['note']]);
        }

        return ['success' => true, 'message' => 'Catatan item disimpan'];
    }

    /**
     * Remove single item from cart
     */
    public function removeItem(string $itemKeyOrId): array
    {
        $cart = $this->getRawCart();
        $matchedKey = $this->resolveItemKey($itemKeyOrId, $cart);

        if (! $matchedKey || ! isset($cart[$matchedKey])) {
            return [
                'success' => false,
                'message' => 'Item tidak ditemukan di keranjang.',
            ];
        }

        $removedItem = $cart[$matchedKey];
        unset($cart[$matchedKey]);
        Session::put(self::SESSION_KEY, $cart);

        // Sync with DB
        if (Auth::check()) {
            $user = Auth::user();
            if (! empty($removedItem['db_id'])) {
                CartItem::where('id', $removedItem['db_id'])->delete();
            } else {
                CartItem::where('user_id', $user->id_user ?? $user->id)
                    ->where('menu_id', $removedItem['menu_id'])
                    ->where('temperature', $removedItem['temperature'] ?? null)
                    ->delete();
            }
        }

        $totals = $this->getTotals();

        return [
            'success' => true,
            'message' => 'Item "'.$removedItem['name'].'" dihapus dari keranjang.',
            'item_key' => $matchedKey,
            'cart_count' => $totals['total_quantity'],
            'cart_items_count' => $totals['items_count'],
            'subtotal' => $totals['subtotal'],
            'subtotal_formatted' => $totals['subtotal_formatted'],
            'discount' => $totals['discount'],
            'discount_formatted' => $totals['discount_formatted'],
            'grand_total' => $totals['grand_total'],
            'grand_total_formatted' => $totals['grand_total_formatted'],
            'is_empty' => $totals['items_count'] === 0,
        ];
    }

    /**
     * Clear all items from cart session and DB
     */
    public function clearCart(): array
    {
        Session::forget(self::SESSION_KEY);
        Session::forget(self::PROMO_KEY);
        Session::forget(self::ORDER_NOTE_KEY);

        if (Auth::check()) {
            $user = Auth::user();
            CartItem::where('user_id', $user->id_user ?? $user->id)->delete();
        }

        return [
            'success' => true,
            'message' => 'Keranjang belanja berhasil dikosongkan.',
            'cart_count' => 0,
            'cart_items_count' => 0,
            'subtotal' => 0,
            'grand_total' => 0,
        ];
    }

    /**
     * Apply Promo Code
     */
    public function applyPromo(string $code): array
    {
        $code = strtoupper(trim($code));
        if (empty($code)) {
            return [
                'success' => false,
                'message' => 'Silakan masukkan kode promo yang valid.',
            ];
        }

        $totals = $this->getTotals(false); // get subtotal without promo
        $subtotal = $totals['subtotal'];

        if ($subtotal <= 0) {
            return [
                'success' => false,
                'message' => 'Keranjang Anda masih kosong.',
            ];
        }

        // Search DiscountScheme in DB
        $scheme = DiscountScheme::where('code', $code)->first();

        if ($scheme) {
            $validation = $scheme->isValid($subtotal);
            if (! $validation['valid']) {
                return [
                    'success' => false,
                    'message' => $validation['reason'] ?? 'Kode promo tidak dapat digunakan untuk pesanan ini.',
                ];
            }

            $discountAmount = $scheme->calculateDiscount($subtotal);

            Session::put(self::PROMO_KEY, [
                'code' => $scheme->code,
                'name' => $scheme->name,
                'description' => $scheme->description,
                'discount' => $discountAmount,
                'scheme_id' => $scheme->id_discount_scheme,
                'discount_type' => $scheme->discount_type,
                'discount_value' => $scheme->discount_value,
            ]);

            $newTotals = $this->getTotals();

            return [
                'success' => true,
                'message' => 'Kode promo "'.$scheme->code.'" berhasil dipasang! Hemat Rp '.number_format($discountAmount, 0, ',', '.'),
                'promo' => Session::get(self::PROMO_KEY),
                'totals' => $newTotals,
            ];
        }

        // Fallback demo coupon codes
        if ($code === 'BERCO10' || $code === 'KOPI10') {
            $discountAmount = round($subtotal * 0.10);
            Session::put(self::PROMO_KEY, [
                'code' => $code,
                'name' => 'Diskon Spesial 10%',
                'description' => 'Diskon 10% untuk seluruh pesanan',
                'discount' => $discountAmount,
                'scheme_id' => null,
            ]);

            $newTotals = $this->getTotals();

            return [
                'success' => true,
                'message' => 'Kode promo "'.$code.'" aktif! Anda hemat Rp '.number_format($discountAmount, 0, ',', '.'),
                'promo' => Session::get(self::PROMO_KEY),
                'totals' => $newTotals,
            ];
        }

        return [
            'success' => false,
            'message' => 'Kode promo "'.$code.'" tidak ditemukan atau sudah tidak berlaku.',
        ];
    }

    /**
     * Remove applied promo
     */
    public function removePromo(): array
    {
        Session::forget(self::PROMO_KEY);
        $totals = $this->getTotals();

        return [
            'success' => true,
            'message' => 'Kode promo telah dihapus.',
            'totals' => $totals,
        ];
    }

    /**
     * Calculate comprehensive cart totals
     */
    public function getTotals(bool $includePromo = true): array
    {
        $cartItems = $this->getCartItems();
        $subtotal = $cartItems->sum('subtotal');
        $totalQty = $cartItems->sum('quantity');
        $itemsCount = $cartItems->count();

        // Calculate discount
        $discountAmount = 0;
        $promoInfo = null;

        if ($includePromo && Session::has(self::PROMO_KEY) && $subtotal > 0) {
            $promo = Session::get(self::PROMO_KEY);
            if (! empty($promo['scheme_id'])) {
                $scheme = DiscountScheme::find($promo['scheme_id']);
                if ($scheme && $scheme->isValid($subtotal)['valid']) {
                    $discountAmount = $scheme->calculateDiscount($subtotal);
                    $promo['discount'] = $discountAmount;
                    Session::put(self::PROMO_KEY, $promo);
                } else {
                    Session::forget(self::PROMO_KEY);
                }
            } else {
                $discountAmount = (float) ($promo['discount'] ?? 0);
            }
            $promoInfo = Session::get(self::PROMO_KEY);
        }

        // Morning discount automatic calculation if no custom promo
        if ($discountAmount <= 0 && $subtotal > 0) {
            $hour = now()->hour;
            if ($hour >= 6 && $hour < 11) {
                $discountAmount = round($subtotal * 0.05);
                $promoInfo = [
                    'code' => 'MORNING_5',
                    'name' => 'Diskon Pagi (06:00 - 11:00)',
                    'discount' => $discountAmount,
                    'is_automatic' => true,
                ];
            }
        }

        // Active tax calculation (if configured)
        $taxConfig = TaxConfiguration::getActiveConfiguration();
        $taxAmount = 0;
        if ($taxConfig && $subtotal > 0) {
            $taxAmount = $taxConfig->calculateTax($subtotal);
        }

        $serviceCharge = 0; // Calculated on checkout based on dine-in / take-away

        $grandTotal = max(0, $subtotal + $taxAmount + $serviceCharge - $discountAmount);

        return [
            'subtotal' => (float) $subtotal,
            'subtotal_formatted' => 'Rp '.number_format($subtotal, 0, ',', '.'),
            'total_quantity' => (int) $totalQty,
            'items_count' => (int) $itemsCount,
            'discount' => (float) $discountAmount,
            'discount_formatted' => 'Rp '.number_format($discountAmount, 0, ',', '.'),
            'tax' => (float) $taxAmount,
            'tax_formatted' => 'Rp '.number_format($taxAmount, 0, ',', '.'),
            'service_charge' => (float) $serviceCharge,
            'service_charge_formatted' => 'Rp '.number_format($serviceCharge, 0, ',', '.'),
            'grand_total' => (float) $grandTotal,
            'grand_total_formatted' => 'Rp '.number_format($grandTotal, 0, ',', '.'),
            'promo' => $promoInfo,
        ];
    }

    /**
     * Resolve key or numeric database ID to cart_item_key
     */
    protected function resolveItemKey(string $itemKeyOrId, array $cart): ?string
    {
        if (isset($cart[$itemKeyOrId])) {
            return $itemKeyOrId;
        }

        // Match by db_id or numeric ID
        foreach ($cart as $key => $val) {
            if (isset($val['db_id']) && (string) $val['db_id'] === (string) $itemKeyOrId) {
                return $key;
            }
        }

        return null;
    }
}
