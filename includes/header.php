<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

include 'includes/db.php';

// Check if user is logged in
$user = null;
if(isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT name, profile_photo FROM users WHERE id=? LIMIT 1");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    // fallback for profile photo
    $user['profile_photo'] = $user['profile_photo'] ? $user['profile_photo'] : 'default.png';
}

// Count cart items (assume $_SESSION['cart'] = [product_id => quantity])
$cart_count = 0;
if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])){
    foreach($_SESSION['cart'] as $qty){
        $cart_count += $qty;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
body { margin: 0; font-family: 'Poppins', sans-serif; }

header {
    background: white;
    padding: 18px 50px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: sticky;
    top: 0;
    z-index: 1000;
    transition: all 0.35s ease;
    border-bottom: 1px solid #eee;
}

header.shrink {
    padding: 10px 50px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
}

.logo-section {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}

.logo {
    font-size: 22px;
    font-weight: 600;
    background: linear-gradient(135deg, #d00b4d, #8e24aa);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.profile-container {
    display: flex;
    align-items: center;
    margin-top: 8px;
}

.profile-mini {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #8e24aa;
    margin-right: 10px;
}

.profile-name {
    font-size: 14px;
    font-weight: 600;
    color: #8e24aa;
}

nav a {
    color: #000;
    text-decoration: none;
    margin: 0 18px;
    font-size: 16px;
    font-weight: 600;
    position: relative;
    transition: 0.3s ease;
}

nav a::after {
    content: "";
    position: absolute;
    left: 0;
    bottom: -6px;
    width: 0;
    height: 3px;
    background: linear-gradient(135deg, #d00b4d, #8e24aa);
    transition: width 0.3s ease;
}

nav a:hover::after { width: 100%; }

.right-menu {
    display: flex;
    align-items: center;
}

.right-menu a {
    color: #444;
    text-decoration: none;
    margin-left: 25px;
    font-size: 14px;
    transition: 0.3s ease;
}

.right-menu a:hover { color: #d00b4d; }

.cart {
    font-size: 18px;
    position: relative;
}

.cart-count {
    position: absolute;
    top: -6px;
    right: -10px;
    background: #d00b4d;
    color: white;
    font-size: 12px;
    font-weight: 600;
    padding: 2px 6px;
    border-radius: 50%;
}

.btn-login {
    background: linear-gradient(135deg, #d00b4d, #8e24aa);
    padding: 8px 18px;
    border-radius: 25px;
    color: white !important;
    font-weight: 500;
}

@media(max-width: 768px){
    header { flex-direction: column; align-items: flex-start; padding: 20px; }
    nav { margin-top: 10px; }
    .right-menu { margin-top: 10px; }
}
</style>
</head>
<body>

<header id="header">

    <div class="logo-section">
        <div class="logo">🎵 Melody Masters</div>

        <?php if($user): ?>
        <div class="profile-container">
            <img src="uploads/<?php echo htmlspecialchars($user['profile_photo']); ?>" class="profile-mini">
            <span class="profile-name">Hi, <?php echo htmlspecialchars($user['name']); ?></span>
        </div>
        <?php endif; ?>
    </div>

    <nav>
        <a href="index.php">Home</a>
        <a href="shop.php">Shop</a>
        <a href="orders.php">Orders</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
    </nav>

    <div class="right-menu">
        <a href="cart.php" class="cart">
            <i class="fas fa-shopping-cart"></i> Cart
            <?php if($cart_count > 0): ?>
                <span class="cart-count"><?php echo $cart_count; ?></span>
            <?php endif; ?>
        </a>

        <?php if(!$user): ?>
            <a href="login.php" class="btn-login">Login</a>
        <?php else: ?>
            <a href="profile.php">Profile</a>
            <a href="logout.php">Logout</a>
        <?php endif; ?>
    </div>

</header>

<script>
window.addEventListener("scroll", function() {
    const header = document.getElementById("header");
    header.classList.toggle("shrink", window.scrollY > 50);
});
</script>

</body>
</html>