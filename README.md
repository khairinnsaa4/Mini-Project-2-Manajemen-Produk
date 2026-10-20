# Maison Heels • Luxury Footwear Product Manager

Aplikasi web manajemen produk (*CRUD*) berbasis **PHP Native** dan **MySQL (PDO)** dengan standar keamanan industri, arsitektur *clean directory*, dan antarmuka bertema koleksi sepatu heels mewah (*High-End Heels*).

---

## 📁 Struktur Direktori & File

```text
product-manager/
├── config/
│   └── db.php              # Koneksi PDO aman & konfigurasi database
├── public/
│   ├── index.php           # READ: Daftar produk, form search (GET), token CSRF
│   ├── create.php          # CREATE: Form tambah heels & server-side validation
│   ├── edit.php            # UPDATE: Edit data per ID & proteksi nilai lama
│   ├── delete.php          # DELETE: Handler POST khusus dengan proteksi CSRF
│   └── assets/
│       └── style.css       # Box Model reset, Flexbox cards, & luxury styling
├── database/
│   └── store_db.sql        # Skema DDL tabel products + 5 data awal heels
└── README.md               # Dokumentasi dan panduan instalasi
```

---

## 🔒 Standar Keamanan & Fitur Teknis

1. **Prepared Statements (PDO)**: Seluruh query SQL (`SELECT`, `INSERT`, `UPDATE`, `DELETE`, hingga pencarian dengan `LIKE`) menggunakan prepared statements untuk mencegah ancaman **SQL Injection**.
2. **Proteksi XSS (Cross-Site Scripting)**: Setiap data string yang ditampilkan ke HTML di-escape menggunakan `htmlspecialchars($var, ENT_QUOTES, "UTF-8")`.
3. **Proteksi CSRF (Cross-Site Request Forgery)**: Operasi destruktif (`DELETE`) hanya menerima request metode `POST` dan mewajibkan verifikasi token acak `session` menggunakan `hash_equals()`.
4. **Pola Post-Redirect-Get (PRG)**: Menghindari pengiriman ulang formulir ganda (*duplicate submission*) saat browser di-refresh setelah operasi *Create*, *Update*, atau *Delete*.
5. **Validasi Sisi Server (Server-Side Validation)**:
   - Nama produk: Wajib diisi, minimal 3 karakter, dan unik.
   - Kategori: Wajib dipilih.
   - Harga: Angka positif (`> 0`).
   - Stok: Angka bulat positif atau nol (`>= 0`).
   - Retensi input formulir saat terjadi kesalahan validasi.
6. **Search & Filter (GET)**: Fitur pencarian berbasis parameter query string aman yang mencari nama produk dan kategori sekaligus.
7. **UI Responsif**: Menggunakan **CSS Box Model** (`border-box`) dan **Flexbox** agar tampilan card produk membungkus rapi di berbagai ukuran layar.

---

## 🚀 Panduan Menjalankan Project di XAMPP

1. **Jalankan XAMPP Control Panel**:
   - Nyalakan modul **Apache** dan **MySQL**.

2. **Siapkan Database**:
   - Buka browser dan akses **phpMyAdmin** di `http://localhost/phpmyadmin`.
   - Buat database baru bernama `store_db` (jika belum ada).
   - Masuk ke tab **Import**, pilih file `database/store_db.sql`, lalu klik **Import / Go**.

3. **Akses Aplikasi**:
   - Letakkan folder project ini di dalam direktori `htdocs` XAMPP Anda:
     ```
     C:/xampp/htdocs/manajemen_produk/
     ```
   - Buka browser dan akses salah satu URL berikut:
     ```
     http://localhost/manajemen_produk/
     ```
     *(Akan otomatis dialihkan ke `public/index.php`)*

---

## 📦 Panduan Git & Unggah ke GitHub

Jalankan perintah berikut di terminal / Git Bash pada folder root project:

```bash
# 1. Inisialisasi repository Git lokal
git init

# 2. Tambahkan semua file project ke staging area
git add .

# 3. Lakukan commit pertama
git commit -m "feat: inisialisasi manajemen produk sepatu heels mewah dengan PHP native dan PDO"

# 4. Ubah nama branch utama menjadi main
git branch -M main

# 5. Hubungkan ke remote repository GitHub (ganti URL dengan repository Anda)
git remote add origin https://github.com/USERNAME/product-manager.git

# 6. Unggah kode ke GitHub
git push -u origin main
```
