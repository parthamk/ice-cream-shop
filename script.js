document.addEventListener('DOMContentLoaded', () => {
    let cart = JSON.parse(localStorage.getItem('iceCreamCart')) || {};
    let currentSubtotal = 0;
    let currentTax = 0;
    let currentTotal = 0;
    
    const cartIcon = document.getElementById('cart-icon');
    const cartDropdown = document.getElementById('cart-dropdown');
    const currentPage = window.location.pathname;

    function saveCart() {
        localStorage.setItem('iceCreamCart', JSON.stringify(cart));
        updateCartUI();
    }

    function updateCartUI() {
        const totalItems = Object.values(cart).reduce((sum, item) => sum + item.qty, 0);
        const cartCountEl = document.getElementById('cart-count');
        if (cartCountEl) cartCountEl.textContent = totalItems;

        document.querySelectorAll('.item-qty-badge').forEach(badge => badge.textContent = '');
        for (const [name, item] of Object.entries(cart)) {
            const formattedName = name.replace(/ /g, '-');
            const badge = document.getElementById(`badge-${formattedName}`);
            if (badge && item.qty > 0) badge.textContent = `x${item.qty}`;
        }

        renderCartDropdown();
        if (currentPage.includes('checkout.php')) renderCheckout();
    }

    function renderCartDropdown() {
        const cartItemsContainer = document.getElementById('cart-items');
        const placeOrderBtn = document.getElementById('place-order-btn');
        if (!cartItemsContainer) return;

        cartItemsContainer.innerHTML = '';
        const items = Object.values(cart);

        if (items.length === 0) {
            cartItemsContainer.innerHTML = '<p style="text-align:center;">Your cart is empty.</p>';
            placeOrderBtn.classList.add('hidden');
            return;
        }

        placeOrderBtn.classList.remove('hidden');
        placeOrderBtn.style.width = '100%';
        placeOrderBtn.style.marginTop = '10px';

        items.forEach(item => {
            const div = document.createElement('div');
            div.className = 'cart-item';
            div.innerHTML = `
                <img src="${item.img}" alt="${item.name}">
                <div class="cart-item-details">
                    <strong>${item.name}</strong><br>$${parseFloat(item.price).toFixed(2)}
                </div>
                <div class="cart-controls">
                    <button onclick="changeQty('${item.name}', -1)"><i class="bi bi-dash"></i></button>
                    <span>${item.qty}</span>
                    <button onclick="changeQty('${item.name}', 1)"><i class="bi bi-plus"></i></button>
                </div>
            `;
            cartItemsContainer.appendChild(div);
        });
    }

    window.changeQty = function(name, delta) {
        if (cart[name]) {
            cart[name].qty += delta;
            if (cart[name].qty <= 0) delete cart[name];
            saveCart();
        }
    };

    if (cartIcon) {
        cartIcon.addEventListener('click', () => { cartDropdown.classList.toggle('hidden'); });
    }

    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            const name = this.getAttribute('data-name');
            const price = parseFloat(this.getAttribute('data-price'));
            const img = this.getAttribute('data-img');

            if (!cart[name]) {
                cart[name] = { name, price, img, qty: 1 };
            } else { cart[name].qty += 1; }
            saveCart();
            if (cartDropdown.classList.contains('hidden')) { cartDropdown.classList.remove('hidden'); }
        });
    });

    function renderCheckout() {
        const checkoutItems = document.getElementById('checkout-items');
        if (!checkoutItems) return;

        checkoutItems.innerHTML = '';
        currentSubtotal = 0;

        Object.values(cart).forEach(item => {
            const itemTotal = item.price * item.qty;
            currentSubtotal += itemTotal;
            checkoutItems.innerHTML += `
                <div class="cart-item" style="border-bottom: 1px solid #ddd; padding: 10px 0;">
                    <div style="flex-grow: 1; text-align: left;"><strong>${item.qty}x ${item.name}</strong></div>
                    <div>$${itemTotal.toFixed(2)}</div>
                </div>
            `;
        });

        currentTax = currentSubtotal * 0.05;
        const platformFee = 1.50;
        currentTotal = currentSubtotal + currentTax + platformFee;

        document.getElementById('subtotal').textContent = currentSubtotal.toFixed(2);
        document.getElementById('tax').textContent = currentTax.toFixed(2);
        document.getElementById('total-price').textContent = currentTotal.toFixed(2);
    }

    const paymentRadios = document.querySelectorAll('input[name="payment"]');
    const paymentDetails = document.getElementById('payment-details');
    const completeBtn = document.getElementById('complete-order-btn');

    paymentRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            paymentDetails.classList.remove('hidden');
            if (this.value === 'upi') {
                paymentDetails.innerHTML = `<div class="dummy-details">Selected UPI:<br><strong>abcd@bank.upi</strong></div>`;
            } else if (this.value === 'card') {
                paymentDetails.innerHTML = `<div class="dummy-details">Selected Card:<br><strong>1111 2222 3333 4444</strong><br>Exp: 12/30 | CVV: 888</div>`;
            }
            if (completeBtn && Object.keys(cart).length > 0) {
                completeBtn.disabled = false;
                completeBtn.classList.remove('btn-disabled');
                completeBtn.classList.add('btn-green');
            }
        });
    });

    if (completeBtn) {
        completeBtn.addEventListener('click', () => {
            // Change button text to show it's processing
            completeBtn.textContent = 'Processing...';
            completeBtn.disabled = true;

            const selectedPayment = document.querySelector('input[name="payment"]:checked').value;
            
            // Build the data payload to send to PHP
            const orderData = {
                cart: Object.values(cart),
                total: currentTotal,
                tax: currentTax,
                paymentMethod: selectedPayment
            };

            // Send to database
            fetch('process_order.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(orderData)
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    // Wipe the cart and redirect to tracking with the database Order ID
                    localStorage.removeItem('iceCreamCart');
                    window.location.href = 'tracking.php?order_id=' + data.order_id;
                } else {
                    alert('Error saving order: ' + data.error);
                    completeBtn.textContent = 'Complete Order';
                    completeBtn.disabled = false;
                }
            })
            .catch(error => console.error('Error:', error));
        });
    }

    updateCartUI();
});