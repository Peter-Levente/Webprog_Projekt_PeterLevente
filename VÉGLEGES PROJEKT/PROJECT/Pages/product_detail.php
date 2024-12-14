<?php
session_start();

include "../Database/db_connection.php";
include "Product.php";
include "../Cart management/Cart.php";
include "../Authentication/User.php";

// Ha nem található a db_connection.php fájl, hibaüzenet
if (!file_exists('../Database/db_connection.php')) {
    die("Connection initiation file not found!");
}

// Ellenőrzi, hogy a kapcsolat létezik-e
if (!isset($conn)) {
    die("Error: No database connection!");
}

// Ha kapcsolat hiba van, akkor hibaüzenet
if ($conn->connect_error) {
    die("Connection error: " . $conn->connect_error);
}

// Ha a URL-ben szerepel a termék azonosítója, akkor lekéri a terméket
if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    $product = Product::getProductById($conn, $product_id);
} else {
    $product = null;
}

$userHandler = new User($conn);

if ($userHandler->autoLogin())
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Football Shop - Product</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="PROJECT.css"> <!-- Stíluslap beillesztése -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Ikonok -->
</head>

<body>
<div class="container">
    <header>
        <nav class="navbar">
            <div class="title">
                <h1>Football Shop</h1> <!-- Weboldal neve -->
                <div class="auth-buttons">
                    <!-- Ha nincs bejelentkezve a felhasználó -->
                    <?php if (!isset($_SESSION['username'])): ?>
                        <a href="index.php" class="home-link">
                            <i class="fa-solid fa-house"></i> Home <!-- Főoldal link -->
                        </a>
                        <a href="../Authentication/login.php"><i class="fa-solid fa-right-to-bracket"></i> Login</a> <!-- Bejelentkezés -->
                        <a href="../Authentication/register.php"><i class="fa-solid fa-user-plus"></i> Registration</a> <!-- Regisztráció -->
                    <?php endif; ?>
                </div>

                <div class="auth-buttons">
                    <!-- Ha be van jelentkezve a felhasználó -->
                    <?php if (isset($_SESSION['username'])): ?>
                        <a href="index.php" class="home-link">
                            <i class="fa-solid fa-house"></i> Home
                        </a>
                        <a href="../Authentication/logout.php" class="auth-right"><i class="fa-solid fa-right-from-bracket"></i> Logout</a> <!-- Kijelentkezés -->
                    <?php endif; ?>
                </div>

                <div class="header-actions">
                    <!-- Ha be van jelentkezve a felhasználó, megjelennek az akciók -->
                    <?php if (isset($_SESSION['username'])): ?>
                        <a id="cart-icon" href="../Cart%20management/my_cart.php" class="header-link">
                            <i class="fa-solid fa-basket-shopping"></i> My Cart <!-- Kosár -->
                        </a>
                        <a id="orders-icon" href="../Order/my_orders.php" class="header-link">
                            <i class="fa-solid fa-box"></i> My Orders <!-- Rendelések -->
                        </a>
                        <a id="mobile-menu-icon" class="header-link"><i class="fa-solid fa-bars"></i></a> <!-- Mobil menü -->
                    <?php endif; ?>
                </div>
            </div>
            <div class="product-title">
                <h3>PRODUCT DETAILS</h3> <!-- Termék részletek -->
            </div>
        </nav>
    </header>

    <main>
        <?php if ($product): ?>
            <!-- Ha létezik a termék, annak részletei -->
            <div class="product-details">
                <img src="<?= htmlspecialchars($product->image) ?>" alt="<?= htmlspecialchars($product->name) ?>"> <!-- Termék kép -->
                <h2><?= htmlspecialchars($product->name) ?></h2> <!-- Termék neve -->
                <p class="price"><?= number_format($product->price, 2) ?> lei</p> <!-- Termék ára -->
                <!-- Kosárhoz adás űrlap -->
                <form action="../Cart%20management/add_to_cart.php" method="post">
                    <input type="hidden" name="product_id" value="<?= $product->id ?>"> <!-- Termék ID -->
                    <label for="size">Size:</label>
                    <select name="size" id="size" required> <!-- Méret kiválasztása -->
                        <option value="S">S</option>
                        <option value="M">M</option>
                        <option value="L">L</option>
                        <option value="XL">XL</option>
                        <option value="XXL">XXL</option>
                    </select>
                    <label for="quantity">Quantity:</label>
                    <input type="number" name="quantity" id="quantity" min="1" max="50" value="1" required> <!-- Mennyiség kiválasztása -->
                    <button type="submit">Add to Cart</button> <!-- Kosárba rakás -->
                </form>
                <?php if (isset($successMessage)): ?>
                    <p class="success"><?= $successMessage ?></p> <!-- Sikerüzenet -->
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p>Product not found.</p> <!-- Ha a termék nem található -->
        <?php endif; ?>
    </main>

    <footer>
        <div class="footer">
            <p>All rights reserved ©Football Shop 2024</p> <!-- Lábléc -->
        </div>
    </footer>
</div>
</body>
</html>
