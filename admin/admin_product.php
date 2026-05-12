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

/* adding products */
if (isset($_POST['add_product'])) {
    $product_name    = $_POST['name'];
    $product_price   = $_POST['price'];
    $product_detail  = $_POST['detail'];
    $image_size      = $_FILES['image']['size'];
    $image_tmp_name  = $_FILES['image']['tmp_name'];
    $image_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $image           = uniqid() . '.' . $image_extension;
    $image_folder    = 'image/' . $image;

    $check = $conn->prepare("SELECT name FROM products WHERE name = ?");
    $check->execute([$product_name]);

    if ($check->rowCount() > 0) {
        $message[] = 'Product name already exists!';
    } elseif ($image_size > 2000000) {
        $message[] = 'Image size is too large!';
    } else {
        $stmt = $conn->prepare("INSERT INTO products (name, price, product_detail, image) VALUES (?, ?, ?, ?)");
        $stmt->execute([$product_name, $product_price, $product_detail, $image]);
        move_uploaded_file($image_tmp_name, $image_folder);
        $message[] = 'Product added successfully!';
    }
}

/* updating products */
if (isset($_POST['update_product'])) {
    $update_id     = (int)$_POST['update_p_id'];
    $update_name   = $_POST['update_p_name'];
    $update_price  = $_POST['update_p_price'];
    $update_detail = $_POST['update_p_detail'];

    if (!empty($_FILES['update_p_image']['name'])) {
        $new_image          = $_FILES['update_p_image']['name'];
        $new_image_size     = $_FILES['update_p_image']['size'];
        $new_image_tmp_name = $_FILES['update_p_image']['tmp_name'];

        if ($new_image_size > 2000000) {
            $message[] = 'Image size is too large! (max 2MB)';
        } else {
            $old = $conn->prepare("SELECT image FROM products WHERE id = ?");
            $old->execute([$update_id]);
            $old_row = $old->fetch();
            if ($old_row && file_exists('image/' . $old_row['image'])) {
                unlink('image/' . $old_row['image']);
            }
            move_uploaded_file($new_image_tmp_name, 'image/' . $new_image);

            $stmt = $conn->prepare("UPDATE products SET name=?, price=?, product_detail=?, image=? WHERE id=?");
            $stmt->execute([$update_name, $update_price, $update_detail, $new_image, $update_id]);
            $message[] = 'Product updated successfully!';
        }
    } else {
        $stmt = $conn->prepare("UPDATE products SET name=?, price=?, product_detail=? WHERE id=?");
        $stmt->execute([$update_name, $update_price, $update_detail, $update_id]);
        $message[] = 'Product updated successfully!';
    }

    header('location:admin_product.php');
    exit();
}

/* delete products */
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];

    $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->execute([$delete_id]);
    $fetch_delete_image = $stmt->fetch();
    if ($fetch_delete_image && file_exists('image/' . $fetch_delete_image['image'])) {
        unlink('image/' . $fetch_delete_image['image']);
    }

    $conn->prepare("DELETE FROM products WHERE id = ?")->execute([$delete_id]);
    $conn->prepare("DELETE FROM cart WHERE pid = ?")->execute([$delete_id]);
    $conn->prepare("DELETE FROM wishlist WHERE pid = ?")->execute([$delete_id]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css">
  <link rel="stylesheet" type="text/css" href="style.css">
  <title>products</title>
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

  <section class="add-products">
    <form method="post" action="" enctype="multipart/form-data">
      <h1 class="title">add a new product</h1>
      <div class="input-field">
        <label>product name</label>
        <input type="text" name="name" required>
      </div>
      <div class="input-field">
        <label>product price</label>
        <input type="text" name="price" min="0" required>
      </div>
      <div class="input-field">
        <label>product detail</label>
        <textarea name="detail" required></textarea>
      </div>
      <div class="input-field">
        <label>product image</label>
        <input type="file" name="image" accept="image/jpg, image/jpeg, image/png, image/webp" required>
      </div>
      <input type="submit" value="add product" name="add_product" class="btn">
    </form>
  </section>

  <!-- show products -->
  <section class="show-products">
    <h1 class="title">Products</h1>
    <div class="box-container">
      <?php
      $stmt = $conn->query("SELECT * FROM products");
      if ($stmt->rowCount() > 0) {
          while ($fetch_product = $stmt->fetch()) {
      ?>
      <div class="box">
          <img src="image/<?php echo $fetch_product['image']; ?>" alt="">
          <p class="price">Price: <?php echo $fetch_product['price']; ?> dt</p>
          <h4><?php echo $fetch_product['name']; ?></h4>
          <p class="detail"><?php echo $fetch_product['product_detail']; ?></p>
          <a href="admin_product.php?edit=<?php echo $fetch_product['id']; ?>" class="edit">Edit</a>
          <a href="admin_product.php?delete=<?php echo $fetch_product['id']; ?>" class="delete"
             onclick="return confirm('Delete this product?');">Delete</a>
      </div>
      <?php }} ?>
    </div>
  </section>

  <section class="update-container" style="display:none;">
    <?php
    if (isset($_GET['edit'])) {
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([(int)$_GET['edit']]);
        $fetch_edit = $stmt->fetch();

        if ($fetch_edit) {
    ?>
    <form method="post" action="" enctype="multipart/form-data">
        <img src="image/<?php echo $fetch_edit['image']; ?>" alt="">
        <input type="hidden" name="update_p_id" value="<?php echo $fetch_edit['id']; ?>">
        <input type="text"   name="update_p_name"   value="<?php echo $fetch_edit['name']; ?>" required>
        <input type="number" name="update_p_price" min="0" step="0.01" value="<?php echo $fetch_edit['price']; ?>" required>
        <textarea name="update_p_detail" required><?php echo $fetch_edit['product_detail']; ?></textarea>
        <input type="file" name="update_p_image" accept="image/jpg, image/jpeg, image/png, image/webp">
        <input type="submit" value="Update" name="update_product" class="edit">
        <input type="button" value="Cancel" class="option-btn btn" id="close-edit">
    </form>
    <?php
        }
        echo "<script>document.querySelector('.update-container').style.display='block';</script>";
    }
    ?>
  </section>

  <script type="text/javascript" src="script.js"></script>
</body>
</html>
