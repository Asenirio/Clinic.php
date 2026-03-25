-- Database Setup for Clinic Portal
CREATE DATABASE IF NOT EXISTS clinic_portal;
USE clinic_portal;
-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM(
        'admin',
        'doctor',
        'patient',
        'staff',
        'pharmacist'
    ) DEFAULT 'patient',
    gender ENUM('Male', 'Female', 'Other') DEFAULT NULL,
    dob DATE DEFAULT NULL,
    avatar VARCHAR(255) DEFAULT 'img/default-avatar.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- Clinics Table
CREATE TABLE IF NOT EXISTS clinics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    admin_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    country VARCHAR(100) DEFAULT 'Kenya',
    timezone VARCHAR(100) DEFAULT 'E. Africa Standard Time',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- Specialties Table
CREATE TABLE IF NOT EXISTS specialties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    icon VARCHAR(50) DEFAULT 'fa-stethoscope',
    description TEXT
);
-- Doctors Table
CREATE TABLE IF NOT EXISTS doctors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    specialty_id INT,
    bio TEXT,
    fees DECIMAL(10, 2),
    availability VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (specialty_id) REFERENCES specialties(id) ON DELETE
    SET NULL
);
-- Patients Table
CREATE TABLE IF NOT EXISTS patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    blood_group VARCHAR(5),
    blood_type VARCHAR(5) DEFAULT NULL,
    status VARCHAR(20) DEFAULT 'Active',
    address TEXT,
    emergency_contact VARCHAR(100),
    medical_history TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
-- Pharmacy Items Table
CREATE TABLE IF NOT EXISTS pharmacy_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(255) NOT NULL,
    category VARCHAR(100) DEFAULT 'Medicine',
    stock INT DEFAULT 0,
    price DECIMAL(10, 2) DEFAULT 0.00,
    expiry_date DATE DEFAULT NULL,
    description TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- Pharmacy Sales Table (for revenue tracking)
CREATE TABLE IF NOT EXISTS pharmacy_sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT,
    quantity INT NOT NULL DEFAULT 1,
    unit_price DECIMAL(10, 2) NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    sold_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (item_id) REFERENCES pharmacy_items(id) ON DELETE
    SET NULL,
        FOREIGN KEY (sold_by) REFERENCES users(id) ON DELETE
    SET NULL
);
-- Add description column if upgrading existing DB
ALTER TABLE pharmacy_items
ADD COLUMN IF NOT EXISTS description TEXT DEFAULT NULL;
-- Treatments Table
CREATE TABLE IF NOT EXISTS treatments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    cost DECIMAL(10, 2),
    duration VARCHAR(50)
);
-- Appointments Table
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT,
    doctor_id INT,
    treatment_id INT,
    appointment_date DATETIME,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    FOREIGN KEY (treatment_id) REFERENCES treatments(id) ON DELETE
    SET NULL
);
-- Chat Messages Table
CREATE TABLE IF NOT EXISTS chat_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT,
    receiver_id INT,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id),
    FOREIGN KEY (receiver_id) REFERENCES users(id)
);
-- Promotions Table
CREATE TABLE IF NOT EXISTS promotions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    discount_percent INT DEFAULT 0,
    start_date DATE,
    end_date DATE,
    image_url VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE
);
-- Enquiries Table
CREATE TABLE IF NOT EXISTS enquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(255),
    message TEXT,
    status ENUM('new', 'in_progress', 'resolved') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- Email Templates Table
CREATE TABLE IF NOT EXISTS email_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    type VARCHAR(50) COMMENT 'e.g., appointment_reminder, welcome_email',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- SEO Settings Table
CREATE TABLE IF NOT EXISTS seo_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_name VARCHAR(100) UNIQUE NOT NULL,
    meta_title VARCHAR(255),
    meta_description TEXT,
    meta_keywords VARCHAR(255),
    og_image VARCHAR(255)
);
-- Content Blocks Table
CREATE TABLE IF NOT EXISTS content_blocks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    identifier VARCHAR(100) UNIQUE NOT NULL,
    title VARCHAR(255),
    content TEXT,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
-- Dashboard Stats Table
CREATE TABLE IF NOT EXISTS stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(100) NOT NULL,
    value INT DEFAULT 0,
    icon VARCHAR(100),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
-- Activity Feed Table
CREATE TABLE IF NOT EXISTS activities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    meta VARCHAR(255),
    icon VARCHAR(50),
    bg_color VARCHAR(50),
    text_color VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- Audit Logs Table
CREATE TABLE IF NOT EXISTS audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    module VARCHAR(100) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE
    SET NULL
);
-- Doctor Shifts Table
CREATE TABLE IF NOT EXISTS doctor_shifts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doctor_id INT,
    day_of_week ENUM(
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday',
        'Sunday'
    ),
    start_time TIME,
    end_time TIME,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
);
-- Facilities Table (Beds, Rooms)
CREATE TABLE IF NOT EXISTS facilities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type ENUM('bed', 'room', 'icu', 'operating_theater') NOT NULL,
    status ENUM('available', 'occupied', 'maintenance') DEFAULT 'available',
    last_cleaned TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    current_patient_id INT,
    FOREIGN KEY (current_patient_id) REFERENCES patients(id) ON DELETE
    SET NULL
);
-- Staff Shifts Table (for non-doctor staff: pharmacists, admin staff, etc.)
CREATE TABLE IF NOT EXISTS staff_shifts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    day_of_week ENUM(
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday',
        'Sunday'
    ) NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    shift_type VARCHAR(50) DEFAULT 'regular' COMMENT 'e.g., regular, overtime, on-call',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
-- Seed Data (Optional/Initial)
INSERT INTO clinics (name, admin_name, email, phone)
VALUES (
        'Northstar Clinic',
        'Dr Paul Malone',
        'northstar.digitalrx@gmail.com',
        '91 1958363346'
    ) ON DUPLICATE KEY
UPDATE id = id;
-- Default User (Admin) - password is 'admin123'
INSERT INTO users (username, password, full_name, email, role)
VALUES (
        'admin',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        'Admin User',
        'admin@clinic.com',
        'admin'
    ) ON DUPLICATE KEY
UPDATE id = id;
INSERT INTO stats (label, value, icon)
VALUES ('Doctors', 10, 'fa-user-doctor'),
    ('Patients', 171, 'fa-hospital-user'),
    (
        'Appointments',
        750,
        'fa-regular fa-calendar-check'
    ),
    ('Available Beds', 24, 'fa-solid fa-bed') ON DUPLICATE KEY
UPDATE id = id;
INSERT INTO specialties (name, icon)
VALUES ('Cardiology', 'fa-heart-pulse'),
    ('Neurology', 'fa-brain'),
    ('Pediatrics', 'fa-child');