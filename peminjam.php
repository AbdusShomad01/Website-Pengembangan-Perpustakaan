<?php
session_start();
include 'koneksi.php';

// CEK KEAMANAN: Cuma Admin yang boleh masuk
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Anggota - Sistem Perpustakaan</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        /* Gaya Tampilan Standar (Sama dengan yang lain) */
        body { font-family: 'Poppins', sans-serif; background-color: #f4f4f9; margin: 0; padding: 0; }
        .header { background-color: #003366; color: white; padding: 20px; text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 600; }
        .container { width: 90%; max-width: 900px; margin: 0 auto 50px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .btn-back { display: inline-block; margin-bottom: 20px; padding: 10px 20px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border-bottom: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f8f9fa; color: #333; }
        tr:hover { background-color: #f1f1f1; }
    </style>
</head>
<body>

    <div class="header">
        <h1>SISTEM INFORMASI PERPUSTAKAAN</h1>
        <p>Universitas Negeri Malang - Proyek Basis Data</p>
    </div>

    <div class="container">
        <a href="index.php" class="btn-back">⬅ Kembali ke Dashboard</a>

        <h2 style="border-bottom: 2px solid #eee; padding-bottom: 10px;">Daftar Anggota Terdaftar</h2>
        
        <p style="color: #666; font-size: 14px;">Data ini dikelola melalui Registrasi Mandiri Mahasiswa. Admin hanya berhak memantau.</p>

        <table>
            <tr>
                <th>NIM</th>
                <th>Nama Lengkap</th>
                <th>Fakultas</th>
                <th>Program Studi</th>
            </tr>
            <?php
            $tampil = mysqli_query($koneksi, "SELECT * FROM peminjam ORDER BY nim_peminjam ASC");
            while ($data = mysqli_fetch_array($tampil)) {
                echo "<tr>";
                echo "<td><b>" . $data['nim_peminjam'] . "</b></td>";
                echo "<td>" . $data['nama_peminjam'] . "</td>";
                echo "<td>" . $data['fakultas_peminjam'] . "</td>";
                echo "<td>" . $data['prodi'] . "</td>";
                // Tidak ada tombol Edit/Hapus disini
                echo "</tr>";
            }
            ?>
        </table>
    </div>

</body>
</html>
