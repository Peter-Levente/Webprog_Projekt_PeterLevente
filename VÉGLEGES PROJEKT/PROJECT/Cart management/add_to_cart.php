<?php
session_start();
include "../Database/db_connection.php";
include "Cart.php";

// Ellenőrizzük, hogy az adatbázis-kapcsolati fájl létezik-e
if (!file_exists('../Database/db_connection.php')) {
    die("Connection initiation file not found!"); // Hibával leáll, ha hiányzik a fájl
}

// Ellenőrizzük, hogy az adatbázis-kapcsolat inicializálva van-e
if (!isset($conn)) {
    die("Error: No database connection!"); // Hibával leáll, ha nincs adatbázis kapcsolat
}

// Ellenőrizzük az adatbázis-kapcsolat hibáit
if ($conn->connect_error) {
    die("Connection error: " . $conn->connect_error); // Hibával leáll, ha hiba történt a kapcsolódás során
}

// Ellenőrizzük, hogy a felhasználó be van-e jelentkezve
$userId = $_SESSION['user_id'] ?? null; // Lekérdezzük a bejelentkezett felhasználó azonosítóját
if (!$userId) {
    header("Location: ../Authentication/login.php"); // Ha nincs bejelentkezve, átirányítjuk a bejelentkezési oldalra
    exit;
}

// Ellenőrizzük, hogy POST kérés érkezett-e (form beküldése)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Formból érkező adatok lekérése
    $productId = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $size = $_POST['size'];


    $cart = new Cart($conn, $_SESSION['user_id']);
    $cart->addToCart($productId, $quantity, $size); // A termék hozzáadása a kosárhoz

    // Átirányítás a kosár oldalra a művelet után
    header("Location: my_cart.php");
    exit;
}
?>
