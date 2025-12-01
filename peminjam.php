<?php
session_start();
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != true) {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

// --- INISIALISASI VARIABEL ---
$nim_edit = "";
$nama_edit = "";
$fakultas_edit = "";
$tombol_label = "Simpan Anggota";
$aksi_form = "";
$popup_status = "";
$popup_pesan = "";
$readonly_nim = ""; // Variabel untuk mengunci NIM saat edit

// --- LOGIKA 1: MENANGKAP TOMBOL EDIT ATAU HAPUS ---
if (isset($_GET['op'])) {
    $op = $_GET['op'];
    $id = $_GET['id'];

    // LOGIKA HAPUS
    if ($op == 'delete') {
        $query_hapus = "DELETE FROM peminjam WHERE nim_peminjam = '$id'";
        if (mysqli_query($koneksi, $query_hapus)) {
            echo "<script>alert('Data Anggota Berhasil Dihapus!'); window.location='peminjam.php';</script>";
        } else {
            // Error jika anggota masih memiliki data peminjaman (Foreign Key)
            echo "<script>alert('Gagal Hapus! Anggota ini masih memiliki riwayat peminjaman buku.'); window.location='peminjam.php';</script>";
        }
    }
    
    // LOGIKA PERSIAPAN EDIT
    if ($op == 'edit') {
        $query_tampil = mysqli_query($koneksi, "SELECT * FROM peminjam WHERE nim_peminjam = '$id'");
        $data_edit = mysqli_fetch_array($query_tampil);
        
        $nim_edit = $data_edit['nim_peminjam'];
        $nama_edit = $data_edit['nama_peminjam'];
        $fakultas_edit = $data_edit['fakultas_peminjam'];
        
        $tombol_label = "Update Data";
        $aksi_form = "?op=update&id=$id";
        $readonly_nim = "readonly style='background-color: #e9ecef; cursor: not-allowed;'"; // Kunci input NIM
    }
}

// --- LOGIKA 2: SIMPAN / UPDATE ---
if (isset($_POST['proses'])) {
    $nim = $_POST['nim'];
    $nama = $_POST['nama'];
    $fakultas = $_POST['fakultas'];

    if (isset($_GET['op']) && $_GET['op'] == 'update') {
        // MODE UPDATE (NIM tidak diubah)
        $id_lama = $_GET['id'];
        $query = "UPDATE peminjam SET nama_peminjam = '$nama', fakultas_peminjam = '$fakultas' WHERE nim_peminjam = '$id_lama'";
        $pesan_sukses = "Data anggota berhasil diperbarui!";
    } else {
        // MODE INSERT BARU
        // Cek apakah NIM sudah terdaftar
        $cek_nim = mysqli_query($koneksi, "SELECT nim_peminjam FROM peminjam WHERE nim_peminjam = '$nim'");
        if(mysqli_num_rows($cek_nim) > 0){
             echo "<script>alert('Error: NIM $nim sudah terdaftar!'); window.location='peminjam.php';</script>";
             exit;
        }

        $query = "INSERT INTO peminjam (nim_peminjam, nama_peminjam, fakultas_peminjam) 
                  VALUES ('$nim', '$nama', '$fakultas')";
        $pesan_sukses = "Anggota baru berhasil didaftarkan!";
    }
    
    if (mysqli_query($koneksi, $query)) {
        $popup_status = "show";
        $popup_pesan = $pesan_sukses;
    } else {
        echo "<script>alert('Gagal: " . mysqli_error($koneksi) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Anggota - Sistem Perpustakaan</title>
    <style>
        /* --- CSS UTAMA (SAMA) --- */
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f4f9; margin: 0; padding: 0; }
        .header { background-color: #003366; color: white; padding: 20px; text-align: center; margin-bottom: 30px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .header h1 { margin: 0; font-size: 24px; }
        .header p { margin: 5px 0 0; font-size: 14px; opacity: 0.8; }
        .container { width: 90%; max-width: 800px; margin: 0 auto 50px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        
        .btn-back { display: inline-block; margin-bottom: 20px; padding: 10px 20px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; transition: 0.3s; }
        .btn-back:hover { background-color: #5a6268; transform: translateX(-3px); }
        
        label { font-weight: bold; color: #333; display: block; margin-top: 15px; }
        input, select { width: 100%; padding: 12px; margin: 5px 0 15px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        
        .btn-simpan { background-color: #007bff; color: white; padding: 12px; border: none; width: 100%; border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: bold; transition: 0.3s; }
        .btn-simpan:hover { background-color: #0056b3; }
        .btn-update { background-color: #28a745; }

        /* Tabel & Aksi */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border-bottom: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f8f9fa; color: #333; }
        
        .btn-aksi { padding: 5px 10px; text-decoration: none; border-radius: 4px; font-size: 12px; font-weight: bold; margin-right: 5px; color: white; }
        .btn-edit { background-color: #ffc107; color: black; }
        .btn-hapus { background-color: #dc3545; }

        /* Pop-up Modal Style */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); display: flex; justify-content: center; align-items: center; z-index: 1000; }
        .modal-box { background-color: white; padding: 30px; width: 400px; border-radius: 10px; text-align: center; box-shadow: 0 5px 15px rgba(0,0,0,0.3); animation: fadeIn 0.3s ease-in-out; }
        .modal-icon { font-size: 50px; color: #28a745; margin-bottom: 10px; }
        .modal-title { font-size: 22px; font-weight: bold; color: #333; margin-bottom: 10px; }
        .modal-text { font-size: 16px; color: #666; margin-bottom: 25px; }
        .btn-modal { background-color: #007bff; color: white; padding: 10px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

    <?php if ($popup_status == "show"): ?>
    <div class="modal-overlay">
        <div class="modal-box">
            <div class="modal-icon">✓</div>
            <div class="modal-title">Sukses!</div>
            <div class="modal-text"><?php echo $popup_pesan; ?></div>
            <a href="peminjam.php" class="btn-modal">OK, Lanjutkan</a>
        </div>
    </div>
    <?php endif; ?>

    <div class="header">
        <h1>SISTEM INFORMASI PERPUSTAKAAN</h1>
        <p>Peminjaman Buku Perpustakaan Universitas Negeri Malang</p>
    </div>

    <div class="container">
        <a href="index.php" class="btn-back">⬅ Kembali ke Dashboard</a>

        <h2 style="border-bottom: 2px solid #eee; padding-bottom: 10px;">
            <?php echo ($aksi_form) ? "Edit Data Anggota" : "Registrasi Anggota Baru"; ?>
        </h2>
        
        <form method="POST" action="<?php echo $aksi_form; ?>">
            <label>NIM Mahasiswa:</label>
            <input type="text" name="nim" required placeholder="Contoh: 2303126" value="<?php echo $nim_edit; ?>" <?php echo $readonly_nim; ?>>

            <label>Nama Lengkap:</label>
            <input type="text" name="nama" required placeholder="Contoh: Abdus Shomad" value="<?php echo $nama_edit; ?>">

            <label>Fakultas:</label>
            <select name="fakultas" required>
                <option value="">-- Pilih Fakultas --</option>
                <option value="FMIPA" <?php echo ($fakultas_edit == "FMIPA") ? "selected" : ""; ?>>FMIPA</option>
                <option value="Teknik" <?php echo ($fakultas_edit == "Teknik") ? "selected" : ""; ?>>Teknik</option>
                <option value="Sastra" <?php echo ($fakultas_edit == "Sastra") ? "selected" : ""; ?>>Sastra</option>
                <option value="Ekonomi" <?php echo ($fakultas_edit == "Ekonomi") ? "selected" : ""; ?>>Ekonomi</option>
                <option value="Ilmu Pendidikan" <?php echo ($fakultas_edit == "Ilmu Pendidikan") ? "selected" : ""; ?>>Ilmu Pendidikan</option>
                <option value="Ilmu Keolahragaan" <?php echo ($fakultas_edit == "Ilmu Keolahragaan") ? "selected" : ""; ?>>Ilmu Keolahragaan</option>
                <option value="Ilmu Sosial" <?php echo ($fakultas_edit == "Ilmu Sosial") ? "selected" : ""; ?>>Ilmu Sosial</option>
                <option value="Psikologi" <?php echo ($fakultas_edit == "Psikologi") ? "selected" : ""; ?>>Psikologi</option>
            </select>

            <button type="submit" name="proses" class="btn-simpan <?php echo ($aksi_form) ? 'btn-update' : ''; ?>">
                <?php echo $tombol_label; ?>
            </button>
            
            <?php if($aksi_form): ?>
                <a href="peminjam.php" style="display:block; text-align:center; margin-top:10px; color:#dc3545; text-decoration:none;">Batal Edit</a>
            <?php endif; ?>
        </form>

        <br><br>
        
        <h3 style="border-bottom: 2px solid #eee; padding-bottom: 10px;">Daftar Anggota Terdaftar</h3>
        <table>
            <tr>
                <th>NIM</th>
                <th>Nama</th>
                <th>Fakultas</th>
                <th width="20%">Aksi</th>
            </tr>
            <?php
            $tampil = mysqli_query($koneksi, "SELECT * FROM peminjam ORDER BY nim_peminjam ASC");
            while ($data = mysqli_fetch_array($tampil)) {
                echo "<tr>";
                echo "<td>" . $data['nim_peminjam'] . "</td>";
                echo "<td><b>" . $data['nama_peminjam'] . "</b></td>";
                echo "<td>" . $data['fakultas_peminjam'] . "</td>";
                echo "<td>
                        <a href='peminjam.php?op=edit&id=" . $data['nim_peminjam'] . "' class='btn-aksi btn-edit'>Edit</a>
                        <a href='peminjam.php?op=delete&id=" . $data['nim_peminjam'] . "' class='btn-aksi btn-hapus' onclick=\"return confirm('Yakin ingin menghapus anggota ini?');\">Hapus</a>
                      </td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>

</body>
</html>