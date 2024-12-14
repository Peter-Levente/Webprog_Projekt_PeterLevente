<?php
session_start();

include "../Database/db_connection.php";
include "User.php";

// Ellenőrizzük, hogy létezik-e az adatbázis-kapcsolatot inicializáló fájl
if (!file_exists('../Database/db_connection.php')) {
    die("Hiányzik az adatbázis-kapcsolat inicializáló fájl!"); // Hibaüzenet, ha hiányzik
}

// Ellenőrizzük, hogy van-e adatbázis-kapcsolati objektum
if (!isset($conn)) {
    die("Hiba: Nincs adatbázis-kapcsolat!"); // Hibaüzenet, ha nincs kapcsolat
}

// Ellenőrizzük az adatbázis-kapcsolat státuszát
if ($conn->connect_error) {
    die("Kapcsolódási hiba: " . $conn->connect_error); // Hibaüzenet, ha a kapcsolat hibás
}

$userHandler = new User($conn);

try {
    // Ellenőrizzük, hogy a kérés POST típusú-e (azaz küldtek-e adatot regisztrációhoz)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'];
        $username = $_POST['username'];
        $password = $_POST['password'];

        if ($userHandler->register($email, $username, $password)) {
            echo "Sikeres regisztráció. <a href='login.php'>Jelentkezz be itt</a>";
        }
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <link rel="stylesheet" href="register.css"> <!-- Regisztrációs oldal CSS stíluslapja -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regisztráció</title> <!-- Az oldal címe -->
</head>
<body>
<h1>Regisztráció</h1> <!-- Oldal címsora -->

<!-- Regisztrációs űrlap -->
<form method="POST" action="">
    <label>Email: <input type="email" name="email" required></label><br> <!-- Email mező -->
    <label>Felhasználónév: <input type="text" name="username" required></label><br> <!-- Felhasználónév mező -->
    <label>Jelszó: <input type="password" name="password" required></label><br> <!-- Jelszó mező -->
    <button type="submit">Regisztráció</button> <!-- Küldés gomb -->
</form>
</body>
</html>
