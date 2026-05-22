<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/../app/Services/AuthService.php';

// عملية تسجيل الخروج
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit();
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $authService = new AuthService();
    if ($authService->login($email, $password)) {
        header('Location: index.php');
        exit();
    } else {
        $error = "البريد الإلكتروني أو كلمة المرور غير صحيحة.";
    }
}
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>ArenaSync - Connexion</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#0b0f19] text-slate-300 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-slate-900/40 border border-slate-900 p-8 rounded-3xl backdrop-blur-sm shadow-2xl">

        <div class="text-center mb-8">

            <h1 class="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-cyan-400 mb-2">
                ArenaSync
            </h1>

            <p class="text-xs text-slate-500">
                Connectez-vous pour demander de l'aide ou aider vos collègues
            </p>

        </div>

        <?php if ($error): ?>

            <div class="mb-4 p-3 bg-rose-950/40 border border-rose-900 text-rose-400 rounded-xl text-xs text-center">
                ⚠️ <?= htmlspecialchars($error) ?>
            </div>

        <?php endif; ?>

        <form method="POST" class="space-y-4">

            <div>

                <label class="block text-xs font-medium text-slate-400 mb-1.5">
                    Adresse e-mail
                </label>

                <input 
                    type="email" 
                    name="email" 
                    required 
                    placeholder="name@enaa.ma"
                    class="w-full bg-slate-950/80 border border-slate-800 text-slate-200 placeholder-slate-700 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-500 transition">

            </div>

            <div>

                <label class="block text-xs font-medium text-slate-400 mb-1.5">
                    Mot de passe
                </label>

                <input 
                    type="password" 
                    name="password" 
                    required 
                    placeholder="••••••••"
                    class="w-full bg-slate-950/80 border border-slate-800 text-slate-200 placeholder-slate-700 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-500 transition">

            </div>

            <button 
                type="submit"
                class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-medium text-sm py-2.5 rounded-xl transition shadow-lg shadow-indigo-600/10 mt-2">

                Se connecter

            </button>

        </form>

    </div>

</body>
</html>