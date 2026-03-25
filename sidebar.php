<aside class="w-64 bg-white border-r border-gray-200 flex flex-col hidden md:flex z-20 shadow-lg">
    <div class="h-16 flex items-center px-6 border-b border-gray-100">
        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white mr-3"
            style="background-color: var(--brand-primary)">
            <i class="fa-solid fa-user-doctor"></i>
        </div>
        <span class="text-xl font-bold text-gray-700">DigitalRX<span
                style="color: var(--brand-primary)">.io</span></span>
    </div>

    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
        <?php foreach ($nav_items as $item): ?>
            <a href="<?php echo $item['url']; ?>"
                class="sidebar-item <?php echo is_active($item['url']); ?> flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors group">
                <div class="relative">
                    <i class="<?php echo $item['icon']; ?> w-6 text-center mr-2"></i>
                    <?php if (isset($item['badge']) && $item['badge']): ?>
                        <span
                            class="absolute top-0 right-0 -mt-1 -mr-1 h-2 w-2 rounded-full bg-green-500 border-2 border-white"></span>
                    <?php endif; ?>
                </div>
                <?php echo $item['label']; ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="p-4 border-t border-gray-100">
        <button onclick="openModal('help-modal')"
            class="w-full flex items-center px-3 py-2.5 rounded-lg text-purple-600 bg-purple-50 hover:bg-purple-100 text-sm font-medium transition-colors">
            <i class="fa-solid fa-circle-info w-6 text-center mr-2"></i>
            Need Help?
        </button>
        <a href="logout.php"
            class="w-full mt-2 flex items-center px-3 py-2.5 rounded-lg text-red-600 hover:bg-red-50 text-sm font-medium transition-colors">
            <i class="fa-solid fa-right-from-bracket w-6 text-center mr-2"></i>
            Logout
        </a>
    </div>
</aside>

