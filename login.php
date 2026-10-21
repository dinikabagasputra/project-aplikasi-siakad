<?php
require_once 'koneksi.php';
session_start();

// Jika sudah login, lempar ke index
if (isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Username dan password wajib diisi!";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        // Pengecekan password teks biasa
        if ($user && $password === $user['password']) {
            $_SESSION['login'] = true;
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            header("Location: index.php");
            exit;
        } else {
            $error = "Username atau password salah!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Aplikasi Akademik</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-slate-900">Selamat Datang</h1>
            <p class="text-sm text-slate-500">Silakan login untuk mengelola data mahasiswa</p>
        </div>

        <?php if ($error): ?>
            <div class="mb-4 p-3 bg-rose-50 border-l-4 border-rose-500 text-rose-700 text-sm rounded">
                <?= $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Username</label>
                <input type="text" name="username" required class="w-full px-4 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm" placeholder="Contoh: admin">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                <input type="password" name="password" required class="w-full px-4 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm" placeholder="••••••••">
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 rounded-xl shadow-sm text-sm transition">Login</button>
        </form>
        <div class="mt-6 text-center text-xs text-slate-400">
            Login Default — Username: <span class="font-semibold text-slate-600">admin</span> | Password: <span class="font-semibold text-slate-600">bagas2007</span>
        </div>
    </div>
</body>
</html>