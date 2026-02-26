<?php
require 'db.php';
$stmtCategories = $pdo->query("SELECT * FROM categories");
$categories = $stmtCategories->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frosty Bites | Ice Cream & Bakery</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="logo"><i class="bi bi-shop"></i> Frosty Bites</div>
        <nav class="nav-links">
            <a href="#home">Home</a>
            <a href="#menu">Menu</a>
            <div class="cart-wrapper">
                <div id="cart-icon"><i class="bi bi-cart3"></i> <span id="cart-count">0</span></div>
                <div id="cart-dropdown" class="hidden">
                    <div id="cart-items"></div>
                    <button id="place-order-btn" class="btn hidden" onclick="window.location.href='checkout.php'">Place Order</button>
                </div>
            </div>
        </nav>
    </header>

    <section id="home" class="hero">
        <div class="hero-content">
            <h1>Scoops of Happiness</h1>
            <p>Handcrafted, artisanal desserts made locally. Taste the joy in every bite!</p>
            <a href="#menu" class="btn" style="margin-top: 10px;">Explore Menu</a>
        </div>
    </section>

    <section id="menu" class="menu-section">
        <?php foreach ($categories as $category): ?>
            <h2 style="margin-top: 3rem; color: var(--primary-color); border-bottom: 2px solid #eee; padding-bottom: 10px;">
                Our <?php echo htmlspecialchars($category['name']); ?>
            </h2>
            <div class="grid-container">
                <?php
                $stmtProducts = $pdo->prepare("SELECT * FROM products WHERE category_id = :cat_id");
                $stmtProducts->execute(['cat_id' => $category['id']]);
                $products = $stmtProducts->fetchAll();
                
                if (count($products) > 0):
                    foreach ($products as $product): 
                ?>
                        <div class="card">
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p style="font-size: 0.9rem; color: #666; margin-bottom: 10px;"><?php echo htmlspecialchars($product['description']); ?></p>
                            <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
                            
                            <div class="card-actions">
                                <button class="btn-small add-to-cart" 
                                        data-name="<?php echo htmlspecialchars($product['name']); ?>" 
                                        data-price="<?php echo htmlspecialchars($product['price']); ?>" 
                                        data-img="<?php echo htmlspecialchars($product['image_url']); ?>">
                                    Add to Order
                                </button>
                                <span class="item-qty-badge" id="badge-<?php echo str_replace(' ', '-', htmlspecialchars($product['name'])); ?>"></span>
                            </div>
                        </div>
                <?php 
                    endforeach; 
                else: 
                ?>
                    <p style="text-align: center; width: 100%; color: #888;">More items coming soon!</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </section>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Frosty Bites. All rights reserved.</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>
