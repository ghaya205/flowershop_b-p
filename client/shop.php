<?php
session_start();
include '../connection.php';

if (isset($_POST['logout'])) {
    session_destroy();
    header('location:../login.php');
    exit();
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort   = isset($_GET['sort'])   ? $_GET['sort'] : 'newest';

$sql    = "SELECT * FROM products WHERE 1";
$params = [];

if ($search !== '') {
    $sql .= " AND (name LIKE ? OR product_detail LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

switch ($sort) {
    case 'price_asc':  $sql .= " ORDER BY price ASC";  break;
    case 'price_desc': $sql .= " ORDER BY price DESC"; break;
    case 'name':       $sql .= " ORDER BY name ASC";   break;
    default:           $sql .= " ORDER BY id DESC";    break;
}

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();
$count    = count($products);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop — Bloom&amp;Petal</title>
</head>
<body>

<?php include 'header.php'; ?>

<div class="page-hero">
    <div class="breadcrumb"><a href="index.php">Home</a> / Shop</div>
    <h1>Our Flower Shop</h1>
    <p>Fresh arrangements handcrafted with love, for every occasion</p>
</div>

<div class="shop-layout">
    <aside class="shop-sidebar">
        <h3 class="sidebar-title">Filter &amp; Search</h3>
        <form method="GET" action="shop.php">
            <div class="search-bar">
                <input type="text" name="search" placeholder="Search flowers..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit"><i class="bi bi-search"></i></button>
            </div>
            <div class="filter-group">
                <p class="filter-label">Sort By</p>
                <label><input type="radio" name="sort" value="newest"     <?php echo $sort=='newest'    ?'checked':''; ?>> Newest First</label>
                <label><input type="radio" name="sort" value="price_asc"  <?php echo $sort=='price_asc' ?'checked':''; ?>> Price: Low to High</label>
                <label><input type="radio" name="sort" value="price_desc" <?php echo $sort=='price_desc'?'checked':''; ?>> Price: High to Low</label>
                <label><input type="radio" name="sort" value="name"       <?php echo $sort=='name'      ?'checked':''; ?>> Name A–Z</label>
            </div>
            <button type="submit" class="btn-primary btn-full">Apply Filter</button>
            <a href="shop.php" class="filter-clear">Clear All</a>
        </form>
    </aside>

    <div class="shop-content">
        <div class="shop-toolbar">
            <p class="results-count"><?php echo $count; ?> product<?php echo $count !== 1 ? 's' : ''; ?> found<?php echo $search ? ' for "' . htmlspecialchars($search) . '"' : ''; ?></p>
            <form method="GET" action="shop.php">
                <?php if ($search): ?>
                <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                <?php endif; ?>
                <select name="sort" onchange="this.form.submit()">
                    <option value="newest"     <?php echo $sort=='newest'    ?'selected':''; ?>>Newest First</option>
                    <option value="price_asc"  <?php echo $sort=='price_asc' ?'selected':''; ?>>Price: Low–High</option>
                    <option value="price_desc" <?php echo $sort=='price_desc'?'selected':''; ?>>Price: High–Low</option>
                    <option value="name"       <?php echo $sort=='name'      ?'selected':''; ?>>Name A–Z</option>
                </select>
            </form>
        </div>

        <?php if ($count > 0): ?>
        <div class="products-grid">
            <?php foreach ($products as $p): ?>
            <div class="product-card">
                <div class="card-img">
                    <img src="image/<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>">
                    <div class="card-actions">
                        <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="wishlist.php?add=<?php echo $p['id']; ?>" class="card-action-btn" title="Wishlist"><i class="bi bi-heart"></i></a>
                        <a href="cart.php?add=<?php echo $p['id']; ?>"     class="card-action-btn" title="Add to Cart"><i class="bi bi-bag-plus"></i></a>
                        <?php endif; ?>
                        <a href="view.php?pid=<?php echo $p['id']; ?>" class="card-action-btn" title="View"><i class="bi bi-eye"></i></a>
                    </div>
                </div>
                <div class="card-body">
                    <h4><?php echo htmlspecialchars($p['name']); ?></h4>
                    <span class="price"><?php echo $p['price']; ?> dt</span>
                    <a href="view.php?pid=<?php echo $p['id']; ?>" class="btn-primary card-btn">View Details</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="bi bi-search"></i>
            <h3>No flowers found</h3>
            <p>Try a different search term or browse all our products</p>
            <a href="shop.php" class="btn-primary">Browse All</a>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
