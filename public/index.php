<?php
session_start();
require_once __DIR__ . '/../app/helpers.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Cafe Berco — Order</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
<style>
:root {
  --bg:      #0F0D0A;
  --surface: #1A1713;
  --card:    #221F1A;
  --border:  #2E2A24;
  --gold:    #C9975A;
  --gold2:   #E8B87A;
  --cream:   #F5ECD7;
  --muted:   #7A6E62;
  --text:    #EDE6DA;
  --green:   #5A8C6A;
  --radius:  14px;
}
*{margin:0;padding:0;box-sizing:border-box}
body{background:var(--bg);color:var(--text);font-family:'DM Sans',sans-serif;min-height:100vh;overflow-x:hidden}

/* HEADER */
.header{background:linear-gradient(135deg,#1A1410,#2A1F14,#1A1410);border-bottom:1px solid var(--border);padding:0 2rem;display:flex;align-items:center;justify-content:space-between;height:72px;position:sticky;top:0;z-index:100}
.logo{display:flex;align-items:center;gap:12px}
.logo-icon{width:40px;height:40px;background:linear-gradient(135deg,var(--gold),var(--gold2));border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px}
.logo-text{font-family:'Playfair Display',serif;font-size:22px;font-weight:900;background:linear-gradient(135deg,var(--gold),var(--gold2));-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.logo-sub{font-size:11px;color:var(--muted);letter-spacing:2px;text-transform:uppercase;margin-top:-2px}
.cart-btn{background:linear-gradient(135deg,var(--gold),var(--gold2));color:#1A1410;border:none;border-radius:12px;padding:10px 20px;font-weight:600;font-size:14px;cursor:pointer;display:flex;align-items:center;gap:8px;transition:all .2s;font-family:'DM Sans',sans-serif}
.cart-btn:hover{transform:translateY(-1px);box-shadow:0 8px 24px rgba(201,151,90,.3)}
.cart-count{background:#1A1410;color:var(--gold);border-radius:50%;width:20px;height:20px;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center}

/* LAYOUT */
.main{max-width:1200px;margin:0 auto;padding:2rem}
.hero{text-align:center;padding:3rem 1rem 2rem;background:linear-gradient(180deg,rgba(201,151,90,.06),transparent);border-radius:20px;margin-bottom:2rem;border:1px solid var(--border)}
.hero h1{font-family:'Playfair Display',serif;font-size:clamp(2rem,5vw,3.5rem);font-weight:900;line-height:1.1;margin-bottom:.75rem}
.hero h1 span{background:linear-gradient(135deg,var(--gold),var(--gold2));-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.hero p{color:var(--muted);font-size:15px;letter-spacing:.5px}

/* FILTER (US 2.2) */
.filter-bar{display:flex;gap:.75rem;margin-bottom:1.5rem;flex-wrap:wrap;align-items:center}
.filter-btn{background:var(--card);border:1px solid var(--border);color:var(--muted);border-radius:10px;padding:8px 18px;font-size:13px;cursor:pointer;transition:all .2s;font-family:'DM Sans',sans-serif;font-weight:500}
.filter-btn:hover,.filter-btn.active{background:var(--gold);color:#1A1410;border-color:var(--gold);font-weight:600}
.search-wrap{margin-left:auto;position:relative}
.search-wrap input{background:var(--card);border:1px solid var(--border);color:var(--text);border-radius:10px;padding:8px 16px 8px 36px;font-size:13px;width:220px;font-family:'DM Sans',sans-serif;outline:none;transition:border-color .2s}
.search-wrap input:focus{border-color:var(--gold)}
.search-wrap input::placeholder{color:var(--muted)}
.search-icon{position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:14px;pointer-events:none}

/* CATEGORY */
.cat-label{font-family:'Playfair Display',serif;font-size:18px;font-weight:700;color:var(--gold);margin:1.5rem 0 1rem;display:flex;align-items:center;gap:10px}
.cat-label::after{content:'';flex:1;height:1px;background:var(--border)}

/* MENU GRID (US 2.1) */
.menu-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(270px,1fr));gap:1rem;margin-bottom:2rem}
.menu-card{background:var(--card);border:1px solid var(--border);border-radius:var(--radius);padding:1.25rem;display:flex;flex-direction:column;gap:.75rem;transition:all .25s;position:relative;overflow:hidden}
.menu-card:hover{border-color:var(--gold);transform:translateY(-2px);box-shadow:0 12px 32px rgba(0,0,0,.3)}
.card-top{display:flex;align-items:flex-start;justify-content:space-between;gap:.5rem}
.card-emoji{font-size:36px;line-height:1}
.tag{font-size:10px;font-weight:600;letter-spacing:1px;text-transform:uppercase;padding:3px 8px;border-radius:6px}
.tag-bestseller{background:rgba(201,151,90,.2);color:var(--gold)}
.tag-new{background:rgba(90,140,106,.2);color:#7AC490}
.tag-healthy{background:rgba(90,140,106,.15);color:#7AC490}
.tag-favorite{background:rgba(201,151,90,.15);color:var(--gold2)}
.card-name{font-weight:600;font-size:15px;line-height:1.3}
.card-desc{font-size:12px;color:var(--muted);line-height:1.5;flex:1}
.card-footer{display:flex;align-items:center;justify-content:space-between;margin-top:.25rem}
.card-price{font-family:'Playfair Display',serif;font-size:17px;font-weight:700;color:var(--gold)}
.add-btn{background:linear-gradient(135deg,var(--gold),var(--gold2));color:#1A1410;border:none;border-radius:9px;width:34px;height:34px;font-size:18px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .2s;font-weight:700}
.add-btn:hover{transform:scale(1.1);box-shadow:0 4px 12px rgba(201,151,90,.4)}
.no-result{text-align:center;padding:3rem;color:var(--muted);font-size:15px;grid-column:1/-1}

/* CART SIDEBAR (US 2.3) */
.overlay{position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:200;opacity:0;pointer-events:none;transition:opacity .3s;backdrop-filter:blur(4px)}
.overlay.show{opacity:1;pointer-events:all}
.cart-sidebar{position:fixed;top:0;right:0;bottom:0;width:400px;max-width:95vw;background:var(--surface);border-left:1px solid var(--border);z-index:201;transform:translateX(100%);transition:transform .35s cubic-bezier(.4,0,.2,1);display:flex;flex-direction:column}
.cart-sidebar.show{transform:translateX(0)}
.sidebar-head{padding:1.5rem;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.sidebar-head h2{font-family:'Playfair Display',serif;font-size:20px;font-weight:700}
.close-btn{background:var(--card);border:1px solid var(--border);color:var(--text);border-radius:8px;width:34px;height:34px;cursor:pointer;font-size:16px;display:flex;align-items:center;justify-content:center;transition:all .2s}
.close-btn:hover{background:var(--border)}
.cart-items{flex:1;overflow-y:auto;padding:1rem 1.5rem}
.cart-empty{text-align:center;padding:3rem 1rem;color:var(--muted)}
.cart-empty .emoji{font-size:48px;display:block;margin-bottom:1rem}
.cart-item{display:flex;align-items:center;gap:.75rem;padding:.875rem 0;border-bottom:1px solid var(--border)}
.ci-emoji{font-size:24px;width:36px;text-align:center}
.ci-info{flex:1;min-width:0}
.ci-name{font-size:13px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.ci-price{font-size:12px;color:var(--gold)}
.ci-controls{display:flex;align-items:center;gap:.5rem}
.qty-btn{background:var(--card);border:1px solid var(--border);color:var(--text);border-radius:7px;width:28px;height:28px;cursor:pointer;font-size:14px;display:flex;align-items:center;justify-content:center;transition:all .2s}
.qty-btn:hover{background:var(--gold);color:#1A1410;border-color:var(--gold)}
.qty-val{font-size:13px;font-weight:600;min-width:16px;text-align:center}
.del-btn{background:rgba(140,58,58,.15);border:1px solid rgba(140,58,58,.3);color:#E57373;border-radius:7px;width:28px;height:28px;cursor:pointer;font-size:12px;display:flex;align-items:center;justify-content:center;transition:all .2s}
.del-btn:hover{background:rgba(140,58,58,.3)}
.cart-footer{padding:1.25rem 1.5rem;border-top:1px solid var(--border)}
.price-row{display:flex;justify-content:space-between;font-size:13px;color:var(--muted);margin-bottom:.5rem}
.price-row.total{font-size:16px;font-weight:700;color:var(--text);border-top:1px solid var(--border);padding-top:.75rem;margin-top:.25rem}
.checkout-btn{width:100%;background:linear-gradient(135deg,var(--gold),var(--gold2));color:#1A1410;border:none;border-radius:12px;padding:14px;font-size:15px;font-weight:700;cursor:pointer;margin-top:1rem;transition:all .2s;font-family:'DM Sans',sans-serif}
.checkout-btn:hover{transform:translateY(-1px);box-shadow:0 8px 24px rgba(201,151,90,.35)}

/* MODAL */
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:300;display:flex;align-items:center;justify-content:center;padding:1rem;opacity:0;pointer-events:none;transition:opacity .3s;backdrop-filter:blur(6px)}
.modal-overlay.show{opacity:1;pointer-events:all}
.modal{background:var(--surface);border:1px solid var(--border);border-radius:20px;width:100%;max-width:480px;max-height:90vh;overflow-y:auto;transform:scale(.95);transition:transform .3s}
.modal-overlay.show .modal{transform:scale(1)}
.modal-head{padding:1.5rem;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.modal-head h2{font-family:'Playfair Display',serif;font-size:20px}
.modal-body{padding:1.5rem}
.form-group{margin-bottom:1rem}
.form-group label{display:block;font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:.4rem}
.form-group input{width:100%;background:var(--card);border:1px solid var(--border);color:var(--text);border-radius:10px;padding:10px 14px;font-size:14px;font-family:'DM Sans',sans-serif;outline:none;transition:border-color .2s}
.form-group input:focus{border-color:var(--gold)}
.pay-methods{display:grid;grid-template-columns:repeat(3,1fr);gap:.5rem;margin-top:.4rem}
.pay-opt{background:var(--card);border:1px solid var(--border);border-radius:10px;padding:.75rem .5rem;text-align:center;cursor:pointer;transition:all .2s;font-size:12px;font-weight:500}
.pay-opt.selected{background:rgba(201,151,90,.15);border-color:var(--gold);color:var(--gold)}
.pay-opt:hover{border-color:var(--gold)}
.pay-icon{font-size:20px;display:block;margin-bottom:.25rem}
.order-summary{background:var(--card);border-radius:12px;padding:1rem;margin-bottom:1rem}
.summary-row{display:flex;justify-content:space-between;font-size:13px;margin-bottom:.4rem;color:var(--muted)}
.summary-row.total{font-weight:700;color:var(--text);font-size:15px;border-top:1px solid var(--border);padding-top:.5rem;margin-top:.25rem}
.place-btn{width:100%;background:linear-gradient(135deg,var(--gold),var(--gold2));color:#1A1410;border:none;border-radius:12px;padding:14px;font-size:15px;font-weight:700;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all .2s}
.place-btn:hover{box-shadow:0 8px 24px rgba(201,151,90,.4)}

/* RECEIPT */
.receipt{padding:1.5rem}
.receipt-header{text-align:center;margin-bottom:1.5rem}
.receipt-logo{font-family:'Playfair Display',serif;font-size:24px;font-weight:900;color:var(--gold)}
.receipt-status{background:rgba(90,140,106,.15);color:#7AC490;border:1px solid rgba(90,140,106,.3);border-radius:20px;padding:4px 14px;font-size:12px;font-weight:600;display:inline-block;margin:.5rem 0}
.receipt-id{font-size:12px;color:var(--muted);letter-spacing:1px}
.receipt-divider{border:none;border-top:1px dashed var(--border);margin:1rem 0}
.receipt-meta{display:grid;grid-template-columns:1fr 1fr;gap:.5rem;margin-bottom:1rem}
.receipt-meta-item label{font-size:10px;color:var(--muted);text-transform:uppercase;letter-spacing:1px}
.receipt-meta-item p{font-size:13px;font-weight:500;margin-top:2px}
.receipt-item{display:flex;justify-content:space-between;font-size:13px;padding:.3rem 0;border-bottom:1px solid var(--border)}
.receipt-item:last-child{border:none}
.receipt-totals{margin-top:.75rem}
.receipt-total-row{display:flex;justify-content:space-between;font-size:13px;color:var(--muted);margin-bottom:.3rem}
.receipt-total-row.grand{font-size:16px;font-weight:700;color:var(--text);border-top:1px solid var(--border);padding-top:.5rem;margin-top:.25rem}
.receipt-footer{text-align:center;margin-top:1.5rem;font-size:12px;color:var(--muted);line-height:1.8}
.print-btn{width:100%;background:var(--card);border:1px solid var(--border);color:var(--text);border-radius:12px;padding:12px;font-size:14px;font-weight:600;cursor:pointer;margin-top:.75rem;font-family:'DM Sans',sans-serif;transition:all .2s}
.print-btn:hover{border-color:var(--gold);color:var(--gold)}

/* TOAST */
.toast{position:fixed;bottom:2rem;left:50%;transform:translateX(-50%) translateY(20px);background:var(--card);border:1px solid var(--gold);color:var(--gold);border-radius:12px;padding:.75rem 1.25rem;font-size:13px;font-weight:600;z-index:500;opacity:0;transition:all .3s;pointer-events:none;white-space:nowrap}
.toast.show{opacity:1;transform:translateX(-50%) translateY(0)}
::-webkit-scrollbar{width:4px}
::-webkit-scrollbar-track{background:var(--bg)}
::-webkit-scrollbar-thumb{background:var(--border);border-radius:4px}
</style>
</head>
<body>

<!-- HEADER -->
<header class="header">
  <div class="logo">
    <div class="logo-icon">☕</div>
    <div>
      <div class="logo-text">Cafe Berco</div>
      <div class="logo-sub">Est. 2024 · Banyuwangi</div>
    </div>
  </div>
  <button class="cart-btn" onclick="openCart()">
    🛒 Keranjang <span class="cart-count" id="cartCount">0</span>
  </button>
</header>

<!-- MAIN -->
<div class="main">
  <div class="hero">
    <h1>Selamat Datang di<br><span>Cafe Berco</span></h1>
    <p>Nikmati cita rasa terbaik — pilih menu favoritmu</p>
  </div>

  <!-- US 2.2: Filter -->
  <div class="filter-bar">
    <button class="filter-btn active" onclick="filterMenu('all',this)">🍽 Semua</button>
    <button class="filter-btn" onclick="filterMenu('food',this)">🍔 Makanan</button>
    <button class="filter-btn" onclick="filterMenu('drinks',this)">☕ Minuman</button>
    <div class="search-wrap">
      <span class="search-icon">🔍</span>
      <input type="text" id="searchInput" placeholder="Cari menu..." oninput="searchMenu(this.value)"/>
    </div>
  </div>

  <!-- US 2.1: Browse Menu -->
  <div id="menuContainer"></div>
</div>

<!-- CART SIDEBAR (US 2.3) -->
<div class="overlay" id="overlay" onclick="closeCart()"></div>
<div class="cart-sidebar" id="cartSidebar">
  <div class="sidebar-head">
    <h2>🛒 Keranjang Pesanan</h2>
    <button class="close-btn" onclick="closeCart()">✕</button>
  </div>
  <div class="cart-items" id="cartItems"></div>
  <div class="cart-footer" id="cartFooter"></div>
</div>

<!-- CHECKOUT MODAL (US 2.4) -->
<div class="modal-overlay" id="checkoutModal">
  <div class="modal">
    <div class="modal-head">
      <h2>Konfirmasi Pesanan</h2>
      <button class="close-btn" onclick="closeCheckout()">✕</button>
    </div>
    <div class="modal-body" id="checkoutBody"></div>
  </div>
</div>

<!-- RECEIPT MODAL (US 2.4) -->
<div class="modal-overlay" id="receiptModal">
  <div class="modal">
    <div id="receiptContent"></div>
  </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast"></div>

<script>
const API = 'routes/api.php';
let currentFilter = 'all', currentSearch = '', cartData = {items:[],total:0,count:0}, selectedPayment = 'Cash';
const fmt = n => 'Rp ' + n.toLocaleString('id-ID');

function showToast(msg) {
  const t = document.getElementById('toast');
  t.textContent = msg; t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 2000);
}

// ── US 2.1 + 2.2 ─────────────────────────────────────────
async function loadMenu() {
  const p = new URLSearchParams({action:'menu'});
  if (currentFilter !== 'all') p.set('category', currentFilter);
  if (currentSearch) p.set('search', currentSearch);
  const res  = await fetch(`${API}?${p}`);
  const data = await res.json();
  renderMenu(data);
}

function renderMenu(data) {
  const labels = {food:'🍽 Makanan', drinks:'☕ Minuman'};
  let html = '';
  const hasItems = Object.values(data).some(a => a.length > 0);
  if (!hasItems) {
    document.getElementById('menuContainer').innerHTML = '<div class="menu-grid"><div class="no-result">😔 Menu tidak ditemukan</div></div>';
    return;
  }
  for (const [cat, items] of Object.entries(data)) {
    if (!items.length) continue;
    html += `<div class="cat-label">${labels[cat]||cat}</div><div class="menu-grid">`;
    for (const item of items) {
      const tc = item.tag ? 'tag-'+item.tag.toLowerCase() : '';
      html += `
      <div class="menu-card">
        <div class="card-top">
          <span class="card-emoji">${item.emoji}</span>
          ${item.tag ? `<span class="tag ${tc}">${item.tag}</span>` : ''}
        </div>
        <div class="card-name">${item.name}</div>
        <div class="card-desc">${item.desc}</div>
        <div class="card-footer">
          <div class="card-price">${fmt(item.price)}</div>
          <button class="add-btn" onclick="addToCart('${item.id}')">+</button>
        </div>
      </div>`;
    }
    html += '</div>';
  }
  document.getElementById('menuContainer').innerHTML = html;
}

function filterMenu(cat, btn) {
  currentFilter = cat;
  document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  loadMenu();
}

let st;
function searchMenu(v) { clearTimeout(st); currentSearch = v; st = setTimeout(loadMenu, 300); }

// ── US 2.3: Cart ─────────────────────────────────────────
async function loadCart() {
  cartData = await (await fetch(`${API}?action=cart`)).json();
  document.getElementById('cartCount').textContent = cartData.count;
  renderCart();
}

function renderCart() {
  const ci = document.getElementById('cartItems');
  const cf = document.getElementById('cartFooter');
  if (!cartData.items.length) {
    ci.innerHTML = `<div class="cart-empty"><span class="emoji">🛒</span><p>Keranjang masih kosong</p><p style="font-size:12px;color:var(--muted);margin-top:.5rem">Tambahkan menu favoritmu!</p></div>`;
    cf.innerHTML = ''; return;
  }
  ci.innerHTML = cartData.items.map(i => `
    <div class="cart-item">
      <span class="ci-emoji">${i.emoji}</span>
      <div class="ci-info">
        <div class="ci-name">${i.name}</div>
        <div class="ci-price">${fmt(i.subtotal)}</div>
      </div>
      <div class="ci-controls">
        <button class="qty-btn" onclick="updateCart('${i.id}',${i.qty-1})">−</button>
        <span class="qty-val">${i.qty}</span>
        <button class="qty-btn" onclick="updateCart('${i.id}',${i.qty+1})">+</button>
        <button class="del-btn" onclick="removeCart('${i.id}')">🗑</button>
      </div>
    </div>`).join('');
  const tax = Math.round(cartData.total * 0.1);
  cf.innerHTML = `
    <div class="price-row"><span>Subtotal</span><span>${fmt(cartData.total)}</span></div>
    <div class="price-row"><span>Pajak (10%)</span><span>${fmt(tax)}</span></div>
    <div class="price-row total"><span>Total</span><span>${fmt(cartData.total+tax)}</span></div>
    <button class="checkout-btn" onclick="openCheckout()">Lanjut ke Pembayaran →</button>`;
}

async function addToCart(id) {
  await fetch(`${API}?action=add`,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({item_id:id,qty:1})});
  await loadCart(); showToast('✓ Ditambahkan ke keranjang');
}
async function updateCart(id,qty) {
  await fetch(`${API}?action=update`,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({item_id:id,qty})});
  await loadCart();
}
async function removeCart(id) {
  await fetch(`${API}?action=remove`,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({item_id:id})});
  await loadCart(); showToast('Item dihapus');
}
function openCart()  { document.getElementById('cartSidebar').classList.add('show'); document.getElementById('overlay').classList.add('show'); }
function closeCart() { document.getElementById('cartSidebar').classList.remove('show'); document.getElementById('overlay').classList.remove('show'); }

// ── US 2.4: Checkout ─────────────────────────────────────
function openCheckout() {
  if (!cartData.items.length) return;
  closeCart();
  const tax = Math.round(cartData.total * 0.1);
  document.getElementById('checkoutBody').innerHTML = `
    <div class="form-group"><label>Nama Pemesan</label><input type="text" id="custName" placeholder="Masukkan nama kamu"/></div>
    <div class="form-group"><label>Nomor Meja</label><input type="text" id="custTable" placeholder="Contoh: Meja 5"/></div>
    <div class="form-group">
      <label>Metode Pembayaran</label>
      <div class="pay-methods">
        ${[['Cash','💵','Tunai'],['QRIS','📱','QRIS'],['Debit','💳','Kartu']].map(([v,ic,lb])=>
          `<div class="pay-opt ${v==='Cash'?'selected':''}" onclick="selectPay('${v}',this)"><span class="pay-icon">${ic}</span>${lb}</div>`).join('')}
      </div>
    </div>
    <div class="order-summary">
      ${cartData.items.map(i=>`<div class="summary-row"><span>${i.emoji} ${i.name} ×${i.qty}</span><span>${fmt(i.subtotal)}</span></div>`).join('')}
      <div class="summary-row total"><span>Total Bayar</span><span>${fmt(cartData.total+tax)}</span></div>
    </div>
    <button class="place-btn" onclick="placeOrder()">✓ Konfirmasi Pesanan</button>`;
  document.getElementById('checkoutModal').classList.add('show');
}
function closeCheckout() { document.getElementById('checkoutModal').classList.remove('show'); }
function selectPay(v,el) { selectedPayment=v; document.querySelectorAll('.pay-opt').forEach(e=>e.classList.remove('selected')); el.classList.add('selected'); }

async function placeOrder() {
  const name  = document.getElementById('custName').value.trim()||'Tamu';
  const table = document.getElementById('custTable').value.trim()||'-';
  const r = await (await fetch(`${API}?action=checkout`,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({name,table,payment:selectedPayment})})).json();
  closeCheckout(); showReceipt(r); await loadCart();
}

function showReceipt(r) {
  const pi = {Cash:'💵',QRIS:'📱',Debit:'💳'}[r.payment]||'💳';
  document.getElementById('receiptContent').innerHTML = `
    <div class="receipt">
      <div class="receipt-header">
        <div class="receipt-logo">☕ Cafe Berco</div>
        <div style="font-size:11px;color:var(--muted);margin:.25rem 0">Banyuwangi, East Java</div>
        <div class="receipt-status">${r.status}</div>
        <div class="receipt-id">Order ID: ${r.order_id}</div>
      </div>
      <hr class="receipt-divider"/>
      <div class="receipt-meta">
        <div class="receipt-meta-item"><label>Nama</label><p>${r.name}</p></div>
        <div class="receipt-meta-item"><label>Meja</label><p>${r.table}</p></div>
        <div class="receipt-meta-item"><label>Waktu</label><p>${r.timestamp}</p></div>
        <div class="receipt-meta-item"><label>Bayar</label><p>${pi} ${r.payment}</p></div>
      </div>
      <hr class="receipt-divider"/>
      ${r.items.map(i=>`<div class="receipt-item"><span>${i.emoji} ${i.name} ×${i.qty}</span><span>${fmt(i.subtotal)}</span></div>`).join('')}
      <div class="receipt-totals">
        <div class="receipt-total-row"><span>Subtotal</span><span>${fmt(r.subtotal)}</span></div>
        <div class="receipt-total-row"><span>Pajak (10%)</span><span>${fmt(r.tax)}</span></div>
        <div class="receipt-total-row grand"><span>Total Bayar</span><span>${fmt(r.total)}</span></div>
      </div>
      <hr class="receipt-divider"/>
      <div class="receipt-footer"><p>Terima kasih telah memesan di Cafe Berco! ☕</p><p>Pesananmu sedang disiapkan dengan penuh cinta 🧡</p></div>
      <button class="print-btn" onclick="window.print()">🖨 Cetak Struk</button>
      <button class="print-btn" onclick="closeReceipt()">✕ Tutup</button>
    </div>`;
  document.getElementById('receiptModal').classList.add('show');
}
function closeReceipt() { document.getElementById('receiptModal').classList.remove('show'); }

loadMenu(); loadCart();
</script>
</body>
</html>~