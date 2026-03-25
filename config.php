<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php';

// Global Configuration
define('APP_NAME', 'DigitalRX.io');
define('BASE_URL', 'http://localhost/');

// Common Variables
$clinic_name = $_SESSION['clinic_name'] ?? "Northstar Clinic";
$admin_name = $_SESSION['admin_name'] ?? "Dr Paul Malone";
$clinic_email = $_SESSION['clinic_email'] ?? "northstar.digitalrx@gmail.com";
$clinic_phone = $_SESSION['clinic_phone'] ?? "91 1958363346";

// Navigation Items
$nav_items = [
    ['label' => 'Dashboard', 'icon' => 'fa-chart-pie', 'url' => 'dashboard.php'],
    ['label' => 'Staff Schedules', 'icon' => 'fa-solid fa-calendar-days', 'url' => 'schedules.php'],
    ['label' => 'My Account', 'icon' => 'fa-user', 'url' => 'my_account.php'],
    ['label' => 'Appointments', 'icon' => 'fa-regular fa-calendar-check', 'url' => 'appointments.php'],
    ['label' => 'Patients', 'icon' => 'fa-solid fa-bed-pulse', 'url' => 'patients.php'],
    ['label' => 'Doctors', 'icon' => 'fa-solid fa-stethoscope', 'url' => 'doctors.php'],
    ['label' => 'Pharmacy', 'icon' => 'fa-solid fa-pills', 'url' => 'pharmacy.php'],
    ['label' => 'Treatment', 'icon' => 'fa-solid fa-syringe', 'url' => 'treatment.php'],
    ['label' => 'Specialty', 'icon' => 'fa-solid fa-star', 'url' => 'specialty.php'],
    ['label' => 'Enquiries', 'icon' => 'fa-solid fa-circle-question', 'url' => 'enquiries.php'],
    ['label' => 'Email Templates', 'icon' => 'fa-solid fa-envelope-open-text', 'url' => 'email_templates.php'],
    ['label' => 'SEO', 'icon' => 'fa-solid fa-globe', 'url' => 'seo.php'],
    ['label' => 'Need Help', 'icon' => 'fa-solid fa-circle-info', 'url' => 'need_help.php'],
];

// Helper for active navigation
function is_active($url)
{
    return strpos($_SERVER['PHP_SELF'], $url) !== false ? 'active' : 'text-gray-600';
}
?>