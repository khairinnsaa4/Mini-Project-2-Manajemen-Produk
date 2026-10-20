<?php
/**
 * Konfigurasi Koneksi Database PDO
 * Database: store_db
 * Keamanan: Prepared Statements aktif & Mode Exception
 */

$host    = "localhost";
$dbname  = "store_db";
$user    = "root";
$pass    = "";
$charset = "utf8mb4";

$dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Koneksi ke database gagal: " . $e->getMessage());
}
