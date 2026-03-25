<?php
require_once 'auth.php';
$page_title = "Email Templates";
require_once 'header.php';
require_once 'sidebar.php';

// Fetch templates from database
try {
    $stmt = $pdo->query("SELECT * FROM email_templates ORDER BY created_at DESC");
    $templates = $stmt->fetchAll();
} catch (PDOException $e) {
    $templates = [];
    $error = "Error: " . $e->getMessage();
}

$colors = ['blue', 'indigo', 'emerald', 'amber', 'rose', 'purple', 'cyan'];
?>

<div class="max-w-7xl mx-auto space-y-6 fade-in">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Email Communication</h2>
            <p class="text-sm text-gray-500 font-medium">Design and manage automated clinic emails</p>
        </div>
        <button onclick="openTemplateModal('add')"
            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-2xl shadow-lg shadow-indigo-100 flex items-center gap-2 transition-all active:scale-95">
            <i class="fa-solid fa-paintbrush"></i>
            Design Template
        </button>
    </div>

    <!-- Templates Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php if (empty($templates)): ?>
            <div class="col-span-full py-20 text-center bg-white rounded-[2rem] border border-dashed border-gray-200">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center text-gray-300 mx-auto mb-4">
                    <i class="fa-solid fa-envelope-open-text text-3xl"></i>
                </div>
                <p class="text-gray-500 font-bold">No templates found.</p>
                <p class="text-gray-400 text-xs mt-1">Start by designing your first communication template.</p>
            </div>
        <?php endif; ?>

        <?php foreach ($templates as $idx => $t): 
            $color = $colors[$idx % count($colors)];
            $template_json = htmlspecialchars(json_encode($t), ENT_QUOTES);
        ?>
            <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-gray-100 hover:shadow-2xl hover:scale-[1.02] transition-all duration-300 group relative">
                <div class="flex items-center justify-between mb-8">
                    <div class="w-14 h-14 bg-<?php echo $color; ?>-50 rounded-2xl flex items-center justify-center text-<?php echo $color; ?>-600 text-2xl shadow-inner group-hover:bg-<?php echo $color; ?>-600 group-hover:text-white transition-all">
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                    <span class="px-3 py-1 bg-gray-50 text-gray-400 group-hover:text-gray-800 rounded-lg text-[10px] font-black uppercase tracking-widest transition-colors">
                        <?php echo htmlspecialchars($t['type'] ?: 'General'); ?>
                    </span>
                </div>

                <h3 class="text-xl font-black text-gray-800 mb-2">
                    <?php echo htmlspecialchars($t['name']); ?>
                </h3>
                <p class="text-sm text-gray-500 mb-8 italic line-clamp-1">Subject:
                    <?php echo htmlspecialchars($t['subject']); ?>
                </p>

                <div class="flex items-center justify-between border-t border-gray-50 pt-6">
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Created</p>
                        <p class="text-xs font-black text-gray-700">
                            <?php echo date('M d, Y', strtotime($t['created_at'])); ?>
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <button onclick='openTemplateModal("edit", <?php echo $template_json; ?>)'
                            class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 hover:text-indigo-600 hover:bg-slate-100 transition-all flex items-center justify-center" title="Edit Template">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button onclick='previewTemplate(<?php echo $template_json; ?>)'
                            class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 hover:text-blue-600 hover:bg-slate-100 transition-all flex items-center justify-center" title="Preview">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                        <button onclick='deleteTemplate(<?php echo $t['id']; ?>, "<?php echo addslashes($t['name']); ?>")'
                            class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 hover:text-red-600 hover:bg-slate-100 transition-all flex items-center justify-center" title="Delete">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Add/Edit Template Modal -->
<div id="template-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-[2.5rem] shadow-2xl max-w-2xl w-full overflow-hidden transform transition-all scale-95 opacity-0 modal-content p-10">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h3 class="text-3xl font-black text-gray-800" id="modal-title">Design Template</h3>
                <p class="text-xs text-gray-400 mt-1 uppercase font-bold tracking-widest" id="modal-subtitle">Craft professional clinic emails</p>
            </div>
            <button onclick="closeTemplateModal()" class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="template-form" class="space-y-6">
            <input type="hidden" name="action" id="form-action" value="add">
            <input type="hidden" name="id" id="form-id" value="">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Template Name</label>
                    <input type="text" name="name" id="form-name" required
                        class="w-full px-5 py-4 bg-gray-50 border border-transparent rounded-2xl outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all text-sm font-bold"
                        placeholder="e.g. Appointment Reminder">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Category / Type</label>
                    <input type="text" name="type" id="form-type"
                        class="w-full px-5 py-4 bg-gray-50 border border-transparent rounded-2xl outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all text-sm font-bold"
                        placeholder="e.g. Transactional">
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Email Subject</label>
                <input type="text" name="subject" id="form-subject" required
                    class="w-full px-5 py-4 bg-gray-50 border border-transparent rounded-2xl outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all text-sm font-bold"
                    placeholder="Enter email subject line">
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Email Body (HTML Supported)</label>
                <textarea name="body" id="form-body" rows="8" required
                    class="w-full px-5 py-4 bg-gray-50 border border-transparent rounded-2xl outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all text-sm font-mono leading-relaxed resize-none"
                    placeholder="<h1>Hello {{patient_name}}</h1>..."></textarea>
            </div>

            <button type="submit" id="form-submit-btn"
                class="w-full py-5 bg-indigo-600 text-white rounded-[1.5rem] font-black uppercase tracking-widest text-xs hover:bg-indigo-700 shadow-xl shadow-indigo-100 active:scale-[0.98] transition-all">
                Save Template
            </button>
        </form>
    </div>
