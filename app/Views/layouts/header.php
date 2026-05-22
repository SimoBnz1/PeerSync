<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <!DOCTYPE html>

    <title>ArenaSync - Help Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#0b0f19] text-slate-300 min-h-screen font-sans selection:bg-indigo-500 selection:text-white">

    <!-- القائمة العلوية -->
    <nav class="border-b border-slate-900 bg-slate-950/60 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="text-2xl font-black text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-cyan-400">ArenaSync</span>
                <span class="text-[10px] uppercase tracking-widest bg-indigo-500/10 text-indigo-400 px-2 py-0.5 rounded border border-indigo-500/20">Peer Help</span>
            </div>
            
            <div class="flex items-center gap-4 text-sm">
                <span class="text-slate-400">Hello <strong class="text-slate-200"><?= htmlspecialchars($_SESSION['user_name'] ?? 'المستخدم') ?></strong></span>
                <a href="login.php?logout=1" class="text-slate-500 hover:text-rose-400 transition font-medium">logout </a>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-6 py-10">