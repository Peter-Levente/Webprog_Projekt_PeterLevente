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

try {
    // Ha a bejelentkezési űrlapot elküldték, próbáljuk meg bejelentkeztetni a felhasználót
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $rememberMe = isset($_POST['rememberMe']); // Ellenőrizzük, hogy be van-e pipálva a "Remember Me" opció

        // Próbáljuk meg a felhasználót bejelentkeztetni a User osztály login metódusával
        if ($userHandler->login($email, $password, $rememberMe)) {
            header("Location: ../Pages/index.php"); // Sikeres bejelentkezés esetén átirányítjuk a főoldalra
            exit; // Megállítjuk a további futást
        }
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
?>

<head>
    <link rel="stylesheet" href="login.css">
</head>

<!-- Bejelentkezési űrlap -->
<h2>Bejelentkezés</h2>

<form method="POST" action="">
    <label>Email: <input type="email" name="email" required></label><br> <!-- Email mező -->
    <label>Jelszó: <input type="password" name="password" required></label><br> <!-- Jelszó mező -->
    <label>Emlékezz rám <input type="checkbox" name="rememberMe"></label><br> <!-- Emlékezz rám jelölőnégyzet -->
    <button type="submit">Bejelentkezés</button> <!-- Bejelentkezés gomb -->
</form>

<!-- Regisztrációs link a bejelentkezési űrlap alatt -->
<p>Nincs még fiókod? <a href="register.php">Regisztrálj itt</a></p> <!-- Regisztrációs oldal linkje -->
