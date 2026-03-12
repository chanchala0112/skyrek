<footer>
    <div class="footer-container">

        <!-- About Section -->
        <div class="footer-section">
            <h3>🎵 Melody Masters</h3>
            <p>Your one-stop shop for musical instruments and digital music products.</p>

            <!-- Social Media -->
            <div class="footer-social">
                <a href="#" class="social">🌐</a>
                <a href="#" class="social">🐦</a>
                <a href="#" class="social">📘</a>
                <a href="#" class="social">📸</a>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="footer-section">
            <h4>Quick Links</h4>
            <a href="index.php">Home</a>
            <a href="shop.php">Shop</a>
            <a href="categories.php">Categories</a>
            <a href="contact.php">Contact</a>
        </div>

        <!-- Contact & Newsletter -->
        <div class="footer-section">
            <h4>Contact</h4>
            <p>Email: support@melodymasters.com</p>
            <p>Phone: +94 75 466 7226</p>

            <h4>Subscribe</h4>
            <form class="newsletter-form">
                <input type="email" placeholder="Your email">
                <button type="submit">Subscribe</button>
            </form>
        </div>

    </div>

    <div class="footer-bottom">
        © <?php echo date("Y"); ?> Melody Masters | All Rights Reserved
    </div>
</footer>

<style>
/* Footer main */
footer {
    background: #f8f8f8; 
    color: #333;
    margin-top: 50px;
    border-top: 1px solid #eee;
    font-family: 'Poppins', sans-serif;
}

.footer-container {
    display: flex;
    justify-content: space-between;
    padding: 40px;
    flex-wrap: wrap;
}
.footer-section h3{
    font-size: 22px;
    font-weight: 600;
    background: linear-gradient(135deg, #d00b4d, #8e24aa);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.footer-section {
    max-width: 300px;
    margin-bottom: 20px;
    
}

.footer-section h3, 
.footer-section h4 {
    color: #d00b4d; /* primary header gradient color */
    margin-bottom: 15px;
}

/* Links */
.footer-section a {
    display: block;
    color: #555;
    text-decoration: none;
    margin: 5px 0;
    font-size: 14px;
    transition: 0.3s ease;
}
.footer-section a:hover {
    color: #8e24aa; /* secondary header gradient color */
}

/* Social Icons */
.footer-social {
    margin-top: 15px;
}
.footer-social a {
    display: inline-block;
    margin-right: 10px;
    font-size: 18px;
    color: #555;
    transition: 0.3s ease;
}
.footer-social a:hover {
    color: #d00b4d;
    transform: scale(1.2);
}

/* Newsletter */
.newsletter-form {
    display: flex;
    margin-top: 10px;
}
.newsletter-form input {
    flex: 1;
    padding: 8px 12px;
    border: 1px solid #ccc;
    border-radius: 25px 0 0 25px;
    outline: none;
}
.newsletter-form button {
    padding: 8px 16px;
    border: none;
    background: linear-gradient(135deg, #d00b4d, #8e24aa);
    color: white;
    border-radius: 0 25px 25px 0;
    cursor: pointer;
    transition: 0.3s ease;
}
.newsletter-form button:hover {
    opacity: 0.85;
}

/* Footer bottom */
.footer-bottom {
    text-align: center;
    padding: 15px;
    background: #eee;
    font-size: 13px;
    color: #666;
    border-top: 1px solid #ddd;
}

/* MOBILE */
@media(max-width: 768px) {
    .footer-container {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .footer-section {
        margin: 15px 0;
    }

    .newsletter-form {
        flex-direction: column;
    }
    .newsletter-form input, .newsletter-form button {
        width: 100%;
        border-radius: 25px;
        margin: 5px 0;
    }
}
</style>