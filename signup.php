<?php
include 'connection.php';

$message = [];

if (isset($_POST['submit-btn'])) {

    $name      = $_POST['name'];
    $email     = $_POST['email'];
    $password  = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    
    $password_valid = '/^(?=.*[A-Za-z])(?=.*\d).{8,}$/';
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        $message[] = 'User already exists!';
    } elseif ($password !== $cpassword) {
        $message[] = 'Passwords do not match!';
    } elseif (!preg_match($password_valid, $password)) {
        $message[] = 'Password must contain at least 8 characters, one uppercase letter, one lowercase letter, and one number!';
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $password]);
        $message[] = 'Account created successfully! Redirecting to login...';
        header('refresh:2; url=login.php');
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
  <title>Sign Up </title>
</head>
<body>
<a href="client/index.php" class="back-home">
  <i class="bi bi-arrow-left"></i> Back to Shop
</a>
<?php
if (isset($message)) {
    foreach ($message as $msg) {
        $icon = (strpos($msg, 'successfully') !== false) ? 'bi-check-circle' : 'bi-exclamation-circle';
        echo '<div class="flash-msg">
        <i class="bi ' . $icon . '"></i>
        <span>' . $msg . '</span>
        <i class="bi bi-x-circle" onclick="this.parentElement.style.display=\'none\';" style="margin-left: auto; cursor: pointer;"></i>
        </div>';
    }
}
?>

<section class="form-container">
  <div class="form-wrapper">
    <div class="form-header">
      <h1><i class="bi bi-person-plus"></i> Create Account</h1>
      <p>Join our community</p>
    </div>
    
    <form action="" method="POST" class="auth-form">
      
  <div class="form-group">
    <label for="name"><i class="bi bi-person"></i> User Name</label>
    <input type="text" id="name" name="name" placeholder="Your user name" required>
  </div>

  <div class="form-group">
    <label for="email"><i class="bi bi-envelope"></i> Email Address</label>
    <input type="email" id="email" name="email" placeholder="your@email.com" required>
  </div>



  <div class="form-group">
    <label for="password"><i class="bi bi-key"></i> Password</label>
    <input type="password" id="password" name="password" placeholder=".........." required>
  </div>

  <div class="form-group">
    <label for="cpassword"><i class="bi bi-key-fill"></i> Confirm Password</label>
    <input type="password" id="cpassword" name="cpassword" placeholder=".........." required>
  </div>


      <button type="submit" name="submit-btn" class="btn-submit">
        <i class="bi bi-check-circle"></i> Create Account
      </button>
    </form>

    <div class="form-footer">
      <p>Already have an account? <a href="login.php">Log In</a></p>
    </div>
  </div>
</section>

</body>
</html>
