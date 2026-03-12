<?php
include 'admin_header.php';

// Handle Stock Update
if(isset($_POST['update_stock'])) {
    $product_id = (int)$_POST['product_id'];
    $new_stock = (int)$_POST['stock'];
    
    $stmt = $conn->prepare("UPDATE products SET stock = ? WHERE id = ?");
    $stmt->bind_param("ii", $new_stock, $product_id);
    if($stmt->execute()){
        header("Location: inventory.php?success=Stock updated for Product ID #$product_id");
        exit();
    }
}

// Fetch products with low stock first
$query = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.stock ASC, p.name ASC";
$products = $conn->query($query);
?>

<style>
    .inventory-card {
        background: var(--white);
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 5px 25px rgba(0,0,0,0.05);
    }
    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }
    .table-header h3 {
        margin: 0;
        color: #8e24aa;
        font-size: 20px;
    }
    .stock-input {
        width: 80px;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 8px;
        text-align: center;
        font-weight: 600;
        background: #fdfdfd;
        transition: 0.3s;
    }
    .stock-input:focus {
        border-color: #8e24aa;
        outline: none;
        box-shadow: 0 0 8px rgba(142,36,170,0.1);
    }
    .update-btn {
        background: var(--primary-gradient);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        transition: 0.3s;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .update-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(142,36,170,0.3);
    }
    .low-stock-row {
        background: #fff8f8;
    }
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }
    .status-low { background: #ffebee; color: #d32f2f; border: 1px solid #ffcdd2; }
    .status-ok { background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }
    
    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 10px;
    }
    th {
        padding: 15px;
        text-align: left;
        color: #777;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 13px;
    }
    td {
        padding: 15px;
        background: #fff;
        vertical-align: middle;
    }
    tr:not(:first-child) td {
        border-top: 1px solid #f0f0f0;
    }
</style>

<div class="inventory-card">
    <div class="table-header">
        <h3><i class="fas fa-warehouse"></i> Inventory Stock Control</h3>
        <p style="color:#777; font-size:14px;">Manage and monitor product availability in real-time.</p>
    </div>
    
    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success" style="background:#d4edda; color:#155724; padding:15px; border-radius:10px; margin-bottom:25px; border-left: 5px solid #28a745;">
            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['success']); ?>
        </div>
    <?php endif; ?>

    <table style="border-spacing: 0;">
        <thead>
            <tr style="background: #f8f9fa;">
                <th style="border-radius: 10px 0 0 10px;">ID</th>
                <th>Product</th>
                <th>Stock Level</th>
                <th>Status</th>
                <th>New Stock</th>
                <th style="border-radius: 0 10px 10px 0;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while($p = $products->fetch_assoc()): 
                $is_low = $p['stock'] < 5;
            ?>
            <tr class="<?php echo $is_low ? 'low-stock-row' : ''; ?>">
                <td style="color:#8e24aa; font-weight:700;">#<?php echo $p['id']; ?></td>
                <td>
                    <div style="font-weight:600; color:#333;"><?php echo htmlspecialchars($p['name']); ?></div>
                    <div style="font-size:12px; color:#999;"><?php echo htmlspecialchars($p['category_name'] ?? 'Uncategorized'); ?></div>
                </td>
                <td style="font-size:18px; font-weight:700; color:<?php echo $is_low ? '#d32f2f' : '#333'; ?>">
                    <?php echo $p['stock']; ?>
                </td>
                <td>
                    <span class="status-badge <?php echo $is_low ? 'status-low' : 'status-ok'; ?>">
                        <i class="fas <?php echo $is_low ? 'fa-exclamation-triangle' : 'fa-check'; ?>"></i>
                        <?php echo $is_low ? 'Low Stock' : 'Normal'; ?>
                    </span>
                </td>
                <form method="POST">
                    <td>
                        <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                        <input type="number" name="stock" value="<?php echo $p['stock']; ?>" class="stock-input" min="0">
                    </td>
                    <td>
                        <button type="submit" name="update_stock" class="update-btn">
                            <i class="fas fa-sync-alt"></i> Update
                        </button>
                    </td>
                </form>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</main>
</body>
</html>
