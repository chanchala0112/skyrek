<?php
session_start();
include 'includes/db.php';

// Check if product ID is provided
if(!isset($_GET['id'])) {
    echo "Invalid request.";
    exit();
}

$product_id = intval($_GET['id']);

// Optional: Check if product exists and stock > 0
$product_query = $conn->query("SELECT * FROM products WHERE id = $product_id");
if($product_query->num_rows == 0){
    echo "Product does not exist.";
    exit();
}

$product = $product_query->fetch_assoc();
if($product['stock'] <= 0){
    echo "Product out of stock.";
    exit();
}

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    // Use JS alert and redirect immediately
    ?>
    <script>
        alert('You must login first to add products to your cart.');
        window.location.href = 'login.php';
    </script>
    <?php
    exit();
}

// User is logged in
$user_id = $_SESSION['user_id'];

// Check if product already in cart
$check_cart = $conn->query("SELECT * FROM cart WHERE user_id = $user_id AND product_id = $product_id");

if($check_cart->num_rows > 0){
    // Update quantity
    $cart_item = $check_cart->fetch_assoc();
    $new_qty = $cart_item['quantity'] + 1;
    $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    $stmt->bind_param("ii", $new_qty, $cart_item['id']);
    $stmt->execute();
    $stmt->close();
} else {
    // Insert new item
    $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $stmt->close();
}

// Redirect to cart page with success message
$_SESSION['success'] = "Product added to cart successfully!";
header("Location: cart.php");
exit();
?>