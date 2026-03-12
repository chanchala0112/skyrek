<?php
include 'admin_header.php';

// Handle Status Update
if(isset($_POST['update_status'])) {
    $review_id = (int)$_POST['review_id'];
    $status = $conn->real_escape_string($_POST['status']);
    
    $stmt = $conn->prepare("UPDATE reviews SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $review_id);
    if($stmt->execute()){
        header("Location: reviews.php?success=Review #$review_id updated to $status");
        exit();
    }
}

// Handle Delete
if(isset($_GET['delete'])){
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM reviews WHERE id = ?");
    $stmt->bind_param("i", $id);
    if($stmt->execute()){
        header("Location: reviews.php?success=Review deleted");
        exit();
    }
}

// Fetch all reviews
$query = "SELECT r.*, u.name as user_name, p.name as product_name 
          FROM reviews r 
          JOIN users u ON r.user_id = u.id 
          JOIN products p ON r.product_id = p.id 
          ORDER BY r.created_at DESC";
$reviews = $conn->query($query);
?>

<style>
    .review-card {
        background: var(--white);
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    }
    .status-select {
        padding: 5px;
        border-radius: 5px;
        font-size: 12px;
        border: 1px solid #ddd;
    }
    .rating-stars {
        color: #ffc107;
    }
    .comment-text {
        color: #555;
        font-size: 13px;
        max-width: 300px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .btn-delete {
        color: #d32f2f;
        background: #ffebee;
        padding: 5px 10px;
        border-radius: 5px;
        text-decoration: none;
        font-size: 12px;
    }
</style>

<div class="review-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="color: #8e24aa; margin: 0;">Customer Review Moderation</h3>
    </div>

    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success" style="background:#d4edda; color:#155724; padding:10px; border-radius:8px; margin-bottom:20px;">
            <?php echo htmlspecialchars($_GET['success']); ?>
        </div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Product</th>
                <th>Rating</th>
                <th>Comment</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($r = $reviews->fetch_assoc()): ?>
            <tr>
                <td>#<?php echo $r['id']; ?></td>
                <td><strong><?php echo htmlspecialchars($r['user_name']); ?></strong></td>
                <td><small><?php echo htmlspecialchars($r['product_name']); ?></small></td>
                <td>
                    <div class="rating-stars">
                        <?php for($i=0; $i<$r['rating']; $i++) echo '★'; ?>
                    </div>
                </td>
                <td><div class="comment-text" title="<?php echo htmlspecialchars($r['comment']); ?>"><?php echo htmlspecialchars($r['comment']); ?></div></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="review_id" value="<?php echo $r['id']; ?>">
                        <select name="status" class="status-select" onchange="this.form.submit()">
                            <option value="Approved" <?php echo $r['status'] == 'Approved' ? 'selected' : ''; ?>>Approved</option>
                            <option value="Hidden" <?php echo $r['status'] == 'Hidden' ? 'selected' : ''; ?>>Hidden</option>
                        </select>
                        <input type="hidden" name="update_status" value="1">
                    </form>
                </td>
                <td><small><?php echo date('M d, Y', strtotime($r['created_at'])); ?></small></td>
                <td>
                    <a href="reviews.php?delete=<?php echo $r['id']; ?>" class="btn-delete" onclick="return confirm('Protect this delete?')">
                        <i class="fas fa-trash"></i> Delete
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
