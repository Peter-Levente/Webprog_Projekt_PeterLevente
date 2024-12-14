<?php
session_start();
include "../Database/db_connection.php";
include "../Cart management/Cart.php";
include "Order.php";

// Ellenőrizze, hogy létezik-e az adatbázis-kapcsolat fájl
if (!file_exists('../Database/db_connection.php')) {
    die("Connection initiation file not found!");  // Ha nem található, hibaüzenet
}

// Ellenőrizze, hogy van-e adatbázis kapcsolat
if (!isset($conn)) {
    die("Error: No database connection!");  // Ha nincs kapcsolat, hibaüzenet
}

// Ha hiba történt a kapcsolat során, jelenjen meg hibaüzenet
if ($conn->connect_error) {
    die("Connection error: " . $conn->connect_error);  // Kapcsolódási hiba üzenet
}

// Ellenőrizzük, hogy a felhasználó be van-e jelentkezve
$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    header("Location: ../Authentication/login.php");  // Ha nincs bejelentkezve, irányítjuk a login oldalra
    exit;
}

$cart = new Cart($conn, $userId);
$order = new Order($conn, $userId);

// Kosár tartalmának lekérése
$cartItems = $cart->getCartItems();

// Hibakezelés, ha a POST kérés érkezik
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // A rendeléshez szükséges adatok lekérése a formból
    $name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $paymentMethod = $_POST['payment_method'];

    try {
        // Új rendelés létrehozása
        $orderId = $order->createOrder($name, $email, $address, $phone, $cartItems, $paymentMethod);

        // Sikeres rendelés után átirányítás a köszönő oldalra
        header("Location: thank_you.php?order_id=" . $orderId);
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();  // Ha hiba történt, elmentjük a hibaüzenetet
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <link rel="stylesheet" href="checkout_style.css">  <!-- Stíluslap betöltése -->
</head>
<body>
<div class="container">
    <h1>Checkout</h1>

    <!-- Ha hibaüzenet van, azt itt jelenítjük meg -->
    <?php if ($error): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <!-- A rendelés feladására szolgáló form -->
    <form method="POST" action="">
        <label for="name">Full Name:</label>
        <input type="text" name="name" id="name" required>

        <label for="email">Email:</label>
        <input type="text" name="email" id="email" required>

        <label for="address">Address:</label>
        <textarea name="address" id="address" rows="4" required></textarea>

        <label for="phone">Phone Number:</label>
        <input type="tel" name="phone" id="phone" required>

        <label for="payment_method">Payment Method:</label>
        <select name="payment_method" id="payment_method" required>
            <option value="cash">Cash on Delivery</option>
            <option value="card">Credit/Debit Card</option>
        </select>

        <!-- Kosár összesített ára -->
        <p>Total Price: <?= number_format(array_sum(array_map(function ($item) {
                return $item['price'] * $item['quantity'];  // Az árakat és mennyiségeket összeadja
            }, $cartItems)), 2) ?> lei</p>

        <!-- Rendelés feladása -->
        <button type="submit">Place Order</button>
    </form>
</div>
</body>
</html>
