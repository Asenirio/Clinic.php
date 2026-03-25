<?php
// Handle updates FIRST before any output
require_once 'auth.php'; // Ensure auth is checked first

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['clinic_name'] = $_POST['clinic_name'] ?? $_SESSION['clinic_name'];
    $_SESSION['admin_name'] = $_POST['admin_name'] ?? $_SESSION['admin_name'];
    $_SESSION['success_msg'] = "Profile updated successfully!";
    header("Location: my_account.php");
    exit;
}

$page_title = "My Account";
require_once 'header.php';
require_once 'sidebar.php';
?>

<div class="max-w-4xl mx-auto fade-in">
    <div class="bg-white/70 backdrop-blur-xl rounded-[40px] shadow-2xl border border-white/20 overflow-hidden">
        <!-- Profile Header -->
        <div class="h-48 relative overflow-hidden"
            style="background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary))">
            <div class="absolute inset-0 opacity-20"
                style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 24px 24px;">
            </div>
            <div class="absolute -bottom-16 left-12 flex items-end gap-8">
                <div class="w-32 h-32 bg-white rounded-[32px] shadow-2xl border-8 border-white flex items-center justify-center text-4xl"
                    style="color: var(--brand-primary)">
                    <i class="fa-solid fa-hospital-user"></i>
                </div>
                <div class="mb-20">
                    <h2 class="text-3xl font-black text-white tracking-tight drop-shadow-md">
                        <?php echo htmlspecialchars($_SESSION['clinic_name'] ?? 'DigitalRX Clinic'); ?>
                    </h2>
                    <p
                        class="text-white/80 text-sm font-bold uppercase tracking-widest bg-black/10 backdrop-blur-sm px-3 py-1 rounded-full inline-block mt-2">
                        Premium License Active
                    </p>
                </div>
            </div>
        </div>

        <!-- Content Tabs -->
        <div class="mt-20 p-10">
            <div class="border-b border-gray-100/50 mb-10">
                <nav class="flex gap-10">
                    <button onclick="switchTab('general')" id="tab-general"
                        class="tab-btn active-tab pb-5 px-2 text-sm font-black uppercase tracking-widest transition-all">General</button>
                    <button onclick="switchTab('security')" id="tab-security"
                        class="tab-btn pb-5 px-2 text-sm font-black uppercase tracking-widest text-gray-400 hover:text-gray-600 transition-all">Security</button>
                    <button onclick="switchTab('privacy')" id="tab-privacy"
                        class="tab-btn pb-5 px-2 text-sm font-black uppercase tracking-widest text-gray-400 hover:text-gray-600 transition-all">Privacy</button>
                </nav>
            </div>

            <style>
                .active-tab {
                    color: var(--brand-primary) !important;
                    border-bottom: 4px solid var(--brand-primary);
                }

                .tab-content {
                    display: none;
                }

                .tab-content.active {
                    display: block;
                    animation: fadeIn 0.3s ease;
                }
            </style>

            <!-- General Tab -->
            <div id="content-general" class="tab-content active">
                <form method="POST" class="space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-3">
                            <label class="text-xs font-black text-gray-400 uppercase tracking-widest ml-1">Clinic Name</label>
                            <input type="text" name="clinic_name" value="<?php echo htmlspecialchars($_SESSION['clinic_name'] ?? ''); ?>"
                                class="w-full px-5 py-4 bg-gray-50/50 border border-gray-100 rounded-[20px] outline-none focus:ring-2 focus:ring-primary focus:bg-white transition-all shadow-inner">
                        </div>
                        <div class="space-y-3">
                            <label class="text-xs font-black text-gray-400 uppercase tracking-widest ml-1">Administrator</label>
                            <input type="text" name="admin_name" value="<?php echo htmlspecialchars($_SESSION['full_name'] ?? ''); ?>"
                                class="w-full px-5 py-4 bg-gray-50/50 border border-gray-100 rounded-[20px] outline-none focus:ring-2 focus:ring-primary focus:bg-white transition-all shadow-inner">
                        </div>
                    </div>

                    <div class="pt-10 border-t border-gray-100/50 flex justify-end gap-5">
                        <button type="button"
                            class="px-8 py-3 rounded-[18px] text-sm font-bold text-gray-500 hover:bg-gray-50 transition-colors">Discard</button>
                        <button type="submit"
                            class="px-10 py-3 bg-primary text-white rounded-[18px] font-black uppercase tracking-widest hover:scale-105 shadow-xl shadow-primary/20 transition-all">Save Profile</button>
                    </div>
                </form>
            </div>

            <!-- Security Tab -->
            <div id="content-security" class="tab-content">
                <div class="space-y-8">
                    <div class="grid grid-cols-1 gap-6">
                        <div class="p-6 bg-gray-50/50 rounded-[24px] border border-gray-100 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-primary shadow-sm"><i class="fa-solid fa-shield-halved"></i></div>
                                <div>
                                    <h4 class="text-sm font-black text-gray-800">Two-Factor Authentication</h4>
                                    <p class="text-xs text-gray-500">Secure your account with a secondary code.</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" checked class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                            </label>
                        </div>
                        <div class="p-6 bg-gray-50/50 rounded-[24px] border border-gray-100 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-amber-500 shadow-sm"><i class="fa-solid fa-key"></i></div>
                                <div>
                                    <h4 class="text-sm font-black text-gray-800">Password Requirements</h4>
                                    <p class="text-xs text-gray-500">Enable high-complexity password rules.</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Privacy Tab -->
            <div id="content-privacy" class="tab-content">
                <div class="space-y-6">
                    <div class="p-8 bg-primary/5 rounded-[32px] border border-primary/10">
                        <h4 class="text-sm font-black text-primary uppercase tracking-widest mb-4">Data Governance</h4>
                        <div class="space-y-4">
                            <button class="w-full text-left p-4 bg-white rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between group hover:border-primary transition-all">
                                <span class="text-sm font-bold text-gray-700">Export All Patient Data (.CSV)</span>
                                <i class="fa-solid fa-download text-gray-300 group-hover:text-primary"></i>
                            </button>
                            <button class="w-full text-left p-4 bg-white rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between group hover:border-red-500 transition-all">
                                <span class="text-sm font-bold text-gray-700">Delete Account & All Records</span>
                                <i class="fa-solid fa-trash-can text-gray-300 group-hover:text-red-500"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function switchTab(tab) {
    // Buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active-tab');
        btn.classList.add('text-gray-400');
    });
    document.getElementById(`tab-${tab}`).classList.add('active-tab');
    document.getElementById(`tab-${tab}`).classList.remove('text-gray-400');

    // Content
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
    });
    document.getElementById(`content-${tab}`).classList.add('active');
}
</script>

<?php require_once 'footer.php'; ?>