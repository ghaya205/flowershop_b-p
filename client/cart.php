<?php
session_start();
require_once __DIR__ . '/../connection.php';

if (isset($_POST['logout'])) {
    session_destroy();
    header('location:../login.php');
    exit();
}

if (!isset($_SESSION['user_id'])) {
    header('location:../login.php');
    exit();
}
$uid = $_SESSION['user_id'];

if (isset($_GET['add'])) {
    $pid  = (int)$_GET['add'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$pid]);
    $p = $stmt->fetch();
    if ($p) {
        $check = $conn->prepare("SELECT id FROM cart WHERE user_id = ? AND pid = ?");
        $check->execute([$uid, $pid]);
        if ($check->fetch()) {
            $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND pid = ?")->execute([$uid, $pid]);
        } else {
            $conn->prepare("INSERT INTO cart (user_id, pid, name, price, quantity, image) VALUES (?, ?, ?, ?, 1, ?)")
                 ->execute([$uid, $pid, $p['name'], $p['price'], $p['image']]);
        }
    }
    $_SESSION['msg'] = 'Item added to cart!';
    header('location:cart.php');
    exit();
}

if (isset($_GET['remove'])) {
    $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?")->execute([(int)$_GET['remove'], $uid]);
    $_SESSION['msg'] = 'Item removed.';
    header('location:cart.php');
    exit();
}

if (isset($_POST['update_cart'])) {
    foreach ($_POST['qty'] as $cart_id => $qty) {
        $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?")->execute([max(1,(int)$qty), (int)$cart_id, $uid]);
    }
    $_SESSION['msg'] = 'Cart updated!';
    header('location:cart.php');
    exit();
}

$stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
$stmt->execute([$uid]);
$cart_items = $stmt->fetchAll();

$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}
$delivery    = $total >= 50 ? 0 : 5;
$grand_total = $total + $delivery;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart — Bloom&amp;Petal</title>
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
    <div class="breadcrumb"><a href="index.php">Home</a> / Cart</div>
    <h1>My Shopping Cart</h1>
    <p><?php echo count($cart_items); ?> item<?php echo count($cart_items) !== 1 ? 's' : ''; ?> in your cart</p>
</div>

<div class="list-section">
    <?php if (!empty($cart_items)): ?>
    <form method="POST" action="cart.php">
        <div class="table-wrap">
            <table class="list-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Remove</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                    <tr>
                        <td><img src="../image/<?php echo htmlspecialchars($item['image']); ?>" class="product-thumb" alt=""></td>
                        <td><div class="product-name"><?php echo htmlspecialchars($item['name']); ?></div></td>
                        <td class="price-col"><?php echo $item['price']; ?> dt</td>
                        <td><input type="number" name="qty[<?php echo $item['id']; ?>]" value="<?php echo $item['quantity']; ?>" min="1" max="99" class="qty-input"></td>
                        <td class="price-col"><?php echo number_format($item['price'] * $item['quantity'], 2); ?> dt</td>
                        <td>
                            <a href="cart.php?remove=<?php echo $item['id']; ?>" class="remove-btn" onclick="return confirm('Remove this item?')">
                                <i class="bi bi-trash3"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="cart-actions">
            <button type="submit" name="update_cart" class="btn-outline"><i class="bi bi-arrow-counterclockwise"></i> Update Cart</button>
            <a href="shop.php" class="btn-outline"><i class="bi bi-arrow-left"></i> Continue Shopping</a>
        </div>
    </form>

    <div class="list-summary">
        <h3>Order Summary</h3>
        <div class="summary-row">
            <span>Subtotal</span>
            <span><?php echo number_format($total, 2); ?> dt</span>
        </div>
        <div class="summary-row">
            <span>Delivery</span>
            <span class="<?php echo $delivery == 0 ? 'free-delivery' : ''; ?>">
                <?php echo $delivery == 0 ? 'Free' : number_format($delivery, 2) . ' dt'; ?>
            </span>
        </div>
        <div class="summary-row total">
            <span>Total</span>
            <span class="total-price"><?php echo number_format($grand_total, 2); ?> dt</span>
        </div>
        <a href="checkout.php" class="btn-primary btn-full"><i class="bi bi-lock"></i> Proceed to Checkout</a>
    </div>

    <?php else: ?>
    <div class="empty-state">
        <i class="bi bi-bag-x"></i>
        <h3>Your cart is empty</h3>
        <p>Looks like you haven't added any flowers yet</p>
        <a href="shop.php" class="btn-primary"><i class="bi bi-flower1"></i> Start Shopping</a>
    </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
