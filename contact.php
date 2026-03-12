<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';

$success = "";
$error = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $subject = $conn->real_escape_string($_POST['subject']);
    $message = $conn->real_escape_string($_POST['message']);

    if(!empty($name) && !empty($email) && !empty($subject) && !empty($message)){

        $insert = $conn->query("
            INSERT INTO contact_messages (name, email, subject, message, created_at)
            VALUES ('$name', '$email', '$subject', '$message', NOW())
        ");

        if($insert){
            $success = "Your message has been sent successfully!";
        } else {
            $error = "Something went wrong. Please try again.";
        }

    } else {
        $error = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Contact Us - Melody Masters</title>

<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #f4f6f9;
    margin: 0;
}

.contact-container {
    max-width: 1000px;
    margin: 50px auto;
    background: #fff;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}

h2 {
    text-align: center;
    color: #8e24aa;
    margin-bottom: 30px;
}

.contact-wrapper {
    display: flex;
    flex-wrap: wrap;
    gap: 40px;
}

.contact-info {
    flex: 1 1 40%;
}

.contact-form {
    flex: 1 1 55%;
}

.contact-info p {
    margin-bottom: 15px;
    color: #555;
}

input, textarea {
    width: 100%;
    padding: 12px;
    margin-bottom: 15px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 14px;
}

textarea {
    resize: none;
    height: 120px;
}

button {
    padding: 12px 20px;
    background: linear-gradient(135deg,#d00b4d,#8e24aa);
    border: none;
    color: white;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
}

button:hover {
    opacity: 0.9;
}

.msg-success {
    background: #e0f7e9;
    color: #2e7d32;
    padding: 10px;
    border-radius: 6px;
    margin-bottom: 15px;
    text-align: center;
}

.msg-error {
    background: #fde0e0;
    color: #c62828;
    padding: 10px;
    border-radius: 6px;
    margin-bottom: 15px;
    text-align: center;
}
.map-container {
    margin-top: 20px;
}

@media(max-width:768px){
    .contact-wrapper {
        flex-direction: column;
    }
}
</style>
</head>

<body>

<div class="contact-container">
    <h2>Contact Us</h2>

    <?php if($success): ?>
        <div class="msg-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <?php if($error): ?>
        <div class="msg-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="contact-wrapper">

        <div class="contact-info">
            <h3>Get In Touch</h3>
            <p><strong>Address:</strong> 123 Music Street, Harmony City, Colombo.</p>
            <p><strong>Phone:</strong> +94 754667226</p>
            <p><strong>Email:</strong> support@melodymasters.com</p>
            <p>
                We’re here to help! Whether you have questions about our products, 
                need assistance with your order, or want expert advice on choosing 
                the perfect instrument, feel free to contact us.
            </p>
        </div>

        <div class="contact-form">
            <form method="POST">
                <input type="text" name="name" placeholder="Your Name">
                <input type="email" name="email" placeholder="Your Email">
                <input type="text" name="subject" placeholder="Subject">
                <textarea name="message" placeholder="Your Message"></textarea>
                <button type="submit">Send Message</button>
            </form>
        </div>

        <h3>Our Shop Location</h3>
        <div class="map-container">
            <iframe 
                src="https://www.google.com/maps?q=Colombo,Sri%20Lanka&output=embed"
                width="100%" 
                height="250" 
                style="border:0; border-radius:10px;" 
                allowfullscreen="" 
                loading="lazy">
            </iframe>
        </div>

    </div>
</div>

</body>
</html>

<?php include 'includes/footer.php'; ?>