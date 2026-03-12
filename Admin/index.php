<?php
include 'admin_header.php';

// Fetch Statistics
$total_products = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
$total_orders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
$total_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE role='customer'")->fetch_assoc()['count'];
$total_revenue = $conn->query("SELECT SUM(total) as total FROM orders WHERE status != 'cancelled'")->fetch_assoc()['total'];

$low_stock = $conn->query("SELECT * FROM products WHERE stock < 5 ORDER BY stock ASC LIMIT 5");
$recent_orders = $conn->query("SELECT o.*, u.name as customer_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5");
?>

<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 25px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: var(--white);
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        display: flex;
        align-items: center;
        transition: var(--transition);
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-right: 20px;
        color: white;
    }

    .icon-products { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .icon-orders { background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 99%, #fecfef 100%); }
    .icon-revenue { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
    .icon-users { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }

    .stat-details h3 {
        margin: 0;
        font-size: 28px;
        font-weight: 700;
        color: var(--text-dark);
    }

    .stat-details p {
        margin: 0;
        color: var(--text-muted);
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .dashboard-row {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 25px;
    }

    .card {
        background: var(--white);
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .card-title {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
        color: var(--text-dark);
    }

    .view-all {
        font-size: 13px;
        color: #8e24aa;
        text-decoration: none;
        font-weight: 600;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th {
        text-align: left;
        padding: 12px 15px;
        border-bottom: 2px solid #f8f9fa;
        color: var(--text-muted);
        font-size: 13px;
        text-transform: uppercase;
    }

    td {
        padding: 15px;
        border-bottom: 1px solid #f8f9fa;
        font-size: 14px;
    }

    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
    }

    .status-pending { background: #fff3cd; color: #856404; }
    .status-processing { background: #cce5ff; color: #004085; }
    .status-shipped { background: #d4edda; color: #155724; }
    .status-completed { background: #d1ecf1; color: #0c5460; }
    .status-cancelled { background: #f8d7da; color: #721c24; }

    .stock-alert {
        display: flex;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }

    .stock-alert:last-child { border: none; }

    .stock-img {
        width: 40px;
        height: 40px;
        border-radius: 6px;
        object-fit: cover;
        margin-right: 15px;
    }

    .stock-info h4 {
        margin: 0;
        font-size: 14px;
        font-weight: 600;
    }

    .stock-info p {
        margin: 0;
        font-size: 12px;
        color: #d00b4d;
    }

    @media (max-width: 1200px) {
        .dashboard-row { grid-template-columns: 1fr; }
    }
</style>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon icon-products">
            <i class="fas fa-guitar"></i>
        </div>
        <div class="stat-details">
            <h3><?php echo $total_products; ?></h3>
            <p>Products</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-orders">
            <i class="fas fa-shopping-bag"></i>
        </div>
        <div class="stat-details">
            <h3><?php echo $total_orders; ?></h3>
            <p>Orders</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-revenue">
            <i class="fas fa-pound-sign"></i>
        </div>
        <div class="stat-details">
            <h3>£<?php echo number_format($total_revenue, 2); ?></h3>
            <p>Revenue</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-users">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-details">
            <h3><?php echo $total_users; ?></h3>
            <p>Customers</p>
        </div>
    </div>
</div>

<div class="dashboard-row">
    <!-- Recent Orders -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Recent Orders</h3>
            <a href="orders.php" class="view-all">View All</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while($order = $recent_orders->fetch_assoc()): ?>
                <tr>
                    <td>#<?php echo $order['id']; ?></td>
                    <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                    <td>£<?php echo number_format($order['total'], 2); ?></td>
                    <td>
                        <span class="status-badge status-<?php echo $order['status']; ?>">
                            <?php echo $order['status']; ?>
                        </span>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Low Stock Alerts -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Low Stock Alerts</h3>
            <a href="products.php" class="view-all">Manage Inventory</a>
        </div>
        <div class="stock-list">
            <?php if($low_stock->num_rows > 0): ?>
                <?php while($product = $low_stock->fetch_assoc()): ?>
                <div class="stock-alert">
                    <img src="../uploads/<?php echo htmlspecialchars($product['image']); ?>" class="stock-img">
                    <div class="stock-info">
                        <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                        <p>Only <?php echo $product['stock']; ?> items remains</p>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="color: var(--text-muted); font-size: 14px;">All items are well stocked!</p>
            <?php endif; ?>
        </div>
    </div>
</div>

</main>
</body>
</html>
