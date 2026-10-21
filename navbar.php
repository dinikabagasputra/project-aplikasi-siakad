<nav class="sticky top-0 z-50 backdrop-blur-xl bg-slate-900/80 border-b border-slate-800/80 px-4 lg:px-8 py-3.5 transition-all">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
        <!-- Logo & Brand -->
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-tr from-blue-600 to-indigo-500 text-white font-extrabold text-lg rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30 ring-2 ring-white/10">
                AM
            </div>
            <div>
                <span class="font-extrabold text-white text-base tracking-tight block">AkademikMHS</span>
                <span class="text-[10px] font-semibold text-blue-400 uppercase tracking-widest block">Portal Admin</span>
            </div>
        </div>

        <!-- Menu Navigasi Tengah -->
        <div class="flex items-center gap-1 bg-slate-800/50 p-1.5 rounded-2xl border border-slate-700/50">
            <a href="index.php" class="px-4 py-2 rounded-xl text-xs font-semibold text-white bg-blue-600 shadow-sm transition">Dashboard</a>
            <a href="tambah.php" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:text-white hover:bg-slate-700/50 transition">Tambah Data</a>
        </div>

        <!-- Profil, Tombol Kelola Admin & Logout -->
        <div class="flex items-center gap-3">
            <!-- Tombol Manajemen Admin -->
            <a href="users.php" class="hidden sm:inline-flex items-center gap-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 px-3.5 py-2 rounded-xl text-xs font-bold transition shadow-sm">
                ⚙️ Manajemen Admin
            </a>

            <div class="hidden sm:block text-right ml-1">
                <span class="text-xs text-slate-400 block">Halo,</span>
                <span class="text-sm font-bold text-slate-200"><?= htmlspecialchars($_SESSION['nama_lengkap'] ?? 'Administrator'); ?></span>
            </div>
            <div class="h-8 w-[1px] bg-slate-800 hidden sm:block"></div>
            
            <a href="logout.php" onclick="return confirm('Keluar dari sistem?')" class="group flex items-center gap-2 bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white px-4 py-2 rounded-xl text-xs font-bold transition-all border border-rose-500/20 shadow-sm">
                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                <span>Logout</span>
            </a>
        </div>
    </div>
</nav>