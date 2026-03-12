<?php
include 'check_admin.php';

// Get current page name for active state
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Melody Masters</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #d00b4d, #8e24aa);
            --sidebar-width: 260px;
            --bg-color: #f4f6f9;
            --white: #ffffff;
            --text-dark: #333;
            --text-muted: #777;
            --transition: all 0.3s ease;
        }

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-color);
            display: flex;
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--white);
            position: fixed;
            left: 0;
            top: 0;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 30px 20px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }

        .logo {
            font-size: 20px;
            font-weight: 700;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-decoration: none;
        }

        .sidebar-menu {
            flex: 1;
            padding: 20px 0;
            overflow-y: auto;
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 12px 25px;
            color: var(--text-dark);
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            transition: var(--transition);
            border-left: 4px solid transparent;
        }

        .menu-item i {
            margin-right: 15px;
            width: 20px;
            text-align: center;
            font-size: 18px;
        }

        .menu-item:hover {
            background: rgba(142, 36, 170, 0.05);
            color: #8e24aa;
        }

        .menu-item.active {
            background: rgba(142, 36, 170, 0.1);
            color: #8e24aa;
            border-left: 4px solid #8e24aa;
        }

        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid #eee;
        }

        .logout-btn {
            display: flex;
            align-items: center;
            color: #d00b4d;
            text-decoration: none;
            font-weight: 600;
            padding: 10px;
            border-radius: 8px;
            transition: var(--transition);
        }

        .logout-btn:hover {
            background: rgba(208, 11, 77, 0.1);
        }

        .logout-btn i {
            margin-right: 10px;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            flex: 1;
            padding: 30px;
            min-height: 100vh;
        }

        .top-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: var(--white);
            padding: 15px 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        .page-title h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
            color: var(--text-dark);
        }

        .user-profile {
            display: flex;
            align-items: center;
        }

        .user-info {
            text-align: right;
            margin-right: 15px;
        }

        .user-name {
            display: block;
            font-weight: 600;
            font-size: 14px;
        }

        .user-role {
            font-size: 12px;
            color: var(--text-muted);
            text-transform: capitalize;
        }

        .admin-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            border: 2px solid #8e24aa;
            object-fit: cover;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                width: 80px;
            }
            .sidebar-header .logo span {
                display: none;
            }
            .menu-item span {
                display: none;
            }
            .menu-item i {
                margin-right: 0;
                font-size: 22px;
            }
            .main-content {
                margin-left: 80px;
            }
        }

        @media (max-width: 576px) {
            .sidebar {
                display: none;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-header">
        <a href="index.php" class="logo">
            <i class="fas fa-music"></i> <span>MelodyAdmin</span>
        </a>
    </div>

    <nav class="sidebar-menu">
        <a href="index.php" class="menu-item <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
            <i class="fas fa-th-large"></i> <span>Dashboard</span>
        </a>
        <a href="products.php" class="menu-item <?php echo ($current_page == 'products.php' || strpos($current_page, 'product') !== false) ? 'active' : ''; ?>">
            <i class="fas fa-guitar"></i> <span>Catalog</span>
        </a>
        <a href="inventory.php" class="menu-item <?php echo ($current_page == 'inventory.php') ? 'active' : ''; ?>">
            <i class="fas fa-warehouse"></i> <span>Inventory</span>
        </a>
        <a href="categories.php" class="menu-item <?php echo ($current_page == 'categories.php') ? 'active' : ''; ?>">
            <i class="fas fa-list"></i> <span>Categories</span>
        </a>
        <a href="orders.php" class="menu-item <?php echo ($current_page == 'orders.php' || strpos($current_page, 'order') !== false) ? 'active' : ''; ?>">
            <i class="fas fa-shopping-bag"></i> <span>Orders</span>
        </a>
        <a href="users.php" class="menu-item <?php echo ($current_page == 'users.php') ? 'active' : ''; ?>">
            <i class="fas fa-users"></i> <span>Users</span>
        </a>
        <a href="reviews.php" class="menu-item <?php echo ($current_page == 'reviews.php') ? 'active' : ''; ?>">
            <i class="fas fa-star"></i> <span>Reviews</span>
        </a>
        <a href="../index.php" class="menu-item">
            <i class="fas fa-external-link-alt"></i> <span>View Site</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="../logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
        </a>
    </div>
</aside>

<main class="main-content">
    <div class="top-nav">
        <div class="page-title">
            <h2>
                <?php
                if($current_page == 'index.php') echo "Dashboard Overview";
                elseif($current_page == 'products.php') echo "Catalog Management";
                elseif($current_page == 'inventory.php') echo "Stock Control System";
                elseif($current_page == 'categories.php') echo "Category Management";
                elseif($current_page == 'orders.php') echo "Order Processing";
                elseif($current_page == 'users.php') echo "User Management";
                elseif($current_page == 'reviews.php') echo "Review Moderation";
                else echo "Admin Panel";
                ?>
            </h2>
        </div>

        <div class="user-profile">
            <div class="user-info">
                <span class="user-name"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?></span>
                <span class="user-role"><?php echo htmlspecialchars($_SESSION['user_role'] ?? 'Administrator'); ?></span>
            </div>
            <?php
            // Fetch profile photo
            $profile_stmt = $conn->prepare("SELECT profile_photo FROM users WHERE id = ?");
            $profile_stmt->bind_param("i", $_SESSION['user_id']);
            $profile_stmt->execute();
            $photo_res = $profile_stmt->get_result()->fetch_assoc();
            $photo = $photo_res['profile_photo'] ?? 'default.png';
            $profile_stmt->close();
            ?>
            <img src="../uploads/<?php echo htmlspecialchars($photo); ?>" class="admin-avatar" alt="Avatar">
        </div>
    </div>
