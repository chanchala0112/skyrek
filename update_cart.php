<?php
session_start();
include 'includes/db.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

if(isset($_POST['cart_id']) && isset($_POST['quantity'])){

    $cart_id = intval($_POST['cart_id']);
    $quantity = intval($_POST['quantity']);
    $user_id = $_SESSION['user_id'];

    if($quantity < 1){
        $_SESSION['error'] = "Quantity must be at least 1.";
        header("Location: cart.php");
        exit();
    }

    $stmt = $conn->prepare("UPDATE cart SET quantity=? WHERE id=? AND user_id=?");
    $stmt->bind_param("iii", $quantity, $cart_id, $user_id);

    if($stmt->execute()){
        $_SESSION['success'] = "Quantity updated successfully.";
    } else {
        $_SESSION['error'] = "Failed to update quantity.";
    }

    $stmt->close();
}

header("Location: cart.php");
exit();
?>