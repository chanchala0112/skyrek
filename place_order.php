<?php
session_start();
include 'includes/db.php';

// Check login
if(!isset($_SESSION['user_id'])){
    echo "<script>
        alert('Please login first.');
        window.location.href='login.php';
    </script>";
    exit();
}

$user_id = $_SESSION['user_id'];

// Check cart
$cart_query = $conn->query("SELECT product_id, quantity FROM cart WHERE user_id=$user_id");
if($cart_query->num_rows == 0){
    echo "<script>
        alert('Your cart is empty.');
        window.location.href='cart.php';
    </script>";
    exit();
}

// Check POST data
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $address = $conn->real_escape_string($_POST['address']);
    $payment_method = $conn->real_escape_string($_POST['payment_method']);

    // Calculate subtotal
    $subtotal = 0;
    $cart_items = [];
    while($item = $cart_query->fetch_assoc()){
        $product_id = $item['product_id'];
        $quantity = $item['quantity'];
        $product = $conn->query("SELECT price FROM products WHERE id=$product_id")->fetch_assoc();
        $price = $product['price'];
        $subtotal += $price * $quantity;
        $cart_items[] = ['product_id'=>$product_id,'quantity'=>$quantity,'price'=>$price];
    }

    // Shipping cost
    $shipping_cost = ($subtotal > 100) ? 0 : 10;
    $total = $subtotal + $shipping_cost;

    // Insert order
    $status = ($payment_method === 'Online Payment') ? 'Paid' : 'Pending';
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total, shipping_cost, shipping_address, payment_method, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
    $stmt->bind_param("iddsss", $user_id, $total, $shipping_cost, $address, $payment_method, $status);
    if($stmt->execute()){
        $order_id = $stmt->insert_id;

        // Insert order items and deduct stock
        $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt_stock = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        foreach($cart_items as $ci){
            $stmt_item->bind_param("iiid", $order_id, $ci['product_id'], $ci['quantity'], $ci['price']);
            $stmt_item->execute();
            
            // Deduct stock
            $stmt_stock->bind_param("ii", $ci['quantity'], $ci['product_id']);
            $stmt_stock->execute();
        }

        // Clear cart
        $conn->query("DELETE FROM cart WHERE user_id=$user_id");

        // Redirect to order confirmation
        header("Location: order_confirmation.php?order_id=$order_id");
        exit();
    } else {
        echo "<script>
            alert('Failed to place order. Please try again.');
            window.location.href='checkout.php';
        </script>";
    }
} else {
    header("Location: checkout.php");
    exit();
}