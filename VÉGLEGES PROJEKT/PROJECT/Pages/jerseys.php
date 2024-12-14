<?php
session_start();

include "../Database/db_connection.php";
include "Product.php";
include "../Authentication/User.php";

// Ellenőrizzük, hogy létezik-e az adatbázis kapcsolatot inicializáló fájl
if (!file_exists('../Database/db_connection.php')) {
    die("Connection initiation file not found!"); // Ha a fájl hiányzik, leállítjuk a végrehajtást
}

// Ellenőrizzük, hogy az adatbázis kapcsolat be van-e állítva
if (!isset($conn)) {
    die("Error: No database connection!"); // Ha nincs kapcsolat, leállítjuk a végrehajtást
}

// Ellenőrizzük, hogy van-e kapcsolat hiba
if ($conn->connect_error) {
    die("Connection error: " . $conn->connect_error); // Ha hiba van a kapcsolatban, leállítjuk a végrehajtást
}

$category = "Jerseys";

$products = Product::getProductsByCategory($conn, $category);

$userHandler = new User($conn);

// Automatikus bejelentkezés végrehajtása, ha van érvényes session
if ($userHandler->autoLogin())
?>

<!-- HTML struktúra az oldal megjelenítéséhez -->
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- FontAwesome ikonok betöltése -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Football Shop</title>
    <!-- Külső CSS fájl összekapcsolása -->
    <link rel="stylesheet" href="PROJECT.css">
</head>
<body>
<div class="wrapper">
    <!-- Fejléc, navigációs menü -->
    <header>
        <nav class="header">
            <div class="title">
                <h1>Football Shop</h1>

                <!-- Autentikációs gombok (bejelentkezés, regisztráció, kijelentkezés) -->
                <div class="auth-buttons">
                    <?php if (!isset($_SESSION['username'])): ?>
                        <!-- Bejelentkezés és regisztrációs linkek megjelenítése, ha a felhasználó nincs bejelentkezve -->
                        <a href="index.php" class="home-link">
                            <i class="fa-solid fa-house"></i> Home
                        </a>
                        <a href="../Authentication/login.php"><i class="fa-solid fa-right-to-bracket"></i> Login</a>
                        <a href="../Authentication/register.php"><i class="fa-solid fa-user-plus"></i> Registration</a>
                    <?php endif; ?>
                </div>

                <!-- Üdvözlő üzenet és kijelentkezés gomb megjelenítése, ha a felhasználó be van jelentkezve -->
                <div class="auth-buttons">
                    <?php if (isset($_SESSION['username'])): ?>
                        <a href="index.php" class="home-link">
                            <i class="fa-solid fa-house"></i> Home
                        </a>
                        <a href="../Authentication/logout.php" class="auth-right"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
                        <span class="welcome-message">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                    <?php endif; ?>
                </div>

                <!-- Fejléc műveletek (Kosár és Megrendelések), csak bejelentkezett felhasználóknak -->
                <div class="header-actions">
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
            <!-- Menü, kategóriák listája -->
            <div class="menu">
                <a href="tracksuits.php">Club Apparel</a>
                <a href="jerseys.php">Club Jerseys</a>
                <a href="shoes.php">Football Shoes</a>
                <a href="balls.php">Football Balls</a>
            </div>
        </nav>
    </header>

    <!-- Fő tartalom, termékek megjelenítése -->
    <main>
        <!-- Ellenőrizzük, hogy van-e termék a kategóriában -->
        <?php if (count($products) > 0): ?>
            <!-- A termékek listázása -->
            <?php foreach ($products as $product): ?>
                <a href="product_detail.php?product_id=<?= $product->id ?>" class="product">
                    <div>
                        <!-- Termék képének és adatainak megjelenítése -->
                        <img src="<?= $product->image ?>" alt="">
                        <p><?= htmlspecialchars($product->name) ?></p>
                        <p class="price"><?= number_format($product->price, 2) ?> lei</p>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- Ha nincs termék a kategóriában, üzenet megjelenítése -->
            <p>No products found in the <?= htmlspecialchars($category) ?> category.</p>
        <?php endif; ?>
    </main>

    <!-- Lábléc, szerzői jogok -->
    <footer>
        <div class="footer">
            <p>All rights reserved ©Football Shop 2024</p>
        </div>
    </footer>
</div>
</body>
</html>
