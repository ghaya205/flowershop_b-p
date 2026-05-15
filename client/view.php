<?php
session_start();
require_once __DIR__ . '/../connection.php';
if (isset($_POST['logout'])) {
    session_destroy();
    header('location:../login.php');
    exit();
}

if (!isset($_GET['pid'])) {
    header('location:shop.php');
    exit();
}

$pid  = (int)$_GET['pid'];
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$pid]);
$product = $stmt->fetch();

if (!$product) {
    header('location:shop.php');
    exit();
}

if (isset($_GET['add_cart'])) {
    if (!isset($_SESSION['user_id'])) { header('location:./../login.php'); exit(); }
    $uid = $_SESSION['user_id'];
    $qty = max(1, (int)($_GET['qty'] ?? 1));

    $check = $conn->prepare("SELECT id FROM cart WHERE user_id = ? AND pid = ?");
    $check->execute([$uid, $pid]);
    if ($check->fetch()) {
        $conn->prepare("UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND pid = ?")->execute([$qty, $uid, $pid]);
    } else {
        $conn->prepare("INSERT INTO cart (user_id, pid, name, price, quantity, image) VALUES (?, ?, ?, ?, ?, ?)")
             ->execute([$uid, $pid, $product['name'], $product['price'], $qty, $product['image']]);
    }
    $_SESSION['msg'] = 'Added to cart!';
    header("location:view.php?pid=$pid");
    exit();
}


if (isset($_GET['add_wish'])) {
    if (!isset($_SESSION['user_id'])) { header('location:./../login.php'); exit(); }
    $uid   = $_SESSION['user_id'];
    $check = $conn->prepare("SELECT id FROM wishlist WHERE user_id = ? AND pid = ?");
    $check->execute([$uid, $pid]);
    if (!$check->fetch()) {
        $conn->prepare("INSERT INTO wishlist (user_id, pid, name, price, image) VALUES (?, ?, ?, ?, ?)")
             ->execute([$uid, $pid, $product['name'], $product['price'], $product['image']]);
        $_SESSION['msg'] = 'Added to wishlist!';
    } else {
        $_SESSION['msg'] = 'Already in your wishlist.';
    }
    header("location:view.php?pid=$pid");
    exit();
}

$related = $conn->prepare("SELECT * FROM products WHERE id != ? ORDER BY RAND() LIMIT 4");
$related->execute([$pid]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> — Bloom&amp;Petal</title>
</head>
<body>

<?php include 'header.php'; ?>

<?php if (isset($_SESSION['msg'])): ?>
<div class="flash-msg">
    <span><?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?></span>
    <i class="bi bi-x-circle" onclick="this.parentElement.remove()"></i>
</div>
<?php endif; ?>

<div class="page-hero page-hero-sm">
    <div class="breadcrumb"><a href="index.php">Home</a> / <a href="shop.php">Shop</a> / <?php echo htmlspecialchars($product['name']); ?></div>
</div>

<section class="product-view">
    <div class="product-gallery">
        <div class="gallery-main">
            <img src="../image/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>
    </div>

    <div class="product-info">
        <span class="product-tag">✦ Fresh Arrangement</span>
        <h1><?php echo htmlspecialchars($product['name']); ?></h1>
        <div class="product-price"><?php echo $product['price']; ?> dt <span>/ arrangement</span></div>
        <div class="product-desc"><?php echo nl2br(htmlspecialchars($product['product_detail'])); ?></div>

        <?php if (isset($_SESSION['user_id'])): ?>
        <form method="GET" action="view.php">
            <input type="hidden" name="pid" value="<?php echo $pid; ?>">
            <input type="hidden" name="add_cart" value="1">
            <div class="product-actions">
                <div class="qty-selector">
                    <button type="button" onclick="changeQty(-1)">−</button>
                    <input type="number" name="qty" id="qty" value="1" min="1" max="99">
                    <button type="button" onclick="changeQty(1)">+</button>
                </div>
                <button type="submit" class="btn-primary"><i class="bi bi-bag-plus"></i> Add to Cart</button>
            </div>
        </form>
        <a href="view.php?pid=<?php echo $pid; ?>&add_wish=1" class="btn-outline btn-full-mobile">
            <i class="bi bi-heart"></i> Add to Wishlist
        </a>
        <?php else: ?>
        <div class="login-prompt">
            <a href="./../login.php">Login</a> to add this item to your cart or wishlist.
        </div>
        <?php endif; ?>

        <div class="product-meta">
            <p><i class="bi bi-truck"></i> Free delivery on orders over <span>50dt</span></p>
            <p><i class="bi bi-arrow-counterclockwise"></i> Freshness <span>guaranteed</span> or your money back</p>
            <p><i class="bi bi-gift"></i> Beautiful <span>gift wrapping</span> available</p>
        </div>
    </div>
</section>

<section class="featured-section related-section">
    <div class="section-title">
        <em>✦ You Might Also Like</em>
        <span>Related Arrangements</span>
        <div class="title-divider"></div>
    </div>
    <div class="products-grid">
        <?php while ($rp = $related->fetch()): ?>
        <div class="product-card">
            <div class="card-img">
                <img src="../image/<?php echo htmlspecialchars($rp['image']); ?>" alt="<?php echo htmlspecialchars($rp['name']); ?>">
                <div class="card-actions">
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="wishlist.php?add=<?php echo $rp['id']; ?>" class="card-action-btn"><i class="bi bi-heart"></i></a>
                    <a href="cart.php?add=<?php echo $rp['id']; ?>"     class="card-action-btn"><i class="bi bi-bag-plus"></i></a>
                    <?php endif; ?>
                    <a href="view.php?pid=<?php echo $rp['id']; ?>" class="card-action-btn"><i class="bi bi-eye"></i></a>
                </div>
            </div>
            <div class="card-body">
                <h4><?php echo htmlspecialchars($rp['name']); ?></h4>
                <span class="price"><?php echo $rp['price']; ?> dt</span>
                <a href="view.php?pid=<?php echo $rp['id']; ?>" class="btn-primary card-btn">View Details</a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</section>

<script>
function changeQty(delta) {
    const input = document.getElementById('qty');
    let val = parseInt(input.value) + delta;
    input.value = Math.min(99, Math.max(1, val));
}
</script>

<?php include 'footer.php'; ?>
</body>
</html>
