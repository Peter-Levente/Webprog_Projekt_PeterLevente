<?php
session_start();
include "../Database/db_connection.php";
include "Cart.php";

// Ellenőrizd, hogy a kapcsolat fájl létezik-e
if (!file_exists('../Database/db_connection.php')) {
    die("Connection initiation file not found!"); // Ha nincs kapcsolat fájl, leállítjuk a szkriptet
}

// Ha a kapcsolat nem létezik, vagy hibás, akkor is leállítjuk a szkriptet
if (!isset($conn)) {
    die("Error: No database connection!");
}
if ($conn->connect_error) {
    die("Connection error: " . $conn->connect_error); // Ha hiba van a kapcsolatban, leállítjuk a szkriptet
}

// Ellenőrizzük, hogy a felhasználó be van-e jelentkezve
$userId = $_SESSION['user_id'] ?? null; // Lekérdezzük a bejelentkezett felhasználó azonosítóját
if (!$userId) {
    header("Location: ../Authentication/login.php"); // Ha nincs bejelentkezve, átirányítjuk a bejelentkezés oldalra
    exit;
}

// Az aktuális felhasználóhoz tartozó kosár létrehozása
$cart = new Cart($conn, $userId);

// Lekérdezzük az összes kosár elemet
$cartItems = $cart->getCartItems();

// Számítjuk a teljes árat
$totalPrice = 0;
foreach ($cartItems as $item) {
    $totalPrice += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart</title>
    <link rel="stylesheet" href="cart.css"> <!-- A kosár stíluslapja -->
</head>
<body>
<div class="container">
    <header>
        <nav class="navbar">
            <div>
                <h1>My Cart</h1> <!-- Kosár oldali cím -->
            </div>
            <div>
                <a href="../Pages/index.php">Home</a> <!-- Link a főoldalra -->
            </div>
        </nav>
    </header>

    <main class="cart-page">
        <h2 class="cart-title">Your Shopping Cart</h2> <!-- Kosár címe -->

        <?php if (!empty($cartItems)): ?> <!-- Ha a kosár nem üres -->
            <table class="cart-table"> <!-- Kosár elemek megjelenítése táblázatban -->
                <thead>
                <tr>
                    <th>Product</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Size</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($cartItems as $item): ?> <!-- Végig iterálunk a kosár elemein -->
                    <tr>
                        <td><img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>"></td> <!-- Termék képe -->
                        <td class="item-name"><?= htmlspecialchars($item['name']) ?></td> <!-- Termék neve -->
                        <td class="item-price"><?= number_format($item['price'], 2) ?> lei</td> <!-- Termék ára -->
                        <td class="item-size"><?= htmlspecialchars($item['size']) ?></td> <!-- Termék mérete -->

                        <td class="item-quantity">
                            <form method="post" action="update_cart.php">
                                <input
                                        type="hidden"
                                        name="cart_id"
                                        value="<?= htmlspecialchars($item['cart_id']) ?>">
                                <input
                                        type="number"
                                        name="quantity"
                                        value="<?= htmlspecialchars($item['quantity']) ?>"
                                        min="1"
                                        onchange="this.form.submit()"> <!-- Mennyiség módosítása -->
                            </form>
                        </td>
                        <td class="item-total"><?= number_format($item['price'] * $item['quantity'], 2) ?> lei</td> <!-- A termék összesített ára -->
                        <td>
                            <form method="post" action="remove_from_cart.php">
                                <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                                <button type="submit">Remove</button> <!-- Termék eltávolítása a kosárból -->
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <div class="cart-total">
                Total: <?= number_format($totalPrice, 2) ?> lei <!-- Kosár összesített ára -->
            </div>

            <div class="cart-buttons">
                <button onclick="location.href='../Order/checkout.php'">Checkout</button> <!-- Fizetés gomb -->
                <button onclick="location.href='../Pages/index.php'">Continue Shopping</button> <!-- Vásárlás folytatása -->
            </div>
        <?php else: ?>
            <p class="empty-cart">Your cart is empty!</p> <!-- Ha a kosár üres -->
        <?php endif; ?>
    </main>
</div>
</body>
</html>
