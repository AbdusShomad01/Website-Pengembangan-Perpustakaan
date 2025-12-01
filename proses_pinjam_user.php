<?php
session_start();
include 'koneksi.php';

// Cek apakah User Login
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    exit("Akses Ditolak");
}

$nim = $_SESSION['id'];
$id_buku = $_GET['id_buku'];
$tanggal = date('Y-m-d');

// 1. CARI 1 COPY BUKU YANG TERSEDIA
// Kita pilih ID Copy terkecil yang statusnya 'Tersedia' dari judul buku tersebut
$cari_copy = mysqli_query($koneksi, "SELECT id_copy FROM copy_buku WHERE id_buku = '$id_buku' AND status = 'Tersedia' LIMIT 1");

if (mysqli_num_rows($cari_copy) > 0) {
    $data_copy = mysqli_fetch_array($cari_copy);
    $id_copy_tersedia = $data_copy['id_copy'];

    // 2. INSERT KE TABEL PEMINJAMAN
    $insert = mysqli_query($koneksi, "INSERT INTO peminjaman (tanggal_pinjam, nim_peminjam, id_copy, status_transaksi) 
                                      VALUES ('$tanggal', '$nim', '$id_copy_tersedia', 'Berjalan')");

    // 3. UPDATE STATUS COPY JADI 'DIPINJAM'
    $update = mysqli_query($koneksi, "UPDATE copy_buku SET status = 'Dipinjam' WHERE id_copy = '$id_copy_tersedia'");

    if ($insert && $update) {
        echo "<script>alert('Berhasil meminjam buku!'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal memproses transaksi.'); window.location='index.php';</script>";
    }

} else {
    echo "<script>alert('Maaf, stok buku ini baru saja habis.'); window.location='index.php';</script>";
}
?>