<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';

// Check login
if(!isset($_SESSION['user_id'])){
    echo "<script>
        alert('Please login first.');
        window.location.href='login.php';
    </script>";
    exit();
}

$user_id = $_SESSION['user_id'];

// Get cart items from database
$query = $conn->query("
    SELECT products.*, cart.quantity 
    FROM cart 
    JOIN products ON cart.product_id = products.id 
    WHERE cart.user_id = $user_id
");

if($query->num_rows == 0){
    echo "<h2 style='text-align:center;margin-top:50px;'>Your cart is empty.</h2>";
    include 'includes/footer.php';
    exit();
}

$total = 0;
$shipping_cost = 0;
$free_shipping_limit = 100; // Free shipping if subtotal > 100
$fixed_shipping = 10;       // Shipping cost if subtotal <= 100
?>

<!DOCTYPE html>
<html>
<head>
<title>Checkout - Melody Masters</title>

<style>
body { font-family: 'Poppins', sans-serif; background: #f4f6f9; margin:0; }
.checkout-container { max-width: 1100px; margin:50px auto; display:grid; grid-template-columns:2fr 1fr; gap:30px; }
.checkout-box { background:#fff; padding:25px; border-radius:12px; box-shadow:0 5px 18px rgba(0,0,0,0.08); }
.checkout-box h2 { margin-top:0; margin-bottom:20px; }
.cart-item { display:flex; justify-content:space-between; margin-bottom:15px; border-bottom:1px solid #eee; padding-bottom:10px; }
.total, .shipping, .grand-total { font-size:18px; font-weight:bold; margin-top:15px; text-align:right; }
input, textarea, select { width:100%; padding:10px; margin-bottom:15px; border-radius:6px; border:1px solid #ccc; }
button { width:100%; padding:12px; border:none; background:linear-gradient(135deg,#d00b4d,#8e24aa); color:white; font-size:16px; border-radius:6px; cursor:pointer; }
button:hover { opacity:0.9; }
.card-details { display:none; margin-top:10px; }
.card-details input { margin-bottom:10px; }
label { font-weight:500; }
@media(max-width:768px){ .checkout-container { grid-template-columns:1fr; } }

/* Card field styling */
.card-details input {
    border:1px solid #ccc;
    border-radius:6px;
    padding:10px;
}
.card-details input:focus { outline:none; border-color:#8e24aa; box-shadow:0 0 5px rgba(142,36,170,0.5); }
</style>

<script>
function toggleCardFields(){
    var payment = document.getElementById('payment_method').value;
    var cardFields = document.getElementById('card-fields');
    if(payment === 'Online Payment'){
        cardFields.style.display = 'block';
    } else {
        cardFields.style.display = 'none';
    }
}
</script>
</head>
<body>

<div class="checkout-container">

    <!-- Billing Section -->
    <div class="checkout-box">
        <h2>Billing Details</h2>

        <form action="place_order.php" method="POST">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="text" name="phone" placeholder="Phone Number" required>
            <textarea name="address" placeholder="Shipping Address" required></textarea>

            <!-- Payment Method -->
            <label for="payment_method">Payment Method</label>
            <select name="payment_method" id="payment_method" required onchange="toggleCardFields()">
                <option value="">-- Select Payment Method --</option>
                <option value="Cash on Delivery">Cash on Delivery</option>
                <option value="Online Payment">Online Payment</option>
            </select>

            <!-- Card Details for Online Payment -->
            <div class="card-details" id="card-fields">
                <input type="text" name="card_number" placeholder="Card Number">
                <input type="text" name="card_expiry" placeholder="Expiry Date (MM/YY)">
                <input type="text" name="card_cvv" placeholder="CVV">
            </div>

            <button type="submit">Place Order</button>
        </form>
    </div>

    <!-- Order Summary -->
    <div class="checkout-box">
        <h2>Order Summary</h2>

        <?php while($row = $query->fetch_assoc()): 
            $subtotal = $row['price'] * $row['quantity'];
            $total += $subtotal;
        ?>
            <div class="cart-item">
                <span><?php echo $row['name']; ?> (x<?php echo $row['quantity']; ?>)</span>
                <span>£<?php echo number_format($subtotal, 2); ?></span>
            </div>
        <?php endwhile; ?>

        <div class="total">
            Subtotal: £<?php echo number_format($total, 2); ?>
        </div>

        <?php
        // Calculate shipping
        if($total > $free_shipping_limit){
            $shipping_cost = 0;
            echo '<div class="shipping">Shipping: Free</div>';
        } else {
            $shipping_cost = $fixed_shipping;
            echo '<div class="shipping">Shipping: £'.number_format($shipping_cost, 2).'</div>';
        }

        $grand_total = $total + $shipping_cost;
        ?>

        <div class="grand-total">
            Total: £<?php echo number_format($grand_total, 2); ?>
        </div>

    </div>

</div>

</body>
</html>

<?php include 'includes/footer.php'; ?>