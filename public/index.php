<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// Inisialisasi CSRF Token untuk operasi DELETE
$_SESSION['csrf'] ??= bin2hex(random_bytes(32));

// Penanganan Fitur Search via GET (Prepared Statement)
$q = trim($_GET['q'] ?? '');

if ($q !== '') {
    $stmt = $pdo->prepare(
        "SELECT * FROM products 
         WHERE name LIKE :q OR category LIKE :q 
         ORDER BY id DESC"
    );
    $stmt->execute(['q' => "%{$q}%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
}

$products = $stmt->fetchAll();

// Status notifikasi dari aksi PRG
$status = $_GET['status'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maison Heels • Luxury Footwear Collection</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

    <!-- Header & Brand Navigation -->
    <header class="navbar">
        <div class="brand-area">
            <span class="brand-title">MAISON HEELS</span>
            <span class="brand-subtitle">Haute Horlogerie & High-End Footwear Manager</span>
        </div>
        <a href="create.php" class="btn btn-primary">+ Tambah Koleksi Heels</a>
    </header>

    <main class="container">

        <!-- Notifikasi Status Operasi (Post-Redirect-Get) -->
        <?php if ($status === 'created'): ?>
            <div class="alert alert-success">
                ✨ <strong>Sukses:</strong> Koleksi sepatu heels baru berhasil ditambahkan ke etalase.
            </div>
        <?php elseif ($status === 'updated'): ?>
            <div class="alert alert-success">
                ✨ <strong>Sukses:</strong> Informasi produk heels berhasil diperbarui.
            </div>
        <?php elseif ($status === 'deleted'): ?>
            <div class="alert alert-danger">
                🗑️ <strong>Dihapus:</strong> Produk heels telah berhasil dihapus dari sistem.
            </div>
        <?php endif; ?>

        <!-- Toolbar: Form Pencarian GET & Aksi Cepat -->
        <div class="toolbar">
            <form method="GET" action="index.php" class="search-form">
                <input 
                    type="text" 
                    name="q" 
                    class="search-input" 
                    placeholder="Cari brand heels, model, atau kategori..." 
                    value="<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?>"
                >
                <button type="submit" class="btn btn-gold">Cari</button>
                <?php if ($q !== ''): ?>
                    <a href="index.php" class="btn btn-outline">Reset</a>
                <?php endif; ?>
            </form>
            <div>
                <span style="font-size:0.85rem; color: var(--text-secondary);">
                    Total: <strong><?= count($products) ?></strong> model heels terdaftar
                </span>
            </div>
        </div>

        <!-- Flexbox Grid Card Produk Heels -->
        <div class="product-grid">
            <?php if (empty($products)): ?>
                <div class="empty-state">
                    <h3>Koleksi Tidak Ditemukan</h3>
                    <p><?= $q !== '' ? 'Tidak ada produk heels yang cocok dengan kata kunci "' . htmlspecialchars($q, ENT_QUOTES, 'UTF-8') . '".' : 'Belum ada koleksi heels di etalase.' ?></p>
                    <a href="create.php" class="btn btn-primary">+ Daftarkan Sepatu Baru</a>
                </div>
            <?php else: ?>
                <?php foreach ($products as $p): ?>
                    <?php 
                        $stock = (int)$p['stock'];
                        $stockClass = $stock > 5 ? 'in-stock' : ($stock > 0 ? 'low-stock' : 'out-stock');
                        $stockText  = $stock > 0 ? "Stok: {$stock} Pasang" : "Habis Terjual";
                    ?>
                    <div class="product-card">
                        <div class="card-top">
                            <span class="category-badge"><?= htmlspecialchars($p['category'], ENT_QUOTES, 'UTF-8') ?></span>
                            <span class="stock-tag <?= $stockClass ?>"><?= $stockText ?></span>
                        </div>

                        <h2 class="product-name"><?= htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8') ?></h2>
                        <div class="product-price">Rp <?= number_format($p['price'], 0, ',', '.') ?></div>

                        <div class="card-actions">
                            <a href="edit.php?id=<?= (int)$p['id'] ?>" class="btn btn-edit">Edit Data</a>

                            <!-- Form Delete Khusus POST + Proteksi CSRF -->
                            <form method="POST" action="delete.php" onsubmit="return confirm('Apakah Anda yakin ingin menghapus seri heels <?= htmlspecialchars(addslashes($p['name']), ENT_QUOTES, 'UTF-8') ?> ini?');">
                                <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                                <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES, 'UTF-8') ?>">
                                <button type="submit" class="btn btn-delete">Hapus</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </main>

</body>
</html>
