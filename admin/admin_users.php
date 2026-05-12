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

if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([(int)$_GET['delete']]);
    header('location:admin_users.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>total registered account</title>
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
  <section class="user-container">
    <h1 class="title">total registered account</h1>
    <div class="box-container">
      <?php
      $stmt = $conn->query("SELECT * FROM users");
      if ($stmt->rowCount() > 0) {
          while ($fetch_users = $stmt->fetch()) {
      ?>
      <div class="box">
          <p>user id : <span><?php echo $fetch_users['id']; ?></span></p>
          <p>user name : <span><?php echo $fetch_users['name']; ?></span></p>
          <p>email : <span><?php echo $fetch_users['email']; ?></span></p>
          <p>user type : <span style="color:<?php if ($fetch_users['user_type'] == 'admin') { echo 'purple'; } ?>;">
              <?php echo $fetch_users['user_type']; ?></span></p>
          <a href="admin_users.php?delete=<?php echo $fetch_users['id']; ?>" onclick="return confirm('delete this user?');" class="delete">delete</a>
      </div>
      <?php }} ?>
    </div>
  </section>
  <script type="text/javascript" src="script.js"></script>
</body>
</html>
