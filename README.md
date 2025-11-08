# 🎓 Student Attendance Management System

A **web-based application** built using **PHP, MySQL, JavaScript, AJAX, HTML, and CSS** that helps educational institutions efficiently manage and track student attendance records.  
This project was created as part of the **Web Technologies** course project (70 marks total).

---

## 🚀 Features

✅ **Admin Login Panel**

- Secure login system for administrator access.
- Admin can manage classes, students, and attendance records.

✅ **Class & Student Management**

- Add, view, and delete classes.
- Add or manage students within each class.

✅ **Attendance Management**

- Mark students as _Present_ or _Absent_ for a specific date and class.
- Supports bulk marking (“Mark All Present”) for quick updates.
- Prevents duplicate attendance marking for the same date.

✅ **Reports Generation**

- Generate detailed attendance reports by class and date range.
- Export attendance data to **CSV** for backup or analysis.

✅ **Responsive Interface**

- Simple and interactive design using **HTML5, CSS3, and Bootstrap**.
- AJAX ensures smooth operations without page reloads.

---

## 🛠️ Technologies Used

| Category            | Technology                               |
| ------------------- | ---------------------------------------- |
| **Frontend**        | HTML5, CSS3, JavaScript, Bootstrap, AJAX |
| **Backend**         | PHP (Core PHP)                           |
| **Database**        | MySQL                                    |
| **Server**          | XAMPP (Apache, MySQL)                    |
| **Version Control** | Git & GitHub                             |

---

## 📁 Project Structure

ATTENDANCE_PROJECT/
│
├── includes/
│ ├── auth_check.php # Session authentication
│ └── db.php # Database connection
│
├── public/
│ ├── api/
│ │ ├── add_class.php
│ │ ├── add_student.php
│ │ ├── get_classes.php
│ │ ├── get_students.php
│ │ ├── login.php
│ │ ├── logout.php
│ │ ├── mark_attendance.php
│ │ ├── reports.php
│ │ └── summary.php
│ │
│ ├── assets/
│ │ ├── css/
│ │ │ └── style.css
│ │ └── js/
│ │ ├── app.js
│ │ ├── attendance.js
│ │ ├── dashboard.js
│ │ ├── manage.js
│ │ └── ui.js
│ │
│ ├── dashboard.php
│ ├── index.php
│ ├── manage.php
│ └── reports.php
│
├── sql/
│ └── schema.sql
│
├── test_connection.php # For checking DB connection
└── README.md # Documentation

