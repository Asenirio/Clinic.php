<?php
session_start();

// Handle Profile Updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $_SESSION['clinic_name'] = $_POST['clinic_name'] ?? "Northstar Clinic";
    $_SESSION['admin_name'] = $_POST['admin_name'] ?? "Dr Paul Malone";
    $_SESSION['clinic_email'] = $_POST['clinic_email'] ?? "northstar.digitalrx@gmail.com";
    $_SESSION['clinic_phone'] = $_POST['clinic_phone'] ?? "91 1958363346";
    $_SESSION['success_msg'] = "Profile updated successfully!";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// DigitalRX.io - Clinic Dashboard Configuration
$clinic_name = $_SESSION['clinic_name'] ?? "Northstar Clinic";
$admin_name = $_SESSION['admin_name'] ?? "Dr Paul Malone";
$clinic_email = $_SESSION['clinic_email'] ?? "northstar.digitalrx@gmail.com";
$clinic_phone = $_SESSION['clinic_phone'] ?? "91 1958363346";
$clinic_country = "Kenya";
$clinic_timezone = "E. Africa Standard Time";

$stats = [
    ['label' => 'Doctors', 'value' => 10, 'icon' => 'fa-user-doctor'],
    ['label' => 'Patients', 'value' => 171, 'icon' => 'fa-hospital-user'],
    ['label' => 'Appointments', 'value' => 750, 'icon' => 'fa-regular fa-calendar-check'],
    ['label' => 'Available Beds', 'value' => 24, 'icon' => 'fa-solid fa-bed'],
];

$nav_items = [
    ['label' => 'Dashboard', 'icon' => 'fa-chart-pie', 'active' => false, 'url' => '#'],
    ['label' => 'My Account', 'icon' => 'fa-user', 'active' => true, 'url' => 'clinicportal_dashboard.php'],
    ['label' => 'Appointments', 'icon' => 'fa-regular fa-calendar-check', 'active' => false, 'has_submenu' => true, 'url' => 'javascript:comingSoon("Appointments")'],
    ['label' => 'Patients', 'icon' => 'fa-solid fa-bed-pulse', 'active' => false, 'url' => 'javascript:comingSoon("Patients")'],
    ['label' => 'Doctors', 'icon' => 'fa-solid fa-stethoscope', 'active' => false, 'url' => 'javascript:comingSoon("Doctors")'],
    ['label' => 'Pharmacy', 'icon' => 'fa-solid fa-pills', 'active' => false, 'url' => 'javascript:comingSoon("Pharmacy")'],
    ['label' => 'Treatment', 'icon' => 'fa-solid fa-syringe', 'active' => false, 'url' => 'javascript:comingSoon("Treatment")'],
    ['label' => 'Specialty', 'icon' => 'fa-solid fa-star', 'active' => false, 'url' => 'javascript:comingSoon("Specialty")'],
    ['label' => 'Chat', 'icon' => 'fa-regular fa-comments', 'active' => false, 'badge' => true, 'url' => 'javascript:comingSoon("Chat")'],
    ['label' => 'Content', 'icon' => 'fa-regular fa-newspaper', 'active' => false, 'has_submenu' => true, 'url' => 'javascript:comingSoon("Content")'],
    ['label' => 'Promotions', 'icon' => 'fa-solid fa-bullhorn', 'active' => false, 'has_submenu' => true, 'url' => 'javascript:comingSoon("Promotions")'],
    ['label' => 'Enquiries', 'icon' => 'fa-solid fa-circle-question', 'active' => false, 'url' => 'javascript:comingSoon("Enquiries")'],
    ['label' => 'Email Templates', 'icon' => 'fa-solid fa-envelope-open-text', 'active' => false, 'url' => 'javascript:comingSoon("Email Templates")'],
    ['label' => 'SEO', 'icon' => 'fa-solid fa-globe', 'active' => false, 'url' => 'javascript:comingSoon("SEO")'],
];

