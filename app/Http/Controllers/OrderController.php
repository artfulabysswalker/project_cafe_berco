<?php

namespace App\Http\Controllers;

use App\Events\OrderCreated;
use App\Exceptions\TableQrCodeException;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Meja;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\TableQrCode;
use App\Models\User;
use App\Services\CartSessionService;
use App\Services\KitchenService;
use App\Services\QrCodeService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(protected QrCodeService $qrService, protected KitchenService $kitchenService)
    {
        //
    }

    /**
     * Halaman order via QR Code per meja: GET /order?token={token_meja}
     *
     * Token divalidasi di server; bila tidak valid / meja nonaktif, tampilkan
     * halaman error yang ramah tanpa membuka menu. Identitas meja disimpan di
     * session supaya input form tidak bisa memanipulasi meja pesanan.
     */
    public function orderPage(Request $request)
    {
        $token = (string) $request->query('token', '');

        if ($token === '') {
            return $this->invalidOrderPage('QR tidak valid, silakan hubungi kasir.');
        }

        try {
            $qr = $this->qrService->validateTokenForOrder($token);
        } catch (TableQrCodeException $e) {
            return $this->invalidOrderPage($e->getMessage().', silakan hubungi kasir.');
        }

        $table = $qr->table;

        $request->session()->put('order_table_token', $qr->token);
        $request->session()->put('order_table', $table->nama_meja);
        $request->session()->put('order_table_id', $table->id_meja);

        return view('Customerviews.order', [
            'table' => $table,
            'qr' => $qr,
            'cartCount' => (int) (auth()->user()?->cartItems()->sum('quantity') ?? 0),
        ]);
    }

    private function invalidOrderPage(string $message)
    {
        return response()->view('Customerviews.order-error', [
            'errorMessage' => $message,
        ], 404);
    }

    /**
     * Show checkout page
     */
    public function checkout(Request $request)
    {
        $user = auth()->user();
        $cartItems = app(CartSessionService::class)->getCartItems();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Keranjang Anda kosong');
        }

        if ($request->filled('meja')) {
            $request->session()->put('order_table', $request->query('meja'));
        }

        $table = $this->resolveTableForRequest($request);

        $priceInfo = $this->calculateOrderPrice($cartItems, 'dine_in');

        $cartItemsArray = $cartItems->map(function ($item) {
            return [
                'id' => $item->id,
                'cart_item_key' => $item->cart_item_key,
                'quantity' => $item->quantity,
                'temperature' => $item->temperature,
                'note' => $item->note,
                'unit_price' => $item->price,
                'subtotal' => $item->subtotal,
                'menu' => [
                    'id_menu' => $item->menu->id_menu,
                    'nama_menu' => $item->menu->nama_menu,
                    'harga' => $item->price,
                    'name' => $item->menu->name,
                    'price' => $item->price,
                ],
            ];
        })->toArray();

        return view('Customerviews.checkout', [
            'cartItems' => $cartItems,
            'cartItemsData' => $cartItemsArray,
            'subtotal' => $priceInfo['subtotal'],
            'serviceCharge' => $priceInfo['service_charge'],
            'discount' => $priceInfo['discount'],
            'total' => $priceInfo['total'],
            'morningDiscount' => $priceInfo['discount'] > 0,
            'table' => $table,
        ]);
    }

    private function calculateOrderPrice($cartItems, $serviceType, $dateTime = null)
    {
        $dateTime = $dateTime ?? now();
        $subtotal = $cartItems->sum(function ($item) {
            $price = isset($item->price) ? $item->price : (isset($item->unit_price) ? $item->unit_price : ($item->menu ? $item->menu->getPriceForTemperature($item->temperature ?? null) : 0));

            return $price * $item->quantity;
        });
        $serviceCharge = 0;

        if ($serviceType === 'take_away') {
            $itemCount = $cartItems->sum('quantity');
            $serviceCharge = $itemCount * 1000;
        }

        $discount = $this->calculateMorningDiscount($subtotal, $dateTime);
        $total = max(0, $subtotal + $serviceCharge - $discount);

        return [
            'subtotal' => $subtotal,
            'service_charge' => $serviceCharge,
            'discount' => $discount,
            'total' => $total,
        ];
    }

    /**
     * Ubah input meja menjadi id_meja yang valid.
     *
     * Input bisa berupa nama_meja ("05", hasil QR Code) maupun id_meja (3).
     * nama_meja dicoba lebih dulu karena kode meja seperti "05" isinya
     * numeric dan akan salah ditafsirkan sebagai id kalau urutannya dibalik.
     *
     * Mengembalikan null bila meja tidak ditemukan atau tidak diisi.
     */
    private function resolveTableId($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        $table = Meja::where('nama_meja', $value)->first();

        if (! $table && is_numeric($value)) {
            $table = Meja::find((int) $value);
        }

        return $table?->id_meja;
    }

    /**
     * Tentukan meja untuk request berjalan.
     *
     * Sumber utama: token QR di session (ditetapkan halaman /order?token=...)
     * sehingga meja ditentukan server dan tidak bisa dimanipulasi lewat form.
     * Fallback: session order_table hasil scan/pilihan URL ?meja= (perilaku
     * lama), lalu input form sebagai cadangan terakhir.
     */
    private function resolveTableForRequest(Request $request): ?Meja
    {
        $token = $request->session()->get('order_table_token');

        if (is_string($token) && $token !== '') {
            try {
                return $this->qrService->validateTokenForOrder($token)->table;
            } catch (TableQrCodeException) {
                // Token sudah dicabut/kedaluwarsa — jatuh ke fallback lama.
            }
        }

        $value = $request->session()->get('order_table')
            ?? $request->query('meja')
            ?? $request->input('meja')
            ?? $request->input('table_id');

        if (is_string($value)) {
            $value = trim($value);
        }

        $tableId = $this->resolveTableId($value === '' || $value === null ? null : $value);

        return $tableId !== null ? Meja::find($tableId) : null;
    }

    private function calculateMorningDiscount($amount, $dateTime)
    {
        $hour = $dateTime->hour;
        if ($hour >= 6 && $hour < 11) {
            return round($amount * 0.05);
        }

        return 0;
    }

    public function store(StoreOrderRequest $request)
    {
        $user = auth()->user();
        $cartItems = app(CartSessionService::class)->getCartItems();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang belanja Anda kosong.',
            ], 400);
        }

        $validated = $request->validated();

        // 1. Stock validation before creating order
        foreach ($cartItems as $item) {
            $menu = $item->menu;
            if (! $menu) {
                return response()->json([
                    'success' => false,
                    'message' => 'Salah satu produk di keranjang tidak valid.',
                ], 400);
            }

            if ($menu->stok < $item->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok menu "'.$menu->nama_menu.'" tidak mencukupi (Sisa stok: '.$menu->stok.' unit).',
                ], 400);
            }
        }

        // 2. Calculations
        $priceInfo = $this->calculateOrderPrice($cartItems, $validated['service_type']);
        $subtotal = $priceInfo['subtotal'];
        $serviceCharge = $priceInfo['service_charge'];
        $discount = $priceInfo['discount'];
        $total = $priceInfo['total'];

        $totalHpp = $cartItems->sum(function ($item) {
            $hpp = $item->menu ? $item->menu->getHppForTemperature($item->temperature ?? null) : 0;

            return $hpp * $item->quantity;
        });
        $profitMargin = max(0, $total - $totalHpp);

        // 3. Meja ditentukan server dari token QR di session (bukan dari input
        //    form); fallback ke ?meja=/session untuk alur lama tanpa QR.
        $table = $this->resolveTableForRequest($request);
        $tableId = $table?->id_meja;

        // 4. Create Order
        $order = Order::create([
            'tanggal' => now(),
            'nama_pelanggan' => $validated['customer_name'] ?: $user->name,
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'] ?? null,
            'table_id' => $tableId,
            'total_harga' => $total,
            'subtotal' => $subtotal,
            'service_charge' => $serviceCharge,
            'discount_amount' => $discount,
            'final_total' => $total,
            'cost_of_goods' => $totalHpp,
            'profit_margin' => $profitMargin,
            // Bayar di kasir: pembayaran menunggu konfirmasi kasir (bukan
            // langsung lunas), QRIS: menunggu settlement dari gateway.
            'status_pembayaran' => 'pending',
            'service_type' => $validated['service_type'],
            'payment_method' => $validated['payment_method'],
            'notes' => $validated['notes'] ?? null,
            'status_order' => 'pending',
            'id_user' => $user->id_user,
        ]);

        // Kode tagihan publik (nomor antrean), unik per pesanan.
        $order->forceFill([
            'order_code' => 'BT-'.str_pad((string) $order->id_order, 4, '0', STR_PAD_LEFT),
        ])->save();

        // 5. Create OrderItems with temperature and note, record historical HPP, and deduct stock
        foreach ($cartItems as $item) {
            $menu = $item->menu;
            $itemUnitPrice = $menu->getPriceForTemperature($item->temperature ?? null);
            $itemHpp = $menu->getHppForTemperature($item->temperature ?? null);

            OrderItem::create([
                'id_order' => $order->id_order,
                'id_menu' => $menu->id_menu,
                'quantity' => $item->quantity,
                'notes' => $item->note,
                'subtotal' => $itemUnitPrice * $item->quantity,
                'hpp' => $itemHpp,
                'hpp_at_sale' => $itemHpp,
                'temperature' => $item->temperature ?? null,
                'note' => $item->note ?? null,
            ]);

            // Deduct stock automatically
            $menu->decrementStock($item->quantity);
        }

        // 6. Clear cart session and database
        app(CartSessionService::class)->clearCart();

        // 7. Broadcast real-time event to cashier tablet
        event(new OrderCreated($order));

        // 8. Return response
        $redirectUrl = ($validated['payment_method'] === 'qris')
            ? route('xendit.qris.redirect', $order->id_order)
            : route('order.waiting', $order->id_order);

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil dibuat!',
            'order_id' => $order->id_order,
            'redirect' => $redirectUrl,
        ]);
    }

    public function receipt(Order $order)
    {
        $order->load('items.menu');

        return view('Customerviews.receipt', compact('order'));
    }

    /**
     * Halaman "pesanan diterima" setelah submit (bayar di kasir): menampilkan
     * kode tagihan/nomor antrean untuk dibawa ke kasir.
     */
    public function waiting(Order $order)
    {
        $this->authorizeOrderOwner($order);
        $order->load(['items.menu', 'table']);

        return view('Customerviews.order-waiting', compact('order'));
    }

    /**
     * Tracking status pesanan untuk pelanggan.
     */
    public function show(Order $order)
    {
        $this->authorizeOrderOwner($order);
        $order->load(['items.menu', 'table']);

        return view('Customerviews.order-status', compact('order'));
    }

    /**
     * Polling status pesanan (JSON) untuk halaman tracking pelanggan.
     */
    public function statusJson(Order $order)
    {
        $this->authorizeOrderOwner($order);

        return response()->json([
            'id_order' => $order->id_order,
            'order_code' => $order->public_code,
            'status_order' => $order->status_order,
            'status_pembayaran' => $order->status_pembayaran,
            'status_label' => $order->statusLabel(),
            'paid' => $order->isPaid(),
            'paid_at' => $order->paid_at?->toISOString(),
        ]);
    }

    /**
     * Kasir mengonfirmasi pembayaran (bayar di kasir) sekaligus meloloskan
     * pesanan ke dapur, lalu membuka struk thermal untuk dicetak.
     */
    public function confirmPayment(Order $order)
    {
        abort_unless(auth()->user()?->isStaff() || auth()->user()?->isAdmin(), 403);

        if (! $order->isPaid()) {
            $order->update([
                'status_pembayaran' => 'paid',
                'paid_at' => now(),
                'status_order' => 'processing',
                'cashier_name' => auth()->user()?->name ?? $order->cashier_name,
            ]);
        }

        return redirect()->route('admin.receipt.print', $order->id_order);
    }

    /**
     * Tolak/langsung proses tanpa menunggu kasir (dari halaman kasir).
     */
    public function markProcessing(Order $order)
    {
        $order->update(['status_order' => in_array($order->status_order, ['pending', 'confirmed', 'processing'], true) ? 'processing' : $order->status_order]);

        return back()->with('success', 'Pesanan masuk proses dapur.');
    }

    /**
     * Dapur menandai pesanan siap diambil/diantar.
     */
    public function markReady(Order $order)
    {
        if (in_array($order->status_order, ['processing', 'ready'], true)) {
            $order->update(['status_order' => 'ready']);
        }

        return back()->with('success', 'Pesanan ditandai siap.');
    }

    /**
     * Pesanan diselesaikan (diserahkan ke pelanggan / meja di-reset).
     */
    public function finishOrder(Order $order)
    {
        $order->update([
            'status_pembayaran' => 'paid',
            'status_order' => 'completed',
            'paid_at' => $order->paid_at ?? now(),
        ]);

        return back()->with('success', 'Pesanan #'.$order->public_code.' selesai.');
    }

    /**
     * Polling pesanan baru untuk peringatan kasir (tanpa WebSocket).
     * Mengembalikan daftar pesanan menunggu pembayaran dengan id > after_id.
     */
    public function poll(Request $request)
    {
        $afterId = max(0, (int) $request->query('after_id', 0));

        $orders = Order::with('table')
            ->where('status_pembayaran', 'pending')
            ->where('id_order', '>', $afterId)
            ->orderBy('id_order')
            ->get();

        $latestId = Order::where('status_pembayaran', 'pending')->max('id_order') ?? $afterId;

        return response()->json([
            'latest_id' => (int) $latestId,
            'count' => $orders->count(),
            'new_orders' => $orders->map(fn (Order $o) => [
                'id' => $o->id_order,
                'code' => $o->public_code,
                'table' => $o->table?->nama_meja,
                'customer' => $o->customer_name ?: $o->nama_pelanggan,
                'total' => (int) $o->total_harga,
                'at' => $o->tanggal->format('H:i'),
            ]),
        ]);
    }

    /**
     * KDS: daftar pesanan terbayar yang sedang di dapur/di antar, dikelompokkan
     * per stasiun berdasarkan kategori menu.
     */
    public function kitchen()
    {
        $orders = Order::with(['table', 'items.menu'])
            ->where('status_pembayaran', 'paid')
            ->whereIn('status_order', ['processing', 'ready'])
            ->latest('tanggal')
            ->get();

        $groups = $this->kitchenService->groupOrdersByStation($orders);

        return view('admin.kitchen', compact('groups', 'orders'));
    }

    /**
     * Tiket dapur (kitchen ticket) per stasiun untuk dicetak.
     */
    public function kitchenTickets(Order $order)
    {
        abort_unless(auth()->user()?->isStaff() || auth()->user()?->isAdmin(), 403);

        $order->load(['table', 'items.menu']);
        $groups = $this->kitchenService->groupOrderByStation($order);

        return view('admin.kitchen-tickets', compact('order', 'groups'));
    }

    private function authorizeOrderOwner(Order $order): void
    {
        $user = auth()->user();

        if ($user && ($user->isAdmin() || $user->isStaff())) {
            return;
        }

        abort_unless($user && (int) $order->id_user === (int) $user->id_user, 403);
    }

    public function history()
    {
        $orders = auth()->user()->orders()->with('items.menu')->latest()->paginate(10);

        return view('Customerviews.order-history', compact('orders'));
    }

    /**
     * Admin: Unified Sales History & Reporting
     */
    public function historyAdmin(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->isAdmin();
        $query = Order::with(['user', 'table', 'items.menu']);

        if (! $isAdmin) {
            $query->where('id_user', $user->id_user);
        }

        // 1. Date Filters
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay(),
            ]);
        } elseif ($request->query('range') === 'today') {
            $query->whereDate('tanggal', Carbon::today());
        }

        // 2. Staff Filter
        if ($request->filled('staff') && $request->staff !== 'all') {
            $query->where(function ($q) use ($request) {
                $q->where('cashier_name', $request->staff)
                    ->orWhereHas('user', function ($sq) use ($request) {
                        $sq->where('name', $request->staff);
                    });
            });
        }

        // 3. Payment Method Filter
        if ($request->filled('payment_method') && $request->payment_method !== 'all') {
            $query->where('payment_method', $request->payment_method);
        }

        // 4. Status Filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status_order', $request->status);
        }

        $historyOrders = $query->latest('tanggal')->paginate(15)->withQueryString();

        // Calculate Header Stats
        $statsQuery = clone $query;
        $totalRevenue = $statsQuery->where('status_order', 'completed')->sum('total_harga');
        $totalOrders = $query->count();

        // Employee list for dropdown
        $staffList = User::whereHas('role', function ($q) {
            $q->whereIn('role_name', ['Admin', 'Staff', 'pegawai']);
        })->get(['name']);

        return view('admin.history', compact('historyOrders', 'totalOrders', 'totalRevenue', 'staffList'));
    }

    public function complete($id_order)
    {
        $order = Order::findOrFail($id_order);
        $order->update(['status_pembayaran' => 'paid', 'status_order' => 'completed']);

        return back()->with('success', 'Pesanan selesai');
    }

    public function cancel($id_order)
    {
        $order = Order::findOrFail($id_order);
        $order->update(['status_order' => 'cancelled']);

        return back()->with('success', 'Pesanan dibatalkan');
    }

    public function index(Request $request)
    {
        $query = Order::where('status_pembayaran', 'pending')
            ->with(['user', 'table', 'items.menu'])
            ->latest();

        // Filter meja: pilih dari dropdown (id_meja)...
        if ($request->filled('meja') && $request->query('meja') !== 'all') {
            $query->where('table_id', (int) $request->query('meja'));
        }

        // ...atau cari bebas: token QR / nama meja (mis. "Meja 05" / "05").
        $search = trim((string) $request->query('q', ''));
        if ($search !== '') {
            $table = $this->findTableByTokenOrName($search);

            if ($table) {
                $query->where('table_id', $table->id_meja);
            } else {
                $query->whereHas('table', function ($q) use ($search) {
                    $q->where('nama_meja', 'like', '%'.$search.'%');
                });
            }
        }

        $orders = $query->paginate(10)->withQueryString();

        $tables = Meja::orderBy('nama_meja')->get(['id_meja', 'nama_meja', 'is_active']);

        return view('admin.orders.index', compact('orders', 'tables'));
    }

    /**
     * API for tablet cashier real-time polling fallback
     */
    public function liveOrders(Request $request)
    {
        $lastId = (int) $request->query('after_id', 0);

        $newOrders = Order::where('id_order', '>', $lastId)
            ->where(function ($q) {
                $q->where('status_pembayaran', 'pending')
                    ->orWhere('status_order', 'pending');
            })
            ->with(['user', 'items.menu'])
            ->latest('id_order')
            ->take(10)
            ->get();

        $formatted = $newOrders->map(function ($order) {
            $tableNumber = 'Meja -';
            if (preg_match('/meja\s*([0-9a-zA-Z_-]+)/i', $order->notes ?? '', $matches)) {
                $tableNumber = 'Meja '.strtoupper($matches[1]);
            } elseif ($order->service_type === 'dine_in') {
                $tableNumber = 'Dine-In (Meja)';
            } else {
                $tableNumber = 'Take Away';
            }

            $itemsSummary = $order->items->map(function ($item) {
                $temp = $item->temperature ? ' ('.ucfirst($item->temperature).')' : '';

                return [
                    'name' => ($item->menu->nama_menu ?? 'Menu').$temp,
                    'quantity' => $item->quantity,
                    'subtotal' => (float) $item->subtotal,
                    'note' => $item->note ?? null,
                ];
            })->toArray();

            return [
                'id_order' => $order->id_order,
                'order_code' => '#ORD-'.$order->id_order,
                'table_number' => $tableNumber,
                'customer_name' => $order->nama_pelanggan ?? ($order->user->name ?? 'Pelanggan Walk-In'),
                'service_type' => $order->service_type ?? 'dine_in',
                'payment_method' => strtoupper($order->payment_method ?? 'CASH'),
                'payment_status' => strtolower($order->status_pembayaran ?? 'pending'),
                'status_order' => strtolower($order->status_order ?? 'pending'),
                'total_amount' => (float) $order->total_harga,
                'total_formatted' => 'Rp '.number_format($order->total_harga ?? 0, 0, ',', '.'),
                'notes' => $order->notes,
                'items' => $itemsSummary,
                'items_count' => $order->items->sum('quantity'),
                'created_at_human' => $order->tanggal
                    ? $order->tanggal->format('H:i')
                    : ($order->created_at ? $order->created_at->format('H:i') : now()->format('H:i')),
                'receipt_url' => route('admin.receipt.view', $order->id_order),
                'complete_url' => route('admin.orders.complete', $order->id_order),
            ];
        });

        $maxId = $newOrders->max('id_order') ?? $lastId;
        $totalPending = Order::where('status_order', 'pending')->orWhere('status_pembayaran', 'pending')->count();

        return response()->json([
            'success' => true,
            'orders' => $formatted,
            'max_id' => $maxId,
            'total_pending' => $totalPending,
        ]);
    }

    /**
     * Cari meja dari token QR (lengkap/parsial) atau nama meja persis.
     * Dipakai filter admin supaya pesanan bisa dicari lewat token QR.
     */
    private function findTableByTokenOrName(string $value): ?Meja
    {
        if (preg_match('/^[0-9a-fA-F-]{8,36}$/', $value)) {
            $qr = TableQrCode::where('token', $value)
                ->orWhere('token', 'like', $value.'%')
                ->orderBy('id_qr_code', 'desc')
                ->first();

            if ($qr?->table) {
                return $qr->table;
            }
        }

        return Meja::where('nama_meja', $value)->first();
    }
}
