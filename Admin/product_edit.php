<?php
include 'admin_header.php';

$message = '';
$id = (int)($_GET['id'] ?? 0);

// Fetch existing product
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$product) {
    header("Location: products.php");
    exit();
}

$categories = $conn->query("SELECT * FROM categories ORDER BY name ASC");

if(isset($_POST['update_product'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $category_id = (int)$_POST['category_id'];
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $description = $conn->real_escape_string($_POST['description']);
    
    // Image Upload
    $image = $product['image'];
    if(isset($_FILES['image']) && $_FILES['image']['name'] != ""){
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $new_name = uniqid() . '.' . $ext;
        if(move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/" . $new_name)){
            // Optional: delete old image if it's not default
            if($product['image'] != 'default_product.png' && file_exists("../uploads/" . $product['image'])){
                unlink("../uploads/" . $product['image']);
            }
            $image = $new_name;
        }
    }

    $stmt = $conn->prepare("UPDATE products SET name=?, category_id=?, price=?, stock=?, image=?, description=? WHERE id=?");
    $stmt->bind_param("siddssi", $name, $category_id, $price, $stock, $image, $description, $id);
    
    // Handle Digital Product
    if(isset($_FILES['digital_file']) && $_FILES['digital_file']['name'] != ""){
        $d_ext = strtolower(pathinfo($_FILES['digital_file']['name'], PATHINFO_EXTENSION));
        $d_new_name = 'sheet_' . uniqid() . '.' . $d_ext;
        if(!is_dir("../uploads/digital")) mkdir("../uploads/digital");
        if(move_uploaded_file($_FILES['digital_file']['tmp_name'], "../uploads/digital/" . $d_new_name)){
            $conn->query("INSERT INTO digital_products (product_id, file_path) VALUES ($id, '$d_new_name') ON DUPLICATE KEY UPDATE file_path='$d_new_name'");
        }
    }
    if($stmt->execute()){
        header("Location: products.php?success=Product updated successfully");
        exit();
    } else {
        $message = "Error updating product: " . $conn->error;
    }
}
?>

<style>
    .form-card {
        background: var(--white);
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        max-width: 800px;
        margin: 0 auto;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        font-size: 14px;
    }

    .form-control {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-family: inherit;
    }

    .row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .submit-btn {
        background: var(--primary-gradient);
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        width: 100%;
        margin-top: 10px;
    }

    .preview-img {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 8px;
        margin-top: 10px;
        border: 1px solid #ddd;
    }
</style>

<div class="form-card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h3 style="margin:0;">Edit Product</h3>
        <a href="products.php" style="color: var(--text-muted); text-decoration: none;"><i class="fas fa-arrow-left"></i> Back</a>
    </div>

    <?php if($message): ?>
        <div class="alert alert-danger" style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px;"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Product Name</label>
            <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($product['name']); ?>">
        </div>

        <div class="row">
            <div class="form-group">
                <label>Category</label>
                <select name="category_id" class="form-control" required>
                    <option value="">Select Category</option>
                    <?php while($cat = $categories->fetch_assoc()): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo ($product['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Price (£)</label>
                <input type="number" step="0.01" name="price" class="form-control" required value="<?php echo $product['price']; ?>">
            </div>
        </div>

        <div class="row">
            <div class="form-group">
                <label>Stock Quantity</label>
                <input type="number" name="stock" class="form-control" required value="<?php echo $product['stock']; ?>">
            </div>
            <div class="form-group">
                <label>Change Product Image</label>
                <input type="file" name="image" class="form-control" onchange="previewImage(this)">
                <img id="img-preview" src="../uploads/<?php echo htmlspecialchars($product['image']); ?>" class="preview-img">
            </div>
        </div>

        <div class="form-group">
            <label>Digital File (Sheet Music - Optional)</label>
            <input type="file" name="digital_file" class="form-control">
            <?php 
                $check_d = $conn->query("SELECT * FROM digital_products WHERE product_id = $id");
                if($check_d->num_rows > 0){
                    $d_item = $check_d->fetch_assoc();
                    echo "<small style='color:green;'>Current file: " . $d_item['file_path'] . "</small>";
                }
            ?>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="5"><?php echo htmlspecialchars($product['description']); ?></textarea>
        </div>

        <button type="submit" name="update_product" class="submit-btn">Update Product</button>
    </form>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('img-preview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

</main>
</body>
</html>
