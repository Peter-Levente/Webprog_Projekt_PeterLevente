<?php
session_start();

include "../Database/db_connection.php";
include "Product.php";
include "../Authentication/User.php";

if (!file_exists('../Database/db_connection.php')) {  // Ellenőrzi, hogy létezik-e az adatbázis-kapcsolatot biztosító fájl.
    die("Connection initiation file not found!");  // Ha nem létezik, leállítja a programot és hibaüzenetet ad.
}

if (!isset($conn)) {  // Ellenőrzi, hogy az adatbázis-kapcsolat ($conn) létezik-e.
    die("Error: No database connection!");  // Ha nincs kapcsolat, leállítja a programot és hibaüzenetet ad.
}

if ($conn->connect_error) {  // Ha kapcsolat hiba történt, akkor azt kezeli.
    die("Connection error: " . $conn->connect_error);  // Kiírja a hibát és leállítja a programot.
}

$category = "Balls";

$products = Product::getProductsByCategory($conn, $category);

$userHandler = new User($conn);

if ($userHandler->autoLogin())  // Ha a felhasználó már be van jelentkezve (automatikus bejelentkezés), akkor ezt az automatikus bejelentkezést végrehajtja.
?>

<html lang="en">
<head>
    <meta charset="UTF-8">  <!-- A karakterkódolás beállítása. -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">  <!-- A Font Awesome ikonszet beillesztése. -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  <!-- A mobil nézet optimalizálásához szükséges meta tag. -->
    <title>Football Shop</title>  <!-- Az oldal címe, amely a böngésző fülén jelenik meg. -->
    <link rel="stylesheet" href="PROJECT.css">  <!-- A stíluslapok beillesztése, hogy az oldal kinézete megfelelő legyen. -->
</head>
<body>
<div class="wrapper">  <!-- Az egész oldal tartalmát tartalmazó konténer. -->
    <header>
        <nav class="header">  <!-- A navigációs sáv, amely az oldalon lévő fontos linkeket tartalmazza. -->
            <div class="title">
                <h1>Football Shop</h1>  <!-- Az oldal címe (Football Shop). -->
                <div class="auth-buttons">  <!-- A bejelentkezési/registrációs gombokat tartalmazó szekció. -->
                    <?php if (!isset($_SESSION['username'])): ?>  <!-- Ha a felhasználó nincs bejelentkezve, akkor megjeleníti a bejelentkezés és regisztráció lehetőségét. -->
                        <a href="index.php" class="home-link">
                            <i class="fa-solid fa-house"></i> Home
                        </a>
                        <a href="../Authentication/login.php"><i class="fa-solid fa-right-to-bracket"></i> Login</a>
                        <a href="../Authentication/register.php"><i class="fa-solid fa-user-plus"></i> Registration</a>
                    <?php endif; ?>
                </div>

                <div class="auth-buttons">
                    <?php if (isset($_SESSION['username'])): ?>  <!-- Ha a felhasználó be van jelentkezve, akkor megjeleníti a kijelentkezés gombot és a felhasználó nevét. -->
                        <a href="index.php" class="home-link">
                            <i class="fa-solid fa-house"></i> Home
                        </a>
                        <a href="../Authentication/logout.php" class="auth-right"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
                        <span class="welcome-message">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>  <!-- Kiírja a felhasználó nevét. -->
                    <?php endif; ?>
                </div>

                <div class="header-actions">  <!-- Az oldal tetején található műveletek gombjai (például kosár és rendelések). -->
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
            <div class="menu">  <!-- Navigációs menü a termékkategóriákhoz. -->
                <a href="tracksuits.php">Club Apparel</a>
                <a href="jerseys.php">Club Jerseys</a>
                <a href="shoes.php">Football Shoes</a>
                <a href="balls.php">Football Balls</a>
            </div>
        </nav>
    </header>
    <main>
        <?php if (count($products) > 0): ?>  <!-- Ha van legalább egy termék a "Balls" kategóriában, akkor megjeleníti azokat. -->
            <?php foreach ($products as $product): ?>  <!-- Végigiterál minden terméken, és megjeleníti őket. -->
                <a href="ball_product_detail.php?product_id=<?= $product->id ?>" class="product">  <!-- Link a termék részletes oldalára. -->
                    <div>
                        <img src="<?= $product->image ?>" alt="">  <!-- A termék képe. -->
                        <p><?= htmlspecialchars($product->name) ?></p>  <!-- A termék neve. -->
                        <p class="price"><?= number_format($product->price, 2) ?> lei</p>  <!-- A termék ára. -->
                    </div>
                </a>
            <?php endforeach; ?>
        <?php else: ?>  <!-- Ha nincs termék, akkor kiírja, hogy nincs találat. -->
            <p>No products found in the <?= htmlspecialchars($category) ?> category.</p>
        <?php endif; ?>
    </main>

    <footer>
        <div class="footer">
            <p>All rights reserved ©Football Shop 2024</p>  <!-- Az oldal alján megjelenő jogi információk. -->
        </div>
    </footer>
</div>
</body>
</html>
