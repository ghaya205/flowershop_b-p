<?php
include '../connection.php';
session_start();
$admin_id = $_SESSION['admin_id'] ?? null;
if (!isset($admin_id)) {
    header('location:login.php');
    exit();
}
if (isset($_POST['logout'])) {
    session_destroy();
    header('location:login.php');
    exit();
}


if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("DELETE FROM message WHERE id = ?");
    $stmt->execute([(int)$_GET['delete']]);
    header('location:admin_messages.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Messages</title>
</head>
<body>

<?php include 'admin_header.php'; ?>

<section class="message-container">
  <h1 class="title">Messages</h1>
  <div class="box-container">
    <?php
    $stmt = $conn->query("SELECT * FROM message");
    if ($stmt->rowCount() > 0) {
        while ($fetch_message = $stmt->fetch()) {
    ?>
    <div class="box">
        <p>User ID: <span><?php echo $fetch_message['user_id']; ?></span></p>
        <p>Name: <span><?php echo $fetch_message['name']; ?></span></p>
        <p>Email: <span><?php echo $fetch_message['email']; ?></span></p>
        <p>Number: <span><?php echo $fetch_message['number']; ?></span></p>
        <p>Message: <span><?php echo $fetch_message['message']; ?></span></p>
        <a href="admin_messages.php?delete=<?php echo $fetch_message['id']; ?>" onclick="return confirm('Delete this message?');" class="delete">Delete</a>
    </div>
    <?php
        }
    } else {
        echo '<p class="empty">No messages found.</p>';
    }
    ?>
  </div>
</section>

<script src="script.js"></script>
</body>
</html>
