<?php require_once 'auth.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo isset($page_title) ? $page_title . " - " . APP_NAME : APP_NAME . " - Clinic Dashboard"; ?>
    </title>
    <!-- Tailwind CSS -->
    <script src="assets/js/tailwind.js"></script>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="assets/css/fontawesome.min.css">
    <!-- Google Fonts -->
    <link href="assets/css/google-fonts.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="assets/js/chart.min.js"></script>
    <style>
        :root {
            --brand-primary: #3b82f6;
            /* Default Blue */
            --brand-secondary: #6366f1;
            --brand-bg: #f8fafc;
        }

        [data-theme="midnight"] {
            --brand-primary: #a855f7;
            /* Purple */
            --brand-secondary: #ec4899;
            --brand-bg: #0f172a;
        }

        [data-theme="mint"] {
            --brand-primary: #10b981;
            /* Emerald */
            --brand-secondary: #06b6d4;
            --brand-bg: #f1f5f9;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--brand-bg);
            transition: background-color 0.3s ease;
        }

        .text-primary {
            color: var(--brand-primary);
        }

        .bg-primary {
            background-color: var(--brand-primary);
        }

        .border-primary {
            border-color: var(--brand-primary);
        }

        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .sidebar-item:hover {
            background-color: #f1f5f9;
            color: var(--brand-primary);
        }

        .sidebar-item.active {
            background-color: var(--brand-primary);
            color: white;
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
        // Apply theme immediately to prevent flicker
        const savedTheme = localStorage.getItem('clinic_theme') || 'ocean';
        document.documentElement.setAttribute('data-theme', savedTheme);

        function setTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('clinic_theme', theme);
        }
    </script>
</head>

<body class="bg-gray-50 text-gray-800 h-screen flex overflow-hidden">
    <!-- Global Notifications -->
    <div class="fixed top-6 right-6 z-[200] space-y-3 pointer-events-none">
        <?php if (isset($_SESSION['success_msg'])): ?>
            <div class="bg-emerald-500 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3 animate-slide-in pointer-events-auto min-w-[300px]">
                <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <p class="font-bold text-sm"><?php echo $_SESSION['success_msg']; ?></p>
                <button onclick="this.parentElement.remove()" class="ml-auto text-white/60 hover:text-white transition-colors">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <?php unset($_SESSION['success_msg']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_msg'])): ?>
            <div class="bg-rose-500 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3 animate-slide-in pointer-events-auto min-w-[300px]">
                <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-circle-exclamation"></i>
                </div>
                <p class="font-bold text-sm"><?php echo $_SESSION['error_msg']; ?></p>
                <button onclick="this.parentElement.remove()" class="ml-auto text-white/60 hover:text-white transition-colors">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <?php unset($_SESSION['error_msg']); ?>
        <?php endif; ?>
    </div>

    <style>
        .animate-slide-in {
            animation: slideIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>