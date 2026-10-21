<?php
require_once 'koneksi.php';
session_start();
if (!isset($_SESSION['login'])) { header("Location: login.php"); exit; }

// Ambil data statistik per jurusan dari database
$stmt = $pdo->query("SELECT jurusan, COUNT(*) as total FROM mahasiswa GROUP BY jurusan");
$stats = $stmt->fetchAll();

$jurusanLabels = [];
$jurusanData = [];
foreach ($stats as $s) {
    $jurusanLabels[] = $s['jurusan'];
    $jurusanData[] = $s['total'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistik Akademik - AkademikMHS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

    <div class="max-w-4xl mx-auto px-4 mt-8">
        <div class="glass-panel p-8 rounded-3xl shadow-xl mb-6 flex justify-between items-center">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-blue-600 bg-blue-50 border border-blue-100 px-3 py-1 rounded-full">Analitik</span>
                <h1 class="text-2xl font-extrabold text-slate-900 mt-2">Statistik Mahasiswa per Jurusan</h1>
            </div>
            <a href="index.php" class="bg-slate-800 hover:bg-slate-900 text-white font-semibold px-4 py-2 rounded-xl text-sm transition">Kembali</a>
        </div>

        <div class="glass-panel p-8 rounded-3xl shadow-xl">
            <div style="width: 100%; max-width: 600px; margin: 0 auto;">
                <canvas id="jurusanChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('jurusanChart').getContext('2d');
        const jurusanChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($jurusanLabels); ?>,
                datasets: [{
                    label: 'Jumlah Mahasiswa',
                    data: <?= json_encode($jurusanData); ?>,
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(139, 92, 246, 0.7)',
                        'rgba(14, 165, 233, 0.7)',
                        'rgba(99, 102, 241, 0.7)'
                    ],
                    borderColor: [
                        'rgb(59, 130, 246)',
                        'rgb(139, 92, 246)',
                        'rgb(14, 165, 233)',
                        'rgb(99, 102, 241)'
                    ],
                    borderWidth: 2,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    </script>
</body>
</html>