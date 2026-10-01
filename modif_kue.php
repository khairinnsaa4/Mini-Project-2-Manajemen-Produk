<?php require 'koneksi_db_reesha.php';
$errors = [];
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: index.php");
    exit;
}

$stmt_get = $pdo->prepare("SELECT * FROM produk_kue WHERE id = :id");
$stmt_get->execute(['id' => $id]);
$kue = $stmt_get->fetch();

if (!$kue) {
    die("Kue tidak ditemukan!");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama = trim($_POST["nama"] ?? "");
    $kategori = trim($_POST["kategori"] ?? "");
    $harga = filter_input(INPUT_POST, "harga", FILTER_VALIDATE_FLOAT);
    $stok = filter_input(INPUT_POST, "stok", FILTER_VALIDATE_INT);
    $id_post = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

    if (mb_strlen($nama) < 3) $errors["nama"] = "Nama kue minimal 3 karakter.";
    if ($harga === false || $harga <= 0) $errors["harga"] = "Harga harus > 0.";
    if ($stok === false || $stok < 0) $errors["stok"] = "Stok tidak boleh negatif.";

    if (empty($errors) && $id_post === $id) {
        $gambar_baru = $_FILES['gambar_kue'];
        $nama_file = $kue['gambar_kue']; 
        
        if ($gambar_baru['error'] === UPLOAD_ERR_OK) {
            $ekstensi = pathinfo($gambar_baru['name'], PATHINFO_EXTENSION);
            $nama_file = uniqid('cake_') . '.' . $ekstensi;
            move_uploaded_file($gambar_baru['tmp_name'], 'unggahan_gambar/' . $nama_file);
            
            if (file_exists("unggahan_gambar/" . $kue['gambar_kue'])) {
                unlink("unggahan_gambar/" . $kue['gambar_kue']);
            }
        }

        try {
            $stmt_update = $pdo->prepare("UPDATE produk_kue SET nama_kue=:nama, kategori=:kat, harga=:harga, stok=:stok, gambar_kue=:gambar WHERE id=:id");
            $stmt_update->execute([
                'nama' => $nama, 
                'kat' => $kategori, 
                'harga' => $harga, 
                'stok' => $stok, 
                'gambar' => $nama_file,
                'id' => $id
            ]);
            
            header("Location: index.php?status=sukses_edit");
            exit;
        } catch (Exception $e) {
            $errors["database"] = "Gagal memperbarui. Pastikan nama kue tidak sama dengan kue lain.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Modif Kue - Reesha Cake</title>
    <link rel="stylesheet" href="aset_visual/tema_reesha.css">
</head>
<body>
    <div class="wadah-form">
        <h2>🛠️ Modifikasi Data Kue</h2>
        <?php foreach($errors as $err): ?>
            <p style="color:red;"><?= htmlspecialchars($err, ENT_QUOTES, "UTF-8") ?></p>
        <?php endforeach; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $kue['id'] ?>">
            
            <label>Nama Kue</label>
            <input type="text" name="nama" value="<?= htmlspecialchars($kue['nama_kue'], ENT_QUOTES, "UTF-8") ?>" required>
            
            <label>Kategori</label>
            <select name="kategori">
                <option value="Sponge Cake" <?= $kue['kategori'] == 'Sponge Cake' ? 'selected' : '' ?>>Sponge Cake</option>
                <option value="Brownies" <?= $kue['kategori'] == 'Brownies' ? 'selected' : '' ?>>Brownies</option>
                <option value="Pastry" <?= $kue['kategori'] == 'Pastry' ? 'selected' : '' ?>>Pastry</option>
            </select>
            
            <label>Harga (Rp)</label>
            <input type="number" name="harga" value="<?= (int)$kue['harga'] ?>" required>
            
            <label>Stok</label>
            <input type="number" name="stok" value="<?= (int)$kue['stok'] ?>" required>

            <label>Foto Saat Ini</label>
            <img src="unggahan_gambar/<?= htmlspecialchars($kue['gambar_kue'], ENT_QUOTES, "UTF-8") ?>" style="width:100px; border-radius:5px; display:block; margin-bottom:10px;">

            <label>Ganti Foto (Biarkan kosong jika tidak diganti)</label>
            <input type="file" name="gambar_kue" accept="image/*">
            
            <button type="submit" class="btn-primary">Simpan Perubahan</button>
            <a href="index.php" style="display:block; text-align:center; margin-top:10px; color:#888;">Batal</a>
        </form>
    </div>
</body>
</html>