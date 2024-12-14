<?php
class Cart {
    private $conn; // Az adatbázis-kapcsolatot tárolja
    private $userId; // Az aktuális felhasználó azonosítója

    public function __construct($conn, $userId) {
        $this->conn = $conn; // Inicializáljuk az adatbázis-kapcsolatot
        $this->userId = $userId; // Beállítjuk a felhasználói azonosítót
    }

    // Új termék hozzáadása a kosárhoz vagy a meglévő mennyiségének frissítése
    public function addToCart($productId, $quantity, $size) {
        $quantity = max(1, $quantity); // A mennyiség nem lehet kevesebb, mint 1

        // Ellenőrizd, van-e már ugyanilyen termék a kosárban a megadott mérettel
        $query = "SELECT * FROM cart WHERE user_id = ? AND product_id = ? AND size = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('iis', $this->userId, $productId, $size);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Ha már létezik ilyen termék, növeljük a mennyiséget
            $updateQuery = "UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ? AND size = ?";
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->bind_param('iiis', $quantity, $this->userId, $productId, $size);
            $updateStmt->execute();
        } else {
            // Ha még nincs ilyen termék a kosárban, új rekordot hozunk létre
            $insertQuery = "INSERT INTO cart (user_id, product_id, quantity, size) VALUES (?, ?, ?, ?)";
            $insertStmt = $this->conn->prepare($insertQuery);
            $insertStmt->bind_param('iiis', $this->userId, $productId, $quantity, $size); // Új elem hozzáadása
            $insertStmt->execute();
        }
    }

    // Egy termék eltávolítása a kosárból
    public function removeFromCart($cartId) {
        $query = "DELETE FROM cart WHERE id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ii', $cartId, $this->userId); // Az aktuális felhasználóhoz tartozó kosár elem törlése
        $stmt->execute();
    }

    // Egy termék mennyiségének frissítése a kosárban
    public function updateQuantity($cartId, $quantity) {
        $quantity = max(1, $quantity); // A mennyiség nem lehet kisebb, mint 1
        $query = "UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('iii', $quantity, $cartId, $this->userId); // Frissítjük a megadott kosár elem mennyiségét
        $stmt->execute();
    }

    // Az összes kosárban lévő elem lekérdezése
    public function getCartItems() {
        $query = "SELECT c.id as cart_id, p.name, p.price, p.image, c.quantity, c.size 
                  FROM cart c 
                  JOIN products p ON c.product_id = p.id 
                  WHERE c.user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $this->userId); // Csak az aktuális felhasználó kosár elemeit kérdezzük le
        $stmt->execute();
        $result = $stmt->get_result();

        $items = []; // A lekérdezett elemek tárolása egy tömbben
        while ($row = $result->fetch_assoc()) {
            $items[] = $row; // Minden elem hozzáadása a tömbhöz
        }
        return $items; // Az összes kosár elem visszaadása
    }
}
?>
