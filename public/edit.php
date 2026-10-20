<?php
require_once __DIR__ . '/../config/db.php';

// Validasi Parameter ID dari URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: index.php");
    exit;
}

// Ambil data produk berdasarkan ID menggunakan Prepared Statement
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute(['id' => $id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: index.php");
    exit;
}

$errors = [];
$name      = $product['name'];
$category  = $product['category'];
$price_raw = $product['price'];
$stock_raw = $product['stock'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name      = trim($_POST['name'] ?? '');
    $category  = trim($_POST['category'] ?? '');
    $price_raw = trim($_POST['price'] ?? '');
    $stock_raw = trim($_POST['stock'] ?? '');

    $price = filter_var($price_raw, FILTER_VALIDATE_FLOAT);
    $stock = filter_var($stock_raw, FILTER_VALIDATE_INT);

    // Validasi Server-Side
    if (mb_strlen($name) < 3) {
        $errors['name'] = "Nama seri heels minimal harus 3 karakter.";
    }

    if (empty($category)) {
        $errors['category'] = "Kategori heels wajib dipilih.";
    }

    if ($price === false || $price <= 0) {
        $errors['price'] = "Harga harus bernilai angka positif lebih dari 0.";
    }

    if ($stock === false || $stock < 0) {
        $errors['stock'] = "Stok harus berupa angka bulat dan tidak boleh negatif (>= 0).";
    }

    // Jika validasi sukses, lakukan UPDATE
    if (empty($errors)) {
        try {
            $stmt_update = $pdo->prepare(
                "UPDATE products 
                 SET name = :name, category = :category, price = :price, stock = :stock 
                 WHERE id = :id"
            );
            $stmt_update->execute([
                'name'     => $name,
                'category' => $category,
                'price'    => $price,
                'stock'    => $stock,
                'id'       => $id
            ]);

            // Pola Post-Redirect-Get (PRG)
            header("Location: index.php?status=updated");
            exit;
        } catch (PDOException $e) {
            // Tangkap bentrok nama unik (Unique Constraint)
            if ($e->getCode() == 23000) {
                $errors['name'] = "Nama seri heels sudah digunakan pada produk lain. Gunakan nama unik.";
            } else {
                $errors['general'] = "Gagal memperbarui data: " . $e->getMessage();
            }
        }
    }
}

$categories = [
    'Stiletto',
    'Classic Pumps',
    'Jeweled Pumps',
    'Strappy Heels',
    'Mules & Slingback',
    'Platform Heels',
    'Kitten Heels'
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Koleksi Heels • Maison Heels</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

    <header class="navbar">
        <div class="brand-area">
            <span class="brand-title">MAISON HEELS</span>
            <span class="brand-subtitle">Haute Horlogerie & High-End Footwear Manager</span>
        </div>
        <a href="index.php" class="btn btn-outline">← Kembali ke Etalase</a>
    </header>

    <main class="container">
        <div class="form-wrapper">
            <div class="form-header">
                <h2>Modifikasi Data Heels</h2>
                <p>Memperbarui Informasi Seri: #<?= (int)$product['id'] ?></p>
            </div>

            <?php if (!empty($errors['general'])): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($errors['general'], ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="edit.php?id=<?= (int)$id ?>" novalidate>
                <!-- Input Nama Heels -->
                <div class="form-group">
                    <label for="name">Nama Seri / Brand Heels</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        class="form-control" 
                        value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" 
                        required
                    >
                    <?php if (!empty($errors['name'])): ?>
                        <div class="field-error"><?= htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>

                <!-- Input Kategori Heels -->
                <div class="form-group">
                    <label for="category">Kategori Heels</label>
                    <select id="category" name="category" class="form-control" required>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat, ENT_QUOTES, 'UTF-8') ?>" <?= $category === $cat ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat, ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($errors['category'])): ?>
                        <div class="field-error"><?= htmlspecialchars($errors['category'], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>

                <!-- Input Harga -->
                <div class="form-group">
                    <label for="price">Harga Satuan (IDR)</label>
                    <input 
                        type="number" 
                        id="price" 
                        name="price" 
                        class="form-control" 
                        min="1" 
                        step="any"
                        value="<?= htmlspecialchars((string)$price_raw, ENT_QUOTES, 'UTF-8') ?>" 
                        required
                    >
                    <?php if (!empty($errors['price'])): ?>
                        <div class="field-error"><?= htmlspecialchars($errors['price'], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>

                <!-- Input Stok -->
                <div class="form-group">
                    <label for="stock">Jumlah Stok (Pasang)</label>
                    <input 
                        type="number" 
                        id="stock" 
                        name="stock" 
                        class="form-control" 
                        min="0" 
                        value="<?= htmlspecialchars((string)$stock_raw, ENT_QUOTES, 'UTF-8') ?>" 
                        required
                    >
                    <?php if (!empty($errors['stock'])): ?>
                        <div class="field-error"><?= htmlspecialchars($errors['stock'], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-buttons">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="index.php" class="btn btn-outline">Batal</a>
                </div>
            </form>
        </div>
    </main>

</body>
</html>
