<!DOCTYPE html>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css">
<link rel="stylesheet" href="client_style.css">

<?php
$cart_count     = 0;
$wishlist_count = 0;
if (isset($_SESSION['user_id'])) {
    $uid  = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT SUM(quantity) FROM cart WHERE user_id = ?");
    $stmt->execute([$uid]);
    $cart_count = (int)($stmt->fetchColumn() ?? 0);

    $stmt2 = $conn->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = ?");
    $stmt2->execute([$uid]);
    $wishlist_count = (int)($stmt2->fetchColumn() ?? 0);
}
?>

<header class="client-header">
    <a href="index.php" class="logo">Bloom<span>&amp;Petal</span></a>

    <nav>
        <a href="index.php">Home</a>
        <a href="shop.php">Shop</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
        <?php if (isset($_SESSION['user_id'])): ?>
        <a href="orders.php">My Orders</a>
        <?php endif; ?>
    </nav>

    <div class="header-icons">
        <a href="wishlist.php" title="Wishlist">
            <i class="bi bi-heart"></i>
            <?php if ($wishlist_count > 0): ?>
            <span class="badge"><?php echo $wishlist_count; ?></span>
            <?php endif; ?>
        </a>
        <a href="cart.php" title="Cart">
            <i class="bi bi-bag"></i>
            <?php if ($cart_count > 0): ?>
            <span class="badge"><?php echo $cart_count; ?></span>
            <?php endif; ?>
        </a>
        <?php if (isset($_SESSION['user_id'])): ?>
        <a href="profile.php" title="Account"><i class="bi bi-person"></i></a>
        <?php else: ?>
        <a href="login.php" title="Login"><i class="bi bi-person"></i></a>
        <?php endif; ?>
        <button id="mobile-menu-btn"><i class="bi bi-list"></i></button>
    </div>
</header>

<nav class="mobile-nav" id="mobile-nav">
    <a href="index.php">Home</a>
    <a href="shop.php">Shop</a>
    <a href="about.php">About</a>
    <a href="contact.php">Contact</a>
    <?php if (isset($_SESSION['user_id'])): ?>
    <a href="orders.php">My Orders</a>
    <a href="profile.php">My Profile</a>
    <form method="POST" class="mobile-logout-form">
        <button type="submit" name="logout" class="mobile-logout-btn">Logout</button>
    </form>
    <?php else: ?>
    <a href="../login.php">Login</a>
    <a href="../signup.php">Sign Up</a>
    <?php endif; ?>
</nav>
