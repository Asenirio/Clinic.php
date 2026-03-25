<?php
require_once 'auth.php';
require_role('admin');

// --- Live Stats from DB ---
$total_items   = 0;
$low_stock     = 0;
$expiring_soon = 0;
$today_revenue = 0;

try {
    $total_items   = $pdo->query("SELECT COUNT(*) FROM pharmacy_items")->fetchColumn();
    $low_stock     = $pdo->query("SELECT COUNT(*) FROM pharmacy_items WHERE stock > 0 AND stock < 20")->fetchColumn();
    $expiring_soon = $pdo->query("SELECT COUNT(*) FROM pharmacy_items WHERE expiry_date IS NOT NULL AND expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)")->fetchColumn();
    // Revenue: sum of (price * units sold today) — using a sales_logs table if it exists, else 0
    $rev_stmt = $pdo->query("SELECT IFNULL(SUM(total_amount),0) FROM pharmacy_sales WHERE DATE(created_at) = CURDATE()");
    $today_revenue = $rev_stmt ? $rev_stmt->fetchColumn() : 0;
} catch (PDOException $e) {
    // Tables may not all exist yet; fail silently
}

// --- Fetch Items (with search + category + state filters) ---
$search   = trim($_GET['search'] ?? '');
$category = trim($_GET['category'] ?? '');
$filter   = trim($_GET['filter'] ?? '');

$where_clauses = [];
$params        = [];

if ($search !== '') {
    $where_clauses[] = "item_name LIKE ?";
    $params[]        = "%{$search}%";
}
if ($category !== '' && $category !== 'all') {
    $where_clauses[] = "category = ?";
    $params[]        = $category;
}

// Add state filters from stats cards
if ($filter === 'low_stock') {
    $where_clauses[] = "stock > 0 AND stock < 20";
} elseif ($filter === 'expiring_soon') {
    $where_clauses[] = "expiry_date IS NOT NULL AND expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)";
}

$where_sql = $where_clauses ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

try {
    $stmt  = $pdo->prepare("SELECT * FROM pharmacy_items {$where_sql} ORDER BY created_at DESC");
    $stmt->execute($params);
    $items = $stmt->fetchAll();
} catch (PDOException $e) {
    $items = [];
    $db_error = $e->getMessage();
}

$page_title = "Pharmacy";
require_once 'header.php';
require_once 'sidebar.php';
?>

