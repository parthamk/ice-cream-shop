<?php
session_start();

// The Bouncer: Kick out anyone who isn't logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

require 'db.php';

// Fetch Quick Stats
$statsQuery = $pdo->query("SELECT COUNT(id) AS total_orders, COALESCE(SUM(total_amount + tax + platform_fee), 0) AS total_revenue FROM orders");
$stats = $statsQuery->fetch();

// Fetch All Orders
$ordersQuery = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC");
$orders = $ordersQuery->fetchAll();

// Define available statuses
$statuses = ['Pending', 'Preparing', 'Out for Delivery', 'Delivered'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Frosty Bites</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-container { max-width: 1100px; margin: 3rem auto; padding: 0 5%; }
        
        /* Stats Cards */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 3rem; }
        .stat-card { background: white; padding: 1.5rem; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); text-align: center; border-bottom: 5px solid var(--primary-color); }
        .stat-card i { font-size: 2.5rem; color: var(--primary-color); margin-bottom: 10px; display: inline-block; }
        .stat-card h3 { font-size: 1.2rem; color: #666; margin-bottom: 5px; }
        .stat-card .value { font-size: 2rem; font-weight: bold; color: var(--text-dark); }
        .stat-card.revenue { border-bottom-color: var(--secondary-color); }
        .stat-card.revenue i { color: var(--secondary-color); }

        /* Table */
        .orders-table-wrapper { background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th, td { padding: 15px; border-bottom: 1px solid #eee; vertical-align: top; }
        th { background-color: var(--bg-light); color: var(--text-dark); font-weight: bold; }
        tr:hover { background-color: #fcfcfc; }
        
        .item-list { list-style: none; margin: 0; padding: 0; font-size: 0.9rem; color: #555; }
        .item-list li { margin-bottom: 5px; }
        .item-list li::before { content: "• "; color: var(--primary-color); font-weight: bold; }
        
        .badge { padding: 5px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; }
        .badge-upi { background: #e0f7fa; color: #00838f; }
        .badge-card { background: #fff3e0; color: #ef6c00; }

        /* Status Dropdown */
        .status-select { padding: 5px; border-radius: 5px; border: 1px solid #ccc; font-weight: bold; cursor: pointer; outline: none; transition: 0.3s; }
        .status-select:focus { border-color: var(--primary-color); box-shadow: 0 0 5px rgba(255, 182, 193, 0.5); }
        
        /* Toast Notification */
        #toast { visibility: hidden; min-width: 250px; background-color: #333; color: #fff; text-align: center; border-radius: 5px; padding: 15px; position: fixed; z-index: 1; bottom: 30px; left: 50%; transform: translateX(-50%); }
        #toast.show { visibility: visible; animation: fadein 0.5s, fadeout 0.5s 2.5s; }
        @keyframes fadein { from {bottom: 0; opacity: 0;} to {bottom: 30px; opacity: 1;} }
        @keyframes fadeout { from {bottom: 30px; opacity: 1;} to {bottom: 0; opacity: 0;} }
    </style>
</head>
<body>
    <header>
        <div class="logo"><i class="bi bi-shield-lock"></i> Frosty Bites Admin</div>
        <nav class="nav-links">
            <a href="index.php"><i class="bi bi-shop"></i> View Live Site</a>

            <a href="debug_ssl.php" style="color: #2ecc71;"><i class="bi bi-cpu"></i> System Health</a>
            
            <a href="logout.php" style="color: #e74c3c;"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </nav>
    </header>

    <div class="admin-container">
        
        <div class="stats-grid">
            <div class="stat-card">
                <i class="bi bi-receipt"></i>
                <h3>Total Orders</h3>
                <div class="value"><?php echo $stats['total_orders']; ?></div>
            </div>
            <div class="stat-card revenue">
                <i class="bi bi-graph-up-arrow"></i>
                <h3>Total Revenue</h3>
                <div class="value">$<?php echo number_format($stats['total_revenue'], 2); ?></div>
            </div>
        </div>

        <div class="orders-table-wrapper">
            <h2 style="margin-bottom: 1.5rem; color: var(--primary-color);">Manage Orders</h2>
            
            <?php if (count($orders) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date & Time</th>
                            <th>Items Ordered</th>
                            <th>Status</th>
                            <th>Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><strong>#FB-<?php echo $order['id']; ?></strong></td>
                                <td><?php echo date('M d, Y g:i A', strtotime($order['created_at'])); ?></td>
                                
                                <td>
                                    <ul class="item-list">
                                        <?php
                                        $stmtItems = $pdo->prepare("SELECT quantity, product_name FROM order_items WHERE order_id = ?");
                                        $stmtItems->execute([$order['id']]);
                                        $items = $stmtItems->fetchAll();
                                        foreach ($items as $item) {
                                            echo "<li>{$item['quantity']}x " . htmlspecialchars($item['product_name']) . "</li>";
                                        }
                                        ?>
                                    </ul>
                                </td>
                                
                                <td>
                                    <select class="status-select" data-order-id="<?php echo $order['id']; ?>">
                                        <?php foreach ($statuses as $status): ?>
                                            <option value="<?php echo $status; ?>" <?php echo ($order['status'] === $status) ? 'selected' : ''; ?>>
                                                <?php echo $status; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                
                                <td><strong>$<?php echo number_format($order['total_amount'] + $order['tax'] + $order['platform_fee'], 2); ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align: center; color: #888; padding: 2rem 0;">No orders have been placed yet.</p>
            <?php endif; ?>
        </div>

    </div>

    <div id="toast">Status updated successfully!</div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const dropdowns = document.querySelectorAll('.status-select');
            const toast = document.getElementById('toast');

            dropdowns.forEach(dropdown => {
                dropdown.addEventListener('change', function() {
                    const orderId = this.getAttribute('data-order-id');
                    const newStatus = this.value;

                    // Disable dropdown while saving
                    this.disabled = true;

                    fetch('update_status.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ order_id: orderId, status: newStatus })
                    })
                    .then(response => response.json())
                    .then(data => {
                        this.disabled = false; // Re-enable
                        if (data.success) {
                            // Show success toast
                            toast.className = "show";
                            setTimeout(() => { toast.className = toast.className.replace("show", ""); }, 3000);
                        } else {
                            alert('Failed to update status: ' + data.error);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        this.disabled = false;
                        alert('A network error occurred.');
                    });
                });
            });
        });
    </script>
</body>
</html>
