<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';

/* Featured products */
$featured = $conn->query("SELECT * FROM products ORDER BY id DESC LIMIT 16");

/* Categories */
$categories = $conn->query("SELECT * FROM categories ORDER BY name ASC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Melody Masters</title>

<style>
body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background: #e8e0e0;
    color: #333;
}

/* HERO */
.hero {
    height: 90vh;
    background: url('images/img.png') center/cover no-repeat;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
}

.hero-content {
    background: rgba(255,255,255,0.15);
    padding: 50px;
    border-radius: 15px;
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,0.3);
    max-width: 600px;
}

.hero h1 { font-size: 48px; margin-bottom: 10px; }
.hero p { font-size: 18px; }

.shop-btn {
    padding: 12px 25px;
    background: linear-gradient(135deg,#d00b4d,#8e24aa);
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-weight: bold;
}

/* SECTION */
.section {
    padding: 50px;
}

.section h2 {
    text-align: center;
    margin-bottom: 30px;
}

/* FEATURED GRID */
.featured-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 25px;
}

@media (max-width: 992px) {
    .featured-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 576px) {
    .featured-grid { grid-template-columns: 1fr; }
}

.product-card {
    background: #fff;
    padding: 15px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 5px 18px rgba(0,0,0,0.08);
    transition: 0.3s;
}

.product-card:hover {
    transform: translateY(-5px);
}

.image-wrapper {
    position: relative;
}

.product-card img {
    width: 100%;
    height: 170px;
    object-fit: cover;
    border-radius: 8px;
}

/* HEART STYLE */
.wishlist {
    position: absolute;
    top: 10px;
    right: 12px;
    font-size: 22px;
    color: white;
    cursor: pointer;
    transition: 0.3s ease;
    text-shadow: 0 2px 6px rgba(0,0,0,0.4);
}

.wishlist.active {
    color: #d00b4d;
}

.product-card h3 {
    margin: 12px 0 6px;
    color: #8e24aa;
}

.price {
    color: #d00b4d;
    font-weight: bold;
}

.details-btn {
    display: inline-block;
    margin-top: 10px;
    padding: 8px 15px;
    background: linear-gradient(135deg,#d00b4d,#8e24aa);
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-size: 14px;
}

/* CATEGORY GRID */
.category-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.category-card {
    background: white;
    padding: 20px;
    text-align: center;
    border-radius: 15px;
    box-shadow: 0 5px 18px rgba(0,0,0,0.08);
    text-decoration: none;
    color: #8e24aa;
    transition: 0.3s;
}

.category-card:hover {
    transform: translateY(-5px);
}

.category-card img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 50%;
    margin-bottom: 10px;
}
</style>
</head>
<body>

<!-- HERO -->
<div class="hero">
    <div class="hero-content">
        <h1>Welcome to Melody Masters</h1>
        <p>Discover premium instruments for beginners and professionals.</p>
        <a href="shop.php" class="shop-btn">Shop Now</a>
    </div>
</div>

<!-- FEATURED PRODUCTS -->
<div class="section">
    <h2>Featured Products</h2>

    <div class="featured-grid">
        <?php while($row = $featured->fetch_assoc()): ?>
            <div class="product-card">

                <div class="image-wrapper">
                    <img src="uploads/<?php echo $row['image']; ?>">
                    <span class="wishlist" onclick="toggleHeart(this)">&#10084;</span>
                </div>

                <h3><?php echo $row['name']; ?></h3>
                <p class="price">£<?php echo $row['price']; ?></p>
                <a href="product.php?id=<?php echo $row['id']; ?>" class="details-btn">View Details</a>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- SHOP BY CATEGORY -->
<div class="section">
    <h2>Shop by Category</h2>

    <div class="category-grid">
        <?php while($cat = $categories->fetch_assoc()): ?>
            <?php
            $cat_id = $cat['id'];
            $check = $conn->query("SELECT id FROM products WHERE category_id = $cat_id LIMIT 1");
            if($check->num_rows > 0):
            ?>
            <a href="shop.php?category=<?php echo $cat_id; ?>" class="category-card">
                <img src="uploads/<?php echo $cat['image']; ?>">
                <h3><?php echo $cat['name']; ?></h3>
            </a>
            <?php endif; ?>
        <?php endwhile; ?>
    </div>
</div>

<script>
function toggleHeart(element) {
    element.classList.toggle("active");
}
</script>

</body>
</html>

<?php include 'includes/footer.php'; ?>