<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if not admin or staff
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

include '../includes/db.php';

$stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$stmt->close();

if (!$user_data || ($user_data['role'] !== 'admin' && $user_data['role'] !== 'staff')) {
    header("Location: ../index.php");
    exit();
}

$_SESSION['user_role'] = $user_data['role'];
?>
