<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';

// Check login and order_id
if(!isset($_SESSION['user_id']) || !isset($_GET['order_id'])){
    echo "<script>
        alert('Invalid access.');
        window.location.href='index.php';
    </script>";
    exit();
}

$user_id = $_SESSION['user_id'];
$order_id = intval($_GET['order_id']);

// Fetch order details
$order_query = $conn->query("SELECT * FROM orders WHERE id=$order_id AND user_id=$user_id");
if($order_query->num_rows == 0){
    echo "<h2 style='text-align:center;margin-top:50px;'>Order not found.</h2>";
    include 'includes/footer.php';
    exit();
}

$order = $order_query->fetch_assoc();

// Fetch order items
$items_query = $conn->query("
    SELECT products.id, products.name, products.price, order_items.quantity, dp.id as digital_id
    FROM order_items
    JOIN products ON order_items.product_id = products.id
    LEFT JOIN digital_products dp ON products.id = dp.product_id
    WHERE order_items.order_id = $order_id
");

// Calculate subtotal
$subtotal = 0;
while($item = $items_query->fetch_assoc()){
    $subtotal += $item['price'] * $item['quantity'];
}
$items_query->data_seek(0); // Reset pointer
?>

<!DOCTYPE html>
<html>
<head>
<title>Order Details - Melody Masters</title>
<style>
body { font-family:'Poppins', sans-serif; background:#f4f6f9; margin:0; }
.details-container { max-width:900px; margin:50px auto; background:#fff; padding:30px; border-radius:12px; box-shadow:0 5px 18px rgba(0,0,0,0.08); }
h2 { text-align:center; color:#8e24aa; margin-bottom:20px; }
.order-summary, .billing, .items { margin-bottom:25px; }
.items table { width:100%; border-collapse: collapse; }
.items th, .items td { padding:12px; border-bottom:1px solid #ddd; text-align:center; }
.items th { background:#f0e0f8; color:#8e24aa; }
.total-row { font-weight:bold; font-size:16px; }
.download-badge, .review-btn { transition: transform 0.2s; }
.download-badge:hover, .review-btn:hover { transform: scale(1.05); opacity: 0.9; }
.back-btn { display:block; width:100%; text-align:center; padding:12px; background:linear-gradient(135deg,#d00b4d,#8e24aa); color:white; text-decoration:none; border-radius:6px; margin-top:20px; font-weight:500; }
.back-btn:hover { opacity:0.9; }
</style>
</head>
<body>

<div class="details-container">

    <h2>Order Details</h2>

    <!-- Order Summary -->
    <div class="order-summary">
        <h3 id="items">Order #<?php echo $order['id']; ?></h3>
        <div class="items">
            <table>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                </tr>
                <?php while($item = $items_query->fetch_assoc()): ?>
                <tr>
                    <td>
                        <?php echo $item['name']; ?>
                        <div style="margin-top: 5px;">
                            <?php if($item['digital_id'] && ($order['status'] == 'Completed' || $order['status'] == 'Paid')): ?>
                                <a href="download.php?product_id=<?php echo $item['id']; ?>" class="download-badge" style="background: #2e7d32; color: white; padding: 2px 8px; border-radius: 4px; font-size: 11px; text-decoration: none; margin-right: 5px; display: inline-block;">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            <?php endif; ?>

                            <?php if($order['status'] == 'Completed' || $order['status'] == 'Paid'): ?>
                                <a href="product.php?id=<?php echo $item['id']; ?>#reviews" class="review-btn" style="background: #ffc107; color: #333; padding: 2px 8px; border-radius: 4px; font-size: 11px; text-decoration: none; display: inline-block; font-weight: 600;">
                                    <i class="fas fa-star"></i> Rate & Review
                                </a>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>£<?php echo number_format($item['price'], 2); ?></td>
                    <td>£<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                </tr>
                <?php endwhile; ?>
                <tr class="total-row">
                    <td colspan="3">Subtotal</td>
                    <td>£<?php echo number_format($subtotal, 2); ?></td>
                </tr>
                <tr class="total-row">
                    <td colspan="3">Shipping</td>
                    <td><?php echo ($order['shipping_cost']==0) ? 'Free' : '£'.number_format($order['shipping_cost'],2); ?></td>
                </tr>
                <tr class="total-row">
                    <td colspan="3">Total</td>
                    <td>£<?php echo number_format($order['total'], 2); ?></td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Billing & Shipping -->
    <div class="billing">
        <h3>Billing & Shipping</h3>
        <p><strong>Shipping Address:</strong> <?php echo $order['shipping_address']; ?></p>
        <p><strong>Payment Method:</strong> <?php echo $order['payment_method']; ?></p>
        <p><strong>Order Status:</strong> <?php echo $order['status']; ?></p>
    </div>

    <!-- Back to Orders Button -->
    <a href="orders.php" class="back-btn">Back to All Orders</a>

</div>

</body>
</html>

<?php include 'includes/footer.php'; ?>