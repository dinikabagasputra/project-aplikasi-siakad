<?php
require_once 'koneksi.php';
session_start();
if (!isset($_SESSION['login'])) { header("Location: login.php"); exit; }

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: index.php"); exit; }

$stmt = $pdo->prepare("SELECT * FROM mahasiswa WHERE id = :id");
$stmt->execute([':id' => $id]);
$mhs = $stmt->fetch();
if (!$mhs) { header("Location: index.php"); exit; }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Mahasiswa - <?= htmlspecialchars($mhs['nama']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .bg-mesh {
            background-color: #0f172a;
            background-image: 
                radial-gradient(at 0% 0%, rgba(59, 130, 246, 0.2) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(139, 92, 246, 0.2) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(14, 165, 233, 0.15) 0px, transparent 50%);
        }
        .glass-panel {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.7);
        }
    </style>
</head>
<body class="bg-slate-900 text-slate-800 min-h-screen relative pb-16">
    <div class="fixed inset-0 bg-mesh -z-10"></div>
    <?php include 'navbar.php'; ?>

    <div class="max-w-xl mx-auto px-4 mt-10">
        <div class="glass-panel p-8 sm:p-10 rounded-3xl shadow-2xl text-center relative overflow-hidden">
            <div class="mb-6">
                <?php if (!empty($mhs['foto']) && file_exists("uploads/" . $mhs['foto'])): ?>
                    <img src="uploads/<?= $mhs['foto']; ?>" alt="Foto" class="w-28 h-28 rounded-full object-cover mx-auto ring-4 ring-blue-500/20 shadow-lg">
                <?php else: ?>
                    <div class="w-28 h-28 rounded-full bg-slate-200 flex items-center justify-center text-slate-500 font-bold text-2xl mx-auto ring-4 ring-blue-500/20 shadow-inner">N/A</div>
                <?php endif; ?>
                <h2 class="text-2xl font-extrabold text-slate-900 mt-4"><?= htmlspecialchars($mhs['nama']); ?></h2>
                <span class="inline-block mt-2 bg-blue-50 text-blue-700 border border-blue-100 text-xs font-bold px-3 py-1 rounded-full"><?= htmlspecialchars($mhs['jurusan']); ?></span>
            </div>

            <div class="bg-white/60 rounded-2xl p-6 text-left space-y-3 border border-slate-200/60 shadow-sm mb-6">
                <div class="flex justify-between border-b border-slate-200/60 pb-2">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">NIM</span>
                    <span class="text-sm font-semibold text-slate-800"><?= htmlspecialchars($mhs['nim']); ?></span>
                </div>
                <div class="flex justify-between border-b border-slate-200/60 pb-2">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Email</span>
                    <span class="text-sm font-semibold text-slate-800"><?= htmlspecialchars($mhs['email']); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tanggal Terdaftar</span>
                    <span class="text-sm font-semibold text-slate-800"><?= $mhs['created_at'] ?? 'Tidak tercatat'; ?></span>
                </div>
            </div>

            <div class="flex items-center justify-center gap-3">
                <a href="index.php" class="bg-slate-800 hover:bg-slate-900 text-white font-semibold px-6 py-2.5 rounded-xl text-sm transition shadow-md">Kembali ke Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>