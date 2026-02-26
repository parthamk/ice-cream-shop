<?php
require 'db.php'; // Connect to the database

// Safely get the order ID from the URL
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$status = 'Pending'; // Default status

// Fetch the current status from the database
if ($order_id > 0) {
    $stmt = $pdo->prepare("SELECT status FROM orders WHERE id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch();
    
    if ($order) {
        $status = $order['status'];
    } else {
        die("<h2 style='text-align:center; padding: 50px;'>Order not found!</h2>");
    }
} else {
    // If someone visits tracking.php without an ID, send them back to the menu
    header("Location: index.php");
    exit();
}

// Logic to determine which steps should light up in the UI
$step1_active = true; // Order placed is always active
$step2_active = in_array($status, ['Preparing', 'Out for Delivery', 'Delivered']);
$step3_active = in_array($status, ['Out for Delivery', 'Delivered']);
$step4_active = ($status === 'Delivered');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if ($status !== 'Delivered'): ?>
        <meta http-equiv="refresh" content="10">
    <?php endif; ?>
    <title>Order Tracking | Frosty Bites</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="logo"><i class="bi bi-shop"></i> Frosty Bites</div>
        <nav class="nav-links"><a href="index.php">Back to Home</a></nav>
    </header>

    <section class="tracking-section">
        <h2 style="color: var(--primary-color); text-align:center; margin-bottom: 2rem;">Order Tracking</h2>
        
        <div class="tracking-status">
            <div class="step <?php echo $step1_active ? 'active' : ''; ?>">
                <i class="bi bi-card-checklist"></i> Order Placed
            </div>
            <div class="step <?php echo $step2_active ? 'active' : ''; ?>">
                <i class="bi bi-box-seam"></i> Preparing Goodies
            </div>
            <div class="step <?php echo $step3_active ? 'active' : ''; ?>">
                <i class="bi bi-truck"></i> Out for Delivery
            </div>
            <div class="step <?php echo $step4_active ? 'active' : ''; ?>">
                <i class="bi bi-check-circle-fill"></i> Delivered
            </div>
        </div>
        
        <div class="mt-2" style="text-align: center; margin-top: 3rem;">
            <p style="font-size: 1.1rem;">
                Tracking ID: <strong style="color: var(--primary-color);">#FB-<?php echo $order_id; ?></strong>
            </p>
            <p style="color: #666; font-size: 0.9rem; margin-top: 10px;">
                <?php 
                if ($status === 'Delivered') {
                    echo "Enjoy your treats! This page will no longer auto-refresh.";
                } else {
                    echo "Checking for updates automatically...";
                }
                ?>
            </p>
        </div>
    </section>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Frosty Bites. All rights reserved.</p>
    </footer>
</body>
</html>
