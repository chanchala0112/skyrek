<?php
include 'admin_header.php';

// Handle Role Update
if(isset($_POST['update_role'])) {
    $uid = (int)$_POST['user_id'];
    $role = $conn->real_escape_string($_POST['role']);
    
    // Prevent self-demotion if current user is admin
    if($uid == $_SESSION['user_id'] && $role != 'admin'){
        header("Location: users.php?error=You cannot demote yourself!");
        exit();
    }

    $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->bind_param("si", $role, $uid);
    if($stmt->execute()){
        header("Location: users.php?success=User role updated");
        exit();
    }
}

// Handle Delete (Optional/Admin only)
if(isset($_GET['delete']) && $_SESSION['user_role'] == 'admin'){
    $uid = (int)$_GET['delete'];
    if($uid != $_SESSION['user_id']){
        $conn->query("DELETE FROM users WHERE id = $uid");
        header("Location: users.php?success=User deleted");
        exit();
    }
}

$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
?>

<style>
    .user-card {
        background: var(--white);
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    }

    .u-avatar {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        object-fit: cover;
    }

    .role-select {
        padding: 5px;
        border-radius: 5px;
        font-size: 12px;
        border: 1px solid #ddd;
    }

    .badge {
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
    }
    .badge-admin { background: #fee2e2; color: #991b1b; }
    .badge-staff { background: #fef3c7; color: #92400e; }
    .badge-customer { background: #dcfce7; color: #166534; }
</style>

<?php if(isset($_GET['success'])): ?>
    <div class="alert alert-success" style="background:#d4edda; color:#155724; padding:15px; border-radius:8px; margin-bottom:20px;"><?php echo htmlspecialchars($_GET['success']); ?></div>
<?php endif; ?>

<?php if(isset($_GET['error'])): ?>
    <div class="alert alert-danger" style="background:#f8d7da; color:#721c24; padding:15px; border-radius:8px; margin-bottom:20px;"><?php echo htmlspecialchars($_GET['error']); ?></div>
<?php endif; ?>

<div class="user-card">
    <table>
        <thead>
            <tr>
                <th>User</th>
                <th>Email</th>
                <th>Role</th>
                <th>Contact</th>
                <th>Joined</th>
                <?php if($_SESSION['user_role'] == 'admin'): ?>
                <th>Actions</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php while($u = $users->fetch_assoc()): ?>
            <tr>
                <td>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <img src="../uploads/<?php echo htmlspecialchars($u['profile_photo']); ?>" class="u-avatar">
                        <span><?php echo htmlspecialchars($u['name']); ?></span>
                    </div>
                </td>
                <td><?php echo htmlspecialchars($u['email']); ?></td>
                <td>
                    <?php if($_SESSION['user_role'] == 'admin'): ?>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                            <select name="role" class="role-select" onchange="this.form.submit()">
                                <option value="customer" <?php echo $u['role'] == 'customer' ? 'selected' : ''; ?>>Customer</option>
                                <option value="staff" <?php echo $u['role'] == 'staff' ? 'selected' : ''; ?>>Staff</option>
                                <option value="admin" <?php echo $u['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                            </select>
                            <input type="hidden" name="update_role" value="1">
                        </form>
                    <?php else: ?>
                        <span class="badge badge-<?php echo $u['role']; ?>"><?php echo $u['role']; ?></span>
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($u['contact']); ?></td>
                <td><?php echo date('M d, Y', strtotime($u['created_at'])); ?></td>
                <?php if($_SESSION['user_role'] == 'admin'): ?>
                <td>
                    <?php if($u['id'] != $_SESSION['user_id']): ?>
                        <a href="users.php?delete=<?php echo $u['id']; ?>" style="color:#d32f2f;" onclick="return confirm('Permanently delete this user?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    <?php endif; ?>
                </td>
                <?php endif; ?>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</main>
</body>
</html>
