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
    SELECT products.name, products.price, order_items.quantity 
    FROM order_items
    JOIN products ON order_items.product_id = products.id
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
<title>Order Confirmation - Melody Masters</title>
<style>
body { font-family:'Poppins', sans-serif; background:#f4f6f9; margin:0; }
.confirmation-container { max-width:800px; margin:50px auto; background:#fff; padding:30px; border-radius:12px; box-shadow:0 5px 18px rgba(0,0,0,0.08); }
h2 { text-align:center; color:#8e24aa; margin-bottom:20px; }
.order-details, .billing, .items { margin-bottom:20px; }
.items table { width:100%; border-collapse: collapse; }
.items th, .items td { padding:10px; border-bottom:1px solid #ddd; text-align:center; }
.items th { background:#f0e0f8; color:#8e24aa; }
.total-row { font-weight:bold; font-size:16px; }
.success-msg { text-align:center; background:#e0f7e9; color:#2e7d32; padding:15px; border-radius:8px; margin-bottom:20px; border:1px solid #2e7d32; }
.view-orders-btn { display:block; width:100%; text-align:center; padding:12px; background:linear-gradient(135deg,#d00b4d,#8e24aa); color:white; text-decoration:none; border-radius:6px; margin-top:20px; font-weight:500; }
.view-orders-btn:hover { opacity:0.9; }
</style>
</head>
<body>

<div class="confirmation-container">

    <div class="success-msg">
        <h2>Thank you! Your order has been placed.</h2>
        <p>Order ID: <?php echo $order['id']; ?></p>
    </div>

    <!-- Order Summary -->
    <div class="order-details">
        <h3>Order Summary</h3>
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
                            <a href="product.php?id=<?php /* Fetch ID for link */ 
                                $p_name = $conn->real_escape_string($item['name']);
                                $p_res = $conn->query("SELECT id FROM products WHERE name='$p_name'")->fetch_assoc();
                                echo $p_res['id'];
                            ?>#reviews" style="background:#ffc107; color:#333; padding:2px 8px; border-radius:4px; font-size:11px; text-decoration:none; font-weight:600;">
                                <i class="fas fa-star"></i> Rate & Review
                            </a>
                        </div>
                    </td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                    <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                </tr>
                <?php endwhile; ?>
                <tr class="total-row">
                    <td colspan="3">Subtotal</td>
                    <td>$<?php echo number_format($subtotal, 2); ?></td>
                </tr>
                <tr class="total-row">
                    <td colspan="3">Shipping</td>
                    <td><?php echo ($order['shipping_cost']==0) ? 'Free' : '$'.number_format($order['shipping_cost'],2); ?></td>
                </tr>
                <tr class="total-row">
                    <td colspan="3">Total</td>
                    <td>$<?php echo number_format($order['total'], 2); ?></td>
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

    <!-- View All Orders Button -->
    <a href="orders.php" class="view-orders-btn">View All Orders</a>

</div>

</body>
</html>

<?php include 'includes/footer.php'; ?>