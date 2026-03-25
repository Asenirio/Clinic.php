<?php
require_once 'auth.php';
require_role('admin');

$page_title = "Doctors";
require_once 'header.php';
require_once 'sidebar.php';

try {
    $specialty_filter = isset($_GET['specialty']) ? (int)$_GET['specialty'] : null;
    $filter = $_GET['filter'] ?? 'all';
    $search = $_GET['search'] ?? '';
    
    $where_clauses = [];
    $params = [];

    if ($specialty_filter) {
        $where_clauses[] = "d.specialty_id = :specialty_id";
        $params['specialty_id'] = $specialty_filter;
    }

    if ($filter === 'on_leave') {
        $where_clauses[] = "d.availability LIKE '%Leave%'";
    }

    if (!empty($search)) {
        $where_clauses[] = "u.full_name LIKE :search";
        $params['search'] = "%$search%";
    }

    $where_sql = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";
    $order_sql = ($filter === 'by_dept') ? "ORDER BY s.name ASC, u.full_name ASC" : "ORDER BY u.full_name ASC";

    // Fetch all doctors with their user info and specialty
    $doctors_stmt = $pdo->prepare("
        SELECT d.*, u.full_name, u.email, u.avatar, s.name as specialty_name, s.icon as specialty_icon
        FROM doctors d
        JOIN users u ON d.user_id = u.id
        LEFT JOIN specialties s ON d.specialty_id = s.id
        $where_sql
        $order_sql
    ");
    
    $doctors_stmt->execute($params);
    $doctors_list = $doctors_stmt->fetchAll();
    $total_doctors = count($doctors_list);

    // Fetch specialties for dropdowns/labels
    $specialties_list = $pdo->query("SELECT id, name FROM specialties ORDER BY name ASC")->fetchAll();
    
    // UI Label logic
    $current_title = 'All Staff';
    if ($specialty_filter) {
        foreach ($specialties_list as $s) {
            if ($s['id'] == $specialty_filter) { $current_title = $s['name'] . ' Staff'; break; }
        }
    } elseif ($filter === 'on_leave') {
        $current_title = 'Staff On Leave';
    } elseif ($filter === 'by_dept') {
        $current_title = 'Staff by Department';
    } elseif (!empty($search)) {
        $current_title = "Search Results: " . htmlspecialchars($search);
    }
} catch (PDOException $e) {
    $doctors_list = [];
    $specialties_list = [];
    $total_doctors = 0;
    $error = "Error: " . $e->getMessage();
}
?>

<div class="max-w-7xl mx-auto space-y-6 fade-in">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Medical Staff Management</h2>
            <p class="text-sm text-gray-500 font-medium">Coordinate doctors and department specialists</p>
        </div>
        <div class="flex gap-3">
            <a href="specialty.php"
                class="bg-white border border-gray-200 text-gray-700 font-bold py-3 px-6 rounded-2xl shadow-sm hover:bg-gray-50 transition-all">Managing
                Departments</a>
            <a href="add_doctor.php"
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-2xl shadow-lg shadow-blue-100 flex items-center gap-2 transition-all">
                <i class="fa-solid fa-user-md"></i>
                Add New Doctor
            </a>
        </div>
    </div>

    <!-- Staff List -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-50 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="doctors.php" class="text-sm font-bold <?php echo ($filter === 'all' && !$specialty_filter && empty($search)) ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-400 hover:text-gray-600'; ?> pb-1">
                    All Staff
                </a>
                <a href="doctors.php?filter=on_leave" class="text-sm font-bold <?php echo $filter === 'on_leave' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-400 hover:text-gray-600'; ?> pb-1 transition-colors">On
                    Leave</a>
                <a href="doctors.php?filter=by_dept" class="text-sm font-bold <?php echo $filter === 'by_dept' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-400 hover:text-gray-600'; ?> pb-1 transition-colors">By
                    Department</a>
                
                <?php if ($specialty_filter || !empty($search) || ($filter !== 'all' && $filter !== 'by_dept')): ?>
                    <span class="text-sm font-bold text-gray-800 ml-4 border-l pl-4 border-gray-200">
                        <?php echo $current_title; ?> (<?php echo $total_doctors; ?>)
                    </span>
                <?php endif; ?>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50/50 text-gray-500 text-[10px] font-bold uppercase tracking-widest">
                    <tr>
                        <th class="px-8 py-5">Doctor Profile</th>
                        <th class="px-6 py-5">Department</th>
                        <th class="px-6 py-5">Contact Info</th>
                        <th class="px-6 py-5">Availability</th>
                        <th class="px-8 py-5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php if (empty($doctors_list)): ?>
                        <tr>
                            <td colspan="5" class="px-8 py-10 text-center text-gray-400 font-medium">No doctors registered
                                in the system yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($doctors_list as $d): ?>
                            <tr class="hover:bg-blue-50/10 transition-all">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-slate-100 rounded-2xl overflow-hidden shadow-sm">
                                            <img src="<?php echo htmlspecialchars($d['avatar'] ?? 'img/default-avatar.png'); ?>"
                                                alt="Avatar" class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-800 text-base">
                                                <?php echo htmlspecialchars($d['full_name']); ?>
                                            </h4>
                                            <p class="text-xs text-gray-500 font-medium italic">
                                                ID: DR-<?php echo str_pad($d['id'], 3, '0', STR_PAD_LEFT); ?>
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-6">
                                    <span
                                        class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-bold border border-blue-100 flex items-center gap-2 w-fit">
                                        <i
                                            class="fa-solid <?php echo htmlspecialchars($d['specialty_icon'] ?? 'fa-stethoscope'); ?> text-[10px]"></i>
                                        <?php echo htmlspecialchars($d['specialty_name'] ?? 'General'); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-6">
                                    <p class="text-sm font-medium text-gray-700">
                                        <?php echo htmlspecialchars($d['email']); ?>
                                    </p>
                                    <p class="text-xs text-gray-400 mt-0.5">Fees:
                                        $<?php echo number_format($d['fees'] ?? 0, 2); ?></p>
                                </td>
                                <td class="px-6 py-6">
                                    <div class="flex items-center gap-2">
                                        <div class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></div>
                                        <span class="text-sm font-bold text-green-600">
                                            <?php echo htmlspecialchars($d['availability'] ?? 'Available'); ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <button onclick="deleteDoctor(<?php echo $d['id']; ?>)" class="p-2 text-gray-300 hover:text-red-500 transition-colors" title="Delete Doctor">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="p-6 bg-gray-50/50 flex justify-between items-center">
            <p class="text-xs text-gray-500 font-medium tracking-wide">Showing 4 of 10 staff members</p>
            <div class="flex gap-2">
                <button
                    class="px-3 py-1 bg-white border border-gray-200 rounded text-xs font-bold text-gray-600 hover:bg-gray-50">Previous</button>
                <button
                    class="px-3 py-1 bg-blue-600 border border-blue-600 rounded text-xs font-bold text-white shadow-md shadow-blue-100">1</button>
                <button
                    class="px-3 py-1 bg-white border border-gray-200 rounded text-xs font-bold text-gray-600 hover:bg-gray-50">2</button>
                <button
                    class="px-3 py-1 bg-white border border-gray-200 rounded text-xs font-bold text-gray-600 hover:bg-gray-50">Next</button>
            </div>
        </div>
    </div>
</div>
<script>
    async function deleteDoctor(id) {
        if (!confirm('Permanently remove this doctor from the system?')) return;
        const fd = new FormData();
        fd.append('action', 'delete');
        fd.append('id', id);
        fd.append('csrf_token', '<?php echo $_SESSION["csrf_token"]; ?>');
        try {
            const res = await fetch('doctor_handler.php', { method: 'POST', body: fd });
            const data = await res.json();
            if (data.success) { showToast(data.message); location.reload(); }
            else { showToast(data.message, 'error'); }
        } catch (err) { showToast('Delete failed', 'error'); }
    }
</script>

<?php require_once 'footer.php'; ?>