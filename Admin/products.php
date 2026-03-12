<?php
include 'admin_header.php';

// Handle Delete
if(isset($_GET['delete'])){
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    if($stmt->execute()){
        header("Location: products.php?success=Product deleted");
        exit();
    }
}

// Search and Filter
$search = $_GET['search'] ?? '';
$category_filter = $_GET['category'] ?? '';

$query = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE 1=1";
if($search) $query .= " AND p.name LIKE '%$search%'";
if($category_filter) $query .= " AND p.category_id = $category_filter";
$query .= " ORDER BY p.id DESC";

$products = $conn->query($query);
$categories = $conn->query("SELECT * FROM categories ORDER BY name ASC");
?>

<style>
    .action-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .search-box {
        display: flex;
        gap: 10px;
    }

    .search-input {
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        width: 300px;
    }

    .filter-select {
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 8px;
    }

    .add-btn {
        background: var(--primary-gradient);
        color: white;
        text-decoration: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .product-table-card {
        background: var(--white);
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    }

    .p-img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 6px;
    }

    .actions {
        display: flex;
        gap: 10px;
    }

    .btn-icon {
        width: 35px;
        height: 35px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: var(--transition);
    }

    .btn-edit { background: #e3f2fd; color: #1976d2; }
    .btn-delete { background: #ffebee; color: #d32f2f; }

    .btn-icon:hover { transform: scale(1.1); }

    .alert {
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .alert-success { background: #d4edda; color: #155724; }
</style>

<div class="action-bar">
    <form class="search-box" method="GET">
        <input type="text" name="search" class="search-input" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
        <select name="category" class="filter-select">
            <option value="">All Categories</option>
            <?php while($cat = $categories->fetch_assoc()): ?>
                <option value="<?php echo $cat['id']; ?>" <?php echo ($category_filter == $cat['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cat['name']); ?>
                </option>
            <?php endwhile; ?>
        </select>
        <button type="submit" style="display:none;"></button>
    </form>
    <a href="product_add.php" class="add-btn">
        <i class="fas fa-plus"></i> Add New Product
    </a>
</div>

<?php if(isset($_GET['success'])): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
<?php endif; ?>

<div class="product-table-card">
    <table>
        <thead>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($p = $products->fetch_assoc()): ?>
            <tr>
                <td><img src="../uploads/<?php echo htmlspecialchars($p['image']); ?>" class="p-img"></td>
                <td><?php echo htmlspecialchars($p['name']); ?></td>
                <td><?php echo htmlspecialchars($p['category_name'] ?? 'Uncategorized'); ?></td>
                <td>£<?php echo number_format($p['price'], 2); ?></td>
                <td>
                    <span style="color: <?php echo ($p['stock'] < 5) ? '#d00b4d' : 'inherit'; ?>; font-weight: <?php echo ($p['stock'] < 5) ? 'bold' : 'normal'; ?>;">
                        <?php echo $p['stock']; ?>
                    </span>
                </td>
                <td class="actions">
                    <a href="product_edit.php?id=<?php echo $p['id']; ?>" class="btn-icon btn-edit" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="products.php?delete=<?php echo $p['id']; ?>" class="btn-icon btn-delete" title="Delete" onclick="return confirm('Protect this product?')">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</main>
</body>
</html>
