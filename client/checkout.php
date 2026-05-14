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

$stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
$stmt->execute([$uid]);
$cart_items = $stmt->fetchAll();

if (empty($cart_items)) {
    header('location:cart.php');
    exit();
}

$total          = 0;
$total_products = '';
foreach ($cart_items as $item) {
    $total          += $item['price'] * $item['quantity'];
    $total_products .= $item['name'] . ' (x' . $item['quantity'] . '), ';
}
$total_products = rtrim($total_products, ', ');
$delivery       = $total >= 50 ? 0 : 5;
$grand_total    = $total + $delivery;

$error = '';

if (isset($_POST['place_order'])) {
    $name    = $_POST['name'];
    $number  = $_POST['number'];
    $email   = $_POST['email'];
    $address = $_POST['address'];
    $method  = $_POST['method'];

    if (empty($name) || empty($number) || empty($email) || empty($address)) {
        $error = 'Please fill in all required fields.';
    } else {
        $stmt = $conn->prepare("INSERT INTO orders (user_id, name, number, email, address, method, total_products, total_price, payment_status, placed_on) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
        $stmt->execute([$uid, $name, $number, $email, $address, $method, $total_products, $grand_total]);
        $conn->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$uid]);
        $_SESSION['msg'] = 'Order placed successfully! We\'ll be in touch soon.';
        header('location:orders.php');
        exit();
    }
}

$user_name  = $_SESSION['user_name']  ?? '';
$user_email = $_SESSION['user_email'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout — Bloom&amp;Petal</title>
</head>
<body>

<?php include 'header.php'; ?>

<div class="page-hero">
    <div class="breadcrumb"><a href="index.php">Home</a> / <a href="cart.php">Cart</a> / Checkout</div>
    <h1>Checkout</h1>
    <p>Almost there! Fill in your delivery details</p>
</div>

<div class="checkout-layout">
    <div>
        <?php if ($error): ?>
        <div class="flash-msg flash-msg-inline"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="checkout-form-box">
            <h3><i class="bi bi-person"></i> Delivery Information</h3>
            <form method="POST" action="checkout.php">
                <div class="form-row">
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($user_name); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Phone Number *</label>
                        <input type="tel" name="number" placeholder="+216 XX XXX XXX" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Email Address *</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user_email); ?>" required>
                </div>
                <div class="form-group">
                    <label>Delivery Address *</label>
                    <textarea name="address" placeholder="Street, City, Postal Code..." required></textarea>
                </div>

                <div class="payment-box">
                    <h3><i class="bi bi-credit-card"></i> Payment Method</h3>
                    <div class="payment-options">
                        <div class="payment-option">
                            <input type="radio" name="method" id="cod" value="Cash on Delivery" checked>
                            <label for="cod"><i class="bi bi-cash-coin"></i> Cash on Delivery</label>
                        </div>
                        <div class="payment-option">
                            <input type="radio" name="method" id="card" value="Card Payment">
                            <label for="card"><i class="bi bi-credit-card-2-front"></i> Card Payment</label>
                        </div>
                    </div>
                </div>

                <button type="submit" name="place_order" class="btn-primary btn-full btn-lg">
                    <i class="bi bi-bag-check"></i> Place Order — <?php echo number_format($grand_total, 2); ?> dt
                </button>
            </form>
        </div>
    </div>

    <div class="order-summary-box">
        <h3><i class="bi bi-bag"></i> Order Summary</h3>
        <?php foreach ($cart_items as $item): ?>
        <div class="order-item">
            <img src="../image/<?php echo htmlspecialchars($item['image']); ?>" alt="">
            <div class="order-item-info">
                <h5><?php echo htmlspecialchars($item['name']); ?></h5>
                <p>Qty: <?php echo $item['quantity']; ?></p>
                <div class="item-price"><?php echo number_format($item['price'] * $item['quantity'], 2); ?> dt</div>
            </div>
        </div>
        <?php endforeach; ?>

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
        <div class="checkout-note">
            <p><i class="bi bi-shield-check"></i> Your order is secured and handled with care.</p>
            <p><i class="bi bi-truck"></i> Estimated delivery: 1–2 business days.</p>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
