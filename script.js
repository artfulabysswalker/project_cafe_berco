document.addEventListener('DOMContentLoaded', () => {
    const cartItemsContainer = document.querySelector('.cart-items-container');
    const subtotalElement = document.querySelector('.summary-val');
    const totalElement = document.querySelector('.total-val');
    const cartBadge = document.getElementById('cart-count') || document.getElementById('cart-badge');
    const floatBadge = document.getElementById('floating-badge');
    
    // Elemen untuk halaman Checkout & Success
    const checkoutList = document.querySelector('.summary-list');
    const btnPay = document.querySelector('.btn-pay');

    // 1. Fungsi Update Badge (Angka di Ikon Keranjang)
    window.updateUI = function() {
        let cart = JSON.parse(localStorage.getItem('berco_cart')) || [];
        let count = cart.reduce((total, item) => total + item.quantity, 0);

        if (cartBadge) {
            cartBadge.innerText = count;
            cartBadge.style.display = count > 0 ? 'inline-block' : 'none';
        }
        if (floatBadge) {
            floatBadge.innerText = count;
            floatBadge.style.display = count > 0 ? 'inline-block' : 'none';
        }
    };

    // 2. Fungsi Menambahkan Produk ke Storage
    window.addToCart = function(button) {
        const card = button.closest('.menu-card');
        const name = card.querySelector('h3').innerText;
        const price = parseInt(card.querySelector('.price').innerText.replace(/[^0-9]/g, ''));
        const image = card.querySelector('img').src;
        const id = name.toLowerCase().replace(/\s/g, '-');

        let cart = JSON.parse(localStorage.getItem('berco_cart')) || [];
        let itemIndex = cart.findIndex(i => i.id === id);

        if (itemIndex > -1) {
            cart[itemIndex].quantity++;
        } else {
            cart.push({ id, name, price, image, quantity: 1 });
        }

        localStorage.setItem('berco_cart', JSON.stringify(cart));
        updateUI();

        // Animasi feedback tombol
        const originalHTML = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check"></i> Berhasil';
        button.style.background = "#28a745";
        setTimeout(() => {
            button.innerHTML = originalHTML;
            button.style.background = "#000";
        }, 800);
    };

    // 3. Fungsi Render Halaman Keranjang
    function renderCart() {
        if (!cartItemsContainer) { updateUI(); return; }

        let cart = JSON.parse(localStorage.getItem('berco_cart')) || [];

        if (cart.length === 0) {
            cartItemsContainer.innerHTML = '<p style="text-align:center; padding: 40px; color: #718096;">Keranjangmu kosong. <br><a href="menu.html" style="color: #bf4f08; font-weight: bold;">Pesan menu sekarang!</a></p>';
            subtotalElement.innerText = "Rp 0";
            totalElement.innerText = "Rp 0";
            updateUI();
            return;
        }

        cartItemsContainer.innerHTML = cart.map(item => `
            <div class="cart-card-item" data-id="${item.id}">
                <div class="cart-item-img"><img src="${item.image}" alt="${item.name}"></div>
                <div class="cart-item-details">
                    <h3>${item.name}</h3>
                    <div class="quantity-control">
                        <button class="btn-qty" onclick="changeQty('${item.id}', -1)">-</button>
                        <span class="qty-number">${item.quantity}</span>
                        <button class="btn-qty" onclick="changeQty('${item.id}', 1)">+</button>
                    </div>
                </div>
                <div class="cart-item-right">
                    <button class="btn-delete" onclick="removeItem('${item.id}')"><i class="fa-regular fa-trash-can"></i></button>
                    <div class="cart-item-price">
                        <span class="price-unit">Rp ${item.price.toLocaleString('id-ID')} x ${item.quantity}</span>
                        <span class="price-total">Rp ${(item.price * item.quantity).toLocaleString('id-ID')}</span>
                    </div>
                </div>
            </div>
        `).join('');

        let total = cart.reduce((sum, i) => sum + (i.price * i.quantity), 0);
        const formatted = "Rp " + total.toLocaleString('id-ID');
        subtotalElement.innerText = formatted;
        totalElement.innerText = formatted;
        updateUI();
    }

    window.changeQty = function(id, delta) {
        let cart = JSON.parse(localStorage.getItem('berco_cart'));
        let idx = cart.findIndex(i => i.id === id);
        cart[idx].quantity += delta;
        if (cart[idx].quantity < 1) cart[idx].quantity = 1;
        localStorage.setItem('berco_cart', JSON.stringify(cart));
        renderCart();
    };

    window.removeItem = function(id) {
        let cart = JSON.parse(localStorage.getItem('berco_cart'));
        localStorage.setItem('berco_cart', JSON.stringify(cart.filter(i => i.id !== id)));
        renderCart();
    };

    // 4. Fungsi Render Ringkasan di Checkout (Menjumlahkan Semua)
    function renderCheckout() {
        if (!checkoutList) return;
        let cart = JSON.parse(localStorage.getItem('berco_cart')) || [];
        
        if (cart.length > 0) {
            checkoutList.innerHTML = cart.map(item => `
                <div class="summary-item">
                    <span>${item.name} x${item.quantity}</span>
                    <span>Rp ${(item.price * item.quantity).toLocaleString('id-ID')}</span>
                </div>
            `).join('');
            
            let total = cart.reduce((sum, i) => sum + (i.price * i.quantity), 0);
            const formatted = "Rp " + total.toLocaleString('id-ID');
            
            // Update teks total di halaman checkout
            const subtotalLabel = document.querySelector('.summary-total-row:not(.final-total) span:last-child');
            const totalLabel = document.querySelector('.final-total span:last-child');
            
            if (subtotalLabel) subtotalLabel.innerText = formatted;
            if (totalLabel) totalLabel.innerText = formatted;
            if (btnPay) btnPay.innerText = `Bayar ${formatted}`;
        }
    }

    // 5. Fungsi Render Struk di Success Page
    function renderReceipt() {
        const receiptBody = document.querySelector('.receipt-body');
        const itemsList = document.getElementById('receipt-items-list');
        if (!receiptBody || !itemsList) return;

        let cart = JSON.parse(localStorage.getItem('berco_cart')) || [];
        if (cart.length === 0) return;

        // Render daftar item ke nota
        itemsList.innerHTML = cart.map(item => `
            <div class="receipt-item">
                <div class="item-info">
                    <strong>${item.name}</strong>
                    <span>Rp ${item.price.toLocaleString('id-ID')} x ${item.quantity}</span>
                </div>
                <span class="item-price">Rp ${(item.price * item.quantity).toLocaleString('id-ID')}</span>
            </div>
        `).join('');

        let total = cart.reduce((sum, i) => sum + (i.price * i.quantity), 0);
        const formatted = "Rp " + total.toLocaleString('id-ID');

        // Update nilai total di struk
        const receiptTotals = receiptBody.querySelectorAll('.receipt-total-row span:last-child');
        receiptTotals.forEach(el => el.innerText = formatted);
        
        // Kosongkan keranjang setelah nota berhasil dibuat
        localStorage.removeItem('berco_cart');
        updateUI();
    }

    renderCart();
    renderCheckout();
    renderReceipt();
    updateUI();

    // --- Logika Filter Menu (Kategori & Search) ---
    const categoryButtons = document.querySelectorAll('.category-tabs button');
    const menuCards = document.querySelectorAll('.menu-card');
    const searchInput = document.getElementById('search-input');
    const priceFilter = document.getElementById('price-filter');
    const favFilterBtn = document.getElementById('fav-filter-btn');
    const menuCountText = document.querySelector('.menu-count');
    let onlyFavorites = false;

    function applyFilters() {
        const activeBtn = document.querySelector('.category-tabs button.active');
        const selectedCategory = activeBtn ? activeBtn.getAttribute('data-category') : 'all';
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const selectedPriceRange = priceFilter ? priceFilter.value : 'all';
        const favs = JSON.parse(localStorage.getItem('berco_favs')) || [];
        
        let count = 0;

        menuCards.forEach(card => {
            const cardCategory = card.getAttribute('data-category');
            const cardName = card.querySelector('h3').innerText.toLowerCase();
            const cardPrice = parseInt(card.querySelector('.price').innerText.replace(/[^0-9]/g, ''));
            const cardId = card.getAttribute('data-id');

            const matchesCategory = (selectedCategory === 'all' || cardCategory === selectedCategory);
            const matchesSearch = cardName.includes(searchTerm);
            
            let matchesPrice = true;
            if (selectedPriceRange === 'low') matchesPrice = cardPrice < 15000;
            if (selectedPriceRange === 'high') matchesPrice = cardPrice >= 15000;

            const matchesFav = !onlyFavorites || favs.includes(cardId);

            if (matchesCategory && matchesSearch && matchesPrice && matchesFav) {
                card.style.display = 'block';
                count++;
            } else {
                card.style.display = 'none';
            }
        });

        if (menuCountText) menuCountText.innerText = `Menampilkan ${count} menu`;
    }

    categoryButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            categoryButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            applyFilters();
        });
    });

    if (searchInput) searchInput.addEventListener('input', applyFilters);
    if (priceFilter) priceFilter.addEventListener('change', applyFilters);
    
    if (favFilterBtn) {
        favFilterBtn.addEventListener('click', () => {
            onlyFavorites = !onlyFavorites;
            favFilterBtn.classList.toggle('active');
            applyFilters();
        });
    }

    // --- Logika Operasional Favorit ---
    window.toggleFavorite = function(id, btn) {
        let favs = JSON.parse(localStorage.getItem('berco_favs')) || [];
        const index = favs.indexOf(id);
        
        if (index > -1) {
            favs.splice(index, 1);
            btn.innerHTML = '<i class="far fa-heart"></i>';
            btn.classList.remove('is-fav');
        } else {
            favs.push(id);
            btn.innerHTML = '<i class="fas fa-heart"></i>';
            btn.classList.add('is-fav');
        }
        
        localStorage.setItem('berco_favs', JSON.stringify(favs));
        if (onlyFavorites) applyFilters();
    };

    function updateFavButtonsUI() {
        const favs = JSON.parse(localStorage.getItem('berco_favs')) || [];
        menuCards.forEach(card => {
            const id = card.getAttribute('data-id');
            if (favs.includes(id)) {
                const btn = card.querySelector('.wishlist-btn');
                btn.innerHTML = '<i class="fas fa-heart"></i>';
                btn.classList.add('is-fav');
            }
        });
    }

    updateFavButtonsUI();
});