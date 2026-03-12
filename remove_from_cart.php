<?php
session_start();
include 'includes/db.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])){
    echo "<script>
        alert('You must login first.');
        window.location.href='login.php';
    </script>";
    exit();
}

// Check if cart item ID is provided
if(!isset($_GET['id'])){
    $_SESSION['error'] = "Invalid request.";
    header("Location: cart.php");
    exit();
}

$cart_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Delete cart item
$stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $cart_id, $user_id);
if($stmt->execute()){
    $_SESSION['success'] = "Item removed from cart.";
} else {
    $_SESSION['error'] = "Failed to remove item.";
}
$stmt->close();

header("Location: cart.php");
exit();
?>