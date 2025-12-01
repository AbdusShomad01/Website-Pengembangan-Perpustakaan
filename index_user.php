<?php
// Koneksi sudah dibuka oleh index.php, jadi tidak perlu session_start lagi disini
include 'koneksi.php';
$nim_saya = $_SESSION['id'];

// AMBIL DATA PROFIL
$profil = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM peminjam WHERE nim_peminjam = '$nim_saya'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Mahasiswa</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f4f9; padding: 20px; }
        .container { max-width: 1000px; margin: auto; }
        
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .btn-logout { background: #dc3545; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px; }

        .section { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        h3 { border-bottom: 2px solid #eee; padding-bottom: 10px; margin-top: 0; color: #003366; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #f8f9fa; }
        
        .btn-pinjam { background: #28a745; color: white; padding: 5px 15px; text-decoration: none; border-radius: 5px; font-size: 14px; }
        .badge { padding: 5px 10px; border-radius: 15px; font-size: 12px; color: white; }
        .bg-blue { background: #007bff; } .bg-grey { background: #6c757d; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>Halo, <?php echo $profil['nama_peminjam']; ?></h2>
        <a href="logout.php" class="btn-logout">Logout</a>
    </div>

    <div class="section">
        <h3>👤 Profil Saya</h3>
        <table>
            <tr><td width="200">NIM</td><td>: <?php echo $profil['nim_peminjam']; ?></td></tr>
            <tr><td>Nama Lengkap</td><td>: <?php echo $profil['nama_peminjam']; ?></td></tr>
            <tr><td>Fakultas</td><td>: <?php echo $profil['fakultas_peminjam']; ?></td></tr>
            <tr><td>Program Studi</td><td>: <?php echo $profil['prodi']; ?></td></tr>
        </table>
    </div>

    <div class="section">
        <h3>📚 Katalog Buku</h3>
        <table>
            <tr>
                <th>Judul Buku</th>
                <th>Penerbit</th>
                <th>Stok Tersedia</th>
                <th>Aksi</th>
            </tr>
            <?php
            // Tampilkan Buku + Hitung Stok Tersedia
            $buku = mysqli_query($koneksi, "SELECT buku.*, 
                    (SELECT COUNT(*) FROM copy_buku WHERE id_buku = buku.id_buku AND status = 'Tersedia') as stok 
                    FROM buku");
            
            while ($b = mysqli_fetch_array($buku)) {
                echo "<tr>";
                echo "<td>" . $b['judul'] . "</td>";
                echo "<td>" . $b['penerbit'] . "</td>";
                echo "<td>" . $b['stok'] . " Copy</td>";
                echo "<td>";
                // Tombol Pinjam (Hanya muncul jika stok > 0)
                if ($b['stok'] > 0) {
                    echo "<a href='proses_pinjam_user.php?id_buku=" . $b['id_buku'] . "' 
                          class='btn-pinjam' 
                          onclick=\"return confirm('Apakah Anda yakin ingin meminjam buku: " . $b['judul'] . "?');\">
                          Pinjam Buku
                          </a>";
                } else {
                    echo "<span style='color:red; font-size:12px;'>Habis</span>";
                }
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>

    <div class="section">
        <h3>🔄 Riwayat Peminjaman Saya</h3>
        <table>
            <tr>
                <th>Judul Buku</th>
                <th>Tgl Pinjam</th>
                <th>Tgl Kembali</th>
                <th>Status</th>
                <th>Denda</th>
            </tr>
            <?php
            // Gabungkan 4 Tabel: Peminjaman -> Copy -> Buku -> Pengembalian
            $riwayat = mysqli_query($koneksi, "
                SELECT peminjaman.*, buku.judul, pengembalian.tanggal_kembali, pengembalian.denda 
                FROM peminjaman 
                JOIN copy_buku ON peminjaman.id_copy = copy_buku.id_copy
                JOIN buku ON copy_buku.id_buku = buku.id_buku
                LEFT JOIN pengembalian ON peminjaman.id_peminjaman = pengembalian.id_peminjaman
                WHERE peminjaman.nim_peminjam = '$nim_saya'
                ORDER BY peminjaman.id_peminjaman DESC
            ");

            while ($r = mysqli_fetch_array($riwayat)) {
                $status_label = ($r['status_transaksi'] == 'Berjalan') ? 'Berlangsung' : 'Selesai';
                $warna = ($r['status_transaksi'] == 'Berjalan') ? 'bg-blue' : 'bg-grey';
                $denda = ($r['denda'] > 0) ? "Rp " . number_format($r['denda']) : "-";
                $tgl_kembali = ($r['tanggal_kembali']) ? $r['tanggal_kembali'] : "-";

                echo "<tr>";
                echo "<td>" . $r['judul'] . "</td>";
                echo "<td>" . $r['tanggal_pinjam'] . "</td>";
                echo "<td>" . $tgl_kembali . "</td>";
                echo "<td><span class='badge $warna'>$status_label</span></td>";
                echo "<td>" . $denda . "</td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>

</div>
</body>
</html>