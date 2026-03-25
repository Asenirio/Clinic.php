<?php
require_once 'auth.php';
require_role('admin');

$page_title = "Add Doctor";
require_once 'header.php';
require_once 'sidebar.php';

try {
    // Fetch specialties for the Add Doctor form
    $specialties_list = $pdo->query("SELECT id, name FROM specialties ORDER BY name ASC")->fetchAll();
} catch (PDOException $e) {
    $specialties_list = [];
    $error = "Error: " . $e->getMessage();
}
?>

<div class="max-w-3xl mx-auto space-y-6 fade-in">
    <div class="flex items-center gap-4">
        <a href="doctors.php" class="text-gray-400 hover:text-gray-600 transition-colors">
            <i class="fa-solid fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Add New Doctor</h2>
            <p class="text-sm text-gray-500 font-medium">Register a new medical professional in the system</p>
        </div>
    </div>

    <!-- Add Doctor Form -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
        <form id="add-doctor-form" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Profile Section -->
                <div class="md:col-span-2 space-y-4">
                    <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-2">Personal Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Full Name <span class="text-red-500">*</span></label>
                            <input type="text" name="full_name" required
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-sm"
                                placeholder="Dr. Jane Smith">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Email Address <span class="text-red-500">*</span></label>
                            <input type="email" name="email" required
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-sm"
                                placeholder="jane@clinic.com">
                        </div>
                    </div>
                </div>

                <!-- Account Section -->
                <div class="md:col-span-2 space-y-4 pt-4">
                    <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-2">Account Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Username <span class="text-red-500">*</span></label>
                            <input type="text" name="username" required
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-sm"
                                placeholder="dr.jsmith">
                        </div>
                        <div class="flex items-end">
                            <div class="w-full px-4 py-3 bg-blue-50 text-blue-700 rounded-xl text-sm border border-blue-100 flex items-center gap-3">
                                <i class="fa-solid fa-circle-info text-blue-500"></i>
                                <span>Default password will be set to: <strong>doctor123</strong></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Professional Section -->
                <div class="md:col-span-2 space-y-4 pt-4">
                    <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-2">Professional Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Department / Specialty</label>
                            <select name="specialty_id"
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-sm appearance-none">
                                <option value="">-- General Practice --</option>
                                <?php foreach ($specialties_list as $sp): ?>
                                    <option value="<?php echo $sp['id']; ?>"><?php echo htmlspecialchars($sp['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Consultation Fee ($)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="text-gray-500 font-medium">$</span>
                                </div>
                                <input type="number" name="fees" min="0" step="0.01" value="0.00"
                                    class="w-full pl-8 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-sm"
                                    placeholder="100.00">
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Availability Schedule</label>
                            <input type="text" name="availability" value="Available"
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-sm"
                                placeholder="e.g. Mon-Fri, 9am-5pm">
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" name="action" value="add">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <div class="pt-6 border-t border-gray-100 flex justify-end gap-3">
                <a href="doctors.php" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition-colors">
                    Cancel
                </a>
                <button type="submit"
                    class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-100 flex items-center gap-2 transition-all">
                    <i class="fa-solid fa-user-plus"></i>
                    Register Doctor
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('add-doctor-form')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = e.target.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Processing...';
        btn.disabled = true;
        
        try {
            const res = await fetch('doctor_handler.php', { method: 'POST', body: new FormData(e.target) });
            const data = await res.json();
            if (data.success) {
                // Show success toast (assuming showToast exists globally as in other pages)
                if (typeof showToast === 'function') {
                    showToast(data.message);
                } else {
                    alert(data.message);
                }
                // Redirect to doctors list
                setTimeout(() => {
                    window.location.href = 'doctors.php';
                }, 1500);
            } else {
                if (typeof showToast === 'function') {
                    showToast(data.message, 'error');
                } else {
                    alert('Error: ' + data.message);
                }
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        } catch (err) { 
            console.error(err);
            if (typeof showToast === 'function') {
                showToast('Request failed. Please try again.', 'error');
            } else {
                alert('Request failed. Please try again.');
            }
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    });
</script>

<?php require_once 'footer.php'; ?>
