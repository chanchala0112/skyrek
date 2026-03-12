<?php
session_start();
include 'includes/db.php';

// Redirect if not logged in
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$errors = [];
$success = "";

// Handle form submission
if(isset($_POST['update'])) {

    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $contact = trim($_POST['contact']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if(empty($name)) {
        $errors[] = "Name is required";
    }

    // Password update
    if($password || $confirm_password) {
        if($password !== $confirm_password) {
            $errors[] = "Passwords do not match";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        }
    }

    // Profile Photo
    $profile_photo = $_POST['current_photo'];

    if(isset($_FILES['profile_photo']) && $_FILES['profile_photo']['name'] != "") {

        $file_name = $_FILES['profile_photo']['name'];
        $file_tmp  = $_FILES['profile_photo']['tmp_name'];
        $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed   = ['jpg','jpeg','png','gif'];

        if(!in_array($file_ext, $allowed)){
            $errors[] = "Invalid image type. Allowed: jpg, jpeg, png, gif";
        } else {
            $new_name = uniqid().".".$file_ext;
            $destination = "uploads/".$new_name;

            if(move_uploaded_file($file_tmp, $destination)){
                $profile_photo = $new_name;
            } else {
                $errors[] = "Failed to upload profile photo";
            }
        }
    }

    // Update database
    if(empty($errors)) {

        if(isset($hashed_password)) {
            $stmt = $conn->prepare("UPDATE users SET name=?, address=?, contact=?, password=?, profile_photo=? WHERE id=?");
            $stmt->bind_param("sssssi", $name, $address, $contact, $hashed_password, $profile_photo, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET name=?, address=?, contact=?, profile_photo=? WHERE id=?");
            $stmt->bind_param("ssssi", $name, $address, $contact, $profile_photo, $user_id);
        }

        if($stmt->execute()){
            $success = "Profile updated successfully!";
        } else {
            $errors[] = "Failed to update profile.";
        }

        $stmt->close();
    }
}

// Fetch user data
$stmt = $conn->prepare("SELECT name, email, address, contact, profile_photo FROM users WHERE id=? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$profile_photo = $user['profile_photo'] ? $user['profile_photo'] : 'default.png';
?>

<!DOCTYPE html>
<html>
<head>
<title>Profile - Melody Masters</title>
<style>

body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #f5e8f2, #e8e0e0);
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 95vh;
    margin: 0;
}

.profile-container {
    background: #fff;
    padding: 35px;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    width: 600px;
    max-width: 90%;
    text-align: center;
}

.profile-container h2 {
    color: #8e24aa;
    margin-bottom: 25px;
}

.profile-photo {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #ccc;
    margin-bottom: 20px;
}

.form-row {
    display: flex;
    align-items: center;
    margin: 10px 0;
}

.form-row label {
    width: 120px;
    text-align: left;
    margin-right: 15px;
    font-size: 14px;
    font-weight: 500;
}

.form-row input {
    flex: 1;
    padding: 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
}

.form-row input:focus {
    border-color: #8e24aa;
    outline: none;
}

input[type="submit"] {
    width: 100%;
    padding: 12px;
    margin-top: 20px;
    background: linear-gradient(135deg, #d00b4d, #8e24aa);
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    cursor: pointer;
}

.button-group {
    margin-top: 15px;
}

.logout-btn,
.home-btn {
    display: inline-block;
    margin: 8px 5px 0 5px;
    padding: 10px 20px;
    border-radius: 6px;
    text-decoration: none;
    color: white;
    font-weight: 500;
}

.logout-btn {
    background-color: #e53935;
}

.home-btn {
    background-color: #43a047;
}

.logout-btn:hover,
.home-btn:hover {
    opacity: 0.9;
}

.error-list {
    color: red;
    text-align: left;
}

.success-msg {
    color: green;
}

@media(max-width:480px){
    .form-row {
        flex-direction: column;
        align-items: stretch;
    }
    .form-row label {
        width: 100%;
        margin-bottom: 5px;
    }
}

</style>
</head>
<body>

<div class="profile-container">
<h2>Update Profile</h2>

<?php
if(!empty($errors)){
    echo "<ul class='error-list'>";
    foreach($errors as $error){
        echo "<li>".$error."</li>";
    }
    echo "</ul>";
}

if($success){
    echo "<p class='success-msg'>$success</p>";
}
?>

<form method="POST" enctype="multipart/form-data">

<img id="profilePreview" src="uploads/<?php echo htmlspecialchars($profile_photo); ?>" class="profile-photo">

<div class="form-row">
<label>Name:</label>
<input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>">
</div>

<div class="form-row">
<label>Email:</label>
<input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
</div>

<div class="form-row">
<label>Address:</label>
<input type="text" name="address" value="<?php echo htmlspecialchars($user['address']); ?>">
</div>

<div class="form-row">
<label>Contact:</label>
<input type="text" name="contact" value="<?php echo htmlspecialchars($user['contact']); ?>">
</div>

<div class="form-row">
<label>New Password:</label>
<input type="password" name="password" placeholder="Enter new password">
</div>

<div class="form-row">
<label>Confirm Password:</label>
<input type="password" name="confirm_password" placeholder="Confirm new password">
</div>

<div class="form-row">
<label>Profile Photo:</label>
<input type="file" name="profile_photo">
</div>

<input type="hidden" name="current_photo" value="<?php echo htmlspecialchars($profile_photo); ?>">

<input type="submit" name="update" value="Update Profile">

</form>

<div class="button-group">
<a href="index.php" class="home-btn">Go to Home</a>
<a href="logout.php" class="logout-btn">Logout</a>
</div>

</div>

<script>
const fileInput = document.querySelector('input[name="profile_photo"]');
const preview = document.getElementById('profilePreview');

fileInput.addEventListener('change', function(e){
    const file = e.target.files[0];
    if(file){
        const reader = new FileReader();
        reader.onload = function(event){
            preview.src = event.target.result;
        }
        reader.readAsDataURL(file);
    }
});
</script>

</body>
</html>