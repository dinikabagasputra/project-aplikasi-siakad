<?php
require_once 'koneksi.php';
session_start();
if (!isset($_SESSION['login'])) { header("Location: login.php"); exit; }

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nim     = trim($_POST['nim']);
    $nama    = trim($_POST['nama']);
    $jurusan = trim($_POST['jurusan']);
    $email   = trim($_POST['email']);
    
    $namaFile = $_FILES['foto']['name'];
    $ukuranFile = $_FILES['foto']['size'];
    $errorFile = $_FILES['foto']['error'];
    $tmpName = $_FILES['foto']['tmp_name'];

    if (empty($nim) || empty($nama) || empty($jurusan) || empty($email)) {
        $error = "Semua field teks wajib diisi!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid!";
    } else {
        $namaFileBaru = null;
        if ($errorFile === 0) {
            $ekstensiValid = ['jpg', 'jpeg', 'png', 'webp'];
            $ekstensiFile = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));

            if (!in_array($ekstensiFile, $ekstensiValid)) {
                $error = "Format foto harus JPG, JPEG, PNG, atau WEBP!";
            } elseif ($ukuranFile > 2097152) {
                $error = "Ukuran foto maksimal 2MB!";
            } else {
                $namaFileBaru = uniqid() . '.' . $ekstensiFile;
                if (!is_dir('uploads')) { mkdir('uploads', 0777, true); }
                move_uploaded_file($tmpName, 'uploads/' . $namaFileBaru);
            }
        }

        if (empty($error)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO mahasiswa (nim, nama, jurusan, email, foto) VALUES (:nim, :nama, :jurusan, :email, :foto)");
                $stmt->execute([
                    ':nim'     => $nim,
                    ':nama'    => $nama,
                    ':jurusan' => $jurusan,
                    ':email'   => $email,
                    ':foto'    => $namaFileBaru
                ]);
                header("Location: index.php?pesan=tambah_sukses");
                exit;
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) { $error = "NIM sudah terdaftar!"; }
                else { $error = "Gagal menyimpan: " . $e->getMessage(); }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Mahasiswa - AkademikMHS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .bg-mesh {
            background-color: #0b0f19;
            background-image: 
                radial-gradient(at 0% 0%, rgba(37, 99, 235, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(124, 58, 237, 0.15) 0px, transparent 50%),
                radial-gradient(at 50% 100%, rgba(14, 165, 233, 0.1) 0px, transparent 50%);
        }
        .glass-panel {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
    </style>
</head>
<body class="bg-mesh text-slate-100 min-h-screen pb-16">

    <?php include 'navbar.php'; ?>

    <div class="max-w-xl mx-auto px-4 mt-10">
        <div class="glass-panel p-8 sm:p-10 rounded-3xl shadow-2xl">
            <div class="mb-6">
                <span class="text-[11px] font-bold uppercase tracking-widest text-blue-400 bg-blue-500/10 border border-blue-500/20 px-3 py-1 rounded-full">Form Entri</span>
                <h2 class="text-2xl font-extrabold text-white mt-2 tracking-tight">Tambah Data Mahasiswa</h2>
                <p class="text-sm text-slate-400 mt-1">Lengkapi formulir registrasi mahasiswa baru di bawah ini.</p>
            </div>

            <?php if ($error): ?>
                <div class="mb-6 p-4 bg-rose-500/10 border-l-4 border-rose-500 text-rose-400 text-sm rounded-2xl shadow-sm"><?= $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="" enctype="multipart/form-data" class="space-y-5">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">NIM</label>
                    <input type="text" name="nim" required class="w-full px-4 py-3 bg-slate-900/60 border border-slate-700 rounded-xl text-sm text-slate-100 placeholder-slate-500 focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="Contoh: 230001">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Nama Lengkap</label>
                    <input type="text" name="nama" required class="w-full px-4 py-3 bg-slate-900/60 border border-slate-700 rounded-xl text-sm text-slate-100 placeholder-slate-500 focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="Nama lengkap mahasiswa">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Jurusan</label>
                    <select name="jurusan" required class="w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-xl text-sm text-slate-100 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="" class="bg-slate-900">-- Pilih Jurusan --</option>
                        <option value="Teknik Informatika" class="bg-slate-900">Teknik Informatika</option>
                        <option value="Sistem Informasi" class="bg-slate-900">Sistem Informasi</option>
                        <option value="Teknik Komputer" class="bg-slate-900">Teknik Komputer</option>
                        <option value="Bisnis Digital" class="bg-slate-900">Bisnis Digital</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Email</label>
                    <input type="email" name="email" required class="w-full px-4 py-3 bg-slate-900/60 border border-slate-700 rounded-xl text-sm text-slate-100 placeholder-slate-500 focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="email@domain.com">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Foto Profil (Opsional)</label>
                    <input type="file" name="foto" class="w-full text-sm text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-500/10 file:text-blue-400 hover:file:bg-blue-500/20 cursor-pointer">
                </div>

                <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-800">
                    <a href="index.php" class="px-5 py-2.5 text-sm font-semibold text-slate-400 hover:text-white transition">Batal</a>
                    <button type="submit" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold px-6 py-2.5 rounded-xl text-sm shadow-lg shadow-blue-500/25 transition">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>