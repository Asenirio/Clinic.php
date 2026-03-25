<?php
require_once 'auth.php';

$page_title = "Specialty";
require_once 'header.php';
require_once 'sidebar.php';

// Fetch Specialties with Staff Count
try {
    $stmt = $pdo->query("
        SELECT s.*, 
        (SELECT COUNT(*) FROM doctors d WHERE d.specialty_id = s.id) as staff_count,
        (SELECT u.full_name FROM users u 
         JOIN doctors d ON u.id = d.user_id 
         WHERE d.specialty_id = s.id LIMIT 1) as head_name,
        (SELECT d.id FROM doctors d 
         WHERE d.specialty_id = s.id LIMIT 1) as head_id
        FROM specialties s
        ORDER BY s.name ASC
    ");
    $specialties = $stmt->fetchAll();
} catch (PDOException $e) {
    $specialties = [];
    $error = "Failed to load specialties: " . $e->getMessage();
}

$colors = ['red', 'indigo', 'blue', 'orange', 'emerald', 'amber', 'purple', 'rose', 'cyan', 'lime'];
?>

<div class="max-w-7xl mx-auto space-y-6 fade-in">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Specialty Departments</h2>
            <p class="text-sm text-gray-500 font-medium">Healthcare units and specialized divisions</p>
        </div>
        <button onclick="openSpecialtyModal('add')"
            class="bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 px-6 rounded-2xl shadow-lg shadow-amber-100 flex items-center gap-2 transition-all active:scale-95">
            <i class="fa-solid fa-layer-group"></i>
            Manage Units
        </button>
    </div>

    <?php if (isset($error)): ?>
        <div class="p-4 bg-red-50 text-red-600 rounded-2xl border border-red-100 flex items-center gap-2">
            <i class="fa-solid fa-circle-exclamation"></i>
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <!-- Departments Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php if (empty($specialties)): ?>
            <div class="col-span-full py-20 text-center">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center text-gray-300 mx-auto mb-4">
                    <i class="fa-solid fa-folder-open text-3xl"></i>
                </div>
                <p class="text-gray-500 font-bold">No specialties found.</p>
                <p class="text-gray-400 text-xs mt-1">Click "Manage Units" to add your first healthcare division.</p>
            </div>
        <?php endif; ?>

        <?php foreach ($specialties as $idx => $s): 
            $color = $colors[$idx % count($colors)];
            $specialty_data = htmlspecialchars(json_encode($s), ENT_QUOTES);
        ?>
            <div onclick="window.location.href='doctors.php?specialty=<?php echo $s['id']; ?>'" 
                class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 hover:shadow-2xl hover:-translate-y-1 transition-all cursor-pointer group text-center relative overflow-hidden">
                <!-- Actions on Hover -->
                <div class="absolute top-4 right-4 flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity z-10">
                    <button onclick='event.stopPropagation(); openSpecialtyModal("edit", <?php echo $specialty_data; ?>)' class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all">
                        <i class="fa-solid fa-pencil text-[10px]"></i>
                    </button>
                    <button onclick='event.stopPropagation(); deleteSpecialty(<?php echo $s['id']; ?>, "<?php echo addslashes($s['name']); ?>")' class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-all">
                        <i class="fa-solid fa-trash text-[10px]"></i>
                    </button>
                </div>

                <div class="w-20 h-20 bg-<?php echo $color; ?>-50 rounded-full flex items-center justify-center text-<?php echo $color; ?>-600 text-3xl mx-auto mb-6 group-hover:scale-110 transition-transform shadow-inner">
                    <i class="fa-solid <?php echo htmlspecialchars($s['icon']); ?>"></i>
                </div>
                <h3 class="text-xl font-black text-gray-800 mb-1">
                    <?php echo htmlspecialchars($s['name']); ?>
                </h3>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-6">Department</p>
                
                <div class="grid grid-cols-2 gap-4 border-t border-gray-50 pt-6 mb-4">
                    <div class="text-left">
                        <p class="text-[10px] text-gray-400 font-bold uppercase">Head</p>
                        <?php if ($s['head_id']): ?>
                            <a href="doctors.php?search=<?php echo urlencode($s['head_name']); ?>" 
                               onclick="event.stopPropagation()"
                               class="text-xs font-bold text-blue-600 hover:text-blue-800 truncate block w-full" 
                               title="View Profile: <?php echo htmlspecialchars($s['head_name']); ?>">
                                <?php echo htmlspecialchars($s['head_name']); ?>
                            </a>
                        <?php else: ?>
                            <p class="text-xs font-bold text-gray-400 italic">Unassigned</p>
                        <?php endif; ?>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] text-gray-400 font-bold uppercase">Staff</p>
                        <a href="doctors.php?specialty=<?php echo $s['id']; ?>" 
                           onclick="event.stopPropagation()"
                           class="text-xs font-bold text-amber-600 hover:text-amber-800 block"
                           title="View All Staff">
                            <?php echo $s['staff_count']; ?> Members
                        </a>
                    </div>
                </div>

                <div class="mt-auto">
                    <span class="inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-tighter text-blue-600 group-hover:text-blue-700 transition-colors">
                        Manage Staff <i class="fa-solid fa-arrow-right"></i>
                    </span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Add/Edit Specialty Modal -->
