<?php
session_start();
include '../connection.php';

if (isset($_POST['logout'])) {
    session_destroy();
    header('location:../login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bloom&amp;Petal : Fresh Flowers Delivered</title>
</head>
<body>

<?php include 'header.php'; ?>

<?php if (isset($_SESSION['msg'])): ?>
<div class="flash-msg">
    <span><?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?></span>
    <i class="bi bi-x-circle" onclick="this.parentElement.remove()"></i>
</div>
<?php endif; ?>

<section class="hero">
    <div class="hero-content">
        <em>✦ Fresh Every Day</em>
        <h1>Beautiful Flowers<br>For Every <span>Moment</span></h1>
        <p>Handpicked, lovingly arranged, and delivered fresh to your door. From romantic bouquets to stunning centerpieces — we have the perfect bloom for every story.</p>
        <div class="hero-btns">
            <a href="shop.php" class="btn-primary"><i class="bi bi-flower1"></i> Shop Now</a>
            <a href="about.php" class="btn-outline">Our Story</a>
        </div>
    </div>
    <div class="hero-image">
        <?php
        $stmt = $conn->query("SELECT image, name FROM products LIMIT 1");
        $hero_product = $stmt->fetch();
        if ($hero_product):
        ?>
        <img src="image/<?php echo htmlspecialchars($hero_product['image']); ?>" alt="<?php echo htmlspecialchars($hero_product['name']); ?>">
        <?php else: ?>
        <div class="hero-img-placeholder"><i class="bi bi-flower1"></i></div>
        <?php endif; ?>
        <div class="hero-float hero-float-1">
            <strong>Free Delivery</strong>
            on orders over 50dt
        </div>
        <div class="hero-float hero-float-2">
            <strong>✦ Fresh Guarantee</strong>
            or your money back
        </div>
    </div>
</section>

<div class="features-strip">
    <div class="feature-item">
        <i class="bi bi-truck"></i>
        <div><h5>Fast Delivery</h5><p>Same-day available</p></div>
    </div>
    <div class="feature-item">
        <i class="bi bi-flower2"></i>
        <div><h5>Always Fresh</h5><p>Sourced daily from growers</p></div>
    </div>
    <div class="feature-item">
        <i class="bi bi-gift"></i>
        <div><h5>Gift Wrapping</h5><p>Beautiful packaging included</p></div>
    </div>
    <div class="feature-item">
        <i class="bi bi-arrow-counterclockwise"></i>
        <div><h5>Easy Returns</h5><p>Not happy? We'll fix it</p></div>
    </div>
</div>

<section class="featured-section">
    <div class="section-title">
        <em>✦ Hand Selected</em>
        <span>Our Featured Collection</span>
        <div class="title-divider"></div>
        <p>Each arrangement is crafted with care, using only the freshest seasonal blooms</p>
    </div>
    <div class="products-grid">
        <?php
        $stmt = $conn->query("SELECT * FROM products ORDER BY id DESC LIMIT 8");
        while ($p = $stmt->fetch()):
        ?>
        <div class="product-card">
            <div class="card-img">
                <img src="image/<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>">
                <div class="card-actions">
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="wishlist.php?add=<?php echo $p['id']; ?>" class="card-action-btn" title="Wishlist"><i class="bi bi-heart"></i></a>
                    <a href="cart.php?add=<?php echo $p['id']; ?>" class="card-action-btn" title="Add to Cart"><i class="bi bi-bag-plus"></i></a>
                    <?php endif; ?>
                    <a href="view.php?pid=<?php echo $p['id']; ?>" class="card-action-btn" title="View"><i class="bi bi-eye"></i></a>
                </div>
            </div>
            <div class="card-body">
                <h4><?php echo htmlspecialchars($p['name']); ?></h4>
                <span class="price"><?php echo $p['price']; ?> dt</span>
                <a href="view.php?pid=<?php echo $p['id']; ?>" class="btn-primary card-btn">View Details</a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    <div class="section-cta">
        <a href="shop.php" class="btn-outline"><i class="bi bi-grid"></i> View All Products</a>
    </div>
</section>

<div class="promo-banner">
    <div class="promo-text">
        <h2>Celebrate Every Moment<br>With Fresh Blooms</h2>
        <p>Special arrangements for birthdays, weddings, anniversaries &amp; more</p>
    </div>
    <a href="shop.php" class="btn-primary btn-white">Shop the Collection</a>
</div>

<section class="testimonials">
    <div class="section-title">
        <em>✦ What They Say</em>
        <span>Loved by Our Customers</span>
        <div class="title-divider"></div>
    </div>
    <div class="testimonials-grid">
        <div class="testimonial-card">
            <div class="testimonial-stars">★★★★★</div>
            <p>"The bouquet I ordered for my wife's birthday was absolutely stunning. The flowers lasted over two weeks!"</p>
            <div class="testimonial-author">— ghaya bedoui</div>
        </div>
        <div class="testimonial-card">
            <div class="testimonial-stars">★★★★★</div>
            <p>"Ordered a custom arrangement for our wedding and it exceeded every expectation. Absolutely perfect."</p>
            <div class="testimonial-author">— nour keskes</div>
        </div>
        <div class="testimonial-card">
            <div class="testimonial-stars">★★★★★</div>
            <p>"Fast delivery, gorgeous packaging, and the flowers smelled divine. Will definitely order again!"</p>
            <div class="testimonial-author">— emna hammami </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
</body>
</html>
