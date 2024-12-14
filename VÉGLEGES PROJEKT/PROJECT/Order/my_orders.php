<?php
session_start();
include "../Database/db_connection.php";
include "Order.php";

// Ellenőrzi, hogy létezik-e az adatbázis-kapcsolat fájl
if (!file_exists('../Database/db_connection.php')) {
    die("Connection initiation file not found!");  // Ha nem található, hibaüzenet
}

// Ellenőrzi, hogy van-e adatbázis kapcsolat
if (!isset($conn)) {
    die("Error: No database connection!");  // Ha nincs kapcsolat, hibaüzenet
}

// Ha hiba történt a kapcsolat során, akkor azt jelezzük
if ($conn->connect_error) {
    die("Connection error: " . $conn->connect_error);  // Kapcsolódási hiba üzenet
}

// A bejelentkezett felhasználó ID-jának lekérése a munkamenetből
$userId = $_SESSION['user_id'];
$order = new Order($conn, $userId);

try {
    // Lekérdezzük a felhasználó rendeléseit
    $orders = $order->getUserOrders();
} catch (Exception $e) {
    die("Error fetching orders: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <link rel="stylesheet" href="my_orders.css">  <!-- A rendelési oldal stíluslapja -->
</head>
<body>
<div class="container">
    <header>
        <h1>My Orders</h1>  <!-- Rendelések oldal cím -->
        <a href="../Pages/index.php">Home</a>  <!-- Link a főoldalra -->
    </header>
    <main>
        <?php if (!empty($orders)): ?>  <!-- Ha vannak rendelései -->
            <?php foreach ($orders as $order): ?>  <!-- Minden rendelést végigiterálunk -->
                <div class="order">  <!-- Rendelés adatainak megjelenítése -->
                    <h2>Order #<?= htmlspecialchars($order['id']) ?> (<?= htmlspecialchars($order['order_date']) ?>)</h2>
                    <p><strong>Name:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($order['customer_email']) ?></p>
                    <p><strong>Address:</strong> <?= htmlspecialchars($order['shipping_address']) ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($order['customer_phone']) ?></p>
                    <p><strong>Payment Method:</strong> <?= htmlspecialchars($order['payment_method']) ?></p>
                    <p><strong>Total Amount:</strong> <?= number_format($order['total_amount'], 2) ?> lei</p>  <!-- Rendelés összesített ára -->

                    <h3>Items:</h3>
                    <ul>
                        <?php foreach ($order['items'] as $item): ?>  <!-- A rendelés tételeit végigiteráljuk -->
                            <li>
                                <img src="<?= htmlspecialchars($item['product_image']) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" style="width: 50px; height: 50px;">
                                <?= htmlspecialchars($item['product_name']) ?>
                                (Size: <?= htmlspecialchars($item['product_size']) ?>)
                                - <?= htmlspecialchars($item['quantity']) ?> pcs @ <?= number_format($item['product_price'], 2) ?> lei each
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <!-- Rendelés törlésére szolgáló űrlap -->
                    <form method="post" action="cancel_order.php">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">  <!-- Az order_id átadása a törléshez -->
                        <button type="submit">Cancel Order</button>  <!-- Törlés gomb -->
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>  <!-- Ha nincsenek rendelései a felhasználónak -->
            <p>You have no orders yet.</p>  <!-- Üzenet, ha nincs rendelés -->
        <?php endif; ?>

    </main>
</div>
</body>
</html>
