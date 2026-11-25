-- Clinic Appointment Booking System
CREATE DATABASE IF NOT EXISTS clinic_db CHARACTER SET utf8mb4;
USE clinic_db;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  phone VARCHAR(15),
  role ENUM('admin','doctor','patient') NOT NULL DEFAULT 'patient',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE departments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE doctors (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL UNIQUE,
  department_id INT NOT NULL,
  experience INT DEFAULT 0,
  fees DECIMAL(8,2) DEFAULT 0,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (department_id) REFERENCES departments(id)
);

-- day_of_week: 1 = Monday ... 7 = Sunday
CREATE TABLE schedules (
  id INT AUTO_INCREMENT PRIMARY KEY,
  doctor_id INT NOT NULL,
  day_of_week TINYINT NOT NULL,
  start_time TIME NOT NULL,
  end_time TIME NOT NULL,
  UNIQUE KEY uq_doc_day (doctor_id, day_of_week),
  FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
);

-- is_active = 1 for live bookings, NULL for cancelled/rejected.
-- UNIQUE with NULL allows many NULLs, so a cancelled slot can be booked again,
-- but two live bookings for the same doctor+date+time are impossible (double booking blocked by DB).
CREATE TABLE appointments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  patient_id INT NOT NULL,
  doctor_id INT NOT NULL,
  appointment_date DATE NOT NULL,
  time_slot TIME NOT NULL,
  reason VARCHAR(255),
  status ENUM('Pending','Approved','Rejected','Completed','Cancelled') DEFAULT 'Pending',
  is_active TINYINT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_slot (doctor_id, appointment_date, time_slot, is_active),
  FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
);

CREATE TABLE prescriptions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  appointment_id INT NOT NULL UNIQUE,
  notes TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE
);

INSERT INTO departments (name) VALUES
('General Physician'),('Cardiology'),('Dermatology'),('Pediatrics'),('Orthopedics'),('Dentist');

-- Default admin: admin@clinic.com / password   (login ke baad password badal dena)
INSERT INTO users (name,email,password,phone,role) VALUES
('Admin','admin@clinic.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','9999999999','admin');
