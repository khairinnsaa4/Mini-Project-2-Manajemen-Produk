<?php
session_start();
require_once '../config/db.php';

$_SESSION['csrf'] ??= bin2hex(random_bytes(32));

$success_message = '';
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'created') $success_message = "Produk berhasil ditambahkan!";
    if ($_GET['status'] === 'updated') $success_message = "Produk berhasil diperbarui!";
    if ($_GET['status'] === 'deleted') $success_message = "Produk berhasil dihapus!";
}

$q = trim($_GET['q'] ?? '');
if ($q) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE :q OR category LIKE :q ORDER BY id DESC");
    $stmt->execute(['q' => "%$q%"]);
    $products = $stmt->fetchAll();
} else {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
    $products = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Product Manager</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container">
    <header>
        <h1>📦 Product Manager</h1>
        <a href="create.php" class="btn btn-primary">+ Tambah Produk</a>
    </header>

    <?php if ($success_message): ?>
        <div class="alert"><?= htmlspecialchars($success_message, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <form method="GET" action="index.php" class="search-form">
        <input type="text" name="q" placeholder="Cari produk..." value="<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?>">
        <button type="submit" class="btn btn-primary">Cari</button>
        <?php if ($q): ?><a href="index.php" class="btn btn-secondary">Reset</a><?php endif; ?>
    </form>

    <div class="products-grid">
        <?php foreach ($products as $p): ?>
            <div class="card">
                <h3><?= htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                <div class="price">Rp <?= number_format($p['price'], 0, ',', '.') ?></div>
                <div class="meta">
                    <span>Kategori: <?= htmlspecialchars($p['category'], ENT_QUOTES, 'UTF-8') ?></span>
                    <span>Stok: <?= (int)$p['stock'] ?></span>
                </div>
                <div class="actions">
                    <a href="edit.php?id=<?= $p['id'] ?>" class="btn btn-secondary" style="flex:1;">Edit</a>
                    <form method="POST" action="delete.php" style="flex:1; display:flex;" onsubmit="return confirm('Hapus produk ini?');">
                        <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                        <button type="submit" class="btn btn-danger" style="width:100%;">Hapus</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>