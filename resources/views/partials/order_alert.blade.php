{{-- REAL-TIME ORDER LISTENER & ALERT MODAL FOR CASHIER / TABLET --}}
<div id="realtimeOrderAlertContainer">
    {{-- Floating Audio Toggle Button (Tablet / Desktop) --}}
    <div class="fixed bottom-4 right-4 z-40 flex items-center gap-2">
        <button id="btnToggleAudioAlert" onclick="toggleCashierAudioAlert()" 
            class="bg-white/95 backdrop-blur border border-stone-200 shadow-md hover:shadow-lg px-3.5 py-2 rounded-full text-xs font-mono text-stone-700 hover:text-stone-900 flex items-center gap-2 cursor-pointer transition-all duration-200 select-none group"
            title="Klik untuk menyalakan/mematikan suara lonceng notifikasi">
            <span id="audioAlertIcon" class="text-sm transition-transform group-hover:scale-110">🔔</span>
            <span id="audioAlertText" class="font-medium">Audio Alert: Aktif</span>
            <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse" id="audioAlertLed"></span>
        </button>
    </div>

    {{-- INTERACTIVE ORDER POP-UP MODAL --}}
    <div id="orderIncomingModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/60 backdrop-blur-xs hidden opacity-0 transition-opacity duration-300">
        <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-stone-200 overflow-hidden transform scale-95 transition-transform duration-300" id="orderIncomingModalBox">
            
            {{-- Modal Header --}}
            <div class="bg-[#18181B] text-white px-6 py-4 flex items-center justify-between border-b border-stone-800">
                <div class="flex items-center space-x-3">
                    <span class="flex h-3 w-3 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#C27835] opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-[#C27835]"></span>
                    </span>
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-widest font-mono text-white">Pesanan Baru Masuk!</h3>
                        <p class="text-[10px] text-stone-400 font-sans">Self-Order Meja Pelanggan</p>
                    </div>
                </div>
                <button type="button" onclick="closeOrderIncomingModal()" class="text-stone-400 hover:text-white text-xl font-bold p-1 leading-none transition-colors" title="Tutup">&times;</button>
            </div>

            {{-- Modal Body --}}
            <div class="p-6 space-y-4 max-h-[72vh] overflow-y-auto">
                
                {{-- Quick Info Badges --}}
                <div class="flex items-center justify-between bg-amber-50/60 border border-amber-200/80 p-3.5 rounded-xl">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-10 h-10 rounded-lg bg-[#C27835] text-white flex items-center justify-center font-bold text-sm shadow-xs">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <div>
                            <span class="text-[10px] text-stone-500 uppercase font-mono block">Lokasi / Meja</span>
                            <span id="modalTableNumber" class="text-base font-bold text-[#9A3412] font-mono">Meja -</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] text-stone-500 uppercase font-mono block">Order ID & Waktu</span>
                        <span id="modalOrderId" class="text-xs font-bold text-stone-900 font-mono">#ORD-0</span>
                        <span id="modalOrderTime" class="text-[11px] text-stone-500 block font-mono">--:-- WIB</span>
                    </div>
                </div>

                {{-- Customer & Payment Info --}}
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div class="p-3 bg-stone-50 rounded-xl border border-stone-100">
                        <span class="text-stone-400 block text-[10px] uppercase font-mono mb-0.5">Pemesan:</span>
                        <span id="modalCustomerName" class="font-semibold text-stone-800 text-xs truncate block">-</span>
                    </div>
                    <div class="p-3 bg-stone-50 rounded-xl border border-stone-100">
                        <span class="text-stone-400 block text-[10px] uppercase font-mono mb-0.5">Metode Bayar:</span>
                        <span id="modalPaymentInfo" class="font-semibold text-stone-800 text-xs block">-</span>
                    </div>
                </div>

                {{-- Ordered Items Summary --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <h4 class="text-[11px] font-bold text-stone-700 uppercase font-mono">Daftar Item:</h4>
                        <span id="modalItemsCount" class="text-[10px] font-mono text-stone-500 bg-stone-100 px-2 py-0.5 rounded">0 Item</span>
                    </div>
                    <div id="modalItemsList" class="space-y-2 divide-y divide-stone-100 border border-stone-200 rounded-xl p-3 bg-stone-50/40 text-xs">
                        {{-- Injected dynamically --}}
                    </div>
                </div>

                {{-- Customer Notes --}}
                <div id="modalNotesContainer" class="hidden bg-amber-50 border border-amber-200 text-amber-900 p-3 rounded-xl text-xs">
                    <span class="font-bold block text-[10px] uppercase font-mono mb-0.5"><i class="fas fa-note-sticky mr-1"></i>Catatan Khusus:</span>
                    <p id="modalNotesText" class="italic text-stone-800 font-sans"></p>
                </div>

                {{-- Total Amount --}}
                <div class="flex items-center justify-between pt-3 border-t border-stone-200">
                    <div>
                        <span class="text-[10px] text-stone-400 uppercase font-mono block">Total Tagihan</span>
                        <span class="text-xs text-stone-500 font-mono" id="modalServiceType">DINE IN</span>
                    </div>
                    <span id="modalTotalAmount" class="text-lg font-bold text-stone-900 font-mono">Rp 0</span>
                </div>
            </div>

            {{-- Modal Action Footer --}}
            <div class="bg-stone-50 px-6 py-3.5 border-t border-stone-200 flex items-center justify-between gap-2">
                <button type="button" onclick="closeOrderIncomingModal()" 
                    class="px-4 py-2 bg-white border border-stone-200 rounded-lg text-xs font-medium text-stone-700 hover:bg-stone-100 transition cursor-pointer">
                    Tutup
                </button>
                <div class="flex items-center gap-2">
                    <a id="modalReceiptBtn" href="#" target="_blank" 
                        class="px-4 py-2 bg-stone-200 hover:bg-stone-300 text-stone-800 font-medium rounded-lg text-xs transition inline-flex items-center gap-1.5 cursor-pointer">
                        <i class="fas fa-print text-xs"></i>
                        <span>Lihat Struk</span>
                    </a>
                    <button id="modalAcceptBtn" type="button" onclick="processIncomingOrder()" 
                        class="px-5 py-2 bg-[#C27835] hover:bg-[#9A3412] text-white font-bold rounded-lg text-xs transition shadow-sm inline-flex items-center gap-1.5 cursor-pointer">
                        <i class="fas fa-check text-xs"></i>
                        <span>Terima & Proses</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- JAVASCRIPT REAL-TIME LISTENER & AUDIO ENGINE --}}
