<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])){
    echo "<script>
        alert('Please login first.');
        window.location.href='login.php';
    </script>";
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user's orders
$orders_query = $conn->query("
    SELECT * FROM orders 
    WHERE user_id = $user_id 
    ORDER BY created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Orders - Melody Masters</title>
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f5f5f5; margin:0; }
        .orders-container { max-width: 1000px; margin: 50px auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow:0 10px 30px rgba(0,0,0,0.1);}
        h2 { text-align:center; color:#8e24aa; margin-bottom:30px; }
        table { width:100%; border-collapse: collapse; }
        th, td { padding:12px; text-align:center; border-bottom:1px solid #ddd; }
        th { background:#f0e0f8; color:#8e24aa; }
        .status { padding:5px 10px; border-radius:6px; color:white; font-weight:bold; }
        .Pending { background:#d00b4d; }
        .Paid { background:#1e88e5; }
        .Processing { background:#f57c00; }
        .Shipped { background:#1976d2; }
        .Completed { background:#2e7d32; }
        .Cancelled { background:#c62828; }
        .view-btn { padding:6px 12px; border:none; border-radius:6px; background:#8e24aa; color:white; text-decoration:none; transition:0.3s; }
        .view-btn:hover { background:#d00b4d; }
        @media(max-width:768px){ table, th, td { font-size:14px; } }
    </style>
</head>
<body>

<div class="orders-container">
    <h2>My Orders</h2>

    <?php if($orders_query->num_rows > 0): ?>
    <table>
        <tr>
            <th>Order ID</th>
            <th>Date</th>
            <th>Total</th>
            <th>Shipping Cost</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php while($order = $orders_query->fetch_assoc()): ?>
        <tr>
            <td><?php echo $order['id']; ?></td>
            <td><?php echo date('d M Y', strtotime($order['created_at'])); ?></td>
            <td>£<?php echo number_format($order['total'],2); ?></td>
            <td><?php echo ($order['shipping_cost']==0) ? 'Free' : '£'.number_format($order['shipping_cost'],2); ?></td>
            <td><span class="status <?php echo $order['status']; ?>"><?php echo $order['status']; ?></span></td>
            <td>
                <a class="view-btn" href="order_details.php?order_id=<?php echo $order['id']; ?>">View Details</a>
                <?php if($order['status'] == 'Completed' || $order['status'] == 'Paid'): ?>
                    <a class="view-btn" style="background:#ffc107; color:#333; margin-top:5px; display:inline-block;" href="order_details.php?order_id=<?php echo $order['id']; ?>#items">Review Items</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    <?php else: ?>
        <p style="text-align:center; margin-top:50px;">You have not placed any orders yet.</p>
    <?php endif; ?>
</div>

</body>
</html>

<?php include 'includes/footer.php'; ?>