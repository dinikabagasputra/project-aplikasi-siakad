<?php
require_once 'koneksi.php';
session_start();
if (!isset($_SESSION['login'])) { header("Location: login.php"); exit; }

// Ambil seluruh data mahasiswa dari database
$stmt = $pdo->prepare("SELECT * FROM mahasiswa ORDER BY id DESC");
$stmt->execute();
$mahasiswa = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Data Mahasiswa PDF - AkademikMHS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        /* Pengaturan Khusus untuk Mode Cetak / PDF */
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                color: #000000 !important;
            }
            .page-break {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 min-h-screen p-6">

    <!-- Tombol Aksi (Hanya tampil di layar browser, tersembunyi saat dicetak/PDF) -->
    <div class="max-w-4xl mx-auto mb-6 flex justify-between items-center no-print bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
        <a href="index.php" class="text-xs font-bold text-slate-600 hover:text-slate-900 transition flex items-center gap-1">
            ← Kembali ke Dashboard
        </a>
        <div class="flex gap-3">
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-xl text-sm transition shadow-md flex items-center gap-2">
                🖨️ Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <!-- Lembar Laporan PDF -->
    <div class="max-w-4xl mx-auto bg-white p-10 rounded-2xl shadow-md border border-slate-200 printable-area">
        
        <!-- KOP SURAT FORMAL -->
        <div class="flex items-center justify-between border-b-2 border-slate-900 pb-4 mb-6">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-blue-600 text-white font-extrabold text-2xl rounded-2xl flex items-center justify-center shadow-md">
                    AM
                </div>
                <div>
                    <h1 class="text-xl font-extrabold text-slate-900 uppercase tracking-wide">Sistem Informasi Akademik</h1>
                    <p class="text-xs text-slate-500">Laporan Rekapitulasi Data Mahasiswa Terpadu</p>
                    <p class="text-[11px] text-slate-400">Jl. Kampus Utama No. 1, Indonesia | Web: akademikmhs.ac.id</p>
                </div>
            </div>
            <div class="text-right">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Tanggal Cetak</span>
                <span class="text-sm font-semibold text-slate-800"><?= date('d F Y'); ?></span>
            </div>
        </div>

        <!-- JUDUL LAPORAN -->
        <div class="text-center mb-6">
            <h2 class="text-lg font-bold text-slate-900 uppercase tracking-wider underline">Laporan Data Mahasiswa</h2>
            <p class="text-xs text-slate-500 mt-0.5">Total Terdaftar: <strong><?= count($mahasiswa); ?> Orang</strong></p>
        </div>

        <!-- TABEL DATA MAHASISWA -->
        <table class="w-full text-left border-collapse border border-slate-300 text-xs mb-8">
            <thead>
                <tr class="bg-slate-100 text-slate-900 font-bold uppercase tracking-wider">
                    <th class="border border-slate-300 p-2.5 text-center">No</th>
                    <th class="border border-slate-300 p-2.5">NIM</th>
                    <th class="border border-slate-300 p-2.5">Nama Lengkap</th>
                    <th class="border border-slate-300 p-2.5">Program Studi / Jurusan</th>
                    <th class="border border-slate-300 p-2.5">Email</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 font-medium">
                <?php if (empty($mahasiswa)): ?>
                    <tr>
                        <td colspan="5" class="p-6 text-center text-slate-400 border border-slate-300">Belum ada data mahasiswa.</td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($mahasiswa as $row): ?>
                        <tr class="hover:bg-slate-50">
                            <td class="border border-slate-300 p-2.5 text-center font-semibold"><?= $no++; ?></td>
                            <td class="border border-slate-300 p-2.5 font-bold text-slate-900"><?= htmlspecialchars($row['nim']); ?></td>
                            <td class="border border-slate-300 p-2.5 font-semibold text-slate-800"><?= htmlspecialchars($row['nama']); ?></td>
                            <td class="border border-slate-300 p-2.5"><?= htmlspecialchars($row['jurusan']); ?></td>
                            <td class="border border-slate-300 p-2.5 text-slate-600"><?= htmlspecialchars($row['email']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- AREA TANDA TANGAN / PENGESAHAN -->
        <div class="flex justify-between items-end pt-6 page-break text-xs">
            <div>
                <p class="text-slate-400">Catatan:</p>
                <p class="text-slate-500 italic">* Dokumen ini dicetak secara otomatis dari Sistem AkademikMHS.</p>
            </div>
            <div class="text-center w-52">
                <p class="text-slate-600 mb-1">Dibuat Oleh,</p>
                <p class="font-bold text-slate-900 mb-12">Administrator Sistem</p>
                <p class="font-bold text-slate-900 underline"><?= htmlspecialchars($_SESSION['nama_lengkap'] ?? 'Admin'); ?></p>
                <p class="text-[10px] text-slate-400">NIP / ID: 1998202610</p>
            </div>
        </div>

    </div>

    <!-- Pemicu otomatis dialog cetak saat halaman terbuka (Opsional) -->
    <script>
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>