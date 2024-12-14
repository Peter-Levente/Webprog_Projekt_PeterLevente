<?php
class User
{
    private $db; // Az adatbázis kapcsolat tárolása

    public function __construct($db)
    {
        $this->db = $db;
    }

    // Regisztrációs metódus
    public function register($email, $username, $password)
    {
        // A jelszó titkosítása a biztonságos tárolás érdekében
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);

        // Adatbázis lekérdezés előkészítése az új felhasználó hozzáadásához
        $stmt = $this->db->prepare("INSERT INTO users (email, username, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $username, $password_hashed);

        // Végrehajtjuk a lekérdezést, és ellenőrizzük az eredményt
        if ($stmt->execute()) {
            return true;
        } else {
            throw new Exception("Error: " . $stmt->error);
        }
    }

    // Bejelentkezési metódus
    public function login($email, $password, $rememberMe = false)
    {
        // Felhasználó ellenőrzése az adatbázisban email alapján
        $stmt = $this->db->prepare("SELECT id, username, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        // Ha pontosan egy felhasználót találunk
        if ($stmt->num_rows === 1) {
            $id = null;
            $username = null;
            $hashed_password = null;

            // Az adatbázisból kiolvasott értékek változókhoz rendelése
            $stmt->bind_result($id, $username, $hashed_password);
            $stmt->fetch();

            // Jelszó ellenőrzése
            if (password_verify($password, $hashed_password)) {
                // Sikeres ellenőrzés után session változók beállítása
                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $username;

                // 'Remember me' opció kezelése
                if ($rememberMe) {
                    // Cookie létrehozása 14 napos érvényességgel
                    setcookie("rememberMe", $id, time() + (14 * 24 * 60 * 60), "/");
                }

                return true;
            } else {
                // Ha a jelszó helytelen
                throw new Exception("Invalid password.");
            }
        } else {
            // Ha nincs felhasználó az adott email-címmel
            throw new Exception("No user found with this email.");
        }
    }

    // Automatikus bejelentkezés kezelése
    public function autoLogin()
    {
        // Ellenőrizzük, hogy a session már létezik-e
        if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
            return true; // A felhasználó már be van jelentkezve
        }

        // Ha nincs aktív session, cookie alapján próbálkozunk
        if (isset($_COOKIE['rememberMe'])) {
            $user_id = $_COOKIE['rememberMe'];

            // Az adatbázisból lekérdezés a cookie-ban lévő user_id alapján
            $stmt = $this->db->prepare("SELECT id, username FROM users WHERE id = ?");
            if (!$stmt) {
                throw new Exception("Database error: " . $this->db->error);
            }

            $stmt->bind_param("i", $user_id);
            $stmt->execute();

            $id = null;
            $username = null;

            // Az eredmények hozzárendelése session változókhoz
            $stmt->bind_result($id, $username);
            $stmt->fetch();

            if ($id) {
                // Ha a felhasználót sikeresen megtaláltuk, állítsuk be a session-t
                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $username;
                return true; // Automatikus bejelentkezés sikeres
            }
        }

        return false; // Automatikus bejelentkezés sikertelen
    }

    // Kijelentkezési metódus
    public function logout()
    {
        // A session adatokat eltávolítjuk
        session_destroy();

        // A 'Remember me' cookie törlése
        if (isset($_COOKIE['rememberMe'])) {
            setcookie("rememberMe", "", time() - 3600, "/"); // A cookie lejárati idejének visszaállítása múltbeli időpontra
        }
    }
}
?>
