<?php
require_once 'koneksi.php';
session_start();
if (!isset($_SESSION['login'])) { header("Location: login.php"); exit; }

$id = $_GET['id'] ?? null;

if ($id) {
    // Ambil info foto dulu untuk dihapus file-nya
    $stmt = $pdo->prepare("SELECT foto FROM mahasiswa WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $mhs = $stmt->fetch();

    if ($mhs && !empty($mhs['foto'])) {
        $filePath = "uploads/" . $mhs['foto'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    // Hapus data dari database
    $stmt = $pdo->prepare("DELETE FROM mahasiswa WHERE id = :id");
    $stmt->execute([':id' => $id]);
}

header("Location: index.php?pesan=hapus_sukses");
exit;
?>