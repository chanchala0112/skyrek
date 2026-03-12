<?php
include 'admin_header.php';

$id = (int)($_GET['id'] ?? 0);

// Fetch Order Info
$order_stmt = $conn->prepare("SELECT o.*, u.name as customer_name, u.email as customer_email, u.contact, u.address as user_address FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?");
$order_stmt->bind_param("i", $id);
$order_stmt->execute();
$order = $order_stmt->get_result()->fetch_assoc();
$order_stmt->close();

if (!$order) {
    header("Location: orders.php");
    exit();
}

// Fetch Order Items
$items_stmt = $conn->prepare("SELECT oi.*, p.name as product_name, p.image FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
$items_stmt->bind_param("i", $id);
$items_stmt->execute();
$items = $items_stmt->get_result();
$items_stmt->close();
?>

<style>
    .order-details-grid {
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: 30px;
    }

    .detail-card {
        background: var(--white);
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        margin-bottom: 30px;
    }

    .detail-header {
        border-bottom: 1px solid #eee;
        padding-bottom: 15px;
        margin-bottom: 20px;
        display:flex;
        justify-content: space-between;
        align-items: center;
    }

    .info-group { margin-bottom: 15px; }
    .info-group label { color: var(--text-muted); font-size: 12px; text-transform: uppercase; font-weight: 600; display: block; }
    .info-group p { margin: 5px 0 0; font-weight: 500; }

    .item-row {
        display: flex;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1px solid #eee;
    }

    .item-img { width: 60px; height: 60px; border-radius: 8px; object-fit: cover; margin-right: 20px; }
    .item-info { flex: 1; }
    .item-info h4 { margin: 0; font-size: 15px; }
    .item-price { text-align: right; }

    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        font-weight: 500;
    }

    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: white;
    }
    .status-Pending { background: #d00b4d; }
    .status-Paid { background: #1e88e5; }
    .status-Processing { background: #f57c00; }
    .status-Shipped { background: #1976d2; }
    .status-Completed { background: #2e7d32; }
    .status-Cancelled { background: #c62828; }
</style>

<div class="order-details-grid">
    <div class="items-section">
        <div class="detail-card">
            <div class="detail-header">
                <h3 style="margin:0;">Order #<?php echo $order['id']; ?> Items</h3>
                <span class="status-badge status-<?php echo $order['status']; ?>"><?php echo $order['status']; ?></span>
            </div>

            <?php while($item = $items->fetch_assoc()): ?>
            <div class="item-row">
                <img src="../uploads/<?php echo htmlspecialchars($item['image']); ?>" class="item-img">
                <div class="item-info">
                    <h4><?php echo htmlspecialchars($item['product_name']); ?></h4>
                    <p style="color:#777; font-size:13px;">Quantity: <?php echo $item['quantity']; ?> x £<?php echo number_format($item['price'], 2); ?></p>
                </div>
                <div class="item-price">
                    <strong>£<?php echo number_format($item['price'] * $item['quantity'], 2); ?></strong>
                </div>
            </div>
            <?php endwhile; ?>

            <div style="margin-top: 20px; border-top: 2px solid #eee; padding-top: 20px;">
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>£<?php echo number_format($order['total'] - $order['shipping_cost'], 2); ?></span>
                </div>
                <div class="summary-row">
                    <span>Shipping</span>
                    <span>£<?php echo number_format($order['shipping_cost'], 2); ?></span>
                </div>
                <div class="summary-row" style="font-size: 1.2em; font-weight: 700; color: #8e24aa;">
                    <span>Total</span>
                    <span>£<?php echo number_format($order['total'], 2); ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="customer-section">
        <div class="detail-card">
            <h3 style="margin-top:0;">Customer</h3>
            <div class="info-group">
                <label>Name</label>
                <p><?php echo htmlspecialchars($order['customer_name']); ?></p>
            </div>
            <div class="info-group">
                <label>Email</label>
                <p><?php echo htmlspecialchars($order['customer_email']); ?></p>
            </div>
            <div class="info-group">
                <label>Phone</label>
                <p><?php echo htmlspecialchars($order['contact']); ?></p>
            </div>
        </div>

        <div class="detail-card">
            <h3 style="margin-top:0;">Shipping Address</h3>
            <p style="white-space: pre-line; line-height: 1.5;">
                <?php echo htmlspecialchars($order['shipping_address'] ?: $order['user_address']); ?>
            </p>
        </div>

        <div class="detail-card">
            <h3 style="margin-top:0;">Payment</h3>
            <div class="info-group">
                <label>Method</label>
                <p><?php echo strtoupper($order['payment_method']); ?></p>
            </div>
            <div class="info-group">
                <label>Date Placed</label>
                <p><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></p>
            </div>
        </div>
    </div>
</div>

</main>
</body>
</html>
