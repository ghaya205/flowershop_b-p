<?php
include '../connection.php';
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>admin pannal</title>
</head>
<body>
  <?php include 'admin_header.php'; ?>
  <section class="dashboard">
    <h1 class="title">dashboard</h1>
    <div class="box-container">

      <div class="box">
        <?php
        $stmt = $conn->query("SELECT SUM(total_price) AS total FROM orders WHERE payment_status = 'pending'");
        $total_pendings = $stmt->fetchColumn() ?? 0;
        ?>
        <h3><?php echo $total_pendings . 'dt'; ?></h3>
        <p>total pendings</p>
      </div>

      <div class="box">
        <?php
        $stmt = $conn->query("SELECT SUM(total_price) AS total FROM orders WHERE payment_status = 'completed'");
        $total_completed = $stmt->fetchColumn() ?? 0;
        ?>
        <h3><?php echo $total_completed . 'dt'; ?></h3>
        <p>total completed</p>
      </div>

      <div class="box">
        <?php
        $num_of_orders = $conn->query("SELECT COUNT(*) FROM orders")->fetchColumn();
        ?>
        <h3><?php echo $num_of_orders; ?></h3>
        <p>orders placed</p>
      </div>

      <div class="box">
        <?php
        $num_of_products = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
        ?>
        <h3><?php echo $num_of_products; ?></h3>
        <p>products added</p>
      </div>

      <div class="box">
        <?php
        $num_of_users = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
        ?>
        <h3><?php echo $num_of_users; ?></h3>
        <p>registered users</p>
      </div>

      <div class="box">
        <?php
        $num_of_admins = $conn->query("SELECT COUNT(*) FROM users WHERE user_type = 'admin'")->fetchColumn();
        ?>
        <h3><?php echo $num_of_admins; ?></h3>
        <p>total admin</p>
      </div>

      <div class="box">
        <?php
        $num_of_totaluser = $conn->query("SELECT COUNT(*) FROM users WHERE user_type = 'user'")->fetchColumn();
        ?>
        <h3><?php echo $num_of_totaluser; ?></h3>
        <p>total users</p>
      </div>

      <div class="box">
        <?php
        $num_of_messages = $conn->query("SELECT COUNT(*) FROM message")->fetchColumn();
        ?>
        <h3><?php echo $num_of_messages; ?></h3>
        <p>new messages</p>
      </div>

    </div>
  </section>
  <script type="text/javascript" src="script.js"></script>
</body>
</html>
