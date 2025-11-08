-- schema.sql
-- Database: attendance_db
-- Created for Student Attendance Management System

CREATE DATABASE IF NOT EXISTS attendance_db;
USE attendance_db;

-- -----------------------------
-- USERS TABLE
-- -----------------------------
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','teacher') DEFAULT 'admin',
  name VARCHAR(100),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- -----------------------------
-- SUBJECTS TABLE
-- -----------------------------
CREATE TABLE IF NOT EXISTS subjects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL
);

-- -----------------------------
-- CLASSES TABLE
-- -----------------------------
CREATE TABLE IF NOT EXISTS classes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  subject_id INT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE SET NULL
);

-- -----------------------------
-- STUDENTS TABLE
-- -----------------------------
CREATE TABLE IF NOT EXISTS students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  roll_no VARCHAR(50) NOT NULL,
  name VARCHAR(100) NOT NULL,
  class_id INT NOT NULL,
  email VARCHAR(150),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_roll (roll_no, class_id),
  FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE
);

-- -----------------------------
-- ATTENDANCE TABLE
-- -----------------------------
CREATE TABLE IF NOT EXISTS attendance (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT NOT NULL,
  class_id INT NOT NULL,
  attendance_date DATE NOT NULL,
  status ENUM('present','absent','late') DEFAULT 'present',
  marked_by INT,
  marked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_attendance (student_id, attendance_date),
  FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
  FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
  FOREIGN KEY (marked_by) REFERENCES users(id) ON DELETE SET NULL
);

-- -----------------------------
-- SAMPLE DATA
-- -----------------------------

INSERT INTO subjects (name) VALUES ('Introduction to Programming');

INSERT INTO classes (name, subject_id)
VALUES ('CSE-2A', (SELECT id FROM subjects WHERE name = 'Introduction to Programming' LIMIT 1));

INSERT INTO students (roll_no, name, class_id, email) VALUES
('CSE2A001', 'Aman Kumar', (SELECT id FROM classes WHERE name = 'CSE-2A' LIMIT 1), 'aman@example.com'),
('CSE2A002', 'Riya Sharma', (SELECT id FROM classes WHERE name = 'CSE-2A' LIMIT 1), 'riya@example.com'),
('CSE2A003', 'Vikram Singh', (SELECT id FROM classes WHERE name = 'CSE-2A' LIMIT 1), 'vikram@example.com'),
('CSE2A004', 'Sneha Patel', (SELECT id FROM classes WHERE name = 'CSE-2A' LIMIT 1), 'sneha@example.com'),
('CSE2A005', 'Rahul Verma', (SELECT id FROM classes WHERE name = 'CSE-2A' LIMIT 1), 'rahul@example.com');

-- Add admin user (default login: admin / admin123)
INSERT INTO users (username, password_hash, role, name)
VALUES ('admin', '$2y$10$zQqLOU/jzqZtX5TOb2R5Ye79a2IoLpkb6e6pA4KX9DfuPt3q6kDle', 'admin', 'Administrator');
-- password_hash corresponds to 'admin123'
