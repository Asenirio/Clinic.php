<?php
$page_title = "Need Help";
require_once 'header.php';
require_once 'sidebar.php';
?>

<div class="max-w-7xl mx-auto space-y-12 py-8 fade-in">
    <!-- Hero Section -->
    <div class="text-center max-w-3xl mx-auto space-y-4">
        <h2 class="text-4xl font-black text-gray-800">How can we help you today?</h2>
        <p class="text-lg text-gray-500 font-medium">Search our knowledge base or reach out to our dedicated support
            team.</p>
        <div class="relative max-w-2xl mx-auto mt-8">
            <i class="fa-solid fa-magnifying-glass absolute left-6 top-1/2 -translate-y-1/2 text-gray-400 text-xl"></i>
            <input type="text" placeholder="Search for articles, guides, or solutions..."
                class="w-full pl-16 pr-8 py-5 bg-white border-2 border-transparent shadow-2xl shadow-blue-100 rounded-[2rem] outline-none focus:border-blue-500 transition-all text-lg">
        </div>
    </div>

    <!-- Support Channels -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div
            class="bg-white rounded-[2rem] p-10 shadow-sm border border-gray-100 text-center hover:shadow-2xl hover:-translate-y-2 transition-all duration-300">
            <div
                class="w-20 h-20 bg-blue-50 rounded-3xl flex items-center justify-center text-blue-600 text-3xl mx-auto mb-8 shadow-inner">
                <i class="fa-solid fa-file-invoice"></i>
            </div>
            <h3 class="text-xl font-black text-gray-800 mb-2">Knowledge Base</h3>
            <p class="text-sm text-gray-500 leading-relaxed mb-8">Browse detailed guides and documentation for using the
                clinic portal.</p>
            <button
                class="w-full py-4 bg-gray-50 text-gray-800 rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-blue-600 hover:text-white transition-all">Explore
                Articles</button>
        </div>
        <div
            class="bg-white rounded-[2rem] p-10 shadow-xl border-2 border-blue-100 text-center hover:shadow-2xl transition-all duration-300 relative overflow-hidden">
            <div
                class="absolute top-0 right-0 px-4 py-1 bg-blue-600 text-white text-[10px] font-black uppercase tracking-widest rounded-bl-2xl">
                Recommended</div>
            <div
                class="w-20 h-20 bg-blue-600 rounded-3xl flex items-center justify-center text-white text-3xl mx-auto mb-8 shadow-xl shadow-blue-200">
                <i class="fa-solid fa-headset"></i>
            </div>
            <h3 class="text-xl font-black text-gray-800 mb-2">Live Support</h3>
            <p class="text-sm text-gray-500 leading-relaxed mb-8">Chat with our technical experts in real-time for
                immediate assistance.</p>
            <button
                class="w-full py-4 bg-blue-600 text-white rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-blue-700 shadow-xl shadow-blue-100 transition-all">Start
                Chatting</button>
        </div>
        <div
            class="bg-white rounded-[2rem] p-10 shadow-sm border border-gray-100 text-center hover:shadow-2xl hover:-translate-y-2 transition-all duration-300">
            <div
                class="w-20 h-20 bg-indigo-50 rounded-3xl flex items-center justify-center text-indigo-600 text-3xl mx-auto mb-8 shadow-inner">
                <i class="fa-solid fa-video"></i>
            </div>
            <h3 class="text-xl font-black text-gray-800 mb-2">Video Training</h3>
            <p class="text-sm text-gray-500 leading-relaxed mb-8">Watch step-by-step video tutorials to master the
                portal features.</p>
            <button
                class="w-full py-4 bg-gray-50 text-gray-800 rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-indigo-600 hover:text-white transition-all">Watch
                Tutorials</button>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="bg-white rounded-[3rem] p-12 shadow-sm border border-gray-100">
        <h3 class="text-3xl font-black text-gray-800 mb-12 text-center">Frequently Asked Questions</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
            <div class="space-y-2">
                <h4 class="font-black text-gray-800">How do I reset my admin password?</h4>
                <p class="text-sm text-gray-500">Go to My Account > Security and click on 'Change Password'. You will
                    need your current password to proceed.</p>
            </div>
            <div class="space-y-2">
                <h4 class="font-black text-gray-800">How do I add a new branch to my clinic?</h4>
                <p class="text-sm text-gray-500">Branches can be managed from the 'My Account' section under the
                    'Branches' tab. This requires a Premium subscription.</p>
            </div>
            <div class="space-y-2">
                <h4 class="font-black text-gray-800">Is my patient data secure?</h4>
                <p class="text-sm text-gray-500">Yes, DigitalRX uses end-to-end encryption for all patient medical
                    records and strictly follows HIPAA compliance standards.</p>
            </div>
            <div class="space-y-2">
                <h4 class="font-black text-gray-800">Can I customize my email templates?</h4>
                <p class="text-sm text-gray-500">Absolutely! Use our built-in template designer in the 'Email Templates'
                    section to match your clinic's branding.</p>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>