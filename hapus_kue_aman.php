<?php session_start();
require 'koneksi_db_reesha.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!hash_equals($_SESSION["csrf"] ?? "", $_POST["csrf"] ?? "")) {
        http_response_code(403);
        exit("Akses ditolak: Token tidak valid");
    }

    $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
    
    if ($id) {
        $stmt_img = $pdo->prepare("SELECT gambar_kue FROM produk_kue WHERE id = :id");
        $stmt_img->execute(['id' => $id]);
        $foto = $stmt_img->fetchColumn();
        
        if ($foto && file_exists("unggahan_gambar/" . $foto)) {
            unlink("unggahan_gambar/" . $foto);
        }

        $stmt = $pdo->prepare("DELETE FROM produk_kue WHERE id = :id");
        $stmt->execute(["id" => $id]);
    }
    
    header("Location: index.php?status=sukses_hapus");
    exit;
}
?>