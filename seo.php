<?php
$page_title = "SEO Settings";
require_once 'header.php';
require_once 'sidebar.php';
?>

<div class="max-w-7xl mx-auto space-y-6 fade-in">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Search Engine Optimization</h2>
            <p class="text-sm text-gray-500 font-medium">Configure metadata, titles and social sharing for each page</p>
        </div>
        <button
            class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-6 rounded-2xl shadow-lg shadow-emerald-100 flex items-center gap-2 transition-all">
            <i class="fa-solid fa-shield-heart"></i>
            Run SEO Audit
        </button>
    </div>

    <!-- SEO Score Card -->
    <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 flex flex-col md:flex-row gap-8 items-center">
        <div class="relative w-32 h-32 flex items-center justify-center">
            <svg class="w-full h-full transform -rotate-90">
                <circle cx="64" cy="64" r="56" stroke="currentColor" stroke-width="8" fill="transparent"
                    class="text-slate-100"></circle>
                <circle cx="64" cy="64" r="56" stroke="currentColor" stroke-width="8" fill="transparent"
                    stroke-dasharray="351.85" stroke-dashoffset="52.78" class="text-emerald-500"></circle>
            </svg>
            <div class="absolute flex flex-col items-center">
                <span class="text-3xl font-black text-gray-800">85</span>
                <span class="text-[10px] font-bold text-gray-400 uppercase">Score</span>
            </div>
        </div>
        <div class="flex-1 space-y-2">
            <h3 class="text-xl font-black text-gray-800">Your Clinic Visibility is Strong!</h3>
            <p class="text-sm text-gray-500 leading-relaxed">Your portal is well-optimized for local search results.
                We've detected 2 minor issues with meta-descriptions on your 'Pharmacy' page.</p>
            <div class="pt-4 flex gap-4">
                <div
                    class="flex items-center gap-2 text-xs font-bold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full">
                    <i class="fa-solid fa-check-circle"></i> Sitemap: OK</div>
                <div
                    class="flex items-center gap-2 text-xs font-bold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full">
                    <i class="fa-solid fa-check-circle"></i> Robot.txt: OK</div>
                <div class="flex items-center gap-2 text-xs font-bold text-rose-600 bg-rose-50 px-3 py-1 rounded-full">
                    <i class="fa-solid fa-triangle-exclamation"></i> 2 Tags Missing</div>
            </div>
        </div>
    </div>

    <!-- SEO Table -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead
                    class="bg-gray-50/50 text-gray-500 text-[10px] font-black uppercase tracking-widest border-b border-gray-100">
                    <tr>
                        <th class="px-8 py-5">Page Name</th>
                        <th class="px-6 py-5">Meta Title</th>
                        <th class="px-6 py-5">Keywords</th>
                        <th class="px-6 py-5">Index Status</th>
                        <th class="px-8 py-5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php
                    $seo_pages = [
                        ['name' => 'Home', 'title' => 'Northstar Clinic - Best Healthcare in Nairobi', 'keywords' => 'clinic, nairobi, health...', 'status' => 'Indexed', 'color' => 'emerald'],
                        ['name' => 'Services', 'title' => 'Our Medical Services | Northstar Clinic', 'keywords' => 'surgery, cardiology...', 'status' => 'Indexed', 'color' => 'emerald'],
                        ['name' => 'About Us', 'title' => 'Learn About Our Medical Expertise', 'keywords' => 'expertise, doctors...', 'status' => 'Indexed', 'color' => 'emerald'],
                        ['name' => 'Contact', 'title' => 'Contact Northstar Clinic Today', 'keywords' => 'contact, location...', 'status' => 'No-Index', 'color' => 'amber'],
                    ];
                    foreach ($seo_pages as $sp):
                        ?>
                        <tr class="hover:bg-slate-50/50 transition-all group">
                            <td class="px-8 py-6 font-black text-gray-800 text-sm">
                                <?php echo $sp['name']; ?>
                            </td>
                            <td class="px-6 py-6 text-sm text-gray-500">
                                <?php echo $sp['title']; ?>
                            </td>
                            <td class="px-6 py-6 text-[10px] font-bold text-gray-400 italic">
                                <?php echo $sp['keywords']; ?>
                            </td>
                            <td class="px-6 py-6">
                                <span
                                    class="px-3 py-1 bg-<?php echo $sp['color'] === 'emerald' ? 'green' : 'amber'; ?>-50 text-<?php echo $sp['color'] === 'emerald' ? 'green' : 'amber'; ?>-600 rounded-full text-[10px] font-bold uppercase ring-1 ring-inset">
                                    <?php echo $sp['status']; ?>
                                </span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <button
                                    class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 hover:text-blue-600 hover:bg-slate-100 transition-all flex items-center justify-center"><i
                                        class="fa-solid fa-gear"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>