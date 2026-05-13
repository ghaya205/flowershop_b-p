<?php
session_start();
include '../connection.php';

if (isset($_POST['logout'])) {
    session_destroy();
    header('location:login.php');
    exit();
}

if (!isset($_SESSION['user_id'])) {
    header('location:../login.php');
    exit();
}
$uid = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY placed_on DESC");
$stmt->execute([$uid]);
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders — Bloom&amp;Petal</title>
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
    <div class="breadcrumb"><a href="index.php">Home</a> / My Orders</div>
    <h1>My Orders</h1>
    <p>Track and review all your past orders</p>
</div>

<div class="orders-section">
    <?php if (!empty($orders)): ?>
        <?php foreach ($orders as $order): ?>
        <div class="order-card">
            <div class="order-card-header">
                <div>
                    <div class="order-id">Order #<?php echo $order['id']; ?></div>
                    <div class="order-date"><?php echo date('d M Y, H:i', strtotime($order['placed_on'])); ?></div>
                </div>
                <span class="status-badge status-<?php echo $order['payment_status']; ?>">
                    <?php echo ucfirst($order['payment_status']); ?>
                </span>
            </div>

            <div class="order-details-grid">
                <div class="order-detail-group">
                    <p class="detail-label">Delivery To</p>
                    <p class="detail-name"><?php echo htmlspecialchars($order['name']); ?></p>
                    <p class="order-address"><?php echo htmlspecialchars($order['address']); ?></p>
                </div>
                <div class="order-detail-group">
                    <p class="detail-label">Contact</p>
                    <p class="detail-value"><?php echo htmlspecialchars($order['number']); ?></p>
                    <p class="detail-value"><?php echo htmlspecialchars($order['email']); ?></p>
                </div>
                <div class="order-detail-group">
                    <p class="detail-label">Payment Method</p>
                    <p class="detail-value"><?php echo htmlspecialchars($order['method']); ?></p>
                </div>
                <div class="order-detail-group">
                    <p class="detail-label">Order Total</p>
                    <p class="order-total"><?php echo number_format($order['total_price'], 2); ?> dt</p>
                </div>
            </div>

            <div class="order-items-summary">
                <i class="bi bi-bag"></i>
                <strong>Items:</strong> <?php echo htmlspecialchars($order['total_products']); ?>
            </div>

            <?php if ($order['payment_status'] === 'completed'): ?>
            <div class="order-card-footer">
                <a href="shop.php" class="btn-outline btn-sm"><i class="bi bi-arrow-counterclockwise"></i> Reorder</a>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>

    <?php else: ?>
    <div class="empty-state">
        <i class="bi bi-bag-x"></i>
        <h3>No orders yet</h3>
        <p>You haven't placed any orders. Start shopping and treat yourself!</p>
        <a href="shop.php" class="btn-primary"><i class="bi bi-flower1"></i> Shop Now</a>
    </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
