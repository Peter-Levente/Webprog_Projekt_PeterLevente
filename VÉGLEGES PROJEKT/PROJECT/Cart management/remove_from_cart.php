<?php
session_start();
include "../Database/db_connection.php";
include "Cart.php";

// Ellenőrzi, hogy létezik-e az adatbázis kapcsolat fájl
if (!file_exists('../Database/db_connection.php')) {
    die("Connection initiation file not found!");  // Ha nem találja, hibát dob
}

// Ellenőrzi, hogy az adatbázis kapcsolat létrejött-e
if (!isset($conn)) {
    die("Error: No database connection!");  // Ha nincs kapcsolat, hibát dob
}

// Ha van kapcsolat, de hiba történt, akkor azt is jelezzük
if ($conn->connect_error) {
    die("Connection error: " . $conn->connect_error);  // Hibát dob a kapcsolódáskor fellépő hibák esetén
}

// Ellenőrzi, hogy a felhasználó be van-e jelentkezve (azaz van-e user_id a session-ben)
$userId = $_SESSION['user_id'] ?? null;  // Lekérdezzük a bejelentkezett felhasználó azonosítóját
if (!$userId) {
    header("Location: ../Authentication/login.php");  // Ha nincs bejelentkezve, átirányítja a login oldalra
    exit;
}

// Csak akkor fut le a következő kód, ha a kérés POST metódusú
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cartId = $_POST['cart_id'];  // Megkapjuk a kosárban lévő termék azonosítóját, amit eltávolítunk

    $cart = new Cart($conn, $userId);
    $cart->removeFromCart($cartId);

    // Az eltávolítás után átirányítjuk a felhasználót a my_cart.php oldalra, hogy frissítse a kosarát
    header("Location: my_cart.php");
    exit;
}
?>
