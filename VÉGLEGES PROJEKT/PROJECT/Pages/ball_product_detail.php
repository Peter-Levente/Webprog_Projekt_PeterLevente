<?php
session_start();

include "../Database/db_connection.php";
include "Product.php";
include "../Authentication/User.php";

// Ellenőrizzük, hogy létezik-e a db_connection.php fájl
if (!file_exists('../Database/db_connection.php')) {
    die("Connection initiation file not found!"); // Ha nem létezik, hibát jelez
}

// Ellenőrizzük, hogy létezik-e az adatbázis kapcsolat
if (!isset($conn)) {
    die("Error: No database connection!"); // Ha nincs kapcsolat, hibát jelez
}

// Ellenőrizzük, hogy van-e kapcsolat problémája
if ($conn->connect_error) {
    die("Connection error: " . $conn->connect_error); // Ha van kapcsolat hiba, azt is jelezzük
}

// Ellenőrizzük, hogy van-e 'product_id' paraméter az URL-ben
if (isset($_GET['product_id'])) {
    // Ha van, a termék ID-ját lekérjük az URL-ből
    $product_id = $_GET['product_id'];

    $product = Product::getProductById($conn, $product_id);
} else {
    $product = null;
}

$userHandler = new User($conn);

// Automatikus bejelentkezés meghívása, ha van mentett session adat
if ($userHandler->autoLogin())
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Football Shop - Product</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Oldal stíluslapjának betöltése -->
    <link rel="stylesheet" href="PROJECT.css">
    <!-- FontAwesome ikonok betöltése -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
<div class="container">
    <header>
        <nav class="navbar">
            <div class="title">
                <h1>Football Shop</h1>
                <div class="auth-buttons">
                    <!-- Ha nincs bejelentkezve a felhasználó -->
                    <?php if (!isset($_SESSION['username'])): ?>
                        <a href="index.php" class="home-link">
                            <i class="fa-solid fa-house"></i> Home
                        </a>
                        <a href="../Authentication/login.php"><i class="fa-solid fa-right-to-bracket"></i> Login</a>
                        <a href="../Authentication/register.php"><i class="fa-solid fa-user-plus"></i> Registration</a>
                    <?php endif; ?>
                </div>

                <div class="auth-buttons">
                    <!-- Ha be van jelentkezve a felhasználó -->
                    <?php if (isset($_SESSION['username'])): ?>
                        <a href="index.php" class="home-link">
                            <i class="fa-solid fa-house"></i> Home
                        </a>
                        <a href="../Authentication/logout.php" class="auth-right"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
                    <?php endif; ?>
                </div>

                <div class="header-actions">
                    <!-- Ha be van jelentkezve a felhasználó -->
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
            <div class="product-title">
                <h3>PRODUCT DETAILS</h3>
            </div>
        </nav>
    </header>

    <main>
        <?php if ($product): ?>
            <!-- Ha van termék, annak részleteit jelenítjük meg -->
            <div class="product-details">
                <img src="<?= htmlspecialchars($product->image) ?>" alt="<?= htmlspecialchars($product->name) ?>">
                <h2><?= htmlspecialchars($product->name) ?></h2>
                <p class="price"><?= number_format($product->price, 2) ?> lei</p>

                <!-- Kosárba tétel űrlap más termékekhez -->
                <form action="../Cart%20management/add_to_cart.php" method="post">
                    <input type="hidden" name="product_id" value="<?= htmlspecialchars($product->id) ?>">
                    <label for="size">Size:</label>
                    <select name="size" id="size" required>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                    <label for="quantity">Quantity:</label>
                    <input type="number" name="quantity" id="quantity" min="1" max="50" value="1" required>
                    <button type="submit">Add to Cart</button>
                </form>
            </div>
        <?php else: ?>
            <!-- Ha a termék nem található -->
            <p>Product not found.</p>
        <?php endif; ?>
    </main>

    <footer>
        <div class="footer">
            <!-- Lábléc információ -->
            <p>All rights reserved ©Football Shop 2024</p>
        </div>
    </footer>
</div>
</body>

</html>
