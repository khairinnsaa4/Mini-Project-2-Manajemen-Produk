<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// Pastikan request menggunakan metode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit("Metode Request Tidak Diizinkan. Operasi hapus harus melalui form POST.");
}

// Validasi Token CSRF menggunakan hash_equals
$session_csrf = $_SESSION['csrf'] ?? '';
$post_csrf    = $_POST['csrf'] ?? '';

if (!hash_equals($session_csrf, $post_csrf)) {
    http_response_code(403);
    exit("Akses Ditolak: Token CSRF tidak valid atau sesi telah kadaluarsa.");
}

// Ambil dan validasi ID produk yang akan dihapus
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if ($id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
        $stmt->execute(['id' => $id]);
    } catch (PDOException $e) {
        die("Gagal menghapus produk: " . $e->getMessage());
    }
}

// Pola Post-Redirect-Get (PRG)
header("Location: index.php?status=deleted");
exit;
