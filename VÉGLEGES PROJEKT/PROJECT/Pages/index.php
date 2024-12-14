<?php
session_start();

include "../Database/db_connection.php";
include "../Authentication/User.php";
include "Product.php";

if (!file_exists('../Database/db_connection.php')) {  // Ellenőrzi, hogy létezik-e az adatbázis kapcsolat fájl.
    die("Connection initiation file not found!");  // Ha nem létezik, akkor leállítja a programot hibaüzenettel.
}

if (!isset($conn)) {  // Ellenőrzi, hogy a kapcsolat változó létezik-e.
    die("Error: No database connection!");  // Ha nincs kapcsolat, leállítja a programot.
}

if ($conn->connect_error) {  // Ha hiba történik a kapcsolódás során.
    die("Connection error: " . $conn->connect_error);  // Kiírja a hibaüzenetet és leállítja a programot.
}

// Lekérdezi az összes terméket az adatbázisból
$products = Product::getAllProducts($conn);

$userHandler = new User($conn);

// Automatikus bejelentkezés meghívása, ha van mentett session adat
if ($userHandler->autoLogin())
?>


<html lang="en">
<head>
    <meta charset="UTF-8">  <!-- A karakterkódolás beállítása (UTF-8), amely biztosítja az ékezetek helyes megjelenítését. -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">  <!-- A Font Awesome ikonszet beillesztése. -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  <!-- Mobilbarát nézet beállítása. -->
    <title>Football Shop - Home</title>  <!-- Az oldal címe. -->
    <link rel="stylesheet" href="PROJECT.css">  <!-- A stíluslapok beillesztése a megjelenéshez. -->
</head>
<body>
<div class="wrapper">
    <header>
        <nav class="header">  <!-- A navigációs sáv a menüpontokkal és bejelentkezési információval. -->
            <div class="title">
                <h1>Football Shop</h1>  <!-- Az oldal főcímsora. -->

                <div class="auth-buttons">  <!-- A bejelentkezési és regisztrációs gombok szekciója. -->
                    <?php if (!isset($_SESSION['username'])): ?>  <!-- Ha a felhasználó nincs bejelentkezve, akkor a login és regisztráció gombok jelennek meg. -->
                        <a href="../Authentication/login.php"><i class="fa-solid fa-right-to-bracket"></i> Login</a>
                        <a href="../Authentication/register.php"><i class="fa-solid fa-user-plus"></i> Registration</a>
                    <?php endif; ?>
                </div>

                <div class="auth-buttons">  <!-- A kijelentkezési információkat és a felhasználói üdvözlő üzenetet tartalmazó szekció. -->
                    <?php if (isset($_SESSION['username'])): ?>  <!-- Ha a felhasználó be van jelentkezve, akkor a kijelentkezés és üdvözlő üzenet jelenik meg. -->
                        <a href="../Authentication/logout.php" class="auth-right"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
                        <span class="welcome-message">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>  <!-- Üdvözli a felhasználót a nevével. -->
                    <?php endif; ?>
                </div>

                <div class="header-actions">  <!-- Kosár és rendelések gombjai a bejelentkezett felhasználóknak. -->
                    <?php if (isset($_SESSION['username'])): ?>  <!-- Ha a felhasználó be van jelentkezve, akkor láthatja a kosár és rendelés linkeket. -->
                        <a id="cart-icon" href="../Cart%20management/my_cart.php" class="header-link">
                            <i class="fa-solid fa-basket-shopping"></i> My Cart
                        </a>
                        <a id="orders-icon" href="../Order/my_orders.php" class="header-link">
                            <i class="fa-solid fa-box"></i> My Orders
                        </a>
                        <a id="mobile-menu-icon" class="header-link"><i class="fa-solid fa-bars"></i></a>  <!-- Mobil menü ikona. -->
                    <?php endif; ?>
                </div>
            </div>
            <div class="menu">  <!-- A menüpontok, amelyek különböző termékkategóriákra vezetnek. -->
                <a href="tracksuits.php">Club Apparel</a>
                <a href="jerseys.php">Club Jerseys</a>
                <a href="shoes.php">Football Shoes</a>
                <a href="balls.php">Football Balls</a>
            </div>
        </nav>
    </header>
    <main>
        <?php foreach ($products as $product): ?>  <!-- A termékek megjelenítése. Minden egyes termékről képet, nevet és árat jelenít meg. -->
            <a href="product_detail.php?product_id=<?= $product->id ?>" class="product">
                <div>
                    <img src="<?= $product->image ?>" alt="">  <!-- A termék képe. -->
                    <p><?= htmlspecialchars($product->name) ?></p>  <!-- A termék neve. -->
                    <p class="price"><?= number_format($product->price, 2) ?> lei</p>  <!-- A termék ára, formázott két tizedesjegy pontossággal. -->
                </div>
            </a>
        <?php endforeach; ?>
    </main>

    <footer>
        <div class="footer">
            <p>All rights reserved ©Football Shop 2024</p>  <!-- Az oldal alján megjelenő jogi információk. -->
        </div>
    </footer>

</div>
</body>
</html>
