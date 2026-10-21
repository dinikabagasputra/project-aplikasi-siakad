<?php
ob_start();
require_once 'koneksi.php';
session_start();
if (!isset($_SESSION['login'])) { 
    header("Location: login.php"); 
    exit; 
}

// Ambil data mahasiswa dari database
$stmt = $pdo->prepare("SELECT * FROM mahasiswa ORDER BY id DESC");
$stmt->execute();
$mahasiswa = $stmt->fetchAll();

// Header untuk mengunduh file Excel
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=Data_Mahasiswa_" . date('Y-m-d') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

ob_clean();
?>
<center>
    <h3>Laporan Data Mahasiswa AkademikMHS</h3>
</center>
<table border="1">
    <thead>
        <tr style="background-color: #2563eb; color: #ffffff; font-weight: bold;">
            <th>No</th>
            <th>NIM</th>
            <th>Nama Lengkap</th>
            <th>Jurusan</th>
            <th>Email</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; foreach ($mahasiswa as $row): ?>
        <tr>
            <td align="center"><?= $no++; ?></td>
            <!-- Format mso-number-format agar NIM tidak berubah menjadi format angka scientific di Excel -->
            <td style="mso-number-format:'\@';"><?= htmlspecialchars($row['nim']); ?></td>
            <td><?= htmlspecialchars($row['nama']); ?></td>
            <td><?= htmlspecialchars($row['jurusan']); ?></td>
            <td><?= htmlspecialchars($row['email']); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>