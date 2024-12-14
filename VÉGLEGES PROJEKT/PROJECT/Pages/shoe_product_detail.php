<?php
session_start();

include "../Database/db_connection.php";
include "Product.php";
include "../Authentication/User.php";

// Ellenőrizzük, hogy létezik-e az adatbázis kapcsolat fájl
if (!file_exists('../Database/db_connection.php')) {
    die("A kapcsolat inicializálásához szükséges fájl nem található!");
}

// Ha nincs adatbázis kapcsolat, hibaüzenet jelenik meg
if (!isset($conn)) {
    die("Hiba: Nincs adatbázis kapcsolat!");
}

// Ha a kapcsolat hibás, szintén hibaüzenet
if ($conn->connect_error) {
    die("Kapcsolati hiba: " . $conn->connect_error);
}

// Ha a URL-ben meg van adva a termék ID-je
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
    <link rel="stylesheet" href="PROJECT.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
<div class="container">
    <header>
        <nav class="navbar">
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
                <div class="product-title">
                    <h3>PRODUCT DETAILS</h3>
                </div>
        </nav>
    </header>

    <main>
        <?php if ($product): ?>
            <!-- Ha a termék létezik, megjelenítjük annak részleteit -->
            <div class="product-details">
                <img src="<?= htmlspecialchars($product->image) ?>" alt="<?= htmlspecialchars($product->name) ?>">
                <h2><?= htmlspecialchars($product->name) ?></h2>
                <p class="price"><?= number_format($product->price, 2) ?> lei</p>
                <!-- Termék hozzáadása a kosárhoz űrlap -->
                <form action="../Cart%20management/add_to_cart.php" method="post">
                    <input type="hidden" name="product_id" value="<?= $product->id ?>">
                    <label for="size">Size:</label>
                    <select name="size" id="size" required>
                        <!-- Méret opciók -->
                        <option value="38">38</option>
                        <option value="38.5">38.5</option>
                        <option value="39">39</option>
                        <option value="40">40</option>
                        <option value="40.5">40.5</option>
                        <option value="41">41</option>
                        <option value="42">42</option>
                        <option value="42.5">42.5</option>
                        <option value="43">43</option>
                        <option value="44">44</option>
                        <option value="44.5">44.5</option>
                        <option value="45">45</option>
                        <option value="45.5">45.5</option>
                        <option value="46">46</option>
                        <option value="47">47</option>
                        <option value="47.5">47.5</option>
                    </select>
                    <label for="quantity">Quantity:</label>
                    <input type="number" name="quantity" id="quantity" min="1" max="50" value="1" required>
                    <button type="submit">Add to Cart</button>
                </form>
            </div>
        <?php else: ?>
            <!-- Ha nem található a termék, hibaüzenet -->
            <p>Product not found.</p>
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
