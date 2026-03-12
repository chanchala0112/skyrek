<?php
session_start();
include 'includes/db.php';

// Initialize variables
$name = $email = $password = $address = $contact = "";
$profile_photo = 'default.png'; 
$errors = [];

if(isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $address = trim($_POST['address']);
    $contact = trim($_POST['contact']);

    if(empty($name)) $errors[] = "Name is required";
    if(empty($email)) $errors[] = "Email is required";
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";
    if(empty($password)) $errors[] = "Password is required";
    if($password !== $confirm_password) $errors[] = "Passwords do not match";

    $profile_photo = 'default.png';
    if(isset($_FILES['profile_photo']) && $_FILES['profile_photo']['name'] != "") {
        $file_name = $_FILES['profile_photo']['name'];
        $file_tmp = $_FILES['profile_photo']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg','jpeg','png','gif'];

        if(!in_array($file_ext, $allowed_ext)){
            $errors[] = "Invalid image type. Allowed: jpg, jpeg, png, gif";
        } else {
            $new_file_name = uniqid() . "." . $file_ext;
            $destination = "uploads/" . $new_file_name;
            if(move_uploaded_file($file_tmp, $destination)) {
                $profile_photo = $new_file_name;
            } else {
                $errors[] = "Failed to upload profile photo";
            }
    }
}

    // Check if email already exists
    if(empty($errors)) {
        $check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check_email->bind_param("s", $email);
        $check_email->execute();
        $check_email->store_result();
        if($check_email->num_rows > 0) {
            $errors[] = "Email address is already registered.";
        }
        $check_email->close();
    }

    if(empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name,email,password,address,contact,profile_photo) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param("ssssss", $name, $email, $hashed_password, $address, $contact, $profile_photo);
        if($stmt->execute()){
            $_SESSION['success'] = "Registration successful! You can now login.";
            header("Location: login.php");
            exit();
        } else {
            $errors[] = "Email already exists or database error.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Register - Melody Masters</title>
<style>
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #f5e8f2, #e8e0e0);
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 90vh;
    margin: 0;
}

.form-container {
    background-color: #fff;
    padding: 35px 30px;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    width: 600px;
    max-width: 90%;
}

.form-container h2 {
    color: #8e24aa;
    margin-bottom: 25px;
    font-weight: 600;
    text-align: center;
}

/* Form rows */
.form-row {
    display: flex;
    align-items: center;
    margin: 10px 0;
}

.form-row label {
    width: 120px;       /* fixed width for all labels */
    text-align: left;   /* left align text */
    margin-right: 15px;
    font-weight: 500;
    font-size: 14px;
}

.form-row input[type="text"],
.form-row input[type="email"],
.form-row input[type="password"],
.form-row input[type="file"] {
    flex: 1;
    padding: 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 14px;
    transition: 0.3s;
}

.form-row input:focus {
    border-color: #8e24aa;
    outline: none;
    box-shadow: 0 0 6px rgba(142,36,170,0.2);
}

input[type="submit"] {
    width: 100%;
    padding: 12px;
    margin-top: 20px;
    background: linear-gradient(135deg, #d00b4d, #8e24aa);
    color: white;
    border: none;
    border-radius: 6px;
    font-weight: 500;
    font-size: 16px;
    cursor: pointer;
    transition: 0.3s;
}

input[type="submit"]:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(208,11,77,0.3);
}

.error-list {
    color: red;
    margin-bottom: 15px;
    text-align: left;
    padding-left: 20px;
}

.success-msg {
    color: green;
    text-align: center;
    margin-bottom: 15px;
}

.profile-preview {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    display: block;
    margin: 0 auto 15px auto;
    border: 2px solid #ccc;
}

.profile-placeholder {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background-color: #ddd;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 0 auto 15px auto;
    font-size: 50px;
    color: #888;
}

.form-container p {
    margin-top: 15px;
    font-size: 14px;
    text-align: center;
}

.form-container p a {
    color: #d00b4d;
    text-decoration: none;
    font-weight: 500;
}

.form-container p a:hover {
    text-decoration: underline;
}

/* Responsive */
@media(max-width: 480px) {
    .form-row {
        flex-direction: column;
        align-items: stretch;
    }
    .form-row label {
        margin-bottom: 5px;
        width: 100%;
    }
}
</style>
</head>
<body>

<div class="form-container">
<h2>Register</h2>

<?php
if(!empty($errors)){
    echo "<ul class='error-list'>";
    foreach($errors as $error){
        echo "<li>".$error."</li>";
    }
    echo "</ul>";
}

if(isset($_SESSION['success'])){
    echo "<p class='success-msg'>".$_SESSION['success']."</p>";
    unset($_SESSION['success']);
}
?>

<form action="" method="POST" enctype="multipart/form-data">

    <div id="profilePreviewContainer">
        <?php if($profile_photo != 'default.png'): ?>
            <img id="profilePreview" src="uploads/<?php echo $profile_photo; ?>" class="profile-preview">
        <?php else: ?>
            <div id="profilePreview" class="profile-placeholder">&#128100;</div>
        <?php endif; ?>
    </div>

    <div class="form-row">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" placeholder="Enter your name" value="<?php echo htmlspecialchars($name); ?>">
    </div>

    <div class="form-row">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" placeholder="Enter your email" value="<?php echo htmlspecialchars($email); ?>">
    </div>

    <div class="form-row">
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" placeholder="Enter your password">
    </div>

    <div class="form-row">
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password">
    </div>

    <div class="form-row">
        <label for="address">Address:</label>
        <input type="text" id="address" name="address" placeholder="Enter your address" value="<?php echo htmlspecialchars($address); ?>">
    </div>

    <div class="form-row">
        <label for="contact">Contact:</label>
        <input type="text" id="contact" name="contact" placeholder="Enter your contact number" value="<?php echo htmlspecialchars($contact); ?>">
    </div>

    <div class="form-row">
        <label for="profile_photo">Profile Photo:</label>
        <input type="file" id="profile_photo" name="profile_photo">
    </div>

    <input type="submit" name="register" value="Register">
</form>

<p>Already have an account? <a href="login.php">Login here</a></p>
</div>

<script>
const fileInput = document.querySelector('input[name="profile_photo"]');
const preview = document.getElementById('profilePreview');

fileInput.addEventListener('change', function(event){
    const file = event.target.files[0];
    if(file){
        const reader = new FileReader();
        reader.onload = function(e){
            if(preview.tagName === "DIV"){
                const img = document.createElement("img");
                img.src = e.target.result;
                img.className = "profile-preview";
                preview.replaceWith(img);
            } else {
                preview.src = e.target.result;
            }
        }
        reader.readAsDataURL(file);
    }
});
</script>

</body>
</html>