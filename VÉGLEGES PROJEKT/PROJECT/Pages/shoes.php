<?php
session_start();

include "../Database/db_connection.php";
include "Product.php";
include "../Authentication/User.php";

// Ellenőrizzük, hogy létezik-e a kapcsolat fájl
if (!file_exists('../Database/db_connection.php')) {
    die("A kapcsolat inicializálásához szükséges fájl nem található!");
}

// Ha nincs adatbázis kapcsolat, hibaüzenet
if (!isset($conn)) {
    die("Hiba: Nincs adatbázis kapcsolat!");
}

// Ha a kapcsolat hibás, szintén hibaüzenet
if ($conn->connect_error) {
    die("Kapcsolati hiba: " . $conn->connect_error);
}

$category = "Shoes";

$products = Product::getProductsByCategory($conn, $category);

$userHandler = new User($conn);

if ($userHandler->autoLogin())
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Football Shop</title>
    <link rel="stylesheet" href="PROJECT.css">
</head>
<body>
<div class="wrapper">
    <header>
        <nav class="header">
            <div class="title">
                <h1>Football Shop</h1>
                <div class="auth-buttons">
                    <!-- Ha a felhasználó nincs bejelentkezve -->
                    <?php if (!isset($_SESSION['username'])): ?>
                        <a href="index.php" class="home-link">
                            <i class="fa-solid fa-house"></i> Home
                        </a>
                        <a href="../Authentication/login.php"><i class="fa-solid fa-right-to-bracket"></i> Login</a>
                        <a href="../Authentication/register.php"><i class="fa-solid fa-user-plus"></i> Registration</a>
                    <?php endif; ?>
                </div>

                <div class="auth-buttons">
                    <!-- Ha a felhasználó be van jelentkezve -->
                    <?php if (isset($_SESSION['username'])): ?>
                        <a href="index.php" class="home-link">
                            <i class="fa-solid fa-house"></i> Home
                        </a>
                        <a href="../Authentication/logout.php" class="auth-right"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
                        <span class="welcome-message">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                    <?php endif; ?>
                </div>

                <div class="header-actions">
                    <!-- Ha a felhasználó be van jelentkezve -->
                    <?php if (isset($_SESSION['username'])): ?>
                        <a id="cart-icon" href="../Cart%20management/my_cart.php" class="header-link">
                            <i class="fa-solid fa-basket-shopping"></i> My Cart
                        </a>
                        <a id="orders-icon" href="../Order/my_orders.php" class="header-link">
                            <i class="fa-solid fa-box"></i> My Orders
                        </a>
                        <a id="mobile-menu-icon" class="header-link"><i class="fa-solid fa-bars"></i></a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="menu">
                <!-- Navigációs menü -->
                <a href="tracksuits.php">Club Apparel</a>
                <a href="jerseys.php">Club Jerseys</a>
                <a href="shoes.php">Football Shoes</a>
                <a href="balls.php">Football Balls</a>
            </div>
        </nav>
    </header>

    <main>
        <?php if (count($products) > 0): ?>
            <!-- Ha találunk termékeket, akkor azokat megjelenítjük -->
            <?php foreach ($products as $product): ?>
                <a href="shoe_product_detail.php?product_id=<?= $product->id ?>" class="product">
                    <div>
                        <!-- Termék képének és nevének megjelenítése -->
                        <img src="<?= $product->image ?>" alt="">
                        <p><?= htmlspecialchars($product->name) ?></p>
                        <p class="price"><?= number_format($product->price, 2) ?> lei</p>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- Ha nincs termék a kategóriában, hibaüzenet -->
            <p>No products found in the <?= htmlspecialchars($category) ?> category.</p>
        <?php endif; ?>
    </main>

    <footer>
        <div class="footer">
            <p>All rights reserved ©Football Shop 2024</p>
        </div>
    </footer>
</div>
</body>
</html>
