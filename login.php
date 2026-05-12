<?php
session_start();
include 'connection.php';
$message = [];

if (isset($_POST['submit-btn'])) {

    $email    = filter_var($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $row = $stmt->fetch();

    if ($row) {
        if ($row['password'] === $password) {
            if ($row['user_type'] === 'admin') {
                $_SESSION['admin_name']  = $row['name'];
                $_SESSION['admin_email'] = $row['email'];
                $_SESSION['admin_id']    = $row['id'];
                header('Location: admin/admin.php');
                exit();
            } elseif ($row['user_type'] === 'user') {
                $_SESSION['user_name']  = $row['name'];
                $_SESSION['user_email'] = $row['email'];
                $_SESSION['user_id']    = $row['id'];
                header('Location: ../client/index.php');
                exit();
            }
        } else {
            $message[] = 'Incorrect email or password!';
        }
    } else {
        $message[] = 'Incorrect email or password!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css">
  <link rel="stylesheet" type="text/css" href="admin/style.css">
  <title>Log In</title>
</head>
<body>
<a href="index.php" class="back-home">
  <i class="bi bi-arrow-left"></i> Back to Shop
</a>
<?php
if (isset($message)) {
    foreach ($message as $msg) {
        echo '<div class="flash-msg">
        <i class="bi bi-exclamation-circle"></i>
        <span>' . $msg . '</span>
        <i class="bi bi-x-circle" onclick="this.parentElement.style.display=\'none\';" style="margin-left: auto; cursor: pointer;"></i>
        </div>';
    }
}
?>

<section class="form-container">
  <div class="form-wrapper">
    <div class="form-header">
      <h1><i class="bi bi-lock"></i> Welcome Back</h1>
      <p>Log in to your account</p>
    </div>
    
    <form action="" method="POST" class="auth-form">
      <div class="form-group">
        <label for="email"><i class="bi bi-envelope"></i> Email Address</label>
        <input type="email" id="email" name="email" placeholder="your@email.com" required>
      </div>

      <div class="form-group">
        <label for="password"><i class="bi bi-key"></i> Password</label>
        <input type="password" id="password" name="password" placeholder="••••••••" required>
      </div>

      <div class="form-group checkbox-group">
        <label class="remember-me">
          <input type="checkbox" name="remember">
          <span>Remember me</span>
        </label>
        <a href="forgot_password.php" class="forgot-password">Forgot password?</a>
      </div>

      <button type="submit" name="submit-btn" class="btn-submit">
        <i class="bi bi-box-arrow-in-right"></i> Log In
      </button>
    </form>

    <div class="form-footer">
      <p>Don't have an account? <a href="signup.php">Sign Up Now</a></p>
    </div>
  </div>
</section>

</body>
</html>
