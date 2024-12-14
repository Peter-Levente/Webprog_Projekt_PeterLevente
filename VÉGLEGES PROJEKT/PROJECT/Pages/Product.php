<?php
class Product
{
    public $id;
    public $name;
    public $price;
    public $image;
    public $category;
    public $size;

    public function __construct($id, $name, $price, $image, $category, $size = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->image = $image;
        $this->category = $category;
        $this->size = $size; // Ha nincs méret, akkor null lesz az alapértelmezett érték, később állítódik be
    }

    public function getSize()
    {
        return $this->size;
    }

    public function setSize($size)
    {
        $this->size = $size;
    }

    // Statikus metódus az összes termék lekérdezéséhez az adatbázisból
    public static function getAllProducts($conn)
    {
        $products = []; // Üres tömb a termékek tárolására
        $query = "SELECT * FROM products"; // SQL lekérdezés, amely az összes terméket lekéri
        $result = $conn->query($query);

        // Minden termék adatainak feldolgozása
        while ($row = $result->fetch_assoc()) {
            $product = new Product(
                $row['id'],
                $row['name'],
                $row['price'],
                $row['image'],
                $row['category'],
                $row['size'] // Ha van méret, az is beállításra kerül
            );
            $products[] = $product; // A terméket hozzáadjuk a tömbhöz
        }

        return $products;
    }

    // Statikus metódus egyetlen termék lekérdezésére az ID alapján
    public static function getProductById($conn, $id)
    {
        $query = "SELECT * FROM products WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id); // A paraméter (termék ID) kötése
        $stmt->execute();
        $result = $stmt->get_result();

        // Ha van találat, visszaadjuk a terméket, különben null értéket adunk vissza
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc(); // Az első sor adatainak lekérése
            return new Product(
                $row['id'],
                $row['name'],
                $row['price'],
                $row['image'],
                $row['category'],
                $row['size'] // A méret is beállításra kerül
            );
        } else {
            return null; // Ha nincs találat, null értéket adunk vissza
        }
    }

    // Statikus metódus termékek lekérdezésére kategória alapján
    public static function getProductsByCategory($conn, $category)
    {
        $products = []; // Üres tömb a termékek tárolására
        $query = "SELECT * FROM products WHERE category = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $category); // A paraméter (kategória) kötése
        $stmt->execute();
        $result = $stmt->get_result();

        // Minden kategóriába tartozó termék adatainak feldolgozása
        while ($row = $result->fetch_assoc()) {
            $product = new Product(
                $row['id'],
                $row['name'],
                $row['price'],
                $row['image'],
                $row['category'],
                $row['size'] // A méret beállítása
            );
            $products[] = $product; // A terméket hozzáadjuk a tömbhöz
        }

        return $products;
    }
}
?>
