<?php
session_start();
require 'koneksi_db_reesha.php';

$_SESSION["csrf"] ??= bin2hex(random_bytes(32));

$stmt = $pdo->query("SELECT * FROM produk_kue ORDER BY id DESC");
$semua_kue = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Toko Cake by Reesha</title>
    <link rel="stylesheet" href="aset_visual/tema_reesha.css">
</head>
<body>
    <div class="header-toko">
        <h1>Toko Cake by Reesha 🧁</h1>
        <a href="racik_kue_baru.php" class="btn-primary">+ Racik Kue Baru</a>
    </div>

    <?php if (($_GET["status"] ?? "") === "sukses_tambah"): ?>
        <div class="alert">Berhasil menambah kue baru ke etalase!</div>
    <?php endif; ?>
    <?php if (($_GET["status"] ?? "") === "sukses_edit"): ?>
        <div class="alert">Berhasil memperbarui data kue!</div>
    <?php endif; ?>
    <?php if (($_GET["status"] ?? "") === "sukses_hapus"): ?>
        <div class="alert">Kue berhasil dihapus dari etalase.</div>
    <?php endif; ?>

    <div class="grid-produk">
        <?php if(empty($semua_kue)): ?>
            <p>Belum ada kue di etalase. Yuk racik kue pertamamu!</p>
        <?php else: ?>
            <?php foreach ($semua_kue as $kue): ?>
                <div class="card-kue">
                    <img src="unggahan_gambar/<?= htmlspecialchars($kue['gambar_kue'], ENT_QUOTES, "UTF-8") ?>" alt="Foto Kue" class="img-kue">
                    
                    <h3><?= htmlspecialchars($kue["nama_kue"], ENT_QUOTES, "UTF-8") ?></h3>
                    <span class="badge"><?= htmlspecialchars($kue["kategori"], ENT_QUOTES, "UTF-8") ?></span>
                    
                    <p class="harga">Rp <?= number_format($kue["harga"], 0, ",", ".") ?></p>
                    <p class="stok">Stok Tersedia: <?= (int)$kue["stok"] ?></p>
                    
                    <div class="aksi-card">
                        <a href="modif_kue.php?id=<?= $kue['id'] ?>" class="btn-edit">Edit</a>
                        
                        <form method="POST" action="hapus_kue_aman.php" onsubmit="return confirm('Buang kue ini dari etalase?');" style="display:inline; display:flex; flex:1;">
                            <input type="hidden" name="id" value="<?= $kue["id"] ?>">
                            <input type="hidden" name="csrf" value="<?= $_SESSION["csrf"] ?>">
                            <button type="submit" class="btn-delete" style="width:100%;">Hapus</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>