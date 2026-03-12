<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>About Us - Melody Masters</title>
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f6f9; margin:0; }
        .about-container { max-width: 1000px; margin:50px auto; background:#fff; padding:40px; border-radius:12px; box-shadow:0 5px 18px rgba(0,0,0,0.08); }
        h1, h2, h3 { color:#8e24aa; text-align:center; margin-bottom:20px; }
        p { font-size:16px; line-height:1.6; margin-bottom:15px; color:#333; }
        ul { margin-left:20px; color:#555; }
        .service-section { display:flex; flex-wrap:wrap; justify-content:space-between; margin-top:30px; }
        .service-box { flex:0 0 95%; background:#fdfdfd; margin-bottom:20px; padding:20px; border-radius:10px; box-shadow:0 3px 10px rgba(0,0,0,0.05); display:flex; align-items:center; }
        .service-box img { width:150px; height:150px; object-fit:cover; border-radius:10px; margin-right:15px; }
        .service-box h4 { margin:0 0 10px 0; color:#8e24aa; font-size:18px; }
        .service-box p{ font-size:14px; color:#555; text-align:justify; }
        @media(max-width:768px){ .service-box { flex:0 0 100%; } }
        .production-quality { text-align:center; margin-top:40px; }
        .production-quality img { max-width:100%; border-radius:12px; margin-top:15px; }
    </style>
</head>
<body>

<div class="about-container">
    <h1>About Melody Masters</h1>
    <p>Welcome to <strong>Melody Masters</strong>, your one-stop online store for high-quality musical instruments and accessories. We are passionate about music and aim to provide our customers with the best products to inspire their musical journey.</p>

    <p>Our mission is to deliver exceptional service, reliable products, and a seamless shopping experience for musicians of all levels, from beginners to professionals.</p>

    <h2>Our Services</h2>
    <div class="service-section">
        <div class="service-box">
            <img src="images/Wide Range of Instruments.avif" alt="Instruments">
            <div>
                <h4>Wide Range of Instruments</h4>
                <p>We offer an extensive collection of musical instruments to cater to musicians of all levels, from beginners to professionals. Our inventory includes:

                    <br><b>Guitars</b> – Acoustic, electric, classical, and bass guitars from world-renowned brands, crafted for precision and rich sound.

                    <br><b>Violins & String Instruments</b> – High-quality violins, cellos, and violas suitable for practice, performances, and orchestral use.

                    <br><b>Drums & Percussion</b> – Drum kits, snares, bongos, and percussion instruments with excellent tone and durability.

                    <br><b>Keyboards & Pianos</b> – Digital pianos, synthesizers, and electronic keyboards with advanced features for learners and professionals.
                    
                </div>
        </div>
        <div class="service-box">
    <img src="images/repairs.webp" alt="Repair">
    <div>
        <h4>Instrument Repair & Maintenance</h4>
        <p>
            Our skilled technicians provide comprehensive repair and maintenance services for all types of instruments. 
            From restringing guitars and tuning pianos to drum head replacements and electronic keyboard servicing, 
            we ensure every instrument performs at its best. We use high-quality replacement parts and precision tools 
            to maintain sound clarity, durability, and long-lasting performance.
        </p>
    </div>
</div>

<div class="service-box">
    <img src="images/accessories.jpg" alt="Accessories">
    <div>
        <h4>Accessories & Gear</h4>
        <p>
            Enhance your musical experience with our wide selection of premium accessories. We offer guitar strings, 
            drumsticks, picks, tuners, cables, amplifiers, instrument cases, music stands, and more. 
            All accessories are carefully selected to complement your instruments and provide comfort, 
            reliability, and superior performance during practice or live performances.
        </p>
    </div>
</div>

<div class="service-box">
    <img src="images/shipping.jpg" alt="Shipping">
    <div>
        <h4>Fast & Secure Shipping</h4>
        <p>
            We ensure safe and timely delivery of your instruments and accessories. Every product is securely packaged 
            to prevent damage during transit. Our reliable shipping partners provide tracking options so you can monitor 
            your order every step of the way. We are committed to delivering your purchases quickly and safely 
            right to your doorstep.
        </p>
    </div>
</div>
    </div>

    <h2>Our Production Quality</h2>
    <div class="production-quality">
        <p>All our instruments undergo strict quality control to ensure precision, durability, and perfect sound quality. We work with experienced craftsmen and reliable manufacturers to bring you instruments that inspire creativity.</p>
        <img src="images/production.webp" alt="Production Quality">
    </div>

    <h2>Why Choose Us?</h2>
    <ul>
        <li>High-quality, authentic instruments and accessories</li>
        <li>Exceptional customer service and support</li>
        <li>Secure and convenient online shopping experience</li>
        <li>Fast and reliable delivery nationwide</li>
        <li>Commitment to inspiring musicians at every level</li>
    </ul>
</div>

</body>
</html>

<?php include 'includes/footer.php'; ?>