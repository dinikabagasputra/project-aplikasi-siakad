<?php
require_once 'koneksi.php';
session_start();
if (!isset($_SESSION['login'])) { header("Location: login.php"); exit; }

$error = "";
$sukses = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['tambah_admin'])) {
    $username     = trim($_POST['username']);
    $password     = trim($_POST['password']);
    $nama_lengkap = trim($_POST['nama_lengkap']);

    if (empty($username) || empty($password) || empty($nama_lengkap)) {
        $error = "Semua field wajib diisi!";
    } else {
        try {
            $stmtCek = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
            $stmtCek->execute([':username' => $username]);
            if ($stmtCek->fetchColumn() > 0) {
                $error = "Username sudah digunakan oleh admin lain!";
            } else {
                $stmt = $pdo->prepare("INSERT INTO users (username, password, nama_lengkap) VALUES (:username, :password, :nama_lengkap)");
                $stmt->execute([
                    ':username'     => $username,
                    ':password'     => $password,
                    ':nama_lengkap' => $nama_lengkap
                ]);
                $sukses = "Admin baru berhasil ditambahkan!";
            }
        } catch (PDOException $e) {
            $error = "Gagal menambah admin: " . $e->getMessage();
        }
    }
}

$stmtUsers = $pdo->query("SELECT * FROM users ORDER BY id DESC");
$adminList = $stmtUsers->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Administrator - AkademikMHS</title>
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

    <div class="max-w-5xl mx-auto px-4 mt-8">
        <div class="glass-panel p-6 sm:p-8 rounded-3xl shadow-xl mb-6 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-widest text-blue-400 bg-blue-500/10 border border-blue-500/20 px-3 py-1 rounded-full">Keamanan Sistem</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white mt-2">Manajemen Administrator</h1>
                <p class="text-slate-400 text-sm mt-1">Kelola akun yang memiliki hak akses masuk ke dalam portal admin.</p>
            </div>
            <a href="index.php" class="bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold px-4 py-2.5 rounded-xl text-sm transition shadow-sm">Kembali ke Dashboard</a>
        </div>

        <?php if ($error): ?>
            <div class="mb-6 p-4 bg-rose-500/10 border-l-4 border-rose-500 text-rose-400 text-sm rounded-2xl shadow-sm"><?= $error; ?></div>
        <?php endif; ?>
        <?php if ($sukses): ?>
            <div class="mb-6 p-4 bg-emerald-500/10 border-l-4 border-emerald-500 text-emerald-400 text-sm rounded-2xl shadow-sm"><?= $sukses; ?></div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="glass-panel p-6 sm:p-8 rounded-3xl shadow-xl">
                <h2 class="text-lg font-bold text-white mb-4">Tambah Admin Baru</h2>
                <form method="POST" action="" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" required class="w-full px-4 py-2.5 bg-slate-900/60 border border-slate-700 rounded-xl text-sm text-slate-100 placeholder-slate-500 focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="Nama admin">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Username</label>
                        <input type="text" name="username" required class="w-full px-4 py-2.5 bg-slate-900/60 border border-slate-700 rounded-xl text-sm text-slate-100 placeholder-slate-500 focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="Username login">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Password</label>
                        <input type="password" name="password" required class="w-full px-4 py-2.5 bg-slate-900/60 border border-slate-700 rounded-xl text-sm text-slate-100 placeholder-slate-500 focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="••••••••">
                    </div>
                    <button type="submit" name="tambah_admin" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-xl text-sm shadow-md transition mt-2">Simpan Admin</button>
                </form>
            </div>

            <div class="md:col-span-2 glass-panel rounded-3xl shadow-xl overflow-hidden border border-slate-800">
                <div class="p-6 border-b border-slate-800">
                    <h2 class="text-lg font-bold text-white">Daftar Akun Administrator</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-800/60 border-b border-slate-800 text-slate-400 text-xs font-bold uppercase tracking-wider">
                                <th class="p-4 pl-6">No</th>
                                <th class="p-4">Nama Lengkap</th>
                                <th class="p-4">Username</th>
                                <th class="p-4 pr-6 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800 text-sm font-medium">
                            <?php $no = 1; foreach ($adminList as $usr): ?>
                            <tr class="hover:bg-slate-800/40 transition">
                                <td class="p-4 pl-6 text-slate-400"><?= $no++; ?></td>
                                <td class="p-4 font-bold text-white"><?= htmlspecialchars($usr['nama_lengkap']); ?></td>
                                <td class="p-4 font-semibold text-blue-400"><?= htmlspecialchars($usr['username']); ?></td>
                                <td class="p-4 pr-6 text-center">
                                    <?php if ($usr['username'] !== 'admin'): ?>
                                        <a href="hapus_user.php?id=<?= $usr['id']; ?>" onclick="return confirm('Yakin ingin menghapus akun admin ini?')" class="bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/20 font-bold px-3 py-1.5 rounded-xl text-xs transition">Hapus</a>
                                    <?php else: ?>
                                        <span class="text-xs text-slate-400 font-semibold bg-slate-800 px-2.5 py-1 rounded-lg border border-slate-700">Utama</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>