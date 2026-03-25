<?php
$page_title = "Treatment";
require_once 'header.php';
require_once 'sidebar.php';
?>

<div class="max-w-7xl mx-auto space-y-6 fade-in">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Treatment Protocols</h2>
            <p class="text-sm text-gray-500 font-medium">Standardized procedures and patient treatment logs</p>
        </div>
        <button
            class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-6 rounded-2xl shadow-lg shadow-emerald-100 flex items-center gap-2 transition-all">
            <i class="fa-solid fa-file-waveform"></i>
            Create New Plan
        </button>
    </div>

    <!-- Treatment Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php
        $treatments = [
            ['name' => 'Cardiac Surgery', 'desc' => 'Standard bypass surgery procedure including post-op care.', 'cost' => '$15,000', 'duration' => '4-6 Hours', 'icon' => 'fa-heart-pulse', 'bg' => 'blue'],
            ['name' => 'Dental Cleaning', 'desc' => 'Professional scaling, polishing and fluoride treatment.', 'cost' => '$150', 'duration' => '45 Mins', 'icon' => 'fa-tooth', 'bg' => 'teal'],
            ['name' => 'Physical Therapy', 'desc' => 'Rehabilitation session for muscle and joint recovery.', 'cost' => '$80', 'duration' => '60 Mins', 'icon' => 'fa-person-walking', 'bg' => 'purple'],
            ['name' => 'MRI Scanning', 'desc' => 'High-resolution diagnostic imaging of internal structures.', 'cost' => '$450', 'duration' => '30 Mins', 'icon' => 'fa-radiation', 'bg' => 'indigo'],
            ['name' => 'Vaccination', 'desc' => 'Standard immunization and booster management.', 'cost' => '$25', 'duration' => '15 Mins', 'icon' => 'fa-syringe', 'bg' => 'emerald'],
        ];
        foreach ($treatments as $t):
            ?>
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 hover:shadow-xl transition-all group">
                <div class="flex items-center gap-4 mb-6">
                    <div
                        class="w-14 h-14 bg-<?php echo $t['bg']; ?>-50 rounded-2xl flex items-center justify-center text-<?php echo $t['bg']; ?>-600 text-2xl shadow-inner group-hover:bg-<?php echo $t['bg']; ?>-600 group-hover:text-white transition-all">
                        <i class="fa-solid <?php echo $t['icon']; ?>"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-gray-800">
                            <?php echo $t['name']; ?>
                        </h3>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                            <?php echo $t['duration']; ?>
                        </p>
                    </div>
                </div>
                <p class="text-sm text-gray-500 leading-relaxed mb-6 italic">
                    <?php echo $t['desc']; ?>
                </p>
                <div class="flex items-center justify-between pt-6 border-t border-gray-50">
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Base Cost</div>
                    <div class="text-xl font-black text-gray-800">
                        <?php echo $t['cost']; ?>
                    </div>
                </div>
                <button
                    onclick="openAssignModal('<?php echo addslashes($t['name']); ?>')"
                    class="w-full mt-6 py-3 border border-gray-100 rounded-xl text-xs font-bold text-gray-500 group-hover:bg-gray-50 group-hover:text-gray-800 transition-all uppercase tracking-widest">Assign
                    to Patient</button>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Assign Treatment Modal -->
<div id="assign-treatment-modal" class="fixed inset-0 bg-black/50 z-[100] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden transform transition-all p-8">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-gray-800">Assign Treatment</h3>
            <button onclick="closeAssignModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <form id="assign-treatment-form" class="space-y-5">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Patient <span class="text-red-500">*</span></label>
                <select name="patient_id" id="patient-select" required
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all text-sm appearance-none cursor-pointer">
                    <option value="">Loading patients...</option>
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Treatment Protocol <span class="text-red-500">*</span></label>
                <select name="treatment_name" id="treatment-select" required
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all text-sm appearance-none cursor-pointer">
                    <option value="">-- Select Protocol --</option>
                    <?php foreach ($treatments as $t): ?>
                        <option value="<?php echo htmlspecialchars($t['name']); ?>"><?php echo htmlspecialchars($t['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Notes</label>
                <textarea name="notes" rows="3"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all text-sm resize-none"
                    placeholder="Specific instructions for this patient..."></textarea>
            </div>

            <input type="hidden" name="action" value="assign">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <button type="submit" id="submit-assignment"
                class="w-full py-4 bg-emerald-600 text-white rounded-xl font-bold text-sm hover:bg-emerald-700 shadow-lg shadow-emerald-100 transition-all flex justify-center items-center gap-2">
                <i class="fa-solid fa-check"></i>
                Confirm Assignment
            </button>
        </form>
    </div>
</div>

<script>
    const assignModal = document.getElementById('assign-treatment-modal');
    const patientSelect = document.getElementById('patient-select');
    const treatmentSelect = document.getElementById('treatment-select');
    let patientsLoaded = false;

    async function loadPatients() {
        if (patientsLoaded) return;
        try {
            const res = await fetch('get_patients_data.php');
            const data = await res.json();
            if (data.success) {
                patientSelect.innerHTML = '<option value="">-- Select Patient --</option>' + 
                    data.patients.map(p => `<option value="${p.id}">${p.full_name} (ID: PT-${String(p.id).padStart(3, '0')})</option>`).join('');
                patientsLoaded = true;
            } else {
                patientSelect.innerHTML = '<option value="">No patients found</option>';
            }
        } catch (e) {
            console.error('Failed to load patients', e);
            patientSelect.innerHTML = '<option value="">Error loading patients</option>';
        }
    }

    function openAssignModal(treatmentName = '') {
        assignModal.classList.remove('hidden');
        if (treatmentName) {
            treatmentSelect.value = treatmentName;
        }
        loadPatients();
    }

    function closeAssignModal() {
        assignModal.classList.add('hidden');
        document.getElementById('assign-treatment-form').reset();
    }
    
    // Also hook the Create New Plan button to open modal
    document.querySelector('button.bg-emerald-600').addEventListener('click', () => openAssignModal());

    document.getElementById('assign-treatment-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('submit-assignment');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Processing...';
        btn.disabled = true;

        try {
            const fd = new FormData(e.target);
            const res = await fetch('assign_treatment_handler.php', { method: 'POST', body: fd });
            const data = await res.json();
            
            if (data.success) {
                if (typeof showToast === 'function') showToast(data.message);
                else alert(data.message);
                closeAssignModal();
            } else {
                if (typeof showToast === 'function') showToast(data.message, 'error');
                else alert('Error: ' + data.message);
            }
        } catch (err) {
            if (typeof showToast === 'function') showToast('Assignment failed', 'error');
            else alert('Assignment failed');
        } finally {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    });

    // Close on background click
    assignModal.addEventListener('click', (e) => {
        if (e.target === assignModal) closeAssignModal();
    });
</script>

<?php require_once 'footer.php'; ?>