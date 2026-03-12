<?php
include 'admin_header.php';

$message = '';
$categories = $conn->query("SELECT * FROM categories ORDER BY name ASC");

if(isset($_POST['add_product'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $category_id = (int)$_POST['category_id'];
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $description = $conn->real_escape_string($_POST['description']);
    
    // Image Upload
    $image = 'default_product.png';
    if(isset($_FILES['image']) && $_FILES['image']['name'] != ""){
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $new_name = uniqid() . '.' . $ext;
        if(move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/" . $new_name)){
            $image = $new_name;
        }
    }

    $stmt = $conn->prepare("INSERT INTO products (name, category_id, price, stock, image, description) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("siddss", $name, $category_id, $price, $stock, $image, $description);
    
    // Handle Digital Product
    if(isset($_FILES['digital_file']) && $_FILES['digital_file']['name'] != ""){
        $d_ext = strtolower(pathinfo($_FILES['digital_file']['name'], PATHINFO_EXTENSION));
        $d_new_name = 'sheet_' . uniqid() . '.' . $d_ext;
        if(!is_dir("../uploads/digital")) mkdir("../uploads/digital");
        if(move_uploaded_file($_FILES['digital_file']['tmp_name'], "../uploads/digital/" . $d_new_name)){
            // Insert or update digital_products table
            $conn->query("INSERT INTO digital_products (product_id, file_path) VALUES ($product_id, '$d_new_name') ON DUPLICATE KEY UPDATE file_path='$d_new_name'");
        }
    }

    if($stmt->execute()){
        $product_id = $stmt->insert_id;

        // Handle Digital Product
        if(isset($_FILES['digital_file']) && $_FILES['digital_file']['name'] != ""){
            $d_ext = strtolower(pathinfo($_FILES['digital_file']['name'], PATHINFO_EXTENSION));
            $d_new_name = 'sheet_' . uniqid() . '.' . $d_ext;
            if(!is_dir("../uploads/digital")) mkdir("../uploads/digital", 0777, true);
            if(move_uploaded_file($_FILES['digital_file']['tmp_name'], "../uploads/digital/" . $d_new_name)){
                $conn->query("INSERT INTO digital_products (product_id, file_path) VALUES ($product_id, '$d_new_name')");
            }
        }
        
        header("Location: products.php?success=Product added successfully");
        exit();
    } else {
        $message = "Error adding product: " . $conn->error;
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
    <?php if($message): ?>
        <div class="alert alert-danger" style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px;"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Product Name</label>
            <input type="text" name="name" class="form-control" required placeholder="e.g. Fender Stratocaster">
        </div>

        <div class="row">
            <div class="form-group">
                <label>Category</label>
                <select name="category_id" class="form-control" required>
                    <option value="">Select Category</option>
                    <?php 
                    $categories->data_seek(0);
                    while($cat = $categories->fetch_assoc()): 
                    ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Price (£)</label>
                <input type="number" step="0.01" name="price" class="form-control" required placeholder="0.00">
            </div>
        </div>

        <div class="row">
            <div class="form-group">
                <label>Stock Quantity</label>
                <input type="number" name="stock" class="form-control" required value="10">
            </div>
            <div class="form-group">
                <label>Product Image</label>
                <input type="file" name="image" class="form-control" onchange="previewImage(this)">
                <img id="img-preview" src="../images/placeholder.png" class="preview-img" style="display:none;">
            </div>
        </div>

        <div class="form-group">
            <label>Digital File (Sheet Music - Optional)</label>
            <input type="file" name="digital_file" class="form-control">
            <small>Leave empty for physical products. Uploading a file makes this a digital product.</small>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="5" placeholder="Enter product details..."></textarea>
        </div>

        <button type="submit" name="add_product" class="submit-btn">Save Product</button>
    </form>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('img-preview').src = e.target.result;
            document.getElementById('img-preview').style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

</main>
</body>
</html>
