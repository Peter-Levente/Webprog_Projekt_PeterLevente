<?php
$servername = "localhost";  // Az adatbázis szerver neve (helyben futtatva: localhost)
$username = "root";
$password = "";
$dbname = "webshop";  // Az adatbázis neve, amivel kapcsolatot szeretnénk létesíteni

// Létrehozza az adatbázis kapcsolatot
$conn = new mysqli($servername, $username, $password, $dbname);

// Ha hiba történik a kapcsolat létrehozásakor, akkor megjelenítjük a hibaüzenetet és leállítjuk a szkriptet
if ($conn->connect_error) {
    die("Connection error: " . $conn->connect_error);
}
?>
