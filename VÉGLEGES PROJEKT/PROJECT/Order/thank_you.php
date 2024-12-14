<?php
session_start();

include "../Database/db_connection.php";

// Ellenőrizzük, hogy az 'order_id' paraméter át lett-e adva az URL-ben
if (!isset($_GET['order_id'])) {
    // Ha nincs, átirányítjuk a felhasználót a főoldalra
    header("Location: ../Pages/index.php");
    exit;
}

// Az 'order_id' paraméter értékét egész számra konvertáljuk
$orderId = intval($_GET['order_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Thank You</title>
    <!-- Külső CSS fájl betöltése -->
    <link rel="stylesheet" href="thank_you.css">
</head>
<body>
<div class="container">
    <h1>Thank You for Your Order!</h1>
    <!-- Az order_id megjelenítése, hogy a felhasználó lássa azonosítóját -->
    <p>Your order ID is: <?= htmlspecialchars($orderId) ?></p>
    <!-- Visszairányítunk a főoldalra egy hivatkozással -->
    <a href="../Pages/index.php">Go back to homepage</a>
</div>
</body>
</html>
