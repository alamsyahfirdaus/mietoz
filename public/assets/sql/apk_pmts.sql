-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 21, 2024 at 06:26 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.0.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `apk_pmts`
--

-- --------------------------------------------------------

--
-- Table structure for table `bank`
--

CREATE TABLE `bank` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nomor_rekening` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `carousel`
--

CREATE TABLE `carousel` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `judul` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gambar` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chats`
--

CREATE TABLE `chats` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_pesanan` bigint(20) UNSIGNED DEFAULT NULL,
  `id_kasir` bigint(20) UNSIGNED DEFAULT NULL,
  `sender` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `tanggal` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `detail_pesanan`
--

CREATE TABLE `detail_pesanan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_pesanan` bigint(20) UNSIGNED NOT NULL,
  `id_produk` bigint(20) UNSIGNED NOT NULL,
  `jumlah_produk` int(11) NOT NULL,
  `harga_satuan` decimal(10,2) NOT NULL,
  `level` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(3, '2024_04_07_144348_create_produk_table', 1),
(4, '2024_04_07_144934_create_pelanggan_table', 1),
(5, '2024_04_07_145554_create_pesanan_table', 1),
(6, '2024_04_07_151450_create_pembayaran_table', 1),
(7, '2024_04_07_151805_create_detail_pesanan_table', 1),
(8, '2024_04_15_114528_create_bank_table', 1),
(9, '2024_04_15_114701_create_carousel_table', 1),
(10, '2024_06_02_153439_create_kategori_table', 1),
(11, '2024_06_02_153545_add_kategori_id_to_produk_table', 1),
(12, '2024_06_17_010337_create_chats_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telepon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_user` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_pesanan` bigint(20) UNSIGNED NOT NULL,
  `metode_pembayaran` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_pembayaran` decimal(10,2) NOT NULL,
  `tanggal_pembayaran` datetime DEFAULT NULL,
  `jumlah_pembayaran` decimal(10,2) DEFAULT NULL,
  `jumlah_kembalian` decimal(10,2) DEFAULT NULL,
  `bukti_pembayaran` text COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `no_transaksi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_pelanggan` bigint(20) UNSIGNED DEFAULT NULL,
  `nama_pelanggan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telepon_pelanggan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_pesanan` datetime NOT NULL,
  `total_harga` decimal(10,2) NOT NULL,
  `biaya_pengiriman` decimal(10,2) DEFAULT NULL,
  `status_pesanan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_kasir` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stok` int(11) NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `diskon` int(11) DEFAULT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gambar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_kategori` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id`, `kode`, `nama`, `stok`, `harga`, `diskon`, `deskripsi`, `gambar`, `id_kategori`) VALUES
(1, '00000001', 'Ramen Seafood', 50, '20000.00', NULL, NULL, NULL, NULL),
(2, '00000002', 'Ramen Katsu', 45, '25000.00', NULL, NULL, NULL, NULL),
(3, '00000003', 'Ramen Tulang Ceker', 60, '18000.00', NULL, NULL, NULL, NULL),
(4, '00000004', 'Ramen Spesial', 40, '25000.00', NULL, NULL, NULL, NULL),
(5, '00000005', 'Hotplate Seafood', 30, '20000.00', NULL, NULL, NULL, NULL),
(6, '00000006', 'Hotplate Katsu', 35, '25000.00', NULL, NULL, NULL, NULL),
(7, '00000007', 'Hotplate Ciken Steak', 25, '22000.00', NULL, NULL, NULL, NULL),
(8, '00000008', 'Hotplate Tulang Ceker', 50, '18000.00', NULL, NULL, NULL, NULL),
(9, '00000009', 'Hotplate Tulang', 45, '16000.00', NULL, NULL, NULL, NULL),
(10, '00000010', 'Hotplate Ceker', 50, '16000.00', NULL, NULL, NULL, NULL),
(11, '00000011', 'Hotplate Jumbo', 20, '25000.00', NULL, NULL, NULL, NULL),
(12, '00000012', 'Udon Seafood', 40, '25000.00', NULL, NULL, NULL, NULL),
(13, '00000013', 'Udon Katsu', 35, '28000.00', NULL, NULL, NULL, NULL),
(14, '00000014', 'Udon Tulang Ceker', 30, '20000.00', NULL, NULL, NULL, NULL),
(15, '00000015', 'Udon Spesial', 25, '30000.00', NULL, NULL, NULL, NULL),
(16, '00000016', 'Udon Goreng Seafood', 40, '25000.00', NULL, NULL, NULL, NULL),
(17, '00000017', 'Udon Goreng Tung Ce', 35, '23000.00', NULL, NULL, NULL, NULL),
(18, '00000018', 'Sosis', 100, '6000.00', NULL, NULL, NULL, NULL),
(19, '00000019', 'Kentang', 90, '10000.00', NULL, NULL, NULL, NULL),
(20, '00000020', 'Katsu', 80, '12000.00', NULL, NULL, NULL, NULL),
(21, '00000021', 'Ciken Stik', 70, '10000.00', NULL, NULL, NULL, NULL),
(22, '00000022', 'Pangsit', 60, '10000.00', NULL, NULL, NULL, NULL),
(23, '00000023', 'Enoki Crispy', 110, '8000.00', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telephone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `username`, `telephone`, `password`, `profile`, `level`, `created_at`, `updated_at`) VALUES
(1, 'Alamsyah Firdaus', 'alamsyah.firdaus.af31@gmail.com', 'alamsyah', NULL, '$2y$10$VclM/OgNoSAnJI8FWyX0CO6J7L32C.oO1miZCZV1QitIAKIcFd9M6', NULL, '1', '2024-06-16 20:11:19', '2024-06-16 20:11:19');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bank`
--
ALTER TABLE `bank`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bank_nomor_rekening_unique` (`nomor_rekening`);

--
-- Indexes for table `carousel`
--
ALTER TABLE `carousel`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chats`
--
ALTER TABLE `chats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chats_id_pesanan_foreign` (`id_pesanan`),
  ADD KEY `chats_id_kasir_foreign` (`id_kasir`);

--
-- Indexes for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `detail_pesanan_id_pesanan_foreign` (`id_pesanan`),
  ADD KEY `detail_pesanan_id_produk_foreign` (`id_produk`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pelanggan_kode_unique` (`kode`),
  ADD KEY `pelanggan_id_user_foreign` (`id_user`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pembayaran_id_pesanan_foreign` (`id_pesanan`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pesanan_no_transaksi_unique` (`no_transaksi`),
  ADD KEY `pesanan_id_pelanggan_foreign` (`id_pelanggan`),
  ADD KEY `pesanan_id_kasir_foreign` (`id_kasir`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `produk_kode_unique` (`kode`),
  ADD KEY `produk_id_kategori_foreign` (`id_kategori`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bank`
--
ALTER TABLE `bank`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `carousel`
--
ALTER TABLE `carousel`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chats`
--
ALTER TABLE `chats`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chats`
--
ALTER TABLE `chats`
  ADD CONSTRAINT `chats_id_kasir_foreign` FOREIGN KEY (`id_kasir`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `chats_id_pesanan_foreign` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD CONSTRAINT `detail_pesanan_id_pesanan_foreign` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detail_pesanan_id_produk_foreign` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD CONSTRAINT `pelanggan_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_id_pesanan_foreign` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `pesanan_id_kasir_foreign` FOREIGN KEY (`id_kasir`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `pesanan_id_pelanggan_foreign` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `produk`
--
ALTER TABLE `produk`
  ADD CONSTRAINT `produk_id_kategori_foreign` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
