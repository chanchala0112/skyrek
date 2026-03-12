<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';

// Check login
if(!isset($_SESSION['user_id'])) {
    echo "<script>
        alert('You must login first to view your cart.');
        window.location.href='login.php';
    </script>";
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch cart items
$cart_query = $conn->query("
    SELECT cart.id AS cart_id, products.*, cart.quantity 
    FROM cart 
    JOIN products ON cart.product_id = products.id 
    WHERE cart.user_id = $user_id
");

// Calculate total
$total = 0;
while($item = $cart_query->fetch_assoc()){
    $total += $item['price'] * $item['quantity'];
}
$cart_query->data_seek(0);
?>

<!DOCTYPE html>
<html>
<head>
<title>Your Cart - Melody Masters</title>
<style>
body { font-family:'Poppins', sans-serif; background:#f5f5f5; margin:0; }
.cart-container { max-width:1000px; margin:50px auto; background:#fff; padding:30px; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,0.1); }
h2 { color:#8e24aa; margin-bottom:20px; text-align:center; }
table { width:100%; border-collapse:collapse; }
th, td { padding:15px; text-align:center; border-bottom:1px solid #ddd; }
th { background:#f0e0f8; color:#8e24aa; }
td img { width:80px; border-radius:6px; }
.qty-input { width:60px; padding:5px; text-align:center; }
.btn-update { padding:6px 10px; background:#8e24aa; color:white; border:none; border-radius:5px; cursor:pointer; }
.btn-remove { padding:6px 12px; background:#d00b4d; color:white; border:none; border-radius:6px; cursor:pointer; }
.btn-update:hover, .btn-remove:hover { opacity:0.85; }
.total { text-align:right; margin-top:20px; font-size:20px; font-weight:bold; color:#d00b4d; }
.checkout-btn { margin-top:25px; padding:12px 25px; background:linear-gradient(135deg,#d00b4d,#8e24aa); color:white; border:none; border-radius:6px; cursor:pointer; float:right; }
.msg-container { max-width:1000px; margin:20px auto; padding:15px 20px; border-radius:8px; font-weight:500; text-align:center; }
.success-msg { background-color:#e0f7e9; color:#2e7d32; border:1px solid #2e7d32; }
.error-msg { background-color:#fde0e0; color:#c62828; border:1px solid #c62828; }
</style>
</head>
<body>

<div class="cart-container">
<h2>Your Cart</h2>

<?php
if(isset($_SESSION['success'])){
    echo "<div class='msg-container success-msg'>".$_SESSION['success']."</div>";
    unset($_SESSION['success']);
}
if(isset($_SESSION['error'])){
    echo "<div class='msg-container error-msg'>".$_SESSION['error']."</div>";
    unset($_SESSION['error']);
}
?>

<?php if($cart_query->num_rows > 0): ?>
<table>
<tr>
    <th>Product</th>
    <th>Name</th>
    <th>Price</th>
    <th>Quantity</th>
    <th>Subtotal</th>
    <th>Action</th>
</tr>

<?php while($item = $cart_query->fetch_assoc()): ?>
<tr>
    <td><img src="uploads/<?php echo $item['image']; ?>"></td>
    <td><?php echo $item['name']; ?></td>
    <td>£<?php echo $item['price']; ?></td>
    <td>
        <form action="update_cart.php" method="POST">
            <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
            <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" class="qty-input">
            <button type="submit" class="btn-update">Update</button>
        </form>
    </td>
    <td>£<?php echo $item['price'] * $item['quantity']; ?></td>
    <td>
        <a href="remove_from_cart.php?id=<?php echo $item['cart_id']; ?>">
            <button class="btn-remove">Remove</button>
        </a>
    </td>
</tr>
<?php endwhile; ?>
</table>

<div class="total">Total: £<?php echo $total; ?></div>
<a href="checkout.php"><button class="checkout-btn">Proceed to Checkout</button></a>

<?php else: ?>
<p style="text-align:center;">Your cart is empty.</p>
<?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>