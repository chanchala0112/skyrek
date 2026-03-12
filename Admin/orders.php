<?php
include 'admin_header.php';

// Handle Status Update
if(isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $status = $conn->real_escape_string($_POST['status']);
    
    $stmt = $conn->prepare("UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("si", $status, $order_id);
    if($stmt->execute()){
        header("Location: orders.php?success=Order #$order_id updated to $status");
        exit();
    }
}

$status_filter = $_GET['status'] ?? '';
$query = "SELECT o.*, u.name as customer_name, u.email as customer_email FROM orders o JOIN users u ON o.user_id = u.id";
if($status_filter) $query .= " WHERE o.status = '$status_filter'";
$query .= " ORDER BY o.created_at DESC";

$orders = $conn->query($query);
?>

<style>
    .filter-bar {
        margin-bottom: 25px;
        display: flex;
        gap: 15px;
    }

    .filter-btn {
        padding: 8px 15px;
        border-radius: 20px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        background: #fff;
        color: #777;
        border: 1px solid #ddd;
        transition: var(--transition);
    }

    .filter-btn.active {
        background: #8e24aa;
        color: #fff;
        border-color: #8e24aa;
    }

    .order-card {
        background: var(--white);
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    }

    .status-select {
        padding: 5px;
        border-radius: 5px;
        font-size: 12px;
        border: 1px solid #ddd;
    }

    .btn-view {
        color: #8e24aa;
        text-decoration: none;
        font-weight: 600;
        font-size: 13px;
    }
</style>

<div class="filter-bar">
    <a href="orders.php" class="filter-btn <?php echo !$status_filter ? 'active' : ''; ?>">All Orders</a>
    <a href="orders.php?status=Pending" class="filter-btn <?php echo $status_filter == 'Pending' ? 'active' : ''; ?>">Pending</a>
    <a href="orders.php?status=Paid" class="filter-btn <?php echo $status_filter == 'Paid' ? 'active' : ''; ?>">Paid</a>
    <a href="orders.php?status=Processing" class="filter-btn <?php echo $status_filter == 'Processing' ? 'active' : ''; ?>">Processing</a>
    <a href="orders.php?status=Shipped" class="filter-btn <?php echo $status_filter == 'Shipped' ? 'active' : ''; ?>">Shipped</a>
    <a href="orders.php?status=Completed" class="filter-btn <?php echo $status_filter == 'Completed' ? 'active' : ''; ?>">Completed</a>
</div>

<?php if(isset($_GET['success'])): ?>
    <div class="alert alert-success" style="background:#d4edda; color:#155724; padding:15px; border-radius:8px; margin-bottom:20px;"><?php echo htmlspecialchars($_GET['success']); ?></div>
<?php endif; ?>

<div class="order-card">
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Total</th>
                <th>Payment</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($o = $orders->fetch_assoc()): ?>
            <tr>
                <td>#<?php echo $o['id']; ?></td>
                <td>
                    <strong><?php echo htmlspecialchars($o['customer_name']); ?></strong><br>
                    <small style="color:#777;"><?php echo htmlspecialchars($o['customer_email']); ?></small>
                </td>
                <td>£<?php echo number_format($o['total'], 2); ?></td>
                <td><?php echo strtoupper($o['payment_method']); ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                        <select name="status" class="status-select" onchange="this.form.submit()">
                            <option value="Pending" <?php echo $o['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="Paid" <?php echo $o['status'] == 'Paid' ? 'selected' : ''; ?>>Paid</option>
                            <option value="Processing" <?php echo $o['status'] == 'Processing' ? 'selected' : ''; ?>>Processing</option>
                            <option value="Shipped" <?php echo $o['status'] == 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                            <option value="Completed" <?php echo $o['status'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="Cancelled" <?php echo $o['status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                        <input type="hidden" name="update_status" value="1">
                    </form>
                </td>
                <td><?php echo date('M d, Y', strtotime($o['created_at'])); ?></td>
                <td>
                    <a href="order_view.php?id=<?php echo $o['id']; ?>" class="btn-view"><i class="fas fa-eye"></i> View Items</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</main>
</body>
</html>
