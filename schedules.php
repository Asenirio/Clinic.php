<?php
require_once 'auth.php';

// Only staff, doctors, and admins can view schedules
/*
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'doctor', 'staff'])) {
    $_SESSION['error_msg'] = "Access denied. You do not have permission to view schedules.";
    header("Location: dashboard.php");
    exit;
}
*/

$page_title = "Staff Schedules";
require_once 'header.php';
require_once 'sidebar.php';
?>

<?php
try {
// Fetch all doctor shifts with doctor names
$shifts_stmt = $pdo->query("
    SELECT ds.*, u.full_name as doctor_name, s.name as specialty
    FROM doctor_shifts ds
    JOIN doctors d ON ds.doctor_id = d.id
    JOIN users u ON d.user_id = u.id
    JOIN specialties s ON d.specialty_id = s.id
    ORDER BY FIELD(ds.day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'),
        ds.start_time
");
$shifts = $shifts_stmt->fetchAll();

// Fetch all staff shifts
$staff_shifts_stmt = $pdo->query("
    SELECT ss.*, u.full_name, u.role
    FROM staff_shifts ss
    JOIN users u ON ss.user_id = u.id
    WHERE ss.is_active = 1
    ORDER BY FIELD(ss.day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'),
        ss.start_time
");
$staff_shifts = $staff_shifts_stmt->fetchAll();

// Fetch facility status
$facilities_stmt = $pdo->query("
    SELECT f.*, u.full_name as patient_name
    FROM facilities f
    LEFT JOIN patients p ON f.current_patient_id = p.id
    LEFT JOIN users u ON p.user_id = u.id
    ORDER BY f.type, f.name
");
$facilities = $facilities_stmt->fetchAll();

// Fetch all staff members (doctors, staff, pharmacists) for the "Add Shift" modal
$all_staff_stmt = $pdo->query("
    SELECT id, full_name, role
    FROM users
    WHERE role IN ('doctor', 'staff', 'pharmacist', 'admin')
    ORDER BY role, full_name
");
$all_staff = $all_staff_stmt->fetchAll();

} catch (PDOException $e) {
echo "Error: " . $e->getMessage();
}
?>

<div class="max-w-7xl mx-auto space-y-8 fade-in">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">Staff & Resource <span
                    style="color: var(--brand-primary)">Schedules</span></h1>
            <p class="text-sm text-gray-500 font-medium">Manage medical shifts and facility availability</p>
        </div>
        <div class="flex gap-3">
            <button onclick="openShiftModal('add-shift-modal')"
                class="text-white font-bold py-3 px-6 rounded-2xl shadow-lg flex items-center gap-2 transition-all hover:scale-105 active:scale-95"
                style="background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary)); box-shadow: 0 10px 20px -5px var(--brand-primary)">
                <i class="fa-solid fa-clock-rotate-left"></i>
                Add New Shift
            </button>
        </div>
    </div>

    <!-- Main Content Tabs -->
    <div class="bg-white rounded-[32px] shadow-xl shadow-blue-900/5 border border-gray-100 overflow-hidden">
        <div class="flex border-b border-gray-100 bg-gray-50/50">
            <button onclick="switchTab('shifts')" id="tab-shifts"
                class="px-8 py-5 text-sm font-black uppercase tracking-widest border-b-2 transition-all border-blue-600 text-blue-600">Doctor
                Shifts</button>
            <button onclick="switchTab('staff')" id="tab-staff"
                class="px-8 py-5 text-sm font-black uppercase tracking-widest border-b-2 transition-all border-transparent text-gray-400 hover:text-gray-600">Staff
                Schedules</button>
            <button onclick="switchTab('facilities')" id="tab-facilities"
                class="px-8 py-5 text-sm font-black uppercase tracking-widest border-b-2 transition-all border-transparent text-gray-400 hover:text-gray-600">Facilities
                & Resources</button>
        </div>

        <!-- Doctor Shifts Section -->
        <div id="section-shifts" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if (empty($shifts)): ?>
                    <div class="col-span-full py-12 text-center">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fa-solid fa-calendar-xmark text-gray-300 text-2xl"></i>
                        </div>
                        <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">No active shifts scheduled</p>
                    </div>
                <?php else: ?>
                    <?php
                    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                    foreach ($days as $day):
                        $day_shifts = array_filter($shifts, fn($s) => $s['day_of_week'] === $day);
                        if (empty($day_shifts))
                            continue;
                        ?>
                        <div class="bg-gray-50/50 rounded-3xl p-6 border border-gray-100">
                            <h3
                                class="text-xs font-black text-blue-600 uppercase tracking-widest mb-4 flex items-center justify-between">
                                <?php echo $day; ?>
                                <span class="bg-blue-100 text-blue-600 px-2 py-0.5 rounded-full text-[10px]">
                                    <?php echo count($day_shifts); ?> Shifts
                                </span>
                            </h3>
                            <div class="space-y-3">
                                <?php foreach ($day_shifts as $shift): ?>
                                    <div
                                        class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all group">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h4 class="text-sm font-bold text-gray-800">
                                                    <?php echo htmlspecialchars($shift['doctor_name']); ?>
                                                </h4>
                                                <p class="text-[10px] text-blue-500 font-black uppercase tracking-tighter">
                                                    <?php echo htmlspecialchars($shift['specialty']); ?>
                                                </p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">
                                                    <?php echo date('h:i A', strtotime($shift['start_time'])); ?>
                                                </p>
                                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">
                                                    <?php echo date('h:i A', strtotime($shift['end_time'])); ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Staff Schedules Section -->
        <div id="section-staff" class="p-6 hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if (empty($staff_shifts)): ?>
                    <div class="col-span-full py-12 text-center">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fa-solid fa-calendar-xmark text-gray-300 text-2xl"></i>
                        </div>
                        <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">No active staff schedules</p>
                    </div>
                <?php else: ?>
                    <?php
                    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                    foreach ($days as $day):
                        $day_staff_shifts = array_filter($staff_shifts, fn($s) => $s['day_of_week'] === $day);
                        if (empty($day_staff_shifts))
                            continue;
                        ?>
                        <div class="bg-gray-50/50 rounded-3xl p-6 border border-gray-100">
                            <h3
                                class="text-xs font-black text-purple-600 uppercase tracking-widest mb-4 flex items-center justify-between">
                                <?php echo $day; ?>
                                <span class="bg-purple-100 text-purple-600 px-2 py-0.5 rounded-full text-[10px]">
                                    <?php echo count($day_staff_shifts); ?> Shifts
                                </span>
                            </h3>
                            <div class="space-y-3">
                                <?php foreach ($day_staff_shifts as $shift): ?>
                                    <div
                                        class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all group">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h4 class="text-sm font-bold text-gray-800">
                                                    <?php echo htmlspecialchars($shift['full_name']); ?>
                                                </h4>
                                                <p class="text-[10px] text-purple-500 font-black uppercase tracking-tighter">
                                                    <?php echo htmlspecialchars(ucfirst($shift['role'])); ?>
                                                </p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">
                                                    <?php echo date('h:i A', strtotime($shift['start_time'])); ?>
                                                </p>
                                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">
                                                    <?php echo date('h:i A', strtotime($shift['end_time'])); ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Facilities Section -->
        <div id="section-facilities" class="p-6 hidden">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                <?php foreach ($facilities as $facility): ?>
                    <div
                        class="bg-white p-6 rounded-[32px] border border-gray-100 shadow-sm hover:shadow-xl transition-all group relative overflow-hidden">
                        <?php
                        $status_colors = [
                            'available' => 'bg-green-500',
                            'occupied' => 'bg-red-500',
                            'maintenance' => 'bg-amber-500'
                        ];
                        $icon_map = [
                            'bed' => 'fa-bed',
                            'room' => 'fa-door-open',
                            'icu' => 'fa-heart-pulse',
                            'operating_theater' => 'fa-microscope'
                        ];
                        ?>
                        <div
                            class="absolute top-0 right-0 w-24 h-24 bg-gray-50/50 -mr-8 -mt-8 rounded-full group-hover:bg-blue-50/50 transition-colors">
                        </div>
                        <div class="relative">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl mb-4 transition-all"
                                style="background-color: var(--brand-bg); color: var(--brand-primary)">
                                <i class="fa-solid <?php echo $icon_map[$facility['type']]; ?>"></i>
                            </div>
                            <h3 class="text-sm font-black text-gray-800 uppercase tracking-tight">
                                <?php echo htmlspecialchars($facility['name']); ?>
                            </h3>
                            <p class="text-[10px] text-gray-400 font-bold uppercase mb-4">
                                <?php echo str_replace('_', ' ', $facility['type']); ?>
                            </p>

                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full <?php echo $status_colors[$facility['status']]; ?>"></div>
                                <span
                                    class="text-[10px] font-black uppercase tracking-widest <?php echo str_replace('bg-', 'text-', $status_colors[$facility['status']]); ?>">
                                    <?php echo $facility['status']; ?>
                                </span>
                            </div>

                            <?php if ($facility['status'] === 'occupied' && $facility['patient_name']): ?>
                                <div class="mt-4 pt-4 border-t border-gray-50">
                                    <p class="text-[9px] text-gray-400 font-black uppercase tracking-widest mb-1">Current
                                        Patient</p>
                                    <p class="text-xs font-bold text-gray-700 truncate">
                                        <?php echo htmlspecialchars($facility['patient_name']); ?>
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Add Shift Modal -->
<div id="add-shift-modal"
    class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4">
    <div
        class="bg-white w-full max-w-md rounded-[40px] shadow-2xl p-8 border border-white/20 transform transition-all scale-95 opacity-0 modal-content">
        <div class="flex justify-between items-center mb-8">
            <h3 class="text-xl font-bold text-gray-800">Assign New Shift</h3>
            <button onclick="closeShiftModal('add-shift-modal')"
                class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-gray-100 transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="scheduling_handler.php" method="POST" class="space-y-5" onsubmit="return validateShiftForm(this)">
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Select
                    Staff Member</label>
                <select name="user_id" required
                    class="w-full px-5 py-4 bg-gray-50 border border-transparent rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition-all text-sm appearance-none">
                    <?php foreach ($all_staff as $staff): ?>
                        <option value="<?php echo $staff['id']; ?>">
                            <?php echo htmlspecialchars($staff['full_name']); ?> (<?php echo ucfirst($staff['role']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Day of Week</label>
                <select name="day_of_week" required
                    class="w-full px-5 py-4 bg-gray-50 border border-transparent rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition-all text-sm appearance-none">
                    <option value="Monday">Monday</option>
                    <option value="Tuesday">Tuesday</option>
                    <option value="Wednesday">Wednesday</option>
                    <option value="Thursday">Thursday</option>
                    <option value="Friday">Friday</option>
                    <option value="Saturday">Saturday</option>
                    <option value="Sunday">Sunday</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Start
                        Time</label>
                    <input type="time" name="start_time" required
                        class="w-full px-5 py-4 bg-gray-50 border border-transparent rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">End Time</label>
                    <input type="time" name="end_time" required
                        class="w-full px-5 py-4 bg-gray-50 border border-transparent rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Shift Type</label>
                <select name="shift_type"
                    class="w-full px-5 py-4 bg-gray-50 border border-transparent rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition-all text-sm appearance-none">
                    <option value="regular">Regular</option>
                    <option value="overtime">Overtime</option>
                    <option value="on-call">On-Call</option>
                </select>
            </div>

            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <button type="submit"
                class="w-full py-4 bg-blue-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-blue-200 hover:scale-[1.02] active:scale-[0.98] transition-all mt-4">
                Confirm Schedule
            </button>
        </form>
    </div>
</div>

<script>
    function switchTab(tab) {
        const shiftsBtn = document.getElementById('tab-shifts');
        const staffBtn = document.getElementById('tab-staff');
        const facilitiesBtn = document.getElementById('tab-facilities');
        const shiftsSection = document.getElementById('section-shifts');
        const staffSection = document.getElementById('section-staff');
        const facilitiesSection = document.getElementById('section-facilities');

        // Remove active state from all tabs
        [shiftsBtn, staffBtn, facilitiesBtn].forEach(btn => {
            btn.classList.add('border-transparent', 'text-gray-400');
            btn.classList.remove('border-blue-600', 'text-blue-600', 'border-purple-600', 'text-purple-600');
        });

        // Hide all sections
        [shiftsSection, staffSection, facilitiesSection].forEach(section => {
            section.classList.add('hidden');
        });

        // Show selected tab
        if (tab === 'shifts') {
            shiftsBtn.classList.add('border-blue-600', 'text-blue-600');
            shiftsBtn.classList.remove('border-transparent', 'text-gray-400');
            shiftsSection.classList.remove('hidden');
        } else if (tab === 'staff') {
            staffBtn.classList.add('border-purple-600', 'text-purple-600');
            staffBtn.classList.remove('border-transparent', 'text-gray-400');
            staffSection.classList.remove('hidden');
        } else if (tab === 'facilities') {
            facilitiesBtn.classList.add('border-blue-600', 'text-blue-600');
            facilitiesBtn.classList.remove('border-transparent', 'text-gray-400');
            facilitiesSection.classList.remove('hidden');
        }
    }

    function openShiftModal(id) {
        const modal = document.getElementById(id);
        const content = modal.querySelector('.modal-content');
        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeShiftModal(id) {
        const modal = document.getElementById(id);
        const content = modal.querySelector('.modal-content');
        content.classList.add('scale-95', 'opacity-0');
        content.classList.remove('scale-100', 'opacity-100');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
    }
    function validateShiftForm(form) {
        const startTime = form.start_time.value;
        const endTime = form.end_time.value;

        if (startTime && endTime && endTime <= startTime) {
            alert('Error: End time must be after start time.');
            return false;
        }
        return true;
    }
</script>

<?php require_once 'footer.php'; ?>