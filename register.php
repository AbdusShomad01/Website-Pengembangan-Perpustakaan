<?php
include 'koneksi.php';

if (isset($_POST['daftar'])) {
    $nim = $_POST['nim'];
    $nama = $_POST['nama'];
    $fakultas = $_POST['fakultas'];
    $prodi = $_POST['prodi'];
    $pass = md5($_POST['password']);

    // Validasi NIM harus Angka
    if (!ctype_digit($nim)) {
        echo "<script>alert('NIM harus berupa angka!');</script>";
    } else {
        $cek = mysqli_query($koneksi, "SELECT * FROM peminjam WHERE nim_peminjam = '$nim'");
        if (mysqli_num_rows($cek) > 0) {
            echo "<script>alert('NIM sudah terdaftar!');</script>";
        } else {
            $query = "INSERT INTO peminjam (nim_peminjam, nama_peminjam, fakultas_peminjam, prodi, password) 
                      VALUES ('$nim', '$nama', '$fakultas', '$prodi', '$pass')";
            if (mysqli_query($koneksi, $query)) {
                echo "<script>alert('Berhasil! Silakan Login'); window.location='login.php';</script>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Daftar Akun</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f4f9; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .card { background: white; padding: 40px; border-radius: 10px; width: 400px; text-align: center; }
        input, select { width: 100%; padding: 12px; margin: 5px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        .btn { width: 100%; padding: 12px; background: #003366; color: white; border: none; border-radius: 5px; cursor: pointer; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Daftar Mahasiswa</h2>
        <form method="POST">
            <input type="text" name="nim" placeholder="NIM (Hanya Angka)" required pattern="[0-9]+">
            <input type="text" name="nama" placeholder="Nama Lengkap" required>
            <input type="text" name="fakultas" placeholder="Fakultas" required>
            <input type="text" name="prodi" placeholder="Program Studi" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="daftar" class="btn">Daftar Sekarang</button>
        </form>
        <div style="margin-top: 15px; font-size: 13px;">Sudah punya akun? <a href="login.php">Login</a></div>
    </div>
</body>
</html>