<script>
(function() {
    let audioCtx = null;
    let isAudioAlertEnabled = true;
    let currentActiveOrder = null;
    const handledOrderIds = new Set();
    let maxKnownOrderId = 0;

    // 1. WEB AUDIO API SYNTHESIZER (Autoplay Safe)
    function getAudioContext() {
        if (!audioCtx) {
            const AudioContextClass = window.AudioContext || window.webkitAudioContext;
            if (AudioContextClass) {
                audioCtx = new AudioContextClass();
            }
        }
        if (audioCtx && audioCtx.state === 'suspended') {
            audioCtx.resume();
        }
        return audioCtx;
    }

    // Unlock audio context on first user touch/click
    const unlockAudio = () => {
        getAudioContext();
    };
    document.addEventListener('click', unlockAudio, { once: true, passive: true });
    document.addEventListener('touchstart', unlockAudio, { once: true, passive: true });
    document.addEventListener('keydown', unlockAudio, { once: true, passive: true });

    // Play 2-tone harmonic chime
    function playCashierChime() {
        if (!isAudioAlertEnabled) return;
        const ctx = getAudioContext();
        if (!ctx) return;

        try {
            const now = ctx.currentTime;

            // Tone 1: 587.33 Hz (D5) - Bell Strike
            const osc1 = ctx.createOscillator();
            const gain1 = ctx.createGain();
            osc1.type = 'sine';
            osc1.frequency.setValueAtTime(587.33, now);
            gain1.gain.setValueAtTime(0.3, now);
            gain1.gain.exponentialRampToValueAtTime(0.0001, now + 0.5);
            osc1.connect(gain1);
            gain1.connect(ctx.destination);
            osc1.start(now);
            osc1.stop(now + 0.5);

            // Tone 2: 880.00 Hz (A5) - Harmonic Chime
            const osc2 = ctx.createOscillator();
            const gain2 = ctx.createGain();
            osc2.type = 'sine';
            osc2.frequency.setValueAtTime(880.00, now + 0.12);
            gain2.gain.setValueAtTime(0.35, now + 0.12);
            gain2.gain.exponentialRampToValueAtTime(0.0001, now + 0.9);
            osc2.connect(gain2);
            gain2.connect(ctx.destination);
            osc2.start(now + 0.12);
            osc2.stop(now + 0.9);
        } catch (err) {
            console.warn('Audio chime warning:', err);
        }
    }

    // Toggle Audio Alert On/Off
    window.toggleCashierAudioAlert = function() {
        getAudioContext();
        isAudioAlertEnabled = !isAudioAlertEnabled;
        const icon = document.getElementById('audioAlertIcon');
        const text = document.getElementById('audioAlertText');
        const led = document.getElementById('audioAlertLed');

        if (isAudioAlertEnabled) {
            if (icon) icon.textContent = '🔔';
            if (text) text.textContent = 'Audio Alert: Aktif';
            if (led) led.className = 'inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse';
            playCashierChime(); // Sample sound test
        } else {
            if (icon) icon.textContent = '🔕';
            if (text) text.textContent = 'Audio Alert: Hening';
            if (led) led.className = 'inline-block w-2 h-2 rounded-full bg-stone-300';
        }
    };

    // 2. MODAL DISPLAY LOGIC
    window.showOrderIncomingModal = function(order) {
        currentActiveOrder = order;

        // Populate fields
        const elTable = document.getElementById('modalTableNumber');
        const elOrderId = document.getElementById('modalOrderId');
        const elOrderTime = document.getElementById('modalOrderTime');
        const elCustName = document.getElementById('modalCustomerName');
        const elPayment = document.getElementById('modalPaymentInfo');
        const elTotal = document.getElementById('modalTotalAmount');
        const elReceiptBtn = document.getElementById('modalReceiptBtn');
        const elServiceType = document.getElementById('modalServiceType');
        const elItemsCount = document.getElementById('modalItemsCount');

        if (elTable) elTable.textContent = order.table_number || 'Meja -';
        if (elOrderId) elOrderId.textContent = order.order_code || ('#ORD-' + order.id_order);
        if (elOrderTime) elOrderTime.textContent = (order.created_at_human || '--:--') + ' WIB';
        if (elCustName) elCustName.textContent = order.customer_name || 'Pelanggan Walk-In';
        
        const isPaid = ['paid', 'sudah'].includes(order.payment_status?.toLowerCase());
        if (elPayment) {
            elPayment.innerHTML = `<span class="${isPaid ? 'text-emerald-700' : 'text-amber-700'}">${order.payment_method || 'CASH'} (${isPaid ? 'LUNAS' : 'PENDING'})</span>`;
        }
        
        if (elTotal) elTotal.textContent = order.total_formatted || ('Rp ' + new Intl.NumberFormat('id-ID').format(order.total_amount || 0));
        if (elReceiptBtn) elReceiptBtn.href = order.receipt_url || '#';
        if (elServiceType) elServiceType.textContent = (order.service_type || 'dine_in').toUpperCase().replace('_', ' ');
        if (elItemsCount) elItemsCount.textContent = (order.items_count || order.items?.length || 0) + ' Item';

        // Render Item List
        const itemsList = document.getElementById('modalItemsList');
        if (itemsList && Array.isArray(order.items)) {
            itemsList.innerHTML = order.items.map(item => `
                <div class="flex justify-between items-start py-1">
                    <div>
                        <span class="font-medium text-stone-900">${item.quantity}x ${item.name}</span>
                        ${item.note ? `<span class="block text-[11px] text-[#C27835] italic">"${item.note}"</span>` : ''}
                    </div>
                    <span class="font-mono text-stone-700 shrink-0 ml-2">Rp ${new Intl.NumberFormat('id-ID').format(item.subtotal)}</span>
                </div>
            `).join('');
        }

        // Notes
        const notesContainer = document.getElementById('modalNotesContainer');
        const notesText = document.getElementById('modalNotesText');
        if (notesContainer && notesText) {
            if (order.notes && order.notes.trim() !== '') {
                notesText.textContent = order.notes;
                notesContainer.classList.remove('hidden');
            } else {
                notesContainer.classList.add('hidden');
            }
        }

        // Show Animation
        const modal = document.getElementById('orderIncomingModal');
        const modalBox = document.getElementById('orderIncomingModalBox');
        if (modal && modalBox) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modalBox.classList.remove('scale-95');
                modalBox.classList.add('scale-100');
            }, 10);
        }

        // Trigger Audio Chime
        playCashierChime();
    };

    window.closeOrderIncomingModal = function() {
        const modal = document.getElementById('orderIncomingModal');
        const modalBox = document.getElementById('orderIncomingModalBox');
        if (modal && modalBox) {
            modal.classList.add('opacity-0');
            modalBox.classList.remove('scale-100');
            modalBox.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
    };

    window.processIncomingOrder = function() {
        if (!currentActiveOrder) return;
        const completeUrl = currentActiveOrder.complete_url;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        if (completeUrl) {
            fetch(completeUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken || '',
                    'Content-Type': 'application/json',
                    'X-HTTP-Method-Override': 'PUT',
                    'Accept': 'application/json'
                }
            }).then(() => {
                closeOrderIncomingModal();
                if (window.location.pathname.includes('/orders')) {
                    window.location.reload();
                }
            }).catch(err => {
                console.error(err);
                closeOrderIncomingModal();
            });
        } else {
            closeOrderIncomingModal();
        }
    };

    // 3. REAL-TIME DOM TABLE UPDATE (Prepend to #ordersTable)
    function appendOrderToTable(order) {
        const tbody = document.querySelector('#ordersTable tbody');
        if (!tbody) return;

        // Remove empty state placeholder row if present
        const emptyRow = tbody.querySelector('td[colspan]');
        if (emptyRow) emptyRow.closest('tr').remove();

        // Check if row already exists
        const existingRow = tbody.querySelector(`tr[data-order-id="${order.id_order}"]`);
        if (existingRow) return;

        const isPaid = ['paid', 'sudah'].includes(order.payment_status?.toLowerCase());
        const payBadge = isPaid 
            ? '<span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px]">LUNAS</span>'
            : '<span class="px-2 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-200 text-[10px]">BELUM BAYAR</span>';

        const serviceBadge = (order.service_type === 'take_away')
            ? '<span class="text-[10px] font-mono px-2 py-0.5 rounded bg-stone-100 text-stone-700 border border-stone-200">TAKE AWAY</span>'
            : '<span class="text-[10px] font-mono px-2 py-0.5 rounded bg-stone-100 text-stone-700 border border-stone-200">DINE IN</span>';

        const newTr = document.createElement('tr');
        newTr.className = 'order-row bg-amber-50/70 hover:bg-stone-50 transition-all duration-300 animate-pulse';
        newTr.setAttribute('data-status', order.status_order || 'pending');
        newTr.setAttribute('data-order-id', order.id_order);

        newTr.innerHTML = `
            <td class="px-4 py-3 font-semibold text-stone-900">${order.order_code}</td>
            <td class="px-4 py-3 font-sans">
                <div class="font-medium text-stone-900">${order.customer_name}</div>
                <div class="text-[11px] text-[#C27835] font-bold">${order.table_number}</div>
            </td>
            <td class="px-4 py-3">${serviceBadge}</td>
            <td class="px-4 py-3 font-semibold text-stone-900">${order.total_formatted}</td>
            <td class="px-4 py-3 text-stone-600 uppercase">${order.payment_method}</td>
            <td class="px-4 py-3">${payBadge}</td>
            <td class="px-4 py-3"><span class="px-2 py-0.5 rounded bg-amber-50 text-amber-800 border border-amber-200 text-[10px]">DIPROSES</span></td>
            <td class="px-4 py-3 text-stone-500 text-[11px]">${order.created_at_human}</td>
            <td class="px-4 py-3 text-right space-x-1.5">
                <a href="${order.receipt_url}" class="inline-block text-stone-600 hover:text-stone-900 p-1 border border-stone-200 rounded hover:bg-stone-100" title="Lihat Struk">
                    <i class="fas fa-eye text-xs"></i>
                </a>
                <form method="POST" action="${order.complete_url}" class="inline-block m-0">
                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''}">
                    <input type="hidden" name="_method" value="PUT">
                    <button type="submit" class="p-1 text-emerald-700 border border-emerald-300 rounded hover:bg-emerald-50 cursor-pointer" title="Selesaikan Pesanan" onclick="return confirm('Tandai pesanan ${order.order_code} selesai?')">
                        <i class="fas fa-check text-xs"></i>
                    </button>
                </form>
            </td>
        `;

        tbody.prepend(newTr);
        setTimeout(() => newTr.classList.remove('animate-pulse', 'bg-amber-50/70'), 4000);
    }

    // 4. INCOMING ORDER DISPATCHER (Deduplicated)
    function handleNewIncomingOrder(payload) {
        if (!payload || !payload.id_order) return;
        if (handledOrderIds.has(payload.id_order)) return;

        handledOrderIds.add(payload.id_order);
        if (payload.id_order > maxKnownOrderId) {
            maxKnownOrderId = payload.id_order;
        }

        // Trigger UI and Audio
        showOrderIncomingModal(payload);
        appendOrderToTable(payload);
    }

    // 5. INITIALIZE LISTENERS & DUAL-ENGINE FALLBACK
    document.addEventListener('DOMContentLoaded', () => {
        // Collect existing order IDs from table to prevent duplicate trigger on initial load
        document.querySelectorAll('#ordersTable tbody tr[data-order-id]').forEach(tr => {
            const id = parseInt(tr.getAttribute('data-order-id'), 10);
            if (!isNaN(id)) {
                handledOrderIds.add(id);
                if (id > maxKnownOrderId) maxKnownOrderId = id;
            }
        });

        // A. WEBSOCKET / LARAVEL ECHO LISTENER
        if (typeof window.Echo !== 'undefined') {
            try {
                window.Echo.private('cashier.orders')
                    .listen('.OrderCreated', (data) => handleNewIncomingOrder(data))
                    .listen('OrderCreated', (data) => handleNewIncomingOrder(data));
            } catch (e) {
                console.warn('Echo listener initialization warning:', e);
            }
        }

        // B. REAL-TIME POLLING ENGINE (Ensures 100% instant alerts even when WebSocket server is offline)
        const pollLiveOrders = () => {
            fetch(`{{ route('admin.orders.live') }}?after_id=${maxKnownOrderId}`, {
                headers: { 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && Array.isArray(data.orders)) {
                    data.orders.reverse().forEach(order => handleNewIncomingOrder(order));
                    if (data.max_id > maxKnownOrderId) {
                        maxKnownOrderId = data.max_id;
                    }
                }
            })
            .catch(() => {
                // Ignore silent network errors during background poll
            });
        };

        // First poll after 2 seconds, then periodically every 3.5 seconds
        setTimeout(pollLiveOrders, 2000);
        setInterval(pollLiveOrders, 3500);
    });
})();
</script>
