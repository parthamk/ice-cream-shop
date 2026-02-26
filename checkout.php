<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | Frosty Bites</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="logo"><i class="bi bi-shop"></i> Frosty Bites</div>
        <nav class="nav-links"><a href="index.php">Back to Menu</a></nav>
    </header>

    <section class="checkout-section">
        <h2 style="margin-bottom: 20px; color: #ff6b81; text-align:center;">Order Summary</h2>
        <div id="checkout-items"></div>
        
        <div class="price-breakdown">
            <p>Subtotal: $<span id="subtotal">0.00</span></p>
            <p>Tax (5%): $<span id="tax">0.00</span></p>
            <p>Platform Fee: $<span id="platform-fee">1.50</span></p>
            <h3>Total: $<span id="total-price">0.00</span></h3>
        </div>

        <div class="payment-section">
            <h3 style="margin-bottom: 15px;">Select Payment Method</h3>
            <label><input type="radio" name="payment" value="upi"> Pay via UPI</label>
            <label><input type="radio" name="payment" value="card"> Credit/Debit Card</label>
            
            <div id="payment-details" class="hidden"></div>
        </div>

        <button id="complete-order-btn" class="btn btn-disabled" disabled>Complete Order</button>
    </section>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Frosty Bites. All rights reserved.</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>