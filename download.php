<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['product_id'])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];
$product_id = intval($_GET['product_id']);

// Verify purchase
$check = $conn->query("
    SELECT dp.file_path, dp.download_limit, oi.id as item_id
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN digital_products dp ON oi.product_id = dp.product_id
    WHERE o.user_id = $user_id AND oi.product_id = $product_id AND (o.status = 'Completed' || o.status = 'Paid')
");

if ($check->num_rows == 0) {
    die("You do not have access to this download or the order is not yet paid/completed.");
}

$data = $check->fetch_assoc();
$file_path = $data['file_path'];

// Handle different path conventions
if (strpos($file_path, 'uploads/') === 0) {
    $file = $file_path;
} elseif (strpos($file_path, 'digital_products/') === 0) {
    $file = "uploads/digital/" . str_replace('digital_products/', '', $file_path);
} else {
    $file = "uploads/digital/" . $file_path;
}

if (!file_exists($file)) {
    // For debugging/demo: if it's one of the default files, create a placeholder
    if (strpos($file, 'guitar_tutorial.pdf') !== false || strpos($file, 'piano_sheet.pdf') !== false) {
        if (!is_dir(dirname($file))) mkdir(dirname($file), 0777, true);
        file_put_contents($file, "Sample Digital Content for " . $data['file_name']);
    } else {
        die("File not found on server ($file). Please contact support.");
    }
}

// Logic for download limits could be added here (e.g., a download_logs table)

// Professional download header
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="'.basename($file).'"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($file));
readfile($file);
exit;
?>
