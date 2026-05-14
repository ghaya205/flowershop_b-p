<?php
session_start();
include 'connection.php';

if (isset($_POST['logout'])) {
    session_destroy();
    header('location:login.php');
    exit();
}

if (!isset($_SESSION['user_id'])) {
    header('location:login.php');
    exit();
}
$uid = $_SESSION['user_id'];

if (isset($_GET['add'])) {
    $pid  = (int)$_GET['add'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$pid]);
    $p = $stmt->fetch();
    if ($p) {
        $check = $conn->prepare("SELECT id FROM wishlist WHERE user_id = ? AND pid = ?");
        $check->execute([$uid, $pid]);
        if (!$check->fetch()) {
            $conn->prepare("INSERT INTO wishlist (user_id, pid, name, price, image) VALUES (?, ?, ?, ?, ?)")
                 ->execute([$uid, $pid, $p['name'], $p['price'], $p['image']]);
            $_SESSION['msg'] = 'Added to wishlist!';
        } else {
            $_SESSION['msg'] = 'Already in your wishlist.';
        }
    }
    header('location:wishlist.php');
    exit();
}

if (isset($_GET['remove'])) {
    $conn->prepare("DELETE FROM wishlist WHERE id = ? AND user_id = ?")->execute([(int)$_GET['remove'], $uid]);
    $_SESSION['msg'] = 'Removed from wishlist.';
    header('location:wishlist.php');
    exit();
}

if (isset($_GET['move_cart'])) {
    $stmt = $conn->prepare("SELECT * FROM wishlist WHERE id = ? AND user_id = ?");
    $stmt->execute([(int)$_GET['move_cart'], $uid]);
    $item = $stmt->fetch();
    if ($item) {
        $check = $conn->prepare("SELECT id FROM cart WHERE user_id = ? AND pid = ?");
        $check->execute([$uid, $item['pid']]);
        if (!$check->fetch()) {
            $conn->prepare("INSERT INTO cart (user_id, pid, name, price, quantity, image) VALUES (?, ?, ?, ?, 1, ?)")
                 ->execute([$uid, $item['pid'], $item['name'], $item['price'], $item['image']]);
        } else {
            $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND pid = ?")->execute([$uid, $item['pid']]);
        }
        $conn->prepare("DELETE FROM wishlist WHERE id = ?")->execute([$item['id']]);
        $_SESSION['msg'] = 'Moved to cart!';
    }
    header('location:wishlist.php');
    exit();
}

$stmt = $conn->prepare("SELECT * FROM wishlist WHERE user_id = ?");
$stmt->execute([$uid]);
$wish_items = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist — Bloom&amp;Petal</title>
</head>
<body>

<?php include 'header.php'; ?>

<?php if (isset($_SESSION['msg'])): ?>
<div class="flash-msg">
    <span><?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?></span>
    <i class="bi bi-x-circle" onclick="this.parentElement.remove()"></i>
</div>
<?php endif; ?>

<div class="page-hero">
    <div class="breadcrumb"><a href="index.php">Home</a> / Wishlist</div>
    <h1>My Wishlist</h1>
    <p><?php echo count($wish_items); ?> saved item<?php echo count($wish_items) !== 1 ? 's' : ''; ?></p>
</div>

<div class="list-section">
    <?php if (!empty($wish_items)): ?>
    <div class="table-wrap">
        <table class="list-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Actions</th>
                    <th>Remove</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($wish_items as $item): ?>
                <tr>
                    <td><img src="image/<?php echo htmlspecialchars($item['image']); ?>" class="product-thumb" alt=""></td>
                    <td>
                        <div class="product-name"><?php echo htmlspecialchars($item['name']); ?></div>
                        <a href="view.php?pid=<?php echo $item['pid']; ?>" class="view-link">View details →</a>
                    </td>
                    <td class="price-col"><?php echo $item['price']; ?> dt</td>
                    <td>
                        <a href="wishlist.php?move_cart=<?php echo $item['id']; ?>" class="btn-primary btn-sm">
                            <i class="bi bi-bag-plus"></i> Add to Cart
                        </a>
                    </td>
                    <td>
                        <a href="wishlist.php?remove=<?php echo $item['id']; ?>" class="remove-btn" onclick="return confirm('Remove from wishlist?')">
                            <i class="bi bi-trash3"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="section-cta">
        <a href="shop.php" class="btn-outline"><i class="bi bi-arrow-left"></i> Continue Shopping</a>
    </div>

    <?php else: ?>
    <div class="empty-state">
        <i class="bi bi-heart"></i>
        <h3>Your wishlist is empty</h3>
        <p>Save your favourite arrangements and come back to them anytime</p>
        <a href="shop.php" class="btn-primary"><i class="bi bi-flower1"></i> Explore Flowers</a>
    </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
