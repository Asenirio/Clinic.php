<?php
$page_title = "Enquiries";
require_once 'header.php';
require_once 'sidebar.php';
?>

<div class="max-w-7xl mx-auto space-y-6 fade-in">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Support Enquiries</h2>
            <p class="text-sm text-gray-500 font-medium">Manage patient questions and general leads</p>
        </div>
        <div class="flex gap-2">
            <button
                class="px-6 py-3 bg-white border border-gray-200 text-gray-600 rounded-2xl font-bold shadow-sm hover:bg-gray-50 transition-all">Export
                Leads</button>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div
            class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 text-center group cursor-pointer hover:bg-blue-600 transition-all duration-300">
            <h4 class="text-3xl font-black text-gray-800 group-hover:text-white">42</h4>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1 group-hover:text-blue-100">New
                Tickets</p>
        </div>
        <div
            class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 text-center group cursor-pointer hover:bg-amber-500 transition-all duration-300">
            <h4 class="text-3xl font-black text-gray-800 group-hover:text-white">15</h4>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1 group-hover:text-amber-100">In
                Progress</p>
        </div>
        <div
            class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 text-center group cursor-pointer hover:bg-emerald-500 transition-all duration-300">
            <h4 class="text-3xl font-black text-gray-800 group-hover:text-white">128</h4>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1 group-hover:text-emerald-100">
                Resolved</p>
        </div>
        <div
            class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 text-center group cursor-pointer hover:bg-rose-500 transition-all duration-300">
            <h4 class="text-3xl font-black text-gray-800 group-hover:text-white">99%</h4>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1 group-hover:text-rose-100">
                Health Score</p>
        </div>
    </div>

    <!-- Enquiries Table -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-50 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <button class="text-sm font-black text-blue-600 border-b-2 border-blue-600 pb-1">Priority</button>
                <button class="text-sm font-medium text-gray-400 hover:text-gray-600">Archived</button>
            </div>
            <div class="relative">
                <i class="fa-solid fa-filter absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <select
                    class="pl-10 pr-4 py-2 bg-gray-50 border border-transparent rounded-xl outline-none focus:ring-2 focus:ring-blue-500 text-sm appearance-none cursor-pointer">
                    <option>All Sources</option>
                    <option>Website</option>
                    <option>Mobile App</option>
                    <option>Calls</option>
                </select>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50/50 text-gray-500 text-[10px] font-black uppercase tracking-widest">
                    <tr>
                        <th class="px-8 py-5">Patient Name</th>
                        <th class="px-6 py-5">Subject</th>
                        <th class="px-6 py-5">Source</th>
                        <th class="px-6 py-5">Status</th>
                        <th class="px-6 py-5">Date</th>
                        <th class="px-8 py-5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php
                    $enquiries = [
                        ['name' => 'Alice Parker', 'subject' => 'Billing Clarification', 'source' => 'Website', 'status' => 'New', 'color' => 'blue', 'date' => '2 mins ago'],
                        ['name' => 'John Doe', 'subject' => 'Appointment Scheduling', 'source' => 'Mobile', 'status' => 'Urgent', 'color' => 'red', 'date' => '1 hour ago'],
                        ['name' => 'Robert Stone', 'subject' => 'Medical Records Request', 'source' => 'Facebook', 'status' => 'Working', 'color' => 'amber', 'date' => 'Yesterday'],
                        ['name' => 'Linda Blair', 'subject' => 'Insurance Verification', 'source' => 'Email', 'status' => 'Closed', 'color' => 'emerald', 'date' => 'Oct 22'],
                    ];
                    foreach ($enquiries as $e):
                        ?>
                        <tr class="hover:bg-slate-50/50 transition-all group">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center text-slate-500 font-bold shadow-inner">
                                        <?php echo substr($e['name'], 0, 1); ?>
                                    </div>
                                    <h4 class="font-bold text-gray-800 text-sm">
                                        <?php echo $e['name']; ?>
                                    </h4>
                                </div>
                            </td>
                            <td class="px-6 py-6 text-sm text-gray-600 font-medium">
                                <?php echo $e['subject']; ?>
                            </td>
                            <td class="px-6 py-6 text-xs font-bold text-gray-400">
                                <?php echo $e['source']; ?>
                            </td>
                            <td class="px-6 py-6">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-1.5 h-1.5 rounded-full bg-<?php echo $e['color'] === 'red' ? 'rose' : ($e['color'] === 'emerald' ? 'green' : $e['color']); ?>-500">
                                    </div>
                                    <span
                                        class="text-xs font-black text-<?php echo $e['color'] === 'red' ? 'rose' : ($e['color'] === 'emerald' ? 'green' : $e['color']); ?>-600 uppercase tracking-wider">
                                        <?php echo $e['status']; ?>
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-6 text-xs text-gray-400 font-medium">
                                <?php echo $e['date']; ?>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <button
                                    class="bg-blue-50 text-blue-600 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-600 hover:text-white transition-all shadow-sm">Reply
                                    Now</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>