</div>

<!-- Preview Modal -->
<div id="preview-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[110] hidden flex items-center justify-center p-4">
    <div class="bg-gray-100 rounded-[2.5rem] shadow-2xl max-w-4xl w-full h-[80vh] overflow-hidden transform transition-all scale-95 opacity-0 modal-content flex flex-col">
        <div class="bg-white p-6 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h3 class="text-xl font-black text-gray-800" id="preview-title">Template Preview</h3>
                <p class="text-xs text-gray-400 font-bold uppercase" id="preview-subject"></p>
            </div>
            <button onclick="closePreviewModal()" class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-gray-100 transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div id="preview-body" class="flex-1 overflow-auto p-8 bg-white m-6 rounded-2xl shadow-inner">
            <!-- Rendered HTML content goes here -->
        </div>
    </div>
</div>

<script>
function openTemplateModal(mode, data = null) {
    const modal = document.getElementById('template-modal');
    const content = modal.querySelector('.modal-content');
    const form = document.getElementById('template-form');
    
    form.reset();
    document.getElementById('form-id').value = '';

    if (mode === 'edit' && data) {
        document.getElementById('modal-title').innerText = 'Edit Template';
        document.getElementById('form-action').value = 'edit';
        document.getElementById('form-id').value = data.id;
        document.getElementById('form-name').value = data.name;
        document.getElementById('form-type').value = data.type;
        document.getElementById('form-subject').value = data.subject;
        document.getElementById('form-body').value = data.body;
        document.getElementById('form-submit-btn').innerText = 'Update Template';
    } else {
        document.getElementById('modal-title').innerText = 'Design Template';
        document.getElementById('form-action').value = 'add';
        document.getElementById('form-submit-btn').innerText = 'Create Template';
    }

    modal.classList.remove('hidden');
    setTimeout(() => {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function closeTemplateModal() {
    const modal = document.getElementById('template-modal');
    const content = modal.querySelector('.modal-content');
    content.classList.add('scale-95', 'opacity-0');
    content.classList.remove('scale-100', 'opacity-100');
    setTimeout(() => modal.classList.add('hidden'), 200);
}

function previewTemplate(data) {
    const modal = document.getElementById('preview-modal');
    const content = modal.querySelector('.modal-content');
    document.getElementById('preview-title').innerText = data.name;
    document.getElementById('preview-subject').innerText = 'Subject: ' + data.subject;
    document.getElementById('preview-body').innerHTML = data.body;

    modal.classList.remove('hidden');
    setTimeout(() => {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function closePreviewModal() {
    const modal = document.getElementById('preview-modal');
    const content = modal.querySelector('.modal-content');
    content.classList.add('scale-95', 'opacity-0');
    content.classList.remove('scale-100', 'opacity-100');
    setTimeout(() => modal.classList.add('hidden'), 200);
}

document.getElementById('template-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('form-submit-btn');
    const originalText = btn.innerText;
    btn.innerText = 'Processing...';
    btn.disabled = true;

    try {
        const formData = new FormData(e.target);
        const response = await fetch('email_handler.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        if (result.success) {
            showToast(result.message);
            closeTemplateModal();
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(result.message, 'error');
        }
    } catch (error) {
        showToast('Something went wrong.', 'error');
    } finally {
        btn.innerText = originalText;
        btn.disabled = false;
    }
});

async function deleteTemplate(id, name) {
    if (!confirm(`Permanently delete the '${name}' template?`)) return;

    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', id);
    formData.append('csrf_token', '<?php echo $_SESSION['csrf_token']; ?>');

    try {
        const response = await fetch('email_handler.php', {
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
        showToast('Delete failed.', 'error');
    }
}
</script>

<?php require_once 'footer.php'; ?>