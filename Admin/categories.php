<?php
include 'admin_header.php';

$message = '';

// Handle Delete
if(isset($_GET['delete'])){
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM categories WHERE id = $id");
    header("Location: categories.php?success=Category deleted");
    exit();
}

// Handle Add/Update
if(isset($_POST['save_category'])){
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);
    $cat_id = (int)$_POST['category_id'];
    $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : "NULL";
    
    // Image Upload
    $image = $_POST['existing_image'] ?? 'default_cat.png';
    if(isset($_FILES['image']) && $_FILES['image']['name'] != ""){
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $new_name = 'cat_' . uniqid() . '.' . $ext;
        if(move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/" . $new_name)){
            $image = $new_name;
        }
    }

    if($cat_id > 0) {
        $conn->query("UPDATE categories SET name='$name', description='$description', image='$image', parent_id=$parent_id WHERE id=$cat_id");
    } else {
        $conn->query("INSERT INTO categories (name, description, image, parent_id) VALUES ('$name', '$description', '$image', $parent_id)");
    }

    header("Location: categories.php?success=Category saved");
    exit();
}

$categories = $conn->query("SELECT * FROM categories ORDER BY name ASC");
$edit_cat = null;
if(isset($_GET['edit'])){
    $id = (int)$_GET['edit'];
    $edit_cat = $conn->query("SELECT * FROM categories WHERE id = $id")->fetch_assoc();
}
?>

<style>
    .cat-container {
        display: grid;
        grid-template-columns: 1fr 1.5fr;
        gap: 30px;
    }

    .form-card {
        background: var(--white);
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        height: fit-content;
    }

    .list-card {
        background: var(--white);
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    }

    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px; }
    .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; }

    .cat-img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }

    .btn-save {
        background: var(--primary-gradient);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        width: 100%;
        font-weight: 600;
        margin-top: 10px;
    }

    .actions { display: flex; gap: 8px; }
    .btn-mini { padding: 5px 10px; border-radius: 5px; text-decoration: none; font-size: 12px; }
    .edit { background: #e3f2fd; color: #1976d2; }
    .del { background: #ffebee; color: #d32f2f; }
</style>

<div class="cat-container">
    <!-- Form -->
    <div class="form-card">
        <h3><?php echo $edit_cat ? 'Edit' : 'Add New'; ?> Category</h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="category_id" value="<?php echo $edit_cat['id'] ?? ''; ?>">
            <input type="hidden" name="existing_image" value="<?php echo $edit_cat['image'] ?? ''; ?>">

            <div class="form-group">
                <label>Category Name</label>
                <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($edit_cat['name'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($edit_cat['description'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label>Parent Category (Optional)</label>
                <select name="parent_id" class="form-control">
                    <option value="">None (Top Level)</option>
                    <?php 
                    $parents = $conn->query("SELECT * FROM categories WHERE parent_id IS NULL ORDER BY name ASC");
                    while($p = $parents->fetch_assoc()): 
                    ?>
                        <option value="<?php echo $p['id']; ?>" <?php echo (isset($edit_cat) && $edit_cat['parent_id'] == $p['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($p['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Category Image</label>
                <input type="file" name="image" class="form-control">
                <?php if($edit_cat && $edit_cat['image']): ?>
                    <img src="../uploads/<?php echo $edit_cat['image']; ?>" style="width: 60px; margin-top: 10px;">
                <?php endif; ?>
            </div>

            <button type="submit" name="save_category" class="btn-save">
                <?php echo $edit_cat ? 'Update Category' : 'Create Category'; ?>
            </button>
            <?php if($edit_cat): ?>
                <a href="categories.php" style="display:block; text-align:center; margin-top:10px; color:#777; font-size:13px;">Cancel Edit</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- List -->
    <div class="list-card">
        <h3>All Categories</h3>
        <table>
            <thead>
                <tr>
                    <th>Icon</th>
                    <th>Name</th>
                    <th>Products</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($cat = $categories->fetch_assoc()): ?>
                <?php 
                    $cid = $cat['id'];
                    $pcount = $conn->query("SELECT COUNT(*) as count FROM products WHERE category_id = $cid")->fetch_assoc()['count'];
                ?>
                <tr>
                    <td><img src="../uploads/<?php echo htmlspecialchars($cat['image'] ?? 'default_cat.png'); ?>" class="cat-img"></td>
                    <td><?php echo htmlspecialchars($cat['name']); ?></td>
                    <td><?php echo $pcount; ?> items</td>
                    <td class="actions">
                        <a href="categories.php?edit=<?php echo $cat['id']; ?>" class="btn-mini edit"><i class="fas fa-edit"></i></a>
                        <a href="categories.php?delete=<?php echo $cat['id']; ?>" class="btn-mini del" onclick="return confirm('Delete this category?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</main>
</body>
</html>
