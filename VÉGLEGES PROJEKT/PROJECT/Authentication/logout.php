<?php
session_start();

include "../Database/db_connection.php";
include "User.php";

// Ellenőrizzük, hogy létezik-e az adatbázis-kapcsolatot inicializáló fájl
if (!file_exists('../Database/db_connection.php')) {
    die("Hiányzik az adatbázis-kapcsolat inicializáló fájl!"); // Ha hiányzik a fájl, álljon le a futás
}

// Ellenőrizzük, hogy van-e adatbázis-kapcsolati objektum
if (!isset($conn)) {
    die("Hiba: Nincs adatbázis-kapcsolat!"); // Ha nincs kapcsolat, álljon le a futás
}

// Ellenőrizzük, hogy az adatbázis-kapcsolat létrejött-e
if ($conn->connect_error) {
    die("Kapcsolódási hiba: " . $conn->connect_error); // Ha a kapcsolat hibás, jelezzük
}

$userHandler = new User($conn);
$userHandler->logout();

header("Location: ../Pages/index.php");
exit;
?>
