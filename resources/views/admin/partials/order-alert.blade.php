@php
    $lastOrderId = \App\Models\Order::where('status_pembayaran', 'pending')->max('id_order') ?? 0;
@endphp

@push('scripts')
<script>
(function () {
    var pollUrl = @json(route('admin.orders.poll'));
    var lastId = {{ (int) $lastOrderId }};
    var unread = 0;

    function chime() {
        try {
            var Ctx = window.AudioContext || window.webkitAudioContext;
            var ctx = new Ctx();
            function play(freq, t, dur) {
                var o = ctx.createOscillator();
                var g = ctx.createGain();
                o.type = 'sine';
                o.frequency.value = freq;
                o.connect(g); g.connect(ctx.destination);
                g.gain.setValueAtTime(0.0001, ctx.currentTime + t);
                g.gain.exponentialRampToValueAtTime(0.2, ctx.currentTime + t + 0.02);
                g.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + t + dur);
                o.start(ctx.currentTime + t); o.stop(ctx.currentTime + t + dur + 0.05);
            }
            play(880, 0, 0.18); play(1174, 0.2, 0.18); play(1568, 0.4, 0.3);
        } catch (e) {}
    }

    function toast(msg) {
        var box = document.createElement('div');
        box.style.cssText = 'position:fixed;right:16px;bottom:16px;z-index:9999;background:#7f1d1d;color:#fff;padding:12px 16px;border-radius:10px;box-shadow:0 10px 25px rgba(0,0,0,.25);font-size:13px;font-weight:600;min-width:220px;';
        box.textContent = '🔔 ' + msg;
        document.body.appendChild(box);
        setTimeout(function () { box.remove(); }, 4500);
    }

    async function tick() {
        try {
            var r = await fetch(pollUrl + '?after_id=' + lastId, { headers: { 'Accept': 'application/json' } });
            if (!r.ok) return;
            var d = await r.json();
            var fresh = d.new_orders || [];
            if (fresh.length) {
                chime();
                fresh.forEach(function (o) {
                    toast('Pesanan baru ' + o.code + ' — ' + (o.table || 'Take Away') + ' (Rp ' + new Intl.NumberFormat('id-ID').format(o.total) + ')');
                });
            }
            if (d.latest_id > lastId) lastId = d.latest_id;
        } catch (e) {}
    }

    setInterval(tick, 5000);
    tick();
})();
</script>
@endpush