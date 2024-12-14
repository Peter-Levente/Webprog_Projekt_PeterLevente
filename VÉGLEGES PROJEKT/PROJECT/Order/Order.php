<?php
class Order {
    private $conn;  // Az adatbázis kapcsolat
    private $userId;  // A felhasználó azonosítója

    public function __construct($conn, $userId) {
        $this->conn = $conn;  // Az adatbázis kapcsolat mentése
        $this->userId = $userId;  // A felhasználó azonosítója
    }

    // Rendelés létrehozása
    public function createOrder($name, $email, $address, $phone, $cartItems, $paymentMethod) {
        // Ha a kosár üres, dobunk egy hibát
        if (empty($cartItems)) {
            throw new Exception("Your cart is empty!");
        }

        try {
            // Rendelés létrehozása az orders táblában
            $query = "INSERT INTO orders (customer_name, customer_email, shipping_address, customer_phone, order_date, payment_method, total_amount) 
                      VALUES (?, ?, ?, ?, NOW(), ?, ?)";
            $stmt = $this->conn->prepare($query);
            // Az összes kosár tétel összegzésével számítjuk ki az összesített árat
            $totalAmount = array_sum(array_map(function ($item) {
                return $item['price'] * $item['quantity'];
            }, $cartItems));
            $stmt->bind_param("sssssd", $name, $email, $address, $phone, $paymentMethod, $totalAmount);
            $stmt->execute();

            // A rendelés azonosítója, amit a későbbi rendelési tételekhez fogunk használni
            $orderId = $stmt->insert_id;

            // Rendelési tételek hozzáadása
            foreach ($cartItems as $item) {
                $this->addOrderItem($orderId, $item);
            }

            // Kosár kiürítése miután a rendelés létrejött
            $this->clearCart();

            return $orderId;  // Visszaadjuk az új rendelés azonosítóját
        } catch (Exception $e) {
            throw $e;
        }
    }

    // Rendelési tétel hozzáadása a rendeléshez
    private function addOrderItem($orderId, $item) {
        $query = "INSERT INTO order_items (order_id, product_name, product_image, product_size, product_price, quantity) 
              VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param(
            'isssdi',
            $orderId,
            $item['name'],
            $item['image'],
            $item['size'],
            $item['price'],
            $item['quantity']
        );
        $stmt->execute();
    }

    // Kosár kiürítése
    private function clearCart() {
        // Kosár kiürítése a cart táblában
        $query = "DELETE FROM cart WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $this->userId);  // Paraméter: a felhasználó ID-ja
        $stmt->execute();
    }

    // Felhasználó rendeléseinek lekérése
    public function getUserOrders() {
        // Rendelések lekérdezése a felhasználó email címe alapján
        $query = "SELECT * FROM orders WHERE customer_email = (
                SELECT email FROM users WHERE id = ?
              )";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $this->userId);  // Paraméter: a felhasználó ID-ja
        $stmt->execute();
        $ordersResult = $stmt->get_result();

        $orders = [];  // A rendeléseket tároló tömb inicializálása
        while ($order = $ordersResult->fetch_assoc()) {
            // A fetch_assoc() minden egyes alkalommal egy új rendelést ad vissza,
            // amely asszociatív tömb formájában tartalmazza a rendelés adatait.
            // A ciklus addig fut, amíg van még rendelés a lekérdezés eredményében.

            // Minden rendeléshez lekérdezzük a rendelési tételeket
            $itemsQuery = "SELECT product_name, product_image, product_size, product_price, quantity 
                       FROM order_items WHERE order_id = ?";
            $itemsStmt = $this->conn->prepare($itemsQuery);
            $itemsStmt->bind_param('i', $order['id']);  // Paraméter: a rendelés ID-ja
            $itemsStmt->execute();
            $itemsResult = $itemsStmt->get_result();

            // A rendelési tételek lekérdezésének eredményét (több tétel esetén több sor) asszociatív tömbbe mentjük
            // A fetch_all() függvény az összes eredményt lekéri, és egy tömböt ad vissza, ahol minden tétel egy asszociatív tömb
            // A MYSQLI_ASSOC biztosítja, hogy a lekért adatokat asszociatív formában kapjuk meg (mezőnevekkel hivatkozva).

            // Rendelési tételek hozzáadása a rendeléshez
            $order['items'] = $itemsResult->fetch_all(MYSQLI_ASSOC);
            $orders[] = $order;  // A rendelést hozzáadjuk a rendelési listához
        }

        return $orders;  // A rendeléseket visszaadjuk
    }

    // Rendelés törlése
    public function cancelOrder($orderId) {
        // Először töröljük a rendelési tételeket
        $deleteItemsQuery = "DELETE FROM order_items WHERE order_id = ?";
        $stmt = $this->conn->prepare($deleteItemsQuery);
        $stmt->bind_param('i', $orderId);  // Paraméter: a rendelés ID-ja
        $stmt->execute();

        // Majd töröljük a rendelést magát az orders táblából
        $deleteOrderQuery = "DELETE FROM orders WHERE id = ?";
        $stmt = $this->conn->prepare($deleteOrderQuery);
        $stmt->bind_param('i', $orderId);  // Paraméter: a rendelés ID-ja
        $stmt->execute();
    }
}

?>
