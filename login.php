<?php
require_once 'auth.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf($_POST['csrf_token'] ?? '')) {
        $error = "Security validation failed. Please try again.";
    } else {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['avatar'] = $user['avatar'];

                log_activity('Successful Login', 'Auth', "User {$username} logged in.");
                header("Location: dashboard.php");
                exit;
            } else {
                log_activity('Failed Login Attempt', 'Auth', "Failed login attempt for username: {$username}");
                $error = "Invalid username or password.";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login -
        <?php echo APP_NAME; ?>
    </title>
    <script src="assets/js/tailwind.js"></script>
    <link rel="stylesheet" href="assets/css/fontawesome.min.css">
    <link href="assets/css/google-fonts.css" rel="stylesheet">
    <style>
        :root {
            --brand-primary: #3b82f6;
            --brand-secondary: #6366f1;
            --brand-bg: #f8fafc;
        }

        [data-theme="midnight"] {
            --brand-primary: #a855f7;
            --brand-secondary: #ec4899;
            --brand-bg: #0f172a;
        }

        [data-theme="mint"] {
            --brand-primary: #10b981;
            --brand-secondary: #06b6d4;
            --brand-bg: #f1f5f9;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--brand-bg);
            transition: background-color 0.3s ease;
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
    <script>
        const savedTheme = localStorage.getItem('clinic_theme') || 'ocean';
        document.documentElement.setAttribute('data-theme', savedTheme);
        function setTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('clinic_theme', theme);
        }
    </script>
</head>

<body class="h-screen flex items-center justify-center p-4">
    <div
        class="max-w-md w-full bg-white/80 backdrop-blur-xl rounded-[40px] shadow-2xl p-10 border border-white/20 fade-in relative">
        <!-- Theme Switcher for Login -->
        <div class="absolute top-6 right-6 flex bg-gray-100/50 p-1 rounded-xl backdrop-blur-sm">
            <button onclick="setTheme('ocean')"
                class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-white transition-all text-blue-500"><i
                    class="fa-solid fa-droplet"></i></button>
            <button onclick="setTheme('midnight')"
                class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-white transition-all text-purple-500"><i
                    class="fa-solid fa-moon"></i></button>
            <button onclick="setTheme('mint')"
                class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-white transition-all text-emerald-500"><i
                    class="fa-solid fa-leaf"></i></button>
        </div>

        <div class="text-center mb-10">
            <div class="w-20 h-20 rounded-3xl flex items-center justify-center text-white text-4xl mx-auto mb-6 shadow-2xl transition-all"
                style="background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary))">
                <i class="fa-solid fa-user-doctor"></i>
            </div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">DigitalRX<span
                    style="color: var(--brand-primary)">.io</span></h1>
            <p class="text-slate-500 mt-2 font-medium">Elevating Clinic Management</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-2xl mb-8 flex items-center gap-3 border border-red-100">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span class="text-sm font-bold">
                    <?php echo $error; ?>
                </span>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-8">
            <div class="space-y-3">
                <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Username</label>
                <div class="relative">
                    <i class="fa-solid fa-user absolute left-5 top-1/2 -translate-y-1/2 text-slate-300"></i>
                    <input type="text" name="username" required
                        class="w-full pl-12 pr-6 py-4 bg-gray-50/50 border border-transparent rounded-[20px] outline-none focus:ring-2 focus:ring-primary focus:bg-white transition-all shadow-inner text-sm"
                        placeholder="admin">
                </div>
            </div>

            <div class="space-y-3">
                <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Password</label>
                <div class="relative">
                    <i class="fa-solid fa-lock absolute left-5 top-1/2 -translate-y-1/2 text-slate-300"></i>
                    <input type="password" name="password" id="password-input" required
                        class="w-full pl-12 pr-14 py-4 bg-gray-50/50 border border-transparent rounded-[20px] outline-none focus:ring-2 focus:ring-primary focus:bg-white transition-all shadow-inner text-sm"
                        placeholder="••••••••">
                    <button type="button" id="toggle-password"
                        class="absolute right-5 top-1/2 -translate-y-1/2 text-slate-300 hover:text-slate-500 transition-colors"
                        onclick="togglePassword()">
                        <i id="eye-icon" class="fa-solid fa-eye"></i>
                    </button>
                </div>
            </div>

            <script>
                function togglePassword() {
                    const input = document.getElementById('password-input');
                    const icon  = document.getElementById('eye-icon');
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.replace('fa-eye', 'fa-eye-slash');
                    } else {
                        input.type = 'password';
                        icon.classList.replace('fa-eye-slash', 'fa-eye');
                    }
                }
            </script>

            <div class="flex items-center justify-between px-1">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <div class="relative">
                        <input type="checkbox" class="sr-only peer">
                        <div
                            class="w-5 h-5 bg-slate-100 border border-slate-200 rounded-md peer-checked:bg-primary peer-checked:border-primary transition-all flex items-center justify-center">
                            <i class="fa-solid fa-check text-white text-[10px]"></i>
                        </div>
                    </div>
                    <span
                        class="text-xs text-slate-500 font-bold group-hover:text-slate-800 transition-colors uppercase tracking-widest">Remember</span>
                </label>
                <a href="#"
                    class="text-xs font-black text-slate-400 hover:text-primary transition-colors uppercase tracking-widest">Help?</a>
            </div>

            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <button type="submit"
                class="w-full py-5 text-white rounded-[20px] font-black text-sm uppercase tracking-widest shadow-2xl hover:scale-[1.02] active:scale-[0.98] transition-all"
                style="background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary)); box-shadow: 0 15px 30px -10px var(--brand-primary)">
                Unlock Portal
            </button>

            <div class="pt-4 border-t border-slate-100/50">
                <a href="register.php"
                    class="w-full py-5 flex items-center justify-center border-2 border-slate-100 text-slate-400 rounded-[20px] font-black text-sm uppercase tracking-widest hover:bg-slate-50 hover:text-slate-600 hover:border-slate-200 transition-all">
                    Create New Account
                </a>
            </div>
        </form>

        <p class="text-center mt-10 text-slate-400 text-[10px] font-black uppercase tracking-[0.2em]">
            Enterprise Security v2.0
        </p>
    </div>
</body>

</html>