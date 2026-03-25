<?php
$page_title = "Appointments";
require_once 'header.php';
require_once 'sidebar.php';

// Live appointment stats
try {
    $today = date('Y-m-d');
    $stat_today     = $pdo->query("SELECT COUNT(*) FROM appointments WHERE DATE(appointment_date) = '{$today}'")->fetchColumn();
    $stat_pending   = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'pending'")->fetchColumn();
    $stat_completed = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'completed'")->fetchColumn();
} catch (PDOException $e) {
    $stat_today = $stat_pending = $stat_completed = 0;
}
?>

<div class="max-w-7xl mx-auto space-y-6 fade-in">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Appointment Management</h2>
            <p class="text-sm text-gray-500 font-medium">Schedule and track patient visits</p>
        </div>
        <button onclick="openModal('appointment-modal')"
            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-2xl shadow-lg shadow-blue-100 flex items-center gap-2 transition-all">
            <i class="fa-solid fa-plus"></i>
            New Appointment
        </button>
    </div>

    <!-- Filters & Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 text-xl"><i
                    class="fa-solid fa-calendar-check"></i></div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase">Today</p>
                <h4 class="text-2xl font-bold text-gray-800"><?php echo $stat_today; ?></h4>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center text-orange-600 text-xl"><i
                    class="fa-solid fa-clock"></i></div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase">Pending</p>
                <h4 class="text-2xl font-bold text-gray-800"><?php echo $stat_pending; ?></h4>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center text-green-600 text-xl"><i
                    class="fa-solid fa-circle-check"></i></div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase">Completed</p>
                <h4 class="text-2xl font-bold text-gray-800"><?php echo $stat_completed; ?></h4>
            </div>
        </div>
    </div>

    <!-- Appointment Table -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-50 flex items-center justify-between">
            <h3 class="font-bold text-gray-800">Upcoming Schedule</h3>
            <div class="flex gap-2">
                <input type="text" placeholder="Search appointments..."
                    class="text-sm border border-gray-200 rounded-xl px-4 py-2 outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-gray-500 text-xs font-bold uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Patient</th>
                        <th class="px-6 py-4">Doctor</th>
                        <th class="px-6 py-4">Date & Time</th>
                        <th class="px-6 py-4">Service</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php
                    try {
                        $query = "
                            SELECT 
                                a.id, 
                                a.appointment_date, 
                                a.status, 
                                a.notes,
                                u_patient.full_name as patient_name,
                                u_doctor.full_name as doctor_name,
                                t.name as treatment_name
                            FROM appointments a
                            LEFT JOIN patients p ON a.patient_id = p.id
                            LEFT JOIN users u_patient ON p.user_id = u_patient.id
                            LEFT JOIN doctors d ON a.doctor_id = d.id
                            LEFT JOIN users u_doctor ON d.user_id = u_doctor.id
                            LEFT JOIN treatments t ON a.treatment_id = t.id
                            ORDER BY a.appointment_date DESC
                        ";

                        // Fallback for demo: if patients/users aren't fully linked yet, we might get NULLs
                        // In that case, we can show raw IDs or placeholders
                        $stmt = $pdo->query($query);
                        $appointments = $stmt->fetchAll();

                        if (empty($appointments)) {
                            echo '<tr><td colspan="6" class="px-6 py-10 text-center text-gray-400 font-medium">No appointments found. Book your first one!</td></tr>';
                        }

                        foreach ($appointments as $app):
                            $date = new DateTime($app['appointment_date']);
                            $status_color = [
                                'pending' => 'orange',
                                'confirmed' => 'blue',
                                'completed' => 'green',
                                'cancelled' => 'red'
                            ][$app['status']] ?? 'gray';
                            ?>
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 font-bold text-gray-800">
                                    <?php echo htmlspecialchars($app['patient_name'] ?? 'Walk-in Patient'); ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <?php echo htmlspecialchars($app['doctor_name'] ?? 'Unassigned'); ?>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="block text-sm font-bold text-gray-800">
                                        <?php echo $date->format('M d, Y'); ?>
                                    </span>
                                    <span class="block text-xs text-gray-500">
                                        <?php echo $date->format('h:i A'); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <?php echo htmlspecialchars($app['treatment_name'] ?? 'General Consult'); ?>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-3 py-1 bg-<?php echo $status_color; ?>-50 text-<?php echo $status_color; ?>-600 rounded-full text-[10px] font-bold uppercase ring-1 ring-inset ring-<?php echo $status_color; ?>-500/20">
                                        <?php echo ucfirst($app['status']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <button class="text-gray-400 hover:text-blue-600 transition-colors"><i
                                            class="fa-solid fa-ellipsis"></i></button>
                                </td>
                            </tr>
                        <?php endforeach;
                    } catch (PDOException $e) {
                        echo '<tr><td colspan="6" class="px-6 py-4 text-red-500 text-sm">Database Error: ' . $e->getMessage() . '</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>