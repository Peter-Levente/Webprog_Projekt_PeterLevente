<?php
session_start();
include "../Database/db_connection.php";
include "Order.php";

// Ellenőrizze, hogy létezik-e az adatbázis-kapcsolat fájl
if (!file_exists('../Database/db_connection.php')) {
    die("Connection initiation file not found!");  // Ha nem található, hibaüzenet
}

// Ellenőrizze, hogy az adatbázis-kapcsolat sikerült-e
if (!isset($conn)) {
    die("Error: No database connection!");  // Ha nincs kapcsolat, hibaüzenet
}

if ($conn->connect_error) {
    die("Connection error: " . $conn->connect_error);  // Ha hiba történik a kapcsolat során, hibaüzenet
}

// A felhasználó azonosítójának lekérése a munkamenetből
$userId = $_SESSION['user_id'];

$order = new Order($conn, $userId);

// Ha a kérelem POST metódussal érkezik
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // A rendelés ID-ja, amit törölni szeretnénk
    $orderId = $_POST['order_id'];

    try {
        // A rendelés törlésének megkísérlése
        $order->cancelOrder($orderId);
        // Ha sikeres, átirányítjuk a felhasználót a rendelései oldalra
        header("Location: my_orders.php");
    } catch (Exception $e) {
        die("Error canceling order: " . $e->getMessage());
    }
}
?>
