<?php
$page_title = "Dashboard";
require_once 'header.php';
require_once 'sidebar.php';

try {
    // Real Stats
    $total_doctors = $pdo->query("SELECT COUNT(*) FROM doctors")->fetchColumn();
    $total_patients = $pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn();
    $total_appointments = $pdo->query("SELECT COUNT(*) FROM appointments")->fetchColumn();
    $total_pharmacy = $pdo->query("SELECT COUNT(*) FROM pharmacy_items")->fetchColumn();

    $stats = [
        ['label' => 'Doctors', 'value' => $total_doctors, 'icon' => 'fa-user-doctor', 'color' => 'blue', 'id' => 'stat-doctors'],
        ['label' => 'Patients', 'value' => $total_patients, 'icon' => 'fa-hospital-user', 'color' => 'indigo', 'id' => 'stat-patients'],
        ['label' => 'Appointments', 'value' => $total_appointments, 'icon' => 'fa-calendar-check', 'color' => 'purple', 'id' => 'stat-appointments'],
        ['label' => 'Active Pharmacy', 'value' => $total_pharmacy, 'icon' => 'fa-pills', 'color' => 'emerald', 'id' => 'stat-pharmacy'],
    ];

    // Recent Appointments
    $recent_stmt = $pdo->query("
        SELECT u.full_name, a.appointment_date, s.name as specialty_name 
        FROM appointments a 
        JOIN patients p ON a.patient_id = p.id 
        JOIN users u ON p.user_id = u.id 
        JOIN doctors d ON a.doctor_id = d.id 
        LEFT JOIN specialties s ON d.specialty_id = s.id
        ORDER BY a.created_at DESC LIMIT 5
    ");
    $recent_appointments = $recent_stmt->fetchAll();

    // Low Stock Pharmacy Items
    $low_stock_stmt = $pdo->query("
        SELECT id, item_name, stock, category 
        FROM pharmacy_items 
        WHERE stock < 20 
        ORDER BY stock ASC LIMIT 4
    ");
    $low_stock_items = $low_stock_stmt->fetchAll();

    // Doctors List for Dashboard Widget
    $dashboard_doctors_stmt = $pdo->query("
        SELECT d.id, u.full_name, s.name as specialty_name, d.availability, u.avatar 
        FROM doctors d 
        JOIN users u ON d.user_id = u.id 
        LEFT JOIN specialties s ON d.specialty_id = s.id
        ORDER BY d.id DESC LIMIT 4
    ");
    $dashboard_doctors = $dashboard_doctors_stmt->fetchAll();

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<div class="max-w-7xl mx-auto space-y-6 fade-in">
    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php foreach ($stats as $stat): ?>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase">
                            <?php echo $stat['label']; ?>
                        </p>
                        <h3 id="<?php echo $stat['id']; ?>" class="text-3xl font-bold text-gray-800 mt-1">
                            <?php echo number_format($stat['value']); ?>
                        </h3>
                    </div>
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl shadow-inner"
                        style="background-color: var(--brand-bg); color: var(--brand-primary)">
                        <i class="fa-solid <?php echo $stat['icon']; ?>"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs text-green-600 font-bold">
                    <i class="fa-solid fa-arrow-up mr-1 text-[10px]"></i>
                    <span>Live Tracking Active</span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- List of Doctors Widget -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Medical Staff Overview</h3>
                    <p class="text-sm text-gray-500">Available specialists and doctors</p>
                </div>
                <a href="doctors.php" class="text-sm font-bold text-blue-600 hover:text-blue-700 hover:underline">
                    View All Staff
                </a>
            </div>
            
            <div class="space-y-4">
                <?php if (empty($dashboard_doctors)): ?>
                    <div class="p-8 text-center bg-gray-50 rounded-xl border border-dashed border-gray-200">
                        <i class="fa-solid fa-user-doctor text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500 font-medium">No doctors registered in the system.</p>
                        <a href="add_doctor.php" class="text-blue-600 font-bold hover:underline text-sm mt-2 inline-block">Add First Doctor</a>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php foreach ($dashboard_doctors as $dd): ?>
                            <div class="flex items-center gap-4 p-4 rounded-xl border border-gray-100 hover:border-blue-100 hover:shadow-md hover:bg-blue-50/10 transition-all bg-white">
                                <div class="w-12 h-12 bg-slate-100 rounded-xl overflow-hidden shadow-sm flex-shrink-0">
                                    <img src="<?php echo htmlspecialchars($dd['avatar'] ?? 'img/default-avatar.png'); ?>" 
                                         alt="Avatar" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-bold text-gray-800 text-sm truncate">
                                        <?php echo htmlspecialchars($dd['full_name']); ?>
                                    </h4>
                                    <p class="text-[11px] text-blue-600 font-bold uppercase tracking-wider truncate mb-1">
                                        <?php echo htmlspecialchars($dd['specialty_name'] ?? 'General Practice'); ?>
                                    </p>
                                    <div class="flex items-center gap-1.5">
                                        <div class="w-1.5 h-1.5 rounded-full <?php echo ($dd['availability'] === 'Available') ? 'bg-green-500 animate-pulse' : 'bg-gray-400'; ?>"></div>
                                        <span class="text-[10px] font-bold <?php echo ($dd['availability'] === 'Available') ? 'text-green-600' : 'text-gray-500'; ?> uppercase">
                                            <?php echo htmlspecialchars($dd['availability']); ?>
                                        </span>
                                    </div>
                                </div>
                                <a href="doctors.php?id=<?php echo $dd['id']; ?>" class="w-8 h-8 rounded-lg bg-gray-50 text-gray-400 flex items-center justify-center hover:bg-blue-100 hover:text-blue-600 transition-colors flex-shrink-0">
                                    <i class="fa-solid fa-angle-right"></i>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Appointments -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-800">Recent Activity</h3>
                <a href="appointments.php" class="text-sm font-bold hover:underline"
                    style="color: var(--brand-primary)">View All</a>
            </div>
            <div class="space-y-4">
                <?php if (empty($recent_appointments)): ?>
                    <p class="text-gray-400 text-sm italic">No recent appointments.</p>
                <?php endif; ?>
                <?php foreach ($recent_appointments as $app): ?>
                    <div
                        class="flex items-center gap-4 p-3 rounded-xl hover:bg-gray-50 transition-colors border border-transparent hover:border-gray-100">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center font-bold text-sm"
                            style="background-color: var(--brand-bg); color: var(--brand-primary)">
                            <?php echo substr($app['full_name'], 0, 1); ?>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-bold text-gray-800"><?php echo htmlspecialchars($app['full_name']); ?>
                            </h4>
                            <p class="text-[10px] text-gray-500 uppercase font-black truncate">
                                <?php echo date('M d, H:i', strtotime($app['appointment_date'])); ?> •
                                <?php echo htmlspecialchars($app['specialty_name']); ?>
                            </p>
                        </div>
                        <div class="w-1.5 h-1.5 rounded-full bg-green-500"></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Pharmacy Alerts -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-800">Pharmacy Alerts</h3>
                <a href="pharmacy.php" class="text-sm font-bold hover:underline"
                    style="color: var(--brand-primary)">Inventory</a>
            </div>
            <div class="space-y-4 flex-1">
                <?php if (empty($low_stock_items)): ?>
                    <div class="flex flex-col items-center justify-center h-full py-10 opacity-40">
                        <i class="fa-solid fa-check-double text-3xl mb-2"></i>
                        <p class="text-xs font-bold uppercase tracking-wider">All Stock Healthy</p>
                    </div>
                <?php endif; ?>
                <?php foreach ($low_stock_items as $item): ?>
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-orange-50/50 border border-orange-100/50">
                        <div class="w-10 h-10 rounded-lg bg-white flex items-center justify-center text-orange-500 shadow-sm">
                            <i class="fa-solid fa-capsules"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold text-gray-800 truncate"><?php echo htmlspecialchars($item['item_name']); ?></h4>
                            <p class="text-[10px] text-orange-600 font-black uppercase">
                                Only <?php echo $item['stock']; ?> remaining
                            </p>
                        </div>
                        <a href="pharmacy.php?search=<?php echo urlencode($item['item_name']); ?>" 
                           class="w-8 h-8 rounded-lg flex items-center justify-center bg-white text-gray-400 hover:text-orange-500 hover:shadow-sm transition-all border border-transparent hover:border-orange-100">
                            <i class="fa-solid fa-arrow-right-long text-xs"></i>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="mt-6 pt-4 border-t border-gray-50">
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest text-center">
                    Auto-Refill Recommendations Enabled
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const rootStyles = getComputedStyle(document.documentElement);

        // Polling for stats
        async function pollStats() {
            try {
                const response = await fetch('stats_handler.php');
                const data = await response.json();
                if (data.success) {
                    document.getElementById('stat-doctors').innerText = data.stats.doctors;
                    document.getElementById('stat-patients').innerText = data.stats.patients;
                    document.getElementById('stat-appointments').innerText = data.stats.appointments;
                    document.getElementById('stat-pharmacy').innerText = data.stats.pharmacy;
                }
            } catch (e) { }
        }
        setInterval(pollStats, 10000);
    });
</script>

<?php require_once 'footer.php'; ?>