$recent_activities = [
    ['type' => 'patient', 'title' => 'New patient registered', 'meta' => 'Sarah Johnson • 2 mins ago', 'icon' => 'fa-user-plus', 'bg' => 'bg-blue-100', 'text' => 'text-blue-600'],
    ['type' => 'appointment', 'title' => 'Appointment confirmed', 'meta' => 'Dr. Malone • 15 mins ago', 'icon' => 'fa-check', 'bg' => 'bg-green-100', 'text' => 'text-green-600'],
    ['type' => 'report', 'title' => 'Report updated', 'meta' => 'Lab results • 1 hour ago', 'icon' => 'fa-file-medical', 'bg' => 'bg-purple-100', 'text' => 'text-purple-600'],
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DigitalRX.io - Clinic Dashboard</title>
    <!-- Tailwind CSS -->
    <script src="assets/js/tailwind.js"></script>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="assets/css/fontawesome.min.css">
    <!-- Google Fonts -->
    <link href="assets/css/google-fonts.css" rel="stylesheet">
    <!-- Chart.js for interactivity -->
    <script src="assets/js/chart.min.js"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
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
            color: #0ea5e9;
        }

        .sidebar-item.active {
            background-color: #0ea5e9;
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
</head>

<body class="bg-gray-50 text-gray-800 h-screen flex overflow-hidden">

    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r border-gray-200 flex flex-col hidden md:flex z-20 shadow-lg">
        <div class="h-16 flex items-center px-6 border-b border-gray-100">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white mr-3">
                <i class="fa-solid fa-user-doctor"></i>
            </div>
            <span class="text-xl font-bold text-gray-700">DigitalRX<span class="text-blue-600">.io</span></span>
        </div>

        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
            <?php foreach ($nav_items as $item): ?>
                <a href="<?php echo $item['url']; ?>"
                    class="sidebar-item <?php echo $item['active'] ? 'active' : 'text-gray-600'; ?> flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors group">
                    <div class="relative">
                        <i class="<?php echo $item['icon']; ?> w-6 text-center mr-2"></i>
                        <?php if (isset($item['badge']) && $item['badge']): ?>
                            <span
                                class="absolute top-0 right-0 -mt-1 -mr-1 h-2 w-2 rounded-full bg-green-500 border-2 border-white"></span>
                        <?php endif; ?>
                    </div>
                    <?php echo $item['label']; ?>
                    <?php if (isset($item['has_submenu']) && $item['has_submenu']): ?>
                        <i
                            class="fa-solid fa-chevron-right ml-auto text-xs opacity-0 group-hover:opacity-100 transition-opacity"></i>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="p-4 border-t border-gray-100">
            <button onclick="openModal('help-modal')"
                class="w-full flex items-center px-3 py-2.5 rounded-lg text-purple-600 bg-purple-50 hover:bg-purple-100 text-sm font-medium transition-colors">
                <i class="fa-solid fa-circle-info w-6 text-center mr-2"></i>
                Need Help?
            </button>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <header
            class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8 z-10">
            <div class="flex items-center">
                <button id="mobile-menu-toggle"
                    class="md:hidden p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 mr-2">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
                <div>
                    <h1 class="text-xl font-semibold text-gray-800">My Account</h1>
                    <p class="text-xs text-gray-500 hidden sm:block">Dashboard - My Account</p>
                </div>
            </div>

            <div class="flex items-center space-x-3 sm:space-x-4">
                <button onclick="openModal('appointment-modal')"
                    class="bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium py-2 px-4 rounded-md shadow transition-colors flex items-center gap-2">
                    <i class="fa-regular fa-calendar-plus"></i>
                    <span class="hidden sm:inline">Book Appointment</span>
                </button>
                <button onclick="openModal('guide-modal')"
                    class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 text-sm font-medium py-2 px-3 rounded-md shadow-sm transition-colors flex items-center gap-2">
                    <i class="fa-solid fa-rocket text-green-500"></i>
                    <span class="hidden sm:inline">Setup guide</span>
                </button>
                <div class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer hover:text-gray-900">
                    <span>English</span>
                    <img src="https://flagcdn.com/w20/us.png" alt="US Flag" class="h-4 w-auto rounded-sm">
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 bg-gray-50">
            
            <?php if (isset($_SESSION['success_msg'])): ?>
                <div id="toast-success" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-bounce flex items-center gap-2">
                    <i class="fa-solid fa-circle-check"></i>
                    <span><?php echo $_SESSION['success_msg'];
                    unset($_SESSION['success_msg']); ?></span>
                    <button onclick="this.parentElement.remove()" class="ml-4"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <script>setTimeout(() => document.getElementById('toast-success')?.remove(), 5000);</script>
            <?php endif; ?>
            <div class="max-w-7xl mx-auto space-y-6 fade-in">
                <!-- Profile Summary -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6">
                        <div class="flex flex-col lg:flex-row gap-6 items-start lg:items-center justify-between">
                            <div class="flex items-center gap-5">
                                <div class="relative">
                                    <div
                                        class="w-20 h-20 bg-blue-50 rounded-xl flex items-center justify-center border border-blue-100">
                                        <i class="fa-solid fa-heart-pulse text-blue-600 text-4xl"></i>
                                    </div>
                                    <div
                                        class="absolute -bottom-1 -right-1 bg-blue-600 text-white text-[10px] px-1.5 py-0.5 rounded-md shadow-sm">
                                        Admin</div>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-800"><?php echo $clinic_name; ?></h2>
                                    <div class="flex items-center text-gray-500 text-sm mt-1">
                                        <i class="fa-solid fa-shield-halved text-gray-400 mr-1.5 text-xs"></i>Admin
                                    </div>
                                </div>
                            </div>

                            <div
                                class="flex gap-8 sm:gap-12 border-l-0 lg:border-l border-gray-100 pl-0 lg:pl-6 w-full lg:w-auto justify-between sm:justify-start">
                                <?php foreach ($stats as $stat): ?>
                                        <div class="text-center lg:text-left">
                                            <div class="flex items-center justify-center lg:justify-start gap-2 mb-1">
                                                <i class="<?php echo $stat['icon']; ?> text-gray-400 text-sm"></i>
                                                <span
                                                    class="text-xl font-bold text-gray-800"><?php echo $stat['value']; ?></span>
                                            </div>
                                            <p class="text-xs text-gray-500"><?php echo $stat['label']; ?></p>
                                        </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="hidden xl:block">
                                <div
                                    class="w-32 h-32 bg-blue-900 rounded-lg flex flex-col items-center justify-center text-white shadow-lg relative overflow-hidden group cursor-pointer hover:scale-105 transition-transform duration-300">
                                    <div
                                        class="absolute top-0 right-0 w-16 h-16 bg-white opacity-5 rounded-full -mr-8 -mt-8">
                                    </div>
                                    <div
                                        class="absolute bottom-0 left-0 w-12 h-12 bg-blue-400 opacity-10 rounded-full -ml-6 -mb-6">
                                    </div>
                                    <span class="text-[10px] uppercase tracking-widest mb-2 opacity-70">Active
                                        Plan</span>
                                    <i
                                        class="fa-solid fa-bolt text-yellow-400 text-2xl mb-1 group-hover:animate-pulse"></i>
                                    <span class="font-bold text-lg tracking-wide">PREMIUM</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 px-6">
                        <nav class="flex space-x-6 overflow-x-auto" aria-label="Tabs">
                            <?php
                            $tabs = ['overview' => 'Overview', 'settings' => 'Settings', 'branches' => 'Branches', 'subscription' => 'Subscription'];
                            foreach ($tabs as $key => $label):
                                ?>
                                    <button onclick="switchTab('<?php echo $key; ?>')" id="tab-<?php echo $key; ?>"
                                        class="tab-btn border-b-2 <?php echo $key === 'overview' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-4 px-1 text-sm font-medium">
                                        <?php echo $label; ?>
                                    </button>
                            <?php endforeach; ?>
                        </nav>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Clinic Details Card -->
                        <div id="panel-overview"
                            class="tab-panel bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-lg font-bold text-gray-800">Clinic Details</h3>
                                <button onclick="toggleEditMode()"
                                    class="bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium py-2 px-4 rounded shadow-sm transition-all flex items-center gap-2">
                                    <i class="fa-solid fa-pen-to-square"></i>Edit Profile
                                </button>
                            </div>

                            <form id="clinic-form" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8">
                                <input type="hidden" name="action" value="update_profile">
                                <div class="view-mode space-y-1">
                                    <label
                                        class="block text-xs font-medium text-gray-400 uppercase tracking-wider">Admin</label>
                                    <p class="text-gray-800 font-medium"><?php echo $admin_name; ?></p>
                                </div>
                                <div class="view-mode space-y-1">
                                    <label
                                        class="block text-xs font-medium text-gray-400 uppercase tracking-wider">Clinic
                                        Name</label>
                                    <p class="text-gray-800 font-medium"><?php echo $clinic_name; ?></p>
                                </div>
                                <div class="view-mode space-y-1">
                                    <label
                                        class="block text-xs font-medium text-gray-400 uppercase tracking-wider">Email</label>
                                    <p class="text-gray-800 font-medium break-all"><?php echo $clinic_email; ?></p>
                                </div>
                                <div class="view-mode space-y-1">
                                    <label
                                        class="block text-xs font-medium text-gray-400 uppercase tracking-wider">Contact
                                        Number</label>
                                    <p class="text-gray-800 font-medium"><?php echo $clinic_phone; ?></p>
                                </div>
                                <div class="view-mode space-y-1">
                                    <label
                                        class="block text-xs font-medium text-gray-400 uppercase tracking-wider">Country</label>
                                    <p class="text-gray-800 font-medium"><?php echo $clinic_country; ?></p>
                                </div>
                                <div class="view-mode space-y-1 md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider">Time
                                        Zone</label>
                                    <p class="text-gray-800 font-medium"><?php echo $clinic_timezone; ?></p>
                                </div>

                                <div class="edit-mode hidden space-y-1">
                                    <label class="block text-sm font-medium text-gray-700">Admin Name</label>
                                    <input type="text" name="admin_name" value="<?php echo $admin_name; ?>"
                                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 outline-none">
                                </div>
                                <div class="edit-mode hidden space-y-1">
                                    <label class="block text-sm font-medium text-gray-700">Clinic Name</label>
                                    <input type="text" name="clinic_name" value="<?php echo $clinic_name; ?>"
                                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 outline-none">
                                </div>
                                <div class="edit-mode hidden space-y-1">
                                    <label class="block text-sm font-medium text-gray-700">Email</label>
                                    <input type="email" name="clinic_email" value="<?php echo $clinic_email; ?>"
                                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 outline-none">
                                </div>
                                <div class="edit-mode hidden space-y-1">
                                    <label class="block text-sm font-medium text-gray-700">Phone</label>
                                    <input type="text" name="clinic_phone" value="<?php echo $clinic_phone; ?>"
                                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 outline-none">
                                </div>

                                <div class="edit-mode hidden md:col-span-2 flex justify-end gap-3 mt-4">
                                    <button type="button" onclick="toggleEditMode()"
                                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                                    <button type="submit"
                                        class="px-4 py-2 bg-blue-600 rounded-md text-sm font-medium text-white hover:bg-blue-700 shadow-md">Save
                                        Changes</button>
                                </div>
                            </form>
                        </div>

                        <!-- Panel Templates -->
                        <div id="panel-settings"
                            class="tab-panel hidden bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-6">System Settings</h3>
                            <div class="space-y-4">
                                <div
                                    class="flex items-center justify-between p-4 border border-gray-100 rounded-lg hover:bg-gray-50 transition-colors">
                                    <div>
                                        <p class="font-medium text-gray-800">Email Notifications</p>
                                        <p class="text-sm text-gray-500">Receive daily summaries and alerts.</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" value="" class="sr-only peer" checked>
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                        </div>
                                    </label>
                                </div>
                                <div
                                    class="flex items-center justify-between p-4 border border-gray-100 rounded-lg hover:bg-gray-50 transition-colors">
                                    <div>
                                        <p class="font-medium text-gray-800">Two-Factor Authentication</p>
                                        <p class="text-sm text-gray-500">Secure your account with 2FA.</p>
                                    </div>
                                    <button class="text-blue-600 text-sm font-medium hover:underline">Enable</button>
                                </div>
                            </div>
                        </div>

                        <div id="panel-branches"
                            class="tab-panel hidden bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-lg font-bold text-gray-800">Clinic Branches</h3>
                                <button class="text-sm text-blue-600 font-medium hover:underline">+ Add Branch</button>
                            </div>
                            <div class="space-y-4">
                                <div class="flex items-start gap-4 p-4 border border-blue-100 bg-blue-50 rounded-lg">
                                    <div
                                        class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-blue-600 shadow-sm">
                                        <i class="fa-solid fa-location-dot"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-800">Main Headquarters</h4>
                                        <p class="text-sm text-gray-600">Nairobi, Kenya • 10 Doctors • Active</p>
                                    </div>
                                    <button class="ml-auto text-gray-400 hover:text-blue-600"><i
                                            class="fa-solid fa-ellipsis-vertical"></i></button>
                                </div>
                            </div>
                        </div>

                        <div id="panel-subscription"
                            class="tab-panel hidden bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-6">Subscription Details</h3>
                            <div
                                class="bg-gradient-to-r from-blue-900 to-blue-800 rounded-xl p-6 text-white shadow-lg relative overflow-hidden">
                                <div class="relative z-10">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <span
                                                class="bg-blue-500/30 text-blue-100 text-xs font-bold px-2 py-1 rounded uppercase tracking-wider">Current
                                                Plan</span>
                                            <h2 class="text-3xl font-bold mt-2">Premium</h2>
                                            <p class="text-blue-200 text-sm mt-1">Valid until Dec 31, 2024</p>
                                        </div>
                                        <i class="fa-solid fa-crown text-yellow-400 text-4xl opacity-80"></i>
                                    </div>
                                    <div class="mt-6 pt-6 border-t border-blue-700/50 flex justify-between items-end">
                                        <div>
                                            <p class="text-sm text-blue-200">Monthly Cost</p>
                                            <p class="text-2xl font-bold">$49.00</p>
                                        </div>
                                        <button
                                            class="bg-white text-blue-900 px-4 py-2 rounded font-bold text-sm hover:bg-blue-50 transition-colors">Upgrade
                                            Plan</button>
                                    </div>
                                </div>
                                <div
                                    class="absolute -right-10 -bottom-10 w-40 h-40 bg-white opacity-5 rounded-full blur-2xl">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <!-- Chart Widget -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                            <h4 class="font-bold text-gray-800 mb-4 text-sm">Appointments Overview</h4>
                            <div class="relative h-48 w-full"><canvas id="appointmentsChart"></canvas></div>
                            <div class="mt-4 grid grid-cols-2 gap-2 text-center">
                                <div class="bg-green-50 p-2 rounded"><span
                                        class="block text-xs text-green-600 font-medium">Completed</span><span
                                        class="block text-lg font-bold text-green-700">85%</span></div>
                                <div class="bg-orange-50 p-2 rounded"><span
                                        class="block text-xs text-orange-600 font-medium">Pending</span><span
                                        class="block text-lg font-bold text-orange-700">15%</span></div>
                            </div>
                        </div>

                        <!-- Recent Activity -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                            <h4 class="font-bold text-gray-800 mb-4 text-sm">Recent Activity</h4>
                            <div class="space-y-4">
                                <?php foreach ($recent_activities as $activity): ?>
                                        <div class="flex gap-3 items-start">
                                            <div
                                                class="w-8 h-8 rounded-full <?php echo $activity['bg']; ?> flex items-center justify-center <?php echo $activity['text']; ?> flex-shrink-0 mt-0.5">
                                                <i class="fa-solid <?php echo $activity['icon']; ?> text-xs"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-800 font-medium"><?php echo $activity['title']; ?>
                                                </p>
                                                <p class="text-xs text-gray-500"><?php echo $activity['meta']; ?></p>
                                            </div>
                                        </div>
                                <?php endforeach; ?>
                            </div>
                            <button
                                class="w-full mt-4 py-2 text-xs font-medium text-blue-600 hover:bg-blue-50 rounded transition-colors">View
                                All Activity</button>
                        </div>
                    </div>
                </div>
            </div>

            <footer class="max-w-7xl mx-auto mt-8 border-t border-gray-200 pt-6 pb-2 text-center">
                <p class="text-xs text-gray-400">&copy; <?php echo date('Y'); ?> DigitalRX.io Healthcare Solutions. All
                    rights reserved.</p>
            </footer>
        </main>
    </div>

    <!-- Modals Container -->
    <div id="modal-container" class="fixed inset-0 bg-black/50 z-[100] hidden flex items-center justify-center p-4">
        
        <!-- Book Appointment Modal -->
        <div id="appointment-modal" class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden hidden transform transition-all">
            <div class="p-6 border-b flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800">Book New Appointment</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>
            <div class="p-6 space-y-4">
                <div class="space-y-2">
                    <label class="text-sm font-medium text-gray-700">Patient Name</label>
                    <input type="text" placeholder="Search patients..." class="w-full border rounded-lg px-4 py-2 outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-700">Date</label>
                        <input type="date" class="w-full border rounded-lg px-4 py-2 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-700">Time</label>
                        <input type="time" class="w-full border rounded-lg px-4 py-2 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-medium text-gray-700">Select Doctor</label>
                    <select class="w-full border rounded-lg px-4 py-2 outline-none focus:ring-2 focus:ring-blue-500">
                        <option>Dr. Paul Malone</option>
                        <option>Dr. Sarah Connor</option>
                    </select>
                </div>
            </div>
            <div class="p-6 bg-gray-50 flex justify-end gap-3">
                <button onclick="closeModal()" class="px-4 py-2 text-gray-600 font-medium hove:text-gray-800">Cancel</button>
                <button onclick="showToast('Appointment Request Sent!', 'info')" class="px-6 py-2 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 shadow-lg">Confirm Booking</button>
            </div>
        </div>

        <!-- Setup Guide Modal -->
        <div id="guide-modal" class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden hidden p-8 text-center">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fa-solid fa-rocket text-green-600 text-3xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Welcome to DigitalRX!</h3>
            <p class="text-gray-500 mb-6">Let's get your clinic set up in 3 easy steps. Ready to start?</p>
            <div class="space-y-3 mb-8 text-left">
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                    <span class="w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs font-bold">1</span>
                    <span class="text-sm font-medium">Complete Clinic Profile</span>
                </div>
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                    <span class="w-6 h-6 bg-gray-300 text-white rounded-full flex items-center justify-center text-xs font-bold">2</span>
                    <span class="text-sm font-medium text-gray-400">Add your Doctors</span>
                </div>
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                    <span class="w-6 h-6 bg-gray-300 text-white rounded-full flex items-center justify-center text-xs font-bold">3</span>
                    <span class="text-sm font-medium text-gray-400">Set working hours</span>
                </div>
            </div>
            <button onclick="closeModal()" class="w-full py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 shadow-xl transition-all">Continue Guide</button>
        </div>

        <!-- Help Modal -->
        <div id="help-modal" class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden hidden p-8">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Need Assistance?</h3>
            <div class="space-y-4">
                <a href="mailto:support@digitalrx.io" class="flex items-center gap-4 p-4 border rounded-xl hover:bg-blue-50 transition-colors group">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-all"><i class="fa-solid fa-envelope text-xl"></i></div>
                    <div><h4 class="font-bold text-gray-800">Email Support</h4><p class="text-sm text-gray-500">Response within 24h</p></div>
                </a>
                <a href="#" class="flex items-center gap-4 p-4 border rounded-xl hover:bg-green-50 transition-colors group">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center text-green-600 group-hover:bg-green-600 group-hover:text-white transition-all"><i class="fa-brands fa-whatsapp text-2xl"></i></div>
                    <div><h4 class="font-bold text-gray-800">WhatsApp Support</h4><p class="text-sm text-gray-500">Live chat assistance</p></div>
                </a>
            </div>
            <button onclick="closeModal()" class="w-full mt-6 py-2 text-gray-400 font-medium hover:text-gray-600 underline">Close</button>
        </div>

    </div>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed bottom-6 right-6 z-[200] space-y-3"></div>

    <script>
        // Tab functionality
        function switchTab(tabName) {
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('border-blue-500', 'text-blue-600');
                btn.classList.add('border-transparent', 'text-gray-500');
            });
            const activeBtn = document.getElementById('tab-' + tabName);
            activeBtn.classList.remove('border-transparent', 'text-gray-500');
            activeBtn.classList.add('border-blue-500', 'text-blue-600');

            document.querySelectorAll('.tab-panel').forEach(panel => { panel.classList.add('hidden'); });
            document.getElementById('panel-' + tabName).classList.remove('hidden');
        }

        // Edit mode functionality
        let isEditMode = false;
        function toggleEditMode() {
            isEditMode = !isEditMode;
            const viewElements = document.querySelectorAll('.view-mode');
            const editElements = document.querySelectorAll('.edit-mode');
            const btn = document.querySelector('button[onclick="toggleEditMode()"]');
            if (isEditMode) {
                viewElements.forEach(el => el.classList.add('hidden'));
                editElements.forEach(el => el.classList.remove('hidden'));
                if (btn) btn.classList.add('hidden');
            } else {
                viewElements.forEach(el => el.classList.remove('hidden'));
                editElements.forEach(el => el.classList.add('hidden'));
                if (btn) btn.classList.remove('hidden');
            }
        }

        // Modal Functionality
        function openModal(modalId) {
            const container = document.getElementById('modal-container');
            container.classList.remove('hidden');
            const target = document.getElementById(modalId);
            target.classList.remove('hidden');
            setTimeout(() => target.classList.add('scale-100', 'opacity-100'), 10);
        }

        function closeModal() {
            const container = document.getElementById('modal-container');
            container.querySelectorAll('> div').forEach(m => m.classList.add('hidden'));
            container.classList.add('hidden');
        }

        // Flash Messages & Notifications
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `flex items-center gap-3 px-6 py-4 rounded-xl shadow-2xl text-white transform transition-all duration-300 translate-y-20 opacity-0 ${type === 'success' ? 'bg-green-500' : 'bg-blue-500'}`;
            toast.innerHTML = `
                <i class="fa-solid ${type === 'success' ? 'fa-circle-check' : 'fa-circle-info'}"></i>
                <span class="font-medium">${message}</span>
            `;
            container.appendChild(toast);
            
            // Animate In
            setTimeout(() => {
                toast.classList.remove('translate-y-20', 'opacity-0');
            }, 100);

            // Close automatically
            setTimeout(() => {
                toast.classList.add('translate-y-[-20px]', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 3000);

            if(type === 'info') closeModal();
        }

        function comingSoon(feature) {
            showToast(`${feature} feature is coming soon!`, 'info');
        }

        // Mobile Menu Logic
        document.getElementById('mobile-menu-toggle')?.addEventListener('click', function() {
            const sidebar = document.querySelector('aside');
            sidebar.classList.toggle('hidden');
            sidebar.classList.toggle('fixed');
            sidebar.classList.toggle('inset-0');
            sidebar.classList.toggle('w-full');
            sidebar.classList.toggle('z-[100]');
        });

        // Chart Initialization
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('appointmentsChart').getContext('2d');
            let gradient = ctx.createLinearGradient(0, 0, 0, 200);
            gradient.addColorStop(0, 'rgba(59, 130, 246, 0.5)');
            gradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    datasets: [{
                        label: 'Appointments',
                        data: [12, 19, 15, 25, 22, 10, 8],
                        borderColor: '#3b82f6',
                        backgroundColor: gradient,
                        borderWidth: 2,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#3b82f6',
                        pointRadius: 4,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { borderDash: [2, 4], color: '#f1f5f9' }, ticks: { font: { size: 10 }, color: '#94a3b8' } },
                        x: { grid: { display: false }, ticks: { font: { size: 10 }, color: '#94a3b8' } }
                    }
                }
            });
        });
    </script>
</body>

</html>
