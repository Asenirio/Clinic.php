<?php
require_once 'auth.php';
require_role('admin');

$page_title = "Patients";
require_once 'header.php';
require_once 'sidebar.php';
?>

<div class="max-w-7xl mx-auto space-y-6 fade-in">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Patient Registry</h2>
            <p class="text-sm text-gray-500 font-medium">Manage patient records and medical history</p>
        </div>
        <button onclick="openPatientModal()"
            class="text-white font-bold py-3 px-6 rounded-2xl shadow-lg flex items-center gap-2 transition-all hover:scale-105 active:scale-95"
            style="background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary)); box-shadow: 0 10px 20px -5px var(--brand-primary)">
            <i class="fa-solid fa-user-plus"></i>
            Register Patient
        </button>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-wrap gap-4 items-center">
        <div class="relative flex-1 min-w-[300px]">
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" placeholder="Search by name, ID, or phone..."
                class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-transparent rounded-xl outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all text-sm">
        </div>
        <div class="flex gap-2">
            <button class="px-4 py-3 bg-gray-50 hover:bg-gray-100 text-gray-600 rounded-xl transition-colors"><i
                    class="fa-solid fa-sliders mr-2"></i>Filters</button>
            <button class="px-4 py-3 bg-gray-50 hover:bg-gray-100 text-gray-600 rounded-xl transition-colors"><i
                    class="fa-solid fa-download mr-2"></i>Export</button>
        </div>
    </div>

    <!-- Patient Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php
        try {
            $query = "
                SELECT 
                    p.id, 
                    p.blood_type, 
                    p.status,
                    u.full_name, 
                    u.gender,
                    TIMESTAMPDIFF(YEAR, u.dob, CURDATE()) AS age
                FROM patients p
                JOIN users u ON p.user_id = u.id
                ORDER BY u.created_at DESC
            ";
            $stmt = $pdo->query($query);
            $patients = $stmt->fetchAll();

            if (empty($patients)) {
                echo '<div class="col-span-full py-20 text-center"><p class="text-gray-400 font-bold">No patients registered yet.</p></div>';
            }

            foreach ($patients as $p):
                $avatar_color = $p['gender'] === 'Female' ? 'bg-pink-100 text-pink-600' : 'bg-blue-100 text-blue-600';
                $name_parts = explode(' ', $p['full_name']);
                $initials = (isset($name_parts[0][0]) ? $name_parts[0][0] : '') . (isset($name_parts[1][0]) ? $name_parts[1][0] : '');
                ?>
                <div
                    class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all group">
                    <div class="flex justify-between items-start mb-6">
                        <div
                            class="w-16 h-16 <?php echo $avatar_color; ?> rounded-2xl flex items-center justify-center font-bold text-xl shadow-inner">
                            <?php echo htmlspecialchars($initials); ?>
                        </div>
                        <button onclick="deletePatient(<?php echo $p['id']; ?>)"
                            class="text-gray-200 group-hover:text-red-500 transition-colors"><i
                                class="fa-solid fa-trash-can"></i></button>
                    </div>
                    <div class="space-y-1 mb-6">
                        <h3 class="text-lg font-bold text-gray-800">
                            <?php echo htmlspecialchars($p['full_name']); ?>
                        </h3>
                        <p class="text-sm font-medium text-indigo-600">
                            P-<?php echo str_pad($p['id'], 4, '0', STR_PAD_LEFT); ?>
                        </p>
                    </div>
                    <div class="grid grid-cols-2 gap-4 border-t border-gray-50 pt-6">
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Age / Sex</p>
                            <p class="text-sm font-bold text-gray-700">
                                <?php echo $p['age']; ?>y,
                                <?php echo htmlspecialchars($p['gender']); ?>
                            </p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Blood Type</p>
                            <p class="text-sm font-bold text-gray-700">
                                <?php echo htmlspecialchars($p['blood_type'] ?? 'N/A'); ?>
                            </p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Status</p>
                            <span
                                class="px-2 py-0.5 bg-green-50 text-green-600 rounded text-[10px] font-bold uppercase ring-1 ring-inset ring-green-500/20">
                                <?php echo htmlspecialchars($p['status'] ?? 'Active'); ?>
                            </span>
                        </div>
                    </div>
                    <a href="patient_profile.php?id=<?php echo $p['id']; ?>"
                        class="w-full mt-6 py-3 bg-gray-50 group-hover:bg-indigo-600 group-hover:text-white rounded-xl text-sm font-bold text-gray-600 transition-all text-center block">View
                        Profile</a>
                </div>
            <?php endforeach;
        } catch (PDOException $e) {
            echo '<div class="col-span-full text-red-500">Error: ' . $e->getMessage() . '</div>';
        }
        ?>
    </div>
</div>

<!-- Register Patient Modal -->
<div id="patient-modal" class="fixed inset-0 bg-black/50 z-[100] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden p-8 transform transition-all">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-black text-gray-800">Register Patient</h3>
            <button onclick="closePatientModal()" class="text-gray-400 hover:text-gray-600"><i
                    class="fa-solid fa-xmark text-xl"></i></button>
        </div>
        <form id="patient-form" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Full
                        Name</label>
                    <input type="text" name="full_name" required
                        class="w-full px-4 py-3 bg-gray-50 border border-transparent rounded-xl outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                </div>
                <div>
                    <label
                        class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Username</label>
                    <input type="text" name="username" required
                        class="w-full px-4 py-3 bg-gray-50 border border-transparent rounded-xl outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">DOB</label>
                    <input type="date" name="dob" required
                        class="w-full px-4 py-3 bg-gray-50 border border-transparent rounded-xl outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                </div>
                <div>
                    <label
                        class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Gender</label>
                    <select name="gender"
                        class="w-full px-4 py-3 bg-gray-50 border border-transparent rounded-xl outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                        <option>Male</option>
                        <option>Female</option>
                        <option>Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Blood
                        Type</label>
                    <input type="text" name="blood_type"
                        class="w-full px-4 py-3 bg-gray-50 border border-transparent rounded-xl outline-none focus:ring-2 focus:ring-indigo-500 text-sm"
                        placeholder="e.g. A+">
                </div>
            </div>
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <button type="submit"
                class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-indigo-700 shadow-xl shadow-indigo-100 transition-all">Register
                Now</button>
        </form>
    </div>
</div>

<script>
    function openPatientModal() { document.getElementById('patient-modal').classList.remove('hidden'); }
    function closePatientModal() { document.getElementById('patient-modal').classList.add('hidden'); }

    document.getElementById('patient-form')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        try {
            const response = await fetch('patient_handler.php', { method: 'POST', body: formData });
            const result = await response.json();
            if (result.success) {
                showToast(result.message);
                closePatientModal();
                location.reload();
            } else {
                showToast(result.message, 'error');
            }
        } catch (error) { showToast('Registration failed', 'error'); }
    });

    async function deletePatient(id) {
        if (!confirm('Permanently delete this patient record?')) return;
        const formData = new FormData();
        formData.append('id', id);
        formData.append('action', 'delete');
        formData.append('csrf_token', '<?php echo $_SESSION['csrf_token']; ?>');
        try {
            const response = await fetch('patient_handler.php', { method: 'POST', body: formData });
            const result = await response.json();
            if (result.success) { showToast(result.message); location.reload(); }
            else { showToast(result.message, 'error'); }
        } catch (error) { showToast('Delete failed', 'error'); }
    }
</script>

<?php require_once 'footer.php'; ?>