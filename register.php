<?php
require_once 'auth.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf($_POST['csrf_token'] ?? '')) {
        $error = "Security validation failed. Please try again.";
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $full_name = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = $_POST['role'] ?? 'patient';

        if (empty($username) || empty($password) || empty($full_name) || empty($email)) {
            $error = "All fields are required.";
        } else {
            try {
                // Check if username exists
                $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
                $stmt->execute([$username]);
                if ($stmt->fetch()) {
                    $error = "Username already taken.";
                } else {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO users (username, password, full_name, email, role) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$username, $hashed_password, $full_name, $email, $role]);

                    log_activity('User Registered', 'Auth', "New user registered: {$username} ({$role})");
                    $success = "Registration successful! You can now <a href='login.php' class='underline font-bold'>login</a>.";
                }
            } catch (PDOException $e) {
                $error = "Registration failed: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join DigitalRX.io</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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

<body class="min-h-screen flex items-center justify-center p-4">
    <div
        class="max-w-xl w-full bg-white/80 backdrop-blur-xl rounded-[40px] shadow-2xl p-10 border border-white/20 fade-in relative my-8">
        <!-- Theme Switcher -->
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

        <div class="text-center mb-8">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-white text-3xl mx-auto mb-4 shadow-xl transition-all"
                style="background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary))">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">Create Account</h1>
            <p class="text-slate-500 mt-1 font-medium text-sm">Join the DigitalRX healthcare network</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-2xl mb-6 flex items-center gap-3 border border-red-100 italic">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span class="text-xs font-bold">
                    <?php echo $error; ?>
                </span>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-green-50 text-green-600 p-4 rounded-2xl mb-6 flex items-center gap-3 border border-green-100">
                <i class="fa-solid fa-circle-check"></i>
                <span class="text-xs font-bold">
                    <?php echo $success; ?>
                </span>
            </div>
        <?php endif; ?>

        <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Full Name</label>
                <div class="relative">
                    <i class="fa-solid fa-signature absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                    <input type="text" name="full_name" required
                        class="w-full pl-11 pr-4 py-3 bg-gray-50/50 border border-transparent rounded-2xl outline-none focus:ring-2 focus:ring-primary focus:bg-white transition-all shadow-inner text-xs"
                        placeholder="John Doe">
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Email
                    Address</label>
                <div class="relative">
                    <i class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                    <input type="email" name="email" required
                        class="w-full pl-11 pr-4 py-3 bg-gray-50/50 border border-transparent rounded-2xl outline-none focus:ring-2 focus:ring-primary focus:bg-white transition-all shadow-inner text-xs"
                        placeholder="john@example.com">
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Username</label>
                <div class="relative">
                    <i class="fa-solid fa-at absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                    <input type="text" name="username" required
                        class="w-full pl-11 pr-4 py-3 bg-gray-50/50 border border-transparent rounded-2xl outline-none focus:ring-2 focus:ring-primary focus:bg-white transition-all shadow-inner text-xs"
                        placeholder="johndoe">
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Initial Role</label>
                <div class="relative">
                    <i class="fa-solid fa-id-card-clip absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                    <select name="role"
                        class="w-full pl-11 pr-4 py-3 bg-gray-50/50 border border-transparent rounded-2xl outline-none focus:ring-2 focus:ring-primary focus:bg-white transition-all shadow-inner text-xs appearance-none">
                        <option value="patient">Patient</option>
                        <option value="pharmacist">Pharmacist</option>
                        <option value="doctor">Doctor</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>
            </div>

            <div class="space-y-2 md:col-span-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Secure
                    Password</label>
                <div class="relative">
                    <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                    <input type="password" name="password" required
                        class="w-full pl-11 pr-4 py-3 bg-gray-50/50 border border-transparent rounded-2xl outline-none focus:ring-2 focus:ring-primary focus:bg-white transition-all shadow-inner text-xs"
                        placeholder="••••••••">
                </div>
            </div>

            <div class="md:col-span-2 py-2">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <button type="submit"
                    class="w-full py-4 text-white rounded-2xl font-black text-sm uppercase tracking-widest shadow-xl hover:scale-[1.01] active:scale-[0.99] transition-all"
                    style="background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary)); box-shadow: 0 10px 20px -5px var(--brand-primary)">
                    Register Account
                </button>
            </div>
        </form>

        <div class="text-center mt-8">
            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">
                Already have an account?
                <a href="login.php" class="text-blue-500 hover:text-blue-600 ml-1 transition-colors">Login Here</a>
            </p>
        </div>
    </div>
</body>

</html>