<div class="max-w-7xl mx-auto space-y-6 fade-in">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Pharmacy Inventory</h2>
            <p class="text-sm text-gray-500 font-medium">Track medical supplies and prescription stock</p>
        </div>
        <div class="flex gap-3">
            <a href="export_pharmacy.php"
                class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 px-5 rounded-2xl flex items-center gap-2 transition-all text-sm">
                <i class="fa-solid fa-file-export"></i> Export CSV
            </a>
            <button onclick="openPharmacyModal('add')"
                class="text-white font-bold py-3 px-6 rounded-2xl shadow-lg flex items-center gap-2 transition-all hover:scale-105 active:scale-95 text-sm"
                style="background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary)); box-shadow: 0 10px 20px -5px var(--brand-primary)">
                <i class="fa-solid fa-box-open"></i> Add New Item
            </button>
        </div>
    </div>

    <!-- Live Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <?php
        $stats_cards = [
            ['id' => '',              'label' => 'Total Items',    'value' => number_format($total_items),   'color' => 'blue',   'icon' => 'fa-boxes-stacked', 'pct' => 100],
            ['id' => 'low_stock',     'label' => 'Low Stock',      'value' => number_format($low_stock),     'color' => 'red',    'icon' => 'fa-triangle-exclamation', 'pct' => $total_items > 0 ? round($low_stock / $total_items * 100) : 0],
            ['id' => 'expiring_soon', 'label' => 'Expiring Soon',  'value' => number_format($expiring_soon), 'color' => 'orange', 'icon' => 'fa-clock-rotate-left', 'pct' => $total_items > 0 ? round($expiring_soon / $total_items * 100) : 0],
            ['id' => 'revenue',       'label' => "Today's Revenue",'value' => '$' . number_format($today_revenue, 2), 'color' => 'green',  'icon' => 'fa-sack-dollar', 'pct' => 100],
        ];
        foreach ($stats_cards as $card): 
            $isSelected = ($filter === $card['id']);
        ?>
        <a href="?filter=<?php echo $card['id']; ?><?php echo $category ? '&category='.$category : ''; ?><?php echo $search ? '&search='.$search : ''; ?>" 
           class="bg-white p-5 rounded-3xl shadow-sm border <?php echo $isSelected ? 'border-'.$card['color'].'-500 ring-2 ring-'.$card['color'].'-100' : 'border-gray-100'; ?> hover:shadow-md transition-all group block">
            <div class="flex items-center justify-between mb-3">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest"><?php echo $card['label']; ?></p>
                <div class="w-8 h-8 rounded-xl bg-<?php echo $card['color']; ?>-50 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fa-solid <?php echo $card['icon']; ?> text-<?php echo $card['color']; ?>-500 text-xs"></i>
                </div>
            </div>
            <h4 class="text-2xl font-black text-<?php echo $card['color']; ?>-<?php echo $card['color'] === 'blue' ? '800' : '500'; ?>"><?php echo $card['value']; ?></h4>
            <div class="mt-3 h-1 w-full bg-<?php echo $card['color']; ?>-100 rounded-full overflow-hidden">
                <div class="h-full bg-<?php echo $card['color']; ?>-500 rounded-full transition-all" style="width:<?php echo $card['pct']; ?>%"></div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- Inventory Table -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">

        <!-- Table Toolbar -->
        <div class="p-5 border-b border-gray-50 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <!-- Category Tabs -->
            <form method="GET" id="filter-form" class="flex items-center gap-2 flex-wrap">
                <?php if ($search): ?><input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>"><?php endif; ?>
                <?php if ($filter): ?><input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>"><?php endif; ?>
                <?php
                $cats = ['all' => 'All Items', 'Medicine' => 'Medicine', 'Surgical' => 'Surgical', 'Equipment' => 'Equipment', 'Supplement' => 'Supplement'];
                foreach ($cats as $val => $label):
                    $active = ($category === $val || ($val === 'all' && $category === ''));
                ?>
                <button type="submit" name="category" value="<?php echo $val; ?>"
                    class="<?php echo $active ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'; ?> px-4 py-2 rounded-xl text-xs font-bold transition-all">
                    <?php echo $label; ?>
                </button>
                <?php endforeach; ?>
            </form>

            <!-- Search -->
            <form method="GET" class="relative w-full sm:w-64">
                <?php if ($category && $category !== 'all'): ?><input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>"><?php endif; ?>
                <?php if ($filter): ?><input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>"><?php endif; ?>
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                    placeholder="Search pharmacy..."
                    class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-transparent rounded-xl outline-none focus:ring-2 focus:ring-blue-500 text-sm">
            </form>
        </div>

        <?php if (isset($db_error)): ?>
        <div class="px-8 py-4 bg-red-50 text-red-600 text-sm font-medium flex items-center gap-2">
            <i class="fa-solid fa-circle-exclamation"></i>
            Database error: <?php echo htmlspecialchars($db_error); ?>. Make sure the <code>pharmacy_items</code> table exists.
        </div>
        <?php endif; ?>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50/70 text-gray-500 text-[10px] font-bold uppercase tracking-widest border-b border-gray-100">
                    <tr>
                        <th class="px-8 py-4">Item Details</th>
                        <th class="px-6 py-4">Category</th>
                        <th class="px-6 py-4">Expiry Date</th>
                        <th class="px-6 py-4">Stock Level</th>
                        <th class="px-6 py-4">Unit Price</th>
                        <th class="px-8 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="6" class="px-8 py-16 text-center">
                            <div class="flex flex-col items-center gap-3 text-gray-400">
                                <i class="fa-solid fa-box-open text-4xl"></i>
                                <p class="font-bold text-sm">No pharmacy items found</p>
                                <p class="text-xs">Add your first supply using the button above.</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>

                    <?php foreach ($items as $item):
                        $stock = (int)$item['stock'];
                        if ($stock <= 0) {
                            $stock_label = 'Out of Stock'; $stock_color = 'red';
                        } elseif ($stock < 20) {
                            $stock_label = 'Low Stock'; $stock_color = 'orange';
                        } else {
                            $stock_label = 'In Stock'; $stock_color = 'green';
                        }

                        $expiry_display = '—';
                        $expiry_color   = 'gray';
                        if (!empty($item['expiry_date'])) {
                            $exp_ts = strtotime($item['expiry_date']);
                            $days   = (int)(($exp_ts - time()) / 86400);
                            $expiry_display = date('M d, Y', $exp_ts);
                            if ($days < 0)       $expiry_color = 'red';
                            elseif ($days <= 30)  $expiry_color = 'orange';
                            else                  $expiry_color = 'green';
                        }

                        $item_json = htmlspecialchars(json_encode([
                            'id'          => $item['id'],
                            'item_name'   => $item['item_name'],
                            'category'    => $item['category'],
                            'stock'       => $item['stock'],
                            'price'       => $item['price'],
                            'expiry_date' => $item['expiry_date'] ?? '',
                            'description' => $item['description'] ?? '',
                        ]), ENT_QUOTES);
                    ?>
                    <tr class="hover:bg-slate-50/50 transition-all group">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center text-slate-500 group-hover:bg-blue-600 group-hover:text-white transition-all shadow-inner flex-shrink-0">
                                    <i class="fa-solid fa-capsules text-sm"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800 text-sm"><?php echo htmlspecialchars($item['item_name']); ?></h4>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">ITEM-<?php echo str_pad($item['id'], 4, '0', STR_PAD_LEFT); ?></p>
                                    <?php if (!empty($item['description'])): ?>
                                    <p class="text-[11px] text-gray-400 mt-0.5 max-w-[200px] truncate"><?php echo htmlspecialchars($item['description']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <span class="bg-gray-50 text-gray-600 px-3 py-1 rounded-lg text-[10px] font-bold uppercase border border-gray-100">
                                <?php echo htmlspecialchars($item['category']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-5">
                            <span class="text-<?php echo $expiry_color; ?>-600 text-xs font-bold">
                                <?php echo $expiry_display; ?>
                            </span>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-black text-gray-800"><?php echo $stock; ?></span>
                                <span class="px-2 py-0.5 bg-<?php echo $stock_color; ?>-50 text-<?php echo $stock_color; ?>-600 rounded text-[10px] font-bold uppercase border border-<?php echo $stock_color; ?>-100">
                                    <?php echo $stock_label; ?>
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-5 font-bold text-gray-800 text-sm">
                            $<?php echo number_format($item['price'], 2); ?>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <button onclick='openPharmacyModal("edit", <?php echo $item_json; ?>)'
                                    class="text-gray-300 hover:text-blue-500 transition-colors" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button onclick="deleteItem(<?php echo $item['id']; ?>, '<?php echo htmlspecialchars(addslashes($item['item_name'])); ?>')"
                                    class="text-gray-300 hover:text-red-500 transition-colors" title="Delete">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Table Footer -->
        <div class="px-8 py-4 border-t border-gray-50 flex items-center justify-between">
            <p class="text-xs text-gray-400 font-medium">
                Showing <span class="font-bold text-gray-600"><?php echo count($items); ?></span> item<?php echo count($items) !== 1 ? 's' : ''; ?>
                <?php if ($search || ($category && $category !== 'all') || $filter): ?>
                    — filtered
                    <?php if ($search): ?> by "<strong><?php echo htmlspecialchars($search); ?></strong>"<?php endif; ?>
                    <?php if ($category && $category !== 'all'): ?> in <strong><?php echo htmlspecialchars($category); ?></strong><?php endif; ?>
                    <?php if ($filter): ?> by <strong><?php echo str_replace('_', ' ', $filter); ?></strong><?php endif; ?>
                    &nbsp;<a href="pharmacy.php" class="text-blue-500 hover:underline">Clear</a>
                <?php endif; ?>
            </p>
            <p class="text-xs text-gray-400">Last updated: <?php echo date('M d, Y H:i'); ?></p>
        </div>
    </div>
</div>

<!-- ===== Add / Edit Modal ===== -->
<div id="pharmacy-modal-overlay"
    class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden transform transition-all scale-95 opacity-0" id="pharmacy-modal-box">
        <div class="p-8">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-2xl font-black text-gray-800" id="modal-title">Add Inventory Item</h3>
                    <p class="text-xs text-gray-400 mt-1" id="modal-subtitle">Fill in the details below</p>
                </div>
                <button onclick="closePharmacyModal()" class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-500 transition-all">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="pharmacy-form" class="space-y-4">
                <input type="hidden" name="action" id="form-action" value="add">
                <input type="hidden" name="id"     id="form-id"     value="">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Item Name *</label>
                        <input type="text" name="item_name" id="form-item_name" required
                            class="w-full px-4 py-3 bg-gray-50 border border-transparent rounded-xl outline-none focus:ring-2 focus:ring-blue-500 text-sm transition-all"
                            placeholder="e.g. Amoxicillin 500mg">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Category *</label>
                        <select name="category" id="form-category" required
                            class="w-full px-4 py-3 bg-gray-50 border border-transparent rounded-xl outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="Medicine">Medicine</option>
                            <option value="Surgical">Surgical</option>
                            <option value="Equipment">Equipment</option>
                            <option value="Supplement">Supplement</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Expiry Date</label>
                        <input type="date" name="expiry_date" id="form-expiry_date"
                            class="w-full px-4 py-3 bg-gray-50 border border-transparent rounded-xl outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Stock Qty *</label>
                        <input type="number" name="stock" id="form-stock" required min="0"
                            class="w-full px-4 py-3 bg-gray-50 border border-transparent rounded-xl outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                            placeholder="0">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Unit Price ($) *</label>
                        <input type="number" step="0.01" name="price" id="form-price" required min="0"
                            class="w-full px-4 py-3 bg-gray-50 border border-transparent rounded-xl outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                            placeholder="0.00">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Description</label>
                        <textarea name="description" id="form-description" rows="2"
                            class="w-full px-4 py-3 bg-gray-50 border border-transparent rounded-xl outline-none focus:ring-2 focus:ring-blue-500 text-sm resize-none"
                            placeholder="Optional notes about this item..."></textarea>
                    </div>
                </div>

                <button type="submit" id="form-submit-btn"
                    class="w-full py-4 text-white rounded-2xl font-black uppercase tracking-widest text-xs shadow-xl transition-all hover:scale-[1.02] active:scale-[0.98]"
                    style="background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary))">
                    <i class="fa-solid fa-box-open mr-2"></i> Add to Stock
                </button>
            </form>
        </div>
    </div>
</div>

<!-- ===== Delete Confirm Modal ===== -->
<div id="delete-modal-overlay"
    class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-sm w-full p-8 text-center">
        <div class="w-16 h-16 bg-red-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-trash-can text-red-500 text-2xl"></i>
        </div>
        <h3 class="text-xl font-black text-gray-800 mb-2">Delete Item?</h3>
        <p class="text-sm text-gray-500 mb-6">You are about to remove <strong id="delete-item-name" class="text-gray-800"></strong> from inventory. This cannot be undone.</p>
        <div class="flex gap-3">
            <button onclick="closeDeleteModal()" class="flex-1 py-3 border-2 border-gray-100 text-gray-500 rounded-2xl font-bold text-sm hover:bg-gray-50 transition-all">Cancel</button>
            <button id="confirm-delete-btn" class="flex-1 py-3 bg-red-500 hover:bg-red-600 text-white rounded-2xl font-bold text-sm shadow-lg shadow-red-100 transition-all">Delete</button>
        </div>
    </div>
</div>

<script>
// ---- Modal helpers ----
function openPharmacyModal(mode, item = null) {
    const overlay = document.getElementById('pharmacy-modal-overlay');
    const box     = document.getElementById('pharmacy-modal-box');

    // Reset form
    document.getElementById('pharmacy-form').reset();

    if (mode === 'edit' && item) {
        document.getElementById('modal-title').textContent    = 'Edit Inventory Item';
        document.getElementById('modal-subtitle').textContent = 'Update the details below';
        document.getElementById('form-action').value          = 'edit';
        document.getElementById('form-id').value              = item.id;
        document.getElementById('form-item_name').value       = item.item_name;
        document.getElementById('form-category').value        = item.category;
        document.getElementById('form-stock').value           = item.stock;
        document.getElementById('form-price').value           = item.price;
        document.getElementById('form-expiry_date').value     = item.expiry_date;
        document.getElementById('form-description').value     = item.description;
        document.getElementById('form-submit-btn').innerHTML  = '<i class="fa-solid fa-floppy-disk mr-2"></i> Save Changes';
    } else {
        document.getElementById('modal-title').textContent    = 'Add Inventory Item';
        document.getElementById('modal-subtitle').textContent = 'Fill in the details below';
        document.getElementById('form-action').value          = 'add';
        document.getElementById('form-id').value              = '';
        document.getElementById('form-submit-btn').innerHTML  = '<i class="fa-solid fa-box-open mr-2"></i> Add to Stock';
    }

    overlay.classList.remove('hidden');
    setTimeout(() => {
        box.classList.remove('scale-95', 'opacity-0');
        box.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function closePharmacyModal() {
    const overlay = document.getElementById('pharmacy-modal-overlay');
    const box     = document.getElementById('pharmacy-modal-box');
    box.classList.add('scale-95', 'opacity-0');
    box.classList.remove('scale-100', 'opacity-100');
    setTimeout(() => overlay.classList.add('hidden'), 200);
}

// Close on overlay click
document.getElementById('pharmacy-modal-overlay').addEventListener('click', function(e) {
    if (e.target === this) closePharmacyModal();
});

// ---- Form Submit (Add / Edit) ----
document.getElementById('pharmacy-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('form-submit-btn');
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Saving...';
    btn.disabled  = true;

    try {
        const response = await fetch('pharmacy_handler.php', {
            method: 'POST',
            body: new FormData(e.target)
        });
        const result = await response.json();
        if (result.success) {
            showToast(result.message, 'success');
            closePharmacyModal();
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(result.message, 'error');
            btn.innerHTML = originalHTML;
            btn.disabled  = false;
        }
    } catch (err) {
        showToast('Network error. Please try again.', 'error');
        btn.innerHTML = originalHTML;
        btn.disabled  = false;
    }
});

// ---- Delete ----
let pendingDeleteId = null;

function deleteItem(id, name) {
    pendingDeleteId = id;
    document.getElementById('delete-item-name').textContent = name;
    document.getElementById('delete-modal-overlay').classList.remove('hidden');
}

function closeDeleteModal() {
    pendingDeleteId = null;
    document.getElementById('delete-modal-overlay').classList.add('hidden');
}

document.getElementById('confirm-delete-btn').addEventListener('click', async () => {
    if (!pendingDeleteId) return;
    const btn = document.getElementById('confirm-delete-btn');
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
    btn.disabled  = true;

    const formData = new FormData();
    formData.append('id',         pendingDeleteId);
    formData.append('action',     'delete');
    formData.append('csrf_token', '<?php echo $_SESSION['csrf_token']; ?>');

    try {
        const response = await fetch('pharmacy_handler.php', { method: 'POST', body: formData });
        const result   = await response.json();
        if (result.success) {
            showToast(result.message, 'success');
            closeDeleteModal();
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(result.message, 'error');
            btn.innerHTML = 'Delete';
            btn.disabled  = false;
        }
    } catch (err) {
        showToast('Delete failed. Try again.', 'error');
        btn.innerHTML = 'Delete';
        btn.disabled  = false;
    }
});

document.getElementById('delete-modal-overlay').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});
</script>

<?php require_once 'footer.php'; ?>