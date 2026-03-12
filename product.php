<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch product details
$product_query = $conn->query("SELECT * FROM products WHERE id = $id");
if($product_query->num_rows == 0){
    header("Location: shop.php");
    exit();
}
$row = $product_query->fetch_assoc();

$is_logged_in = isset($_SESSION['user_id']);
$user_id = $is_logged_in ? $_SESSION['user_id'] : 0;

/* Check if user has purchased this product */
$has_purchased = false;
if($is_logged_in) {
    $purchase_check = $conn->query("
        SELECT oi.id 
        FROM order_items oi 
        JOIN orders o ON oi.order_id = o.id 
        WHERE o.user_id = $user_id AND oi.product_id = $id AND (o.status = 'Completed' OR o.status = 'Paid')
    ");
    if($purchase_check && $purchase_check->num_rows > 0) $has_purchased = true;
}

/* Fetch reviews */
$reviews = $conn->query("
    SELECT r.*, u.name as user_name, u.profile_photo 
    FROM reviews r 
    JOIN users u ON r.user_id = u.id 
    WHERE r.product_id = $id AND r.status = 'Approved'
    ORDER BY r.created_at DESC
");

/* Handle Review Submission */
if(isset($_POST['submit_review']) && $has_purchased) {
    $rating = intval($_POST['rating']);
    $comment = $conn->real_escape_string($_POST['comment']);
    $stmt = $conn->prepare("INSERT INTO reviews (user_id, product_id, rating, comment) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $user_id, $id, $rating, $comment);
    $stmt->execute();
    header("Location: product.php?id=$id&msg=Review submitted!");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $row['name']; ?></title>
    <style>
        body { margin:0; font-family:'Poppins', sans-serif; background:#e8e0e0; }
        .product-container { max-width:1100px; margin:50px auto; background:white; border-radius:12px; padding:30px; display:flex; gap:40px; box-shadow:0 10px 30px rgba(0,0,0,0.1); }
        .product-image img { width:400px; border-radius:10px; }
        .product-details h1 { margin-top:0; color:#8e24aa; }
        .price { font-size:24px; color:#d00b4d; font-weight:bold; margin:10px 0; }
        .stock { color:#555; margin-bottom:20px; }
        .description { line-height:1.6; color:#444; }
        .btn-cart { margin-top:25px; padding:12px 22px; background:linear-gradient(135deg,#d00b4d,#8e24aa); color:white; border:none; border-radius:6px; font-weight:500; cursor:pointer; transition:0.3s; }
        .btn-cart:hover { transform:translateY(-2px); box-shadow:0 6px 18px rgba(208,11,77,0.3); }
        @media(max-width:768px) { .product-container { flex-direction:column; text-align:center; } .product-image img { width:100%; } }
    </style>
</head>
<body>

<div class="product-container">

    <div class="product-image">
        <img src="uploads/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>">
    </div>

    <div class="product-details">
        <h1><?php echo $row['name']; ?></h1>

        <div class="price">£<?php echo $row['price']; ?></div>

        <div class="stock">
            Stock Available: <?php echo $row['stock']; ?>
        </div>

        <div class="description">
            <?php echo $row['description']; ?>
        </div>

        <?php if($row['stock'] <= 0): ?>
            <p style="color: #d00b4d; font-weight: bold; margin-top: 20px;">Out of Stock</p>
        <?php else: ?>
            <button class="btn-cart" onclick="addToCart()">Add to Cart</button>
        <?php endif; ?>
    </div>
</div>

<!-- REVIEWS SECTION -->
<div id="reviews" class="product-container" style="flex-direction: column; margin-top: 0;">
    <h2 style="color: #8e24aa;">Customer Reviews</h2>

    <?php if($has_purchased): ?>
        <div class="review-form" style="background: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
            <h3>Leave a Review</h3>
            <form method="POST">
                <div style="margin-bottom: 15px;">
                    <label>Rating:</label>
                    <select name="rating" required style="padding: 5px; border-radius: 4px;">
                        <option value="5">5 Stars</option>
                        <option value="4">4 Stars</option>
                        <option value="3">3 Stars</option>
                        <option value="2">2 Stars</option>
                        <option value="1">1 Star</option>
                    </select>
                </div>
                <div style="margin-bottom: 15px;">
                    <textarea name="comment" required placeholder="Write your review here..." style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ddd;"></textarea>
                </div>
                <button type="submit" name="submit_review" class="btn-cart" style="margin-top: 0;">Submit Review</button>
            </form>
        </div>
    <?php elseif($is_logged_in): ?>
        <p style="background: #f0f0f0; padding: 15px; border-radius: 8px; font-size: 14px; color: #666;">
            <i class="fas fa-info-circle"></i> Only customers who have purchased this product (Order Status: Paid or Completed) can leave a review.
        </p>
    <?php endif; ?>

    <div class="reviews-list">
        <?php if($reviews->num_rows > 0): ?>
            <?php while($rev = $reviews->fetch_assoc()): ?>
                <div class="review-item" style="border-bottom: 1px solid #eee; padding: 20px 0;">
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 10px;">
                        <img src="uploads/<?php echo $rev['profile_photo']; ?>" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                        <div>
                            <strong style="display: block;"><?php echo htmlspecialchars($rev['user_name']); ?></strong>
                            <span style="color: #ffc107;">
                                <?php for($i=0; $i<$rev['rating']; $i++) echo '★'; ?>
                                <?php for($i=0; $i<5-$rev['rating']; $i++) echo '☆'; ?>
                            </span>
                        </div>
                        <small style="margin-left: auto; color: #999;"><?php echo date('M d, Y', strtotime($rev['created_at'])); ?></small>
                    </div>
                    <p style="color: #555; line-height: 1.5;"><?php echo htmlspecialchars($rev['comment']); ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="color: #777; font-style: italic;">No reviews yet for this product.</p>
        <?php endif; ?>
    </div>
</div>

<script>
function addToCart() {
    var isLoggedIn = <?php echo $is_logged_in ? 'true' : 'false'; ?>;

    if (!isLoggedIn) {
        alert("You must login first to add products to your cart.");
        window.location.href = "login.php";
    } else {
        // Redirect to add_to_cart.php
        window.location.href = "add_to_cart.php?id=<?php echo $row['id']; ?>";
    }
}
</script>

</body>
</html>

<?php include 'includes/footer.php'; ?>