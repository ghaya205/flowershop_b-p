<?php
include 'connection.php';
session_start();
$admin_id = $_SESSION['admin_id'] ?? null;
if (!isset($admin_id)) {
    header('location:../login.php');
    exit();
}
if (isset($_POST['logout'])) {
    session_destroy();
    header('location:../login.php');
    exit();
}


if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->execute([(int)$_GET['delete']]);
    header('location:admin_orders.php');
    exit();
}

if (isset($_POST['update_order'])) {
    $stmt = $conn->prepare("UPDATE orders SET payment_status = ? WHERE id = ?");
    $stmt->execute([$_POST['update_payment'], (int)$_POST['order_id']]);
    $message[] = 'Payment status has been updated!';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>orders</title>
</head>
<body>
  <?php include 'admin_header.php'; ?>
  
  <?php
  if (isset($message)) {
      foreach ($message as $msg) {
          echo '<div class="flash-msg">
          <span>' . $msg . '</span>
          <i class="bi bi-x-circle" onclick="this.parentElement.remove()"></i>
          </div>';
      }
  }
  ?>
  <section class="order-container">
    <h1 class="title">total placed orders</h1>
    <div class="box-container">
      <?php
      $stmt = $conn->query("SELECT * FROM orders");
      if ($stmt->rowCount() > 0) {
          while ($fetch_orders = $stmt->fetch()) {
      ?>
      <div class="box">
        <p>user id : <span><?php echo $fetch_orders['user_id']; ?></span></p>
        <p>placed on : <span><?php echo $fetch_orders['placed_on']; ?></span></p>
        <p>user name : <span><?php echo $fetch_orders['name']; ?></span></p>
        <p>number : <span><?php echo $fetch_orders['number']; ?></span></p>
        <p>email : <span><?php echo $fetch_orders['email']; ?></span></p>
        <p>address : <span><?php echo $fetch_orders['address']; ?></span></p>
        <p>method : <span><?php echo $fetch_orders['method']; ?></span></p>
        <p>total products : <span><?php echo $fetch_orders['total_products']; ?></span></p>
        <p>total price : <span>$<?php echo $fetch_orders['total_price']; ?>/-</span></p>
        <p>payment status : <span style="color:<?php echo ($fetch_orders['payment_status'] == 'pending') ? 'red' : 'green'; ?>;">
            <?php echo $fetch_orders['payment_status']; ?></span></p>
        <form method="post" action="">
          <input type="hidden" name="order_id" value="<?php echo $fetch_orders['id']; ?>">
          <select name="update_payment">
            <option disabled selected><?php echo $fetch_orders['payment_status']; ?></option>
            <option value="pending">pending</option>
            <option value="completed">completed</option>
          </select>
          <input type="submit" value="update order" name="update_order" class="btn">
          <a href="admin_orders.php?delete=<?php echo $fetch_orders['id']; ?>" class="delete"
             onclick="return confirm('Delete this order?');">delete</a>
        </form>
      </div>
      <?php
          }
      } else {
          echo '<p class="empty">no orders placed yet!</p>';
      }
      ?>
    </div>
  </section>
  <script type="text/javascript" src="script.js"></script>
</body>
</html>