<div class="flex-1 flex flex-col min-w-0 overflow-hidden">
    <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8 z-10">
        <div class="flex items-center">
            <button id="mobile-menu-toggle"
                class="md:hidden p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 mr-2">
                <i class="fa-solid fa-bars text-xl"></i>
            </button>
            <div>
                <h1 class="text-xl font-semibold text-gray-800">
                    <?php echo isset($page_title) ? $page_title : 'Dashboard'; ?>
                </h1>
                <p class="text-xs text-gray-500 hidden sm:block">DigitalRX.io -
                    <?php echo isset($page_title) ? $page_title : 'Dashboard'; ?>
                </p>
            </div>
        </div>

        <div class="flex items-center space-x-4">
            <!-- Notifications -->
            <div class="relative group" id="notification-center">
                <button onclick="toggleNotifications()"
                    class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-400 hover:bg-white hover:text-blue-600 transition-all relative">
                    <i class="fa-regular fa-bell"></i>
                    <span id="notif-badge"
                        class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-[8px] font-bold rounded-full flex items-center justify-center hidden">0</span>
                </button>
                <div id="notif-dropdown"
                    class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-2xl border border-gray-100 hidden z-50 overflow-hidden">
                    <div class="p-4 border-b border-gray-50 flex justify-between items-center">
                        <h4 class="text-sm font-bold text-gray-800">Notifications</h4>
                        <span class="text-[10px] text-blue-600 font-bold uppercase tracking-wider">Live</span>
                    </div>
                    <div id="notif-list" class="max-h-64 overflow-y-auto">
                        <div class="p-8 text-center text-gray-400 text-xs text-gray-500">No new alerts</div>
                    </div>
                </div>
            </div>

            <!-- Theme Switcher -->
            <div class="flex bg-gray-100 p-1 rounded-xl">
                <button onclick="setTheme('ocean')"
                    class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-white transition-all text-blue-500"
                    title="Ocean Theme">
                    <i class="fa-solid fa-droplet"></i>
                </button>
                <button onclick="setTheme('midnight')"
                    class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-white transition-all text-purple-500"
                    title="Midnight Theme">
                    <i class="fa-solid fa-moon"></i>
                </button>
                <button onclick="setTheme('mint')"
                    class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-white transition-all text-emerald-500"
                    title="Mint Theme">
                    <i class="fa-solid fa-leaf"></i>
                </button>
            </div>

            <button onclick="openModal('appointment-modal')"
                class="text-white text-sm font-medium py-2 px-4 rounded-md shadow flex items-center gap-2 transition-all hover:opacity-90 active:scale-95"
                style="background-color: var(--brand-primary)">
                <i class="fa-regular fa-calendar-plus"></i>
                <span class="hidden sm:inline">Book Appointment</span>
            </button>
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <img src="https://flagcdn.com/w20/us.png" alt="US" class="h-4 w-auto">
                <span class="hidden md:inline">English</span>
            </div>
            <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center text-gray-500">
                <i class="fa-solid fa-user"></i>
            </div>
        </div>
    </header>

    <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 bg-gray-50">
        <?php if (isset($_SESSION['success_msg'])): ?>
            <div id="toast-success"
                class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-2">
                <i class="fa-solid fa-circle-check"></i>
                <span>
                    <?php echo $_SESSION['success_msg'];
                    unset($_SESSION['success_msg']); ?>
                </span>
            </div>
            <script>setTimeout(() => document.getElementById('toast-success')?.remove(), 5000);</script>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_msg'])): ?>
            <div id="toast-error"
                class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-2">
                <i class="fa-solid fa-circle-xmark"></i>
                <span>
                    <?php echo $_SESSION['error_msg'];
                    unset($_SESSION['error_msg']); ?>
                </span>
            </div>
            <script>setTimeout(() => document.getElementById('toast-error')?.remove(), 7000);</script>
        <?php endif; ?>

        <script>
            function toggleNotifications() {
                const dropdown = document.getElementById('notif-dropdown');
                if (dropdown) dropdown.classList.toggle('hidden');
            }

            async function fetchNotifications() {
                try {
                    const response = await fetch('notifications_handler.php');
                    const result = await response.json();
                    if (result.success) {
                        const badge = document.getElementById('notif-badge');
                        const list = document.getElementById('notif-list');

                        if (result.count > 0) {
                            if (badge) {
                                badge.innerText = result.count;
                                badge.classList.remove('hidden');
                            }

                            if (list) {
                                list.innerHTML = result.notifications.map(n => `
                                <div class="p-4 border-b border-gray-50 hover:bg-gray-50 transition-colors flex gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-${n.type === 'warning' ? 'orange' : (n.type === 'success' ? 'green' : 'blue')}-50 flex items-center justify-center text-${n.type === 'warning' ? 'orange' : (n.type === 'success' ? 'green' : 'blue')}-500">
                                        <i class="fa-solid ${n.icon}"></i>
                                    </div>
                                    <div>
                                        <h5 class="text-xs font-bold text-gray-800">${n.title}</h5>
                                        <p class="text-[10px] text-gray-500 mt-0.5">${n.message}</p>
                                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter mt-1 block">${n.time}</span>
                                    </div>
                                </div>
                            `).join('');
                            }
                        } else {
                            if (badge) badge.classList.add('hidden');
                            if (list) list.innerHTML = '<div class="p-8 text-center text-gray-400 text-xs">No new alerts</div>';
                        }
                    }
                } catch (e) { }
            }

            window.addEventListener('click', (e) => {
                const center = document.getElementById('notification-center');
                const dropdown = document.getElementById('notif-dropdown');
                if (center && dropdown && !center.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            });

            // Mobile menu toggle
            document.addEventListener('DOMContentLoaded', () => {
                const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
                const sidebar = document.querySelector('aside');
                
                if (mobileMenuToggle && sidebar) {
                    mobileMenuToggle.addEventListener('click', () => {
                        sidebar.classList.toggle('hidden');
                        sidebar.classList.toggle('absolute');
                        sidebar.classList.toggle('h-full');
                        sidebar.classList.toggle('z-40');
                    });
                }
            });

            setInterval(fetchNotifications, 10000);
            fetchNotifications();
        </script>