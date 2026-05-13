<?php
session_start();
include '../connection.php';

if (isset($_POST['logout'])) {
    session_destroy();
    header('location:login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us  Bloom&amp;Petal</title>
</head>
<body>

<?php include 'header.php'; ?>

<section class="about-hero">
    <div class="about-hero-text">
        <em class="about-tag"> Our Story</em>
        <h1>Flowers That<br>Tell Your Story</h1>
        <p> Bloom&amp;Petal , a premier boutique florist located on the Highway 280 corridor, offers the Birmingham, Alabama area superior floral arrangements and assortments. Our deep-rooted passion for flowers, along with our dedication to service, makes us the ideal choice for any occasion!</p>
        <div class="about-stats">
            <div class="about-stat">
                <div class="stat-number">500+</div>
                <div class="stat-label">Happy Clients</div>
            </div>
            <div class="about-stat">
                <div class="stat-number">3+</div>
                <div class="stat-label">Years of Blooming</div>
            </div>
            <div class="about-stat">
                <div class="stat-number">100%</div>
                <div class="stat-label">Fresh Daily</div>
            </div>
        </div>
    </div>
    <div class="about-hero-img">
        <?php
        $stmt = $conn->query("SELECT image FROM products LIMIT 1");
        $img  = $stmt->fetchColumn();
        if ($img):
        ?>
        <img src="image/<?php echo htmlspecialchars($img); ?>" alt="About Bloom and Petal">
        <?php else: ?>
        <div class="about-img-placeholder"><i class="bi bi-flower2"></i></div>
        <?php endif; ?>
    </div>
</section>

<section class="about-values-section">
    <div class="section-title">
        <em> What We Believe In</em>
        <span>Our Values</span>
        <div class="title-divider"></div>
    </div>
    <div class="about-values">
        <div class="value-card">
            <div class="value-icon"><i class="bi bi-flower1"></i></div>
            <h3>Always Fresh</h3>
            <p>We source our flowers daily from the finest local and international growers, ensuring every bloom is at its peak beauty.</p>
        </div>
        <div class="value-card">
            <div class="value-icon"><i class="bi bi-heart"></i></div>
            <h3>Made With Love</h3>
            <p>Every arrangement is hand-crafted by our talented florists who pour their passion and creativity into every bouquet.</p>
        </div>
        <div class="value-card">
            <div class="value-icon"><i class="bi bi-leaf"></i></div>
            <h3>Eco Conscious</h3>
            <p>We're committed to sustainable practices — from eco-friendly packaging to working with growers who share our values.</p>
        </div>
    </div>
</section>

<section class="mission-section">
    <div class="mission-inner">
        <em class="about-tag"> Our Mission</em>
        <blockquote class="mission-quote">
            "To turn every ordinary moment into something extraordinary — one flower at a time."
        </blockquote>
        <div class="title-divider"></div>
        <p class="mission-text">Whether it's a grand wedding centrepiece, a heartfelt birthday bouquet, or simply flowers to brighten a Tuesday  we believe in the power of flowers to move people. That's why we put our heart into everything we create.</p>
        <div class="mission-btns">
            <a href="shop.php" class="btn-primary"><i class="bi bi-flower2"></i> Explore Our Flowers</a>
            <a href="contact.php" class="btn-outline">Talk To Us</a>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
</body>
</html>
