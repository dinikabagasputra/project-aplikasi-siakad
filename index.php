<?php
require_once 'koneksi.php';
session_start();
if (!isset($_SESSION['login'])) { header("Location: login.php"); exit; }

// Fitur Pencarian
$keyword = $_GET['cari'] ?? '';
if (!empty($keyword)) {
    $stmt = $pdo->prepare("SELECT * FROM mahasiswa WHERE nim LIKE :keyword OR nama LIKE :keyword OR jurusan LIKE :keyword ORDER BY id DESC");
    $stmt->execute([':keyword' => "%$keyword%"]);
} else {
    $stmt = $pdo->prepare("SELECT * FROM mahasiswa ORDER BY id DESC");
    $stmt->execute();
}
$mahasiswa = $stmt->fetchAll();

// Hitung total mahasiswa
$totalMhs = $pdo->query("SELECT COUNT(*) FROM mahasiswa")->fetchColumn();

// Ambil data untuk grafik statistik jurusan
$stmtStat = $pdo->query("SELECT jurusan, COUNT(*) as total FROM mahasiswa GROUP BY jurusan");
$stats = $stmtStat->fetchAll();

$jurusanLabels = [];
$jurusanData = [];
foreach ($stats as $s) {
    if (!empty($s['jurusan'])) {
        $jurusanLabels[] = $s['jurusan'];
        $jurusanData[] = (int)$s['total'];
    }
}
if (empty($jurusanLabels)) {
    $jurusanLabels = ["Belum Ada Data"];
    $jurusanData = [0];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Akademik MHS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

    <!-- Include Navbar Modern -->
    <?php include 'navbar.php'; ?>

    <div class="max-w-6xl mx-auto px-4 mt-8">
        
        <!-- Header & Shortcut Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="lg:col-span-2 glass-panel p-8 rounded-3xl shadow-2xl flex flex-col justify-between relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-widest text-blue-400 bg-blue-500/10 border border-blue-500/20 px-3 py-1 rounded-full">Sistem Terpadu v2.0</span>
                    <h1 class="text-3xl font-extrabold text-white mt-3 tracking-tight">Dashboard Akademik</h1>
                    <p class="text-slate-400 text-sm mt-1">Kelola data mahasiswa, dan laporan akademik dengan cepat.</p>
                </div>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="tambah.php" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold px-5 py-3 rounded-2xl shadow-lg shadow-blue-500/25 transition text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Mahasiswa
                    </a>
                    <a href="ekspor_excel.php" class="inline-flex items-center gap-2 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 border border-emerald-500/20 font-semibold px-4 py-3 rounded-2xl transition text-sm">
                        📥 Unduh Excel
                    </a>
                    <a href="cetak_pdf.php" target="_blank" class="inline-flex items-center gap-2 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/20 font-semibold px-4 py-3 rounded-2xl transition text-sm">
                        📄 Cetak PDF
                    </a>
                </div>
            </div>

            <!-- Card Total Mahasiswa & Grafik Statistik -->
            <div class="glass-panel p-6 rounded-3xl shadow-2xl flex flex-col justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center font-bold text-xl shadow-md">🎓</div>
                    <div>
                        <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Total Mahasiswa</p>
                        <h3 class="text-2xl font-extrabold text-white mt-0.5"><?= $totalMhs; ?> <span class="text-xs font-medium text-slate-400">Orang</span></h3>
                    </div>
                </div>
                <div class="my-4 border-t border-slate-700/60"></div>
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Statistik Jurusan</p>
                    <div class="h-28 relative">
                        <canvas id="miniJurusanChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toolbar Pencarian -->
        <div class="glass-panel p-4 rounded-2xl shadow-xl mb-6 flex flex-col sm:flex-row justify-between items-center gap-4">
            <form method="GET" action="" class="w-full sm:w-96 flex gap-2">
                <input type="text" name="cari" value="<?= htmlspecialchars($keyword); ?>" placeholder="Cari NIM, Nama, Jurusan..." class="w-full px-4 py-2.5 bg-slate-900/60 border border-slate-700 rounded-xl text-sm text-slate-100 placeholder-slate-500 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition shadow-sm">Cari</button>
                <?php if (!empty($keyword)): ?>
                    <a href="index.php" class="bg-slate-800 hover:bg-slate-700 text-slate-300 px-4 py-2.5 rounded-xl text-sm font-semibold flex items-center justify-center transition">Reset</a>
                <?php endif; ?>
            </form>
            <div class="text-xs text-slate-400 font-medium px-2">
                Menampilkan <span class="font-bold text-white"><?= count($mahasiswa); ?></span> data
            </div>
        </div>

        <!-- Tabel Data Modern -->
        <div class="glass-panel rounded-3xl shadow-2xl overflow-hidden border border-slate-800">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-800/60 border-b border-slate-700/60 text-slate-400 text-xs font-bold uppercase tracking-wider">
                            <th class="p-4 pl-6">Foto</th>
                            <th class="p-4">NIM</th>
                            <th class="p-4">Nama</th>
                            <th class="p-4">Jurusan</th>
                            <th class="p-4">Email</th>
                            <th class="p-4 pr-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 text-sm font-medium">
                        <?php if (empty($mahasiswa)): ?>
                            <tr><td colspan="6" class="p-10 text-center text-slate-500">Tidak ada data mahasiswa ditemukan.</td></tr>
                        <?php else: ?>
                            <?php foreach ($mahasiswa as $row): 
                                $fotoPath = str_replace('uploads/', '', $row['foto'] ?? '');
                            ?>
                                <tr class="hover:bg-slate-800/40 transition duration-150">
                                    <td class="p-4 pl-6">
                                        <?php if (!empty($fotoPath) && file_exists("uploads/" . $fotoPath)): ?>
                                            <img src="uploads/<?= $fotoPath; ?>" class="w-10 h-10 rounded-full object-cover border border-slate-700 shadow-sm">
                                        <?php else: ?>
                                            <div class="w-10 h-10 rounded-full bg-slate-800 text-slate-400 flex items-center justify-center font-bold text-xs border border-slate-700">N/A</div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-4 font-bold text-slate-200"><?= htmlspecialchars($row['nim']); ?></td>
                                    <td class="p-4 font-bold text-white"><?= htmlspecialchars($row['nama']); ?></td>
                                    <td class="p-4"><span class="bg-blue-500/10 text-blue-400 border border-blue-500/20 text-xs font-bold px-3 py-1 rounded-full"><?= htmlspecialchars($row['jurusan']); ?></span></td>
                                    <td class="p-4 text-slate-400"><?= htmlspecialchars($row['email']); ?></td>
                                    <td class="p-4 pr-6 text-center">
                                        <div class="inline-flex gap-1.5">
                                            <a href="detail.php?id=<?= $row['id']; ?>" class="bg-sky-500/10 hover:bg-sky-500/20 text-sky-400 border border-sky-500/20 font-bold px-3 py-1.5 rounded-xl text-xs transition">Detail</a>
                                            <a href="edit.php?id=<?= $row['id']; ?>" class="bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 border border-amber-500/20 font-bold px-3 py-1.5 rounded-xl text-xs transition">Edit</a>
                                            <a href="hapus.php?id=<?= $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus data ini?')" class="bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/20 font-bold px-3 py-1.5 rounded-xl text-xs transition">Hapus</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Script Chart.js -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('miniJurusanChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?= json_encode($jurusanLabels); ?>,
                    datasets: [{
                        data: <?= json_encode($jurusanData); ?>,
                        backgroundColor: 'rgba(59, 130, 246, 0.5)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 1,
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 10 } } },
                        y: { beginAtZero: true, grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#94a3b8', stepSize: 1, font: { size: 10 } } }
                    }
                }
            });
        });
    </script>
</body>
</html>