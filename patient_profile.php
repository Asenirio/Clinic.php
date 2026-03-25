<?php
require_once 'auth.php';
require_role('admin');

$id = $_GET['id'] ?? 0;

try {
    $stmt = $pdo->prepare("
        SELECT p.*, u.full_name, u.email, u.dob, u.gender 
        FROM patients p 
        JOIN users u ON p.user_id = u.id 
        WHERE p.id = ?
    ");
    $stmt->execute([$id]);
    $patient = $stmt->fetch();

    if (!$patient) {
        die("Patient not found.");
    }

    log_activity('View Patient Profile', 'Patients', "Viewed profile for Patient ID: {$id}");
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Patient Record -
        <?php echo htmlspecialchars($patient['full_name']); ?>
    </title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print {
                display: none;
            }

            body {
                background: white;
            }

            .print-border {
                border: 1px solid #e2e8f0;
            }
        }
    </style>
</head>

<body class="bg-gray-50 p-8">
    <div class="max-w-4xl mx-auto bg-white p-10 rounded-3xl shadow-sm border border-gray-100 print-border">
        <div class="flex justify-between items-start border-b pb-8 mb-8">
            <div>
                <h1 class="text-3xl font-black text-gray-800 uppercase tracking-tighter">Medical Record</h1>
                <p class="text-sm text-gray-400 font-bold mt-1">DigitalRX.io Healthcare Portal</p>
            </div>
            <div class="text-right">
                <button onclick="window.print()"
                    class="no-print bg-blue-600 text-white px-6 py-2 rounded-xl text-sm font-bold shadow-lg hover:bg-blue-700 transition-all">Print
                    / Save as PDF</button>
                <div class="mt-2 text-[10px] text-gray-400 font-bold uppercase tracking-widest">Date Generated:
                    <?php echo date('Y-m-d H:i'); ?>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-12 mb-12">
            <div>
                <h3 class="text-[10px] font-bold text-blue-600 uppercase tracking-widest mb-4">Patient Information</h3>
                <div class="space-y-3">
                    <div class="flex justify-between border-b border-gray-50 pb-2">
                        <span class="text-xs text-gray-400 font-medium">Full Name</span>
                        <span class="text-sm font-bold text-gray-800">
                            <?php echo htmlspecialchars($patient['full_name']); ?>
                        </span>
                    </div>
                    <div class="flex justify-between border-b border-gray-50 pb-2">
                        <span class="text-xs text-gray-400 font-medium">Date of Birth</span>
                        <span class="text-sm font-bold text-gray-800">
                            <?php echo htmlspecialchars($patient['dob']); ?>
                        </span>
                    </div>
                    <div class="flex justify-between border-b border-gray-50 pb-2">
                        <span class="text-xs text-gray-400 font-medium">Gender</span>
                        <span class="text-sm font-bold text-gray-800">
                            <?php echo htmlspecialchars($patient['gender']); ?>
                        </span>
                    </div>
                    <div class="flex justify-between border-b border-gray-50 pb-2">
                        <span class="text-xs text-gray-400 font-medium">Blood Group</span>
                        <span class="text-sm font-bold text-gray-800">
                            <?php echo htmlspecialchars($patient['blood_group'] ?? 'N/A'); ?>
                        </span>
                    </div>
                </div>
            </div>
            <div>
                <h3 class="text-[10px] font-bold text-blue-600 uppercase tracking-widest mb-4">Security & Status</h3>
                <div class="space-y-3">
                    <div class="flex justify-between border-b border-gray-50 pb-2">
                        <span class="text-xs text-gray-400 font-medium">Patient ID</span>
                        <span class="text-sm font-bold text-gray-800">P-
                            <?php echo str_pad($patient['id'], 4, '0', STR_PAD_LEFT); ?>
                        </span>
                    </div>
                    <div class="flex justify-between border-b border-gray-50 pb-2">
                        <span class="text-xs text-gray-400 font-medium">Contact</span>
                        <span class="text-sm font-bold text-gray-800 text-blue-500">
                            <?php echo htmlspecialchars($patient['email']); ?>
                        </span>
                    </div>
                    <div class="flex justify-between border-b border-gray-50 pb-2">
                        <span class="text-xs text-gray-400 font-medium">Account Status</span>
                        <span class="text-[10px] px-2 py-0.5 bg-green-50 text-green-600 rounded font-black uppercase">
                            <?php echo htmlspecialchars($patient['status'] ?? 'Active'); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-12">
            <h3 class="text-[10px] font-bold text-blue-600 uppercase tracking-widest mb-4">Medical History / Notes</h3>
            <div
                class="bg-gray-50 p-6 rounded-2xl min-h-[150px] text-sm text-gray-600 leading-relaxed italic border border-gray-100 italic">
                <?php echo nl2br(htmlspecialchars($patient['medical_history'] ?? 'No medical history recorded.')); ?>
            </div>
        </div>

        <div class="border-t pt-8 text-center">
            <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">This document is a confidential
                medical record. Unauthorized disclosure is strictly prohibited.</p>
        </div>
    </div>
</body>

</html>