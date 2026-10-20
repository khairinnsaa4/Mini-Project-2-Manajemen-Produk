-- =======================================================
-- PEMBARUAN DATABASE: STORE_DB
-- Dari Tema Toko Kue menjadi Koleksi Sepatu Heels Mewah
-- =======================================================

CREATE DATABASE IF NOT EXISTS store_db;
USE store_db;

-- 1. Hapus tabel lama (jika ada) agar struktur bersih & seragam
DROP TABLE IF EXISTS produk_kue;
DROP TABLE IF EXISTS products;

-- 2. Buat tabel products sesuai spesifikasi standar
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    category VARCHAR(50) NOT NULL DEFAULT 'Stiletto',
    price DECIMAL(12,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Masukkan 5 data awal koleksi sepatu heels mewah (High-End Luxury Heels)
INSERT INTO products (name, category, price, stock) VALUES
('Christian Louboutin So Kate 120 Patent', 'Stiletto', 12950000.00, 6),
('Jimmy Choo Romy 100 Platinum Ice Glitter', 'Classic Pumps', 14200000.00, 5),
('Manolo Blahnik Hangisi 105 Royal Blue Satin', 'Jeweled Pumps', 18800000.00, 4),
('Valentino Garavani Rockstud Ankle Strap Calfskin', 'Strappy Heels', 16500000.00, 7),
('Amina Muaddi Begum Glass Transparent Slingback', 'Mules & Slingback', 19800000.00, 3);
