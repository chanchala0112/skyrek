<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';

// Filter inputs
$cat_id = isset($_GET['category']) ? intval($_GET['category']) : 0;
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : 0;
$max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : 10000;

// Base query
$query = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE 1=1";

if($cat_id > 0) {
    // Check if it's a parent category or child
    $query .= " AND (p.category_id = $cat_id OR c.parent_id = $cat_id)";
}
if($search != '') {
    $query .= " AND (p.name LIKE '%$search%' OR p.description LIKE '%$search%')";
}
if($min_price > 0) $query .= " AND p.price >= $min_price";
if($max_price < 10000) $query .= " AND p.price <= $max_price";

$query .= " ORDER BY p.id DESC";
$products = $conn->query($query);

// Fetch categories for filter
$all_categories = $conn->query("SELECT * FROM categories WHERE parent_id IS NULL ORDER BY name ASC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Shop - Melody Masters</title>
<style>
    body { font-family: 'Poppins', sans-serif; background: #f4f6f9; margin:0; }
    .shop-container { max-width: 1200px; margin: 40px auto; display: grid; grid-template-columns: 280px 1fr; gap: 30px; padding: 0 20px; }
    
    /* Sidebar Filters */
    .filter-sidebar { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); height: fit-content; }
    .filter-group { margin-bottom: 25px; }
    .filter-group h3 { font-size: 16px; margin-bottom: 15px; color: #8e24aa; border-bottom: 2px solid #f0e0f8; padding-bottom: 8px; }
    .filter-list { list-style: none; padding: 0; }
    .filter-list li { margin-bottom: 10px; }
    .filter-list a { text-decoration: none; color: #555; font-size: 14px; transition: 0.3s; }
    .filter-list a:hover, .filter-list a.active { color: #d00b4d; font-weight: 600; }
    
    .search-bar { display: flex; gap: 10px; margin-bottom: 20px; }
    .search-input { flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 6px; }
    .filter-btn { padding: 8px 15px; background: #8e24aa; color: white; border: none; border-radius: 6px; cursor: pointer; }

    /* Product Grid */
    .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 25px; }
    .product-card { background: white; padding: 15px; border-radius: 12px; text-align: center; box-shadow: 0 5px 15px rgba(0,0,0,0.05); transition: 0.3s; }
    .product-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
    .product-card img { width: 100%; height: 180px; object-fit: cover; border-radius: 8px; margin-bottom: 15px; }
    .product-card h4 { margin: 10px 0 5px; font-size: 16px; color: #333; }
    .product-card .price { color: #d00b4d; font-weight: 700; margin-bottom: 15px; display: block; }
    .view-btn { padding: 8px 15px; background: linear-gradient(135deg, #d00b4d, #8e24aa); color: white; text-decoration: none; border-radius: 6px; font-size: 14px; display: inline-block; }

    @media (max-width: 768px) {
        .shop-container { grid-template-columns: 1fr; }
    }
</style>
</head>
<body>

<div class="shop-container">
    <!-- Sidebar -->
    <aside class="filter-sidebar">
        <div class="filter-group">
            <h3>Search</h3>
            <form action="shop.php" method="GET" class="search-bar">
                <input type="text" name="search" placeholder="Search instruments..." class="search-input" value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="filter-btn"><i class="fas fa-search"></i></button>
            </form>
        </div>

        <div class="filter-group">
            <h3>Categories</h3>
            <ul class="filter-list">
                <li><a href="shop.php" class="<?php echo $cat_id == 0 ? 'active' : ''; ?>">All Products</a></li>
                <?php while($cat = $all_categories->fetch_assoc()): ?>
                    <?php 
                        $parent_id = $cat['id'];
                        $sub_query = $conn->query("SELECT * FROM categories WHERE parent_id = $parent_id");
                    ?>
                    <li>
                        <a href="shop.php?category=<?php echo $cat['id']; ?>" class="<?php echo $cat_id == $cat['id'] ? 'active' : ''; ?>">
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </a>
                        <?php if($sub_query->num_rows > 0): ?>
                            <ul class="filter-list" style="padding-left: 15px; margin-top: 5px;">
                                <?php while($sub = $sub_query->fetch_assoc()): ?>
                                    <li>
                                        <a href="shop.php?category=<?php echo $sub['id']; ?>" class="<?php echo $cat_id == $sub['id'] ? 'active' : ''; ?>" style="font-size: 13px;">
                                            - <?php echo htmlspecialchars($sub['name']); ?>
                                        </a>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>

        <div class="filter-group">
            <h3>Price Range</h3>
            <form action="shop.php" method="GET">
                <?php if($cat_id > 0): ?><input type="hidden" name="category" value="<?php echo $cat_id; ?>"><?php endif; ?>
                <div style="display:flex; gap:5px; align-items:center;">
                    <input type="number" name="min_price" placeholder="Min" class="search-input" style="padding:5px;" value="<?php echo $min_price ?: ''; ?>">
                    <span>-</span>
                    <input type="number" name="max_price" placeholder="Max" class="search-input" style="padding:5px;" value="<?php echo $max_price < 10000 ? $max_price : ''; ?>">
                </div>
                <button type="submit" class="filter-btn" style="width:100%; margin-top:10px;">Apply Filter</button>
            </form>
        </div>
    </aside>

    <!-- Product Grid -->
    <section>
        <div class="product-grid">
            <?php if($products->num_rows > 0): ?>
                <?php while($p = $products->fetch_assoc()): ?>
                    <div class="product-card">
                        <img src="uploads/<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>">
                        <h4><?php echo htmlspecialchars($p['name']); ?></h4>
                        <span class="category" style="font-size: 12px; color: #777;"><?php echo htmlspecialchars($p['category_name']); ?></span>
                        <span class="price">£<?php echo number_format($p['price'], 2); ?></span>
                        <a href="product.php?id=<?php echo $p['id']; ?>" class="view-btn">View Details</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 50px;">
                    <i class="fas fa-search" style="font-size: 48px; color: #ddd; margin-bottom: 20px;"></i>
                    <p style="color: #777;">No products found matching your criteria.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
