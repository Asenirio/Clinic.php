</main>
</div>

<!-- Modals Container -->
<div id="modal-container" class="fixed inset-0 bg-black/50 z-[100] hidden flex items-center justify-center p-4">
    <!-- Help Modal -->
    <div id="help-modal" class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden hidden p-8">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Need Assistance?</h3>
        <div class="space-y-4">
            <a href="mailto:support@digitalrx.io"
                class="flex items-center gap-4 p-4 border rounded-xl hover:bg-blue-50 group transition-all">
                <div
                    class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-all">
                    <i class="fa-solid fa-envelope text-xl"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800">Email Support</h4>
                    <p class="text-sm text-gray-500">Response within 24h</p>
                </div>
            </a>
        </div>
        <button onclick="closeModal()"
            class="w-full mt-6 py-2 text-gray-400 hover:text-gray-600 underline">Close</button>
    </div>

    <!-- Appointment Modal -->
    <div id="appointment-modal"
        class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden hidden transform transition-all p-8">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-black text-gray-800 tracking-tight">Book Appointment</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors"><i
                    class="fa-solid fa-xmark text-xl"></i></button>
        </div>
        <form id="appointment-form" class="space-y-6">
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label
                        class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Patient
                        Name</label>
                    <input type="text" name="patient_name" required
                        class="w-full px-4 py-3 bg-gray-50 border border-transparent rounded-xl outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-sm"
                        placeholder="Enter full name">
                </div>
                <div>
                    <label
                        class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Doctor</label>
                    <select name="doctor_id" id="modal-doctor-select" required
                        class="w-full px-4 py-3 bg-gray-50 border border-transparent rounded-xl outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-sm appearance-none cursor-pointer">
                        <option value="">Loading doctors...</option>
                    </select>
                </div>
                <div>
                    <label
                        class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Treatment</label>
                    <select name="treatment_id" id="modal-treatment-select"
                        class="w-full px-4 py-3 bg-gray-50 border border-transparent rounded-xl outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-sm appearance-none cursor-pointer">
                        <option value="">Loading treatments...</option>
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Date
                        & Time</label>
                    <input type="datetime-local" name="appointment_date" required
                        class="w-full px-4 py-3 bg-gray-50 border border-transparent rounded-xl outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-sm">
                </div>
                <div class="col-span-2">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Notes
                        (Optional)</label>
                    <textarea name="notes" rows="3"
                        class="w-full px-4 py-3 bg-gray-50 border border-transparent rounded-xl outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-sm resize-none"
                        placeholder="Special requirements..."></textarea>
                </div>
            </div>

            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <button type="submit" id="submit-appointment"
                class="w-full py-4 bg-blue-600 text-white rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-blue-700 shadow-xl shadow-blue-100 active:scale-[0.98] transition-all">
                Finalize Booking
            </button>
        </form>
    </div>
</div>

<div id="toast-container" class="fixed bottom-6 right-6 z-[200] space-y-3"></div>

<script>
    function openModal(id) {
        document.getElementById('modal-container').classList.remove('hidden');
        document.getElementById(id).classList.remove('hidden');
        if (id === 'appointment-modal') loadAppointmentFormData();
    }
    function closeModal() {
        document.getElementById('modal-container').classList.add('hidden');
        document.querySelectorAll('#modal-container > div').forEach(m => m.classList.add('hidden'));
    }

    // Load doctors & treatments dynamically
    let formDataLoaded = false;
    async function loadAppointmentFormData() {
        if (formDataLoaded) return;
        try {
            const res = await fetch('get_form_data.php');
            const data = await res.json();
            if (data.success) {
                const doctorSel = document.getElementById('modal-doctor-select');
                const treatSel  = document.getElementById('modal-treatment-select');
                if (doctorSel) {
                    doctorSel.innerHTML = data.doctors.length
                        ? data.doctors.map(d => `<option value="${d.id}">${d.full_name}${d.specialty ? ' — ' + d.specialty : ''}</option>`).join('')
                        : '<option value="">No doctors registered yet</option>';
                }
                if (treatSel) {
                    treatSel.innerHTML = data.treatments.length
                        ? '<option value="">-- Select Treatment (Optional) --</option>' + data.treatments.map(t => `<option value="${t.id}">${t.name}</option>`).join('')
                        : '<option value="">No treatments found</option>';
                }
                formDataLoaded = true;
            }
        } catch (e) {
            console.error('Failed to load form data:', e);
        }
    }
    document.getElementById('mobile-menu-toggle')?.addEventListener('click', () => {
        document.querySelector('aside').classList.toggle('hidden');
    });

    // Toast Helper
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        const bgColor = type === 'success' ? 'bg-emerald-600' : 'bg-rose-600';
        toast.className = `${bgColor} text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3 fade-in`;
        toast.innerHTML = `<i class="fa-solid ${type === 'success' ? 'fa-check-circle' : 'fa-circle-exclamation'}"></i> <span class="text-sm font-bold">${message}</span>`;
        document.getElementById('toast-container').appendChild(toast);
        setTimeout(() => toast.remove(), 4000);
    }

    // AJAX Appointment Submission
    document.getElementById('appointment-form')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('submit-appointment');
        const originalText = btn.innerText;
        btn.innerText = 'Processing...';
        btn.disabled = true;

        try {
            const formData = new FormData(e.target);
            const response = await fetch('booking_handler.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (result.success) {
                showToast(result.message);
                closeModal();
                e.target.reset();
                if (window.location.pathname.includes('appointments.php')) setTimeout(() => window.location.reload(), 1500);
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
</script>
</body>

</html>