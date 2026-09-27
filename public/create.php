<?php
session_start();
require_once '../config/db.php';

$_SESSION['csrf'] ??= bin2hex(random_bytes(32));
$errors = [];
$val_name = ''; $val_cat = ''; $val_price = ''; $val_stock = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(403);
        exit("Token tidak valid");
    }

    $val_name = trim($_POST['name'] ?? '');
    $val_cat = trim($_POST['category'] ?? '');
    $val_price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
    $val_stock = filter_input(INPUT_POST, 'stock', FILTER_VALIDATE_INT);

    if (mb_strlen($val_name) < 3) $errors['name'] = "Nama minimal 3 karakter.";
    if (empty($val_cat)) $errors['category'] = "Kategori wajib diisi.";
    if ($val_price === false || $val_price <= 0) $errors['price'] = "Harga harus > 0.";
    if ($val_stock === false || $val_stock < 0) $errors['stock'] = "Stok tidak boleh negatif.";

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM products WHERE name = :name");
        $stmt->execute(['name' => $val_name]);
        if ($stmt->fetch()) $errors['name'] = "Nama produk sudah digunakan.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO products (name, category, price, stock) VALUES (:name, :category, :price, :stock)");
        $stmt->execute(['name' => $val_name, 'category' => $val_cat, 'price' => $val_price, 'stock' => $val_stock]);
        header("Location: index.php?status=created");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Produk</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container">
    <header>
        <h1>Tambah Produk</h1>
        <a href="index.php" class="btn btn-secondary">Kembali</a>
    </header>
    <div class="form-card">
        <form method="POST">
            <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
            
            <div class="form-group">
                <label>Nama Produk</label>
                <input type="text" name="name" value="<?= htmlspecialchars($val_name, ENT_QUOTES, 'UTF-8') ?>" required minlength="3">
                <?php if (isset($errors['name'])): ?><div class="error"><?= $errors['name'] ?></div><?php endif; ?>
            </div>
            <div class="form-group">
                <label>Kategori</label>
                <input type="text" name="category" value="<?= htmlspecialchars($val_cat, ENT_QUOTES, 'UTF-8') ?>" required>
                <?php if (isset($errors['category'])): ?><div class="error"><?= $errors['category'] ?></div><?php endif; ?>
            </div>
            <div class="form-group">
                <label>Harga</label>
                <input type="number" name="price" value="<?= htmlspecialchars($val_price, ENT_QUOTES, 'UTF-8') ?>" required min="1">
                <?php if (isset($errors['price'])): ?><div class="error"><?= $errors['price'] ?></div><?php endif; ?>
            </div>
            <div class="form-group">
                <label>Stok</label>
                <input type="number" name="stock" value="<?= htmlspecialchars($val_stock, ENT_QUOTES, 'UTF-8') ?>" required min="0">
                <?php if (isset($errors['stock'])): ?><div class="error"><?= $errors['stock'] ?></div><?php endif; ?>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%;">Simpan Produk</button>
        </form>
    </div>
</div>
</body>
</html>