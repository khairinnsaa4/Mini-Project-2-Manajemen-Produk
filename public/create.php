<?php
require_once __DIR__ . '/../config/db.php';

$errors = [];
$name = '';
$category = 'Stiletto';
$price_raw = '';
$stock_raw = '';

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

    // Jika validasi lolos, simpan ke database menggunakan Prepared Statement
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO products (name, category, price, stock) 
                 VALUES (:name, :category, :price, :stock)"
            );
            $stmt->execute([
                'name'     => $name,
                'category' => $category,
                'price'    => $price,
                'stock'    => $stock
            ]);

            // Pola Post-Redirect-Get (PRG)
            header("Location: index.php?status=created");
            exit;
        } catch (PDOException $e) {
            // Tangkap jika terjadi duplikasi nama (Unique Constraint)
            if ($e->getCode() == 23000) {
                $errors['name'] = "Nama model heels ini sudah ada dalam etalase. Mohon gunakan nama yang berbeda.";
            } else {
                $errors['general'] = "Gagal menyimpan data: " . $e->getMessage();
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
    <title>Tambah Koleksi Heels • Maison Heels</title>
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
                <h2>Tambah Koleksi Heels Baru</h2>
                <p>Form Pendaftaran Model High-End Footwear</p>
            </div>

            <?php if (!empty($errors['general'])): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($errors['general'], ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="create.php" novalidate>
                <!-- Input Nama Heels -->
                <div class="form-group">
                    <label for="name">Nama Seri / Brand Heels</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        class="form-control" 
                        placeholder="Contoh: Christian Louboutin Kate 100 Nude" 
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
                        placeholder="Contoh: 15500000" 
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
                        placeholder="Contoh: 10" 
                        min="0" 
                        value="<?= htmlspecialchars((string)$stock_raw, ENT_QUOTES, 'UTF-8') ?>" 
                        required
                    >
                    <?php if (!empty($errors['stock'])): ?>
                        <div class="field-error"><?= htmlspecialchars($errors['stock'], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-buttons">
                    <button type="submit" class="btn btn-primary">Simpan ke Etalase</button>
                    <a href="index.php" class="btn btn-outline">Batal</a>
                </div>
            </form>
        </div>
    </main>

</body>
</html>
