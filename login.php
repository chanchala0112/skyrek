<?php
session_start();
include 'includes/db.php';

/* Redirect if already logged in */
if (isset($_SESSION['user_id'])) {
    if(isset($_SESSION['user_role']) && ($_SESSION['user_role'] == 'admin' || $_SESSION['user_role'] == 'staff')){
        header("Location: Admin/index.php");
    } else {
        header("Location: index.php");
    }
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    if (strlen($password) != 9) {
        $message = "Password must be exactly 9 characters!";
    } else {

        $result = $conn->query("SELECT * FROM users WHERE email='$email' LIMIT 1");

        if ($result && $result->num_rows == 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];

                if($user['role'] == 'admin' || $user['role'] == 'staff'){
                    header("Location: Admin/index.php");
                } else {
                    header("Location: index.php");
                }
                exit();

            } else {
                $message = "Invalid email or password!";
            }

        } else {
            $message = "Invalid email or password!";
        }
    }
}
?>



<div class="login-wrapper">
    <div class="login-container">
        <h2>Welcome Back 👋</h2>

        <?php if($message != ''): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST" action="" onsubmit="return validatePassword()">
            
            <input type="email" name="email" placeholder="Enter Email" required>

            <div class="password-wrapper">
                <input type="password" 
                       name="password" 
                       id="password"
                       placeholder="Password (exactly 9 characters)"
                       required
                       maxlength="9"
                       oninput="checkLength(this)">
                <span class="toggle-password" onclick="togglePassword()">👁</span>
            </div>

            <input type="submit" value="Login">
        </form>

        <p>Don't have an account? <a href="register.php">Register Here</a></p>
    </div>
</div>

<style>
/* General body styles */
body {
    margin: 0;
    font-family: Arial, sans-serif;
}

/* Wrapper for centering login container */
.login-wrapper {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: linear-gradient(135deg, #f9f9f9, #f2f4f6);
}

/* Login box container */
.login-container {
    width: 100%;
    max-width: 400px;
    background: #fff;
    padding: 40px 30px;
    border-radius: 15px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.2);
    text-align: center;
    animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
    from {opacity: 0; transform: translateY(20px);}
    to {opacity: 1; transform: translateY(0);}
}

/* Header */
.login-container h2 {
    margin-bottom: 25px;
    color: #333;
}

/* Inputs */
.login-container input[type="email"],
.login-container input[type="password"] {
    width: 100%;
    padding: 12px;
    margin: 12px 0;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 15px;
    transition: 0.3s;
}

.login-container input:focus {
    border-color: #cb1187;
    box-shadow: 0 0 8px rgba(203, 17, 129, 0.3);
    outline: none;
}

/* Password wrapper to keep input stable */
.password-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.password-wrapper input {
    flex: 1; 
    padding: 12px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 15px;
}

/* Password icon */
.toggle-password {
    position: absolute;
    right: 10px;
    cursor: pointer;
    font-size: 18px;
    width: 25px;        /* fixed width prevents input shrinking */
    text-align: center;
    pointer-events: auto;
}

/* Submit button */
.login-container input[type="submit"] {
    width: 100%;
    padding: 12px;
    margin-top: 15px;
    background: linear-gradient(135deg, #cb1193, #df25fc);
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
    transition: 0.3s;
}

.login-container input[type="submit"]:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}

/* Error message */
.message {
    color: red;
    font-size: 14px;
    margin-bottom: 10px;
}

/* Footer link */
.login-container p {
    margin-top: 15px;
    font-size: 14px;
}

.login-container a {
    color: #9011cb;
    text-decoration: none;
    font-weight: bold;
}
</style>

<script>
function togglePassword() {
    const passwordField = document.getElementById("password");
    const icon = document.querySelector(".toggle-password");

    if (passwordField.type === "password") {
        passwordField.type = "text";
        icon.textContent = "🙈";
    } else {
        passwordField.type = "password";
        icon.textContent = "👁";
    }
}

function checkLength(input) {
    if (input.value.length > 9) {
        alert("Password must be exactly 9 characters only!");
        input.value = input.value.slice(0,9);
    }
}

function validatePassword() {
    const password = document.getElementById("password").value;

    if (password.length != 9) {
        alert("Password must be exactly 9 characters!");
        return false;
    }
    return true;
}
</script>

