<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css"
    />
    <link rel="stylesheet" type="text/css" href="style.css" />
    <title>Document</title>
  </head>

  <body>
    <header class="header">
      <div class="flex">
        <a href="admin.php" class="logo">Admin<span>Pannel</span></a>

        <nav class="navbar">
          <a href="admin.php">Home</a>
          <a href="admin_product.php">Products</a>
          <a href="admin_orders.php">Orders</a>
          <a href="admin_users.php">Users</a>
          <a href="admin_message.php">Messages</a>
            <div class="navbar-user-info">
    <span>
      <?php echo $_SESSION['admin_name']; ?></span>
    <form method="post">
      <button name="logout" class="logout-btn">LOG OUT</button>
    </form>
  </div>
        </nav>

        <div class="icons">
          <i class="bi bi-list" id="menu-btn"></i>
          <i class="bi bi-person" id="user-btn"></i>
        </div>

        <div class="user-box">
          <p>
            username : <span><?php echo $_SESSION['admin_name']; ?></span>
          </p>
          <p>
            email : <span><?php echo $_SESSION['admin_email']; ?></span>
          </p>

          <form method="post" class="logout">
            <button name="logout" class="logout-btn">LOG OUT</button>
          </form>
        </div>
      </div>
    </header>
    <nav class="mobile-nav" id="mobile-nav">
    <a href="index.php">Home</a>
    <a href="shop.php">Shop</a>
    <a href="about.php">About</a>
    <a href="contact.php">Contact</a>
    <?php if (isset($_SESSION['user_id'])): ?>
    <a href="orders.php">My Orders</a>
    <a href="profile.php">My Profile</a>
    <form method="POST" class="mobile-logout-form">
        <button type="submit" name="logout" class="mobile-logout-btn">Logout</button>
    </form>
    <?php else: ?>
    <a href="login.php">Login</a>
    <a href="signup.php">Sign Up</a>
    <?php endif; ?>
</nav>
      
  </body>
</html>
