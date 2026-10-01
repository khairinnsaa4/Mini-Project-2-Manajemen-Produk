<?php require 'koneksi_db_reesha.php';
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama = trim($_POST["nama"] ?? "");
    $kategori = trim($_POST["kategori"] ?? "");
    $harga = filter_input(INPUT_POST, "harga", FILTER_VALIDATE_FLOAT);
    $stok = filter_input(INPUT_POST, "stok", FILTER_VALIDATE_INT);

    if (mb_strlen($nama) < 3) $errors["nama"] = "Nama kue minimal 3 karakter.";
    if ($harga === false || $harga <= 0) $errors["harga"] = "Harga harus > 0.";
    if ($stok === false || $stok < 0) $errors["stok"] = "Stok tidak boleh negatif.";

    $gambar = $_FILES['gambar_kue'];
    $nama_file = "";
    if ($gambar['error'] === UPLOAD_ERR_OK) {
        $ekstensi = pathinfo($gambar['name'], PATHINFO_EXTENSION);
        $nama_file = uniqid('cake_') . '.' . $ekstensi;
        move_uploaded_file($gambar['tmp_name'], 'unggahan_gambar/' . $nama_file);
    } else {
        $errors["gambar_kue"] = "Gambar wajib diunggah.";
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO produk_kue (nama_kue, kategori, harga, stok, gambar_kue) VALUES (:nama, :kat, :harga, :stok, :gambar)");
            $stmt->execute(['nama' => $nama, 'kat' => $kategori, 'harga' => $harga, 'stok' => $stok, 'gambar' => $nama_file]);
            
            header("Location: index.php?status=sukses_tambah");
            exit;
        } catch (Exception $e) {
            $errors["database"] = "Gagal menyimpan. Pastikan nama kue unik.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Racik Kue Baru - Reesha Cake</title>
    <link rel="stylesheet" href="aset_visual/tema_reesha.css">
</head>
<body>
    <div class="wadah-form">
        <h2>🍰 Tambah Menu Kue Baru</h2>
        <?php foreach($errors as $err): ?>
            <p style="color:red;"><?= htmlspecialchars($err, ENT_QUOTES, "UTF-8") ?></p>
        <?php endforeach; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <label>Nama Kue</label>
            <input type="text" name="nama" minlength="3" required>
            
            <label>Kategori</label>
            <select name="kategori">
                <option value="Sponge Cake">Sponge Cake</option>
                <option value="Brownies">Brownies</option>
                <option value="Pastry">Pastry</option>
            </select>
            
            <label>Harga (Rp)</label>
            <input type="number" name="harga" min="1" required>
            
            <label>Stok</label>
            <input type="number" name="stok" min="0" required>

            <label>Foto Kue</label>
            <input type="file" name="gambar_kue" accept="image/*" required>
            
            <button type="submit" class="btn-primary">Simpan ke Etalase</button>
            <a href="index.php" style="display:block; text-align:center; margin-top:10px; color:#888;">Batal</a>
        </form>
    </div>
</body>
</html>