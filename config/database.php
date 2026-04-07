<?php
// ==============================
// config/database.php
// ==============================

define('DB_HOST', 'localhost');
define('DB_NAME', 'db_overzicht');
define('DB_USER', 'root');       // Aanpassen naar jouw MySQL gebruiker
define('DB_PASS', '');           // Aanpassen naar jouw MySQL wachtwoord
define('DB_CHARSET', 'utf8mb4');

function getDB(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // In productie: log de fout, toon geen details
            error_log("Database verbinding mislukt: " . $e->getMessage());
            die("Database verbinding mislukt. Probeer het later opnieuw.");
        }
    }

    return $pdo;
}