<div id="specialty-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full overflow-hidden transform transition-all scale-95 opacity-0 modal-content p-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="text-2xl font-black text-gray-800" id="modal-title">Add Specialty</h3>
                <p class="text-xs text-gray-400 mt-1" id="modal-subtitle">Configure healthcare division</p>
            </div>
            <button onclick="closeSpecialtyModal()" class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-gray-100 transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="specialty-form" class="space-y-5">
            <input type="hidden" name="action" id="form-action" value="add">
            <input type="hidden" name="id" id="form-id" value="">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Specialty Name</label>
                <input type="text" name="name" id="form-name" required
                    class="w-full px-4 py-3 bg-gray-50 border border-transparent rounded-xl outline-none focus:ring-2 focus:ring-amber-500 transition-all text-sm"
                    placeholder="e.g. Cardiology">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Icon (FontAwesome Class)</label>
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="icon" id="form-icon" required
                        class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-transparent rounded-xl outline-none focus:ring-2 focus:ring-amber-500 transition-all text-sm"
                        placeholder="fa-stethoscope">
                </div>
                <p class="text-[9px] text-gray-400 mt-1.5 ml-1 uppercase font-bold tracking-tighter">Use FontAwesome 6 classes (e.g., fa-heart-pulse)</p>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Description (Optional)</label>
                <textarea name="description" id="form-description" rows="3"
                    class="w-full px-4 py-3 bg-gray-50 border border-transparent rounded-xl outline-none focus:ring-2 focus:ring-amber-500 transition-all text-sm resize-none"
                    placeholder="Brief overview..."></textarea>
            </div>

            <button type="submit" id="form-submit-btn"
                class="w-full py-4 bg-amber-500 text-white rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-amber-600 shadow-xl shadow-amber-50 active:scale-[0.98] transition-all">
                Save Unit
            </button>
        </form>
    </div>
</div>

<script>
function openSpecialtyModal(mode, data = null) {
    const modal = document.getElementById('specialty-modal');
    const content = modal.querySelector('.modal-content');
    const form = document.getElementById('specialty-form');
    
    // Reset form
    form.reset();
    document.getElementById('form-id').value = '';

    if (mode === 'edit' && data) {
        document.getElementById('modal-title').innerText = 'Edit Specialty';
        document.getElementById('form-action').value = 'edit';
        document.getElementById('form-id').value = data.id;
        document.getElementById('form-name').value = data.name;
        document.getElementById('form-icon').value = data.icon;
        document.getElementById('form-description').value = data.description || '';
        document.getElementById('form-submit-btn').innerText = 'Update Specialty';
    } else {
        document.getElementById('modal-title').innerText = 'Add Specialty';
        document.getElementById('form-action').value = 'add';
        document.getElementById('form-submit-btn').innerText = 'Create Unit';
    }

    modal.classList.remove('hidden');
    setTimeout(() => {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function closeSpecialtyModal() {
    const modal = document.getElementById('specialty-modal');
    const content = modal.querySelector('.modal-content');
    content.classList.add('scale-95', 'opacity-0');
    content.classList.remove('scale-100', 'opacity-100');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 200);
}

document.getElementById('specialty-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('form-submit-btn');
    const originalText = btn.innerText;
    btn.innerText = 'Processing...';
    btn.disabled = true;

    try {
        const formData = new FormData(e.target);
        const response = await fetch('specialty_handler.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        if (result.success) {
            showToast(result.message);
            closeSpecialtyModal();
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(result.message, 'error');
        }
    } catch (error) {
        showToast('Something went wrong. Please try again.', 'error');
    } finally {
        btn.innerText = originalText;
        btn.disabled = false;
    }
});

async function deleteSpecialty(id, name) {
    if (!confirm(`Are you sure you want to delete the ${name} department? This cannot be undone if it's empty.`)) return;

    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', id);
    formData.append('csrf_token', '<?php echo $_SESSION['csrf_token']; ?>');

    try {
        const response = await fetch('specialty_handler.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        if (result.success) {
            showToast(result.message);
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(result.message, 'error');
        }
    } catch (error) {
        showToast('Delete failed. Please try again.', 'error');
    }
}
</script>

<?php require_once 'footer.php'; ?>