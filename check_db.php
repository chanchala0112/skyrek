<?php
include 'includes/db.php';
$sql = "ALTER TABLE orders MODIFY COLUMN status ENUM('Pending', 'Paid', 'Processing', 'Shipped', 'Completed', 'Cancelled')";
if($conn->query($sql)){
    echo "Database updated successfully";
} else {
    echo "Error updating database: " . $conn->error;
}
?>
