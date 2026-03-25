-- ============================================================
-- Migration Script: Add Missing Columns to Existing Database
-- Run this in phpMyAdmin if you already have the clinic_portal
-- database set up and don't want to re-import everything.
-- ============================================================
USE clinic_portal;
-- Add missing columns to users table
ALTER TABLE users
ADD COLUMN IF NOT EXISTS gender ENUM('Male', 'Female', 'Other') DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS dob DATE DEFAULT NULL;
-- Add missing columns to patients table
ALTER TABLE patients
ADD COLUMN IF NOT EXISTS blood_type VARCHAR(5) DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS status VARCHAR(20) DEFAULT 'Active';
-- Fix admin password to 'admin123' (valid bcrypt hash)
UPDATE users
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
WHERE username = 'admin';
-- Seed treatments if not already present (needed for appointment booking)
INSERT IGNORE INTO treatments (id, name, description, cost, duration)
VALUES (
        1,
        'General Checkup',
        'Routine health examination',
        50.00,
        '30 mins'
    ),
    (
        2,
        'Cardiac Care',
        'Heart health assessment and monitoring',
        150.00,
        '60 mins'
    ),
    (
        3,
        'Dental Cleaning',
        'Professional teeth cleaning and oral exam',
        80.00,
        '45 mins'
    ),
    (
        4,
        'Neurology Consult',
        'Neurological evaluation and diagnosis',
        200.00,
        '60 mins'
    ),
    (
        5,
        'Pediatric Visit',
        'Child health checkup and vaccination',
        60.00,
        '30 mins'
    );
SELECT 'Migration complete!' AS Status;