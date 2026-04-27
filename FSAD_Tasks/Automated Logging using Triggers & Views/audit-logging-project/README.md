# 🛡️ Automated Logging using Triggers & Views

A complete Database Mini Project demonstrating real-time audit logging in enterprise databases using **MySQL Triggers** and **Views**, built with **PHP + HTML + CSS + JavaScript** (XAMPP Compatible).

---

## 📋 Table of Contents

1. [Project Overview](#project-overview)
2. [Features](#features)
3. [Technologies Used](#technologies-used)
4. [Folder Structure](#folder-structure)
5. [Step-by-Step Setup Guide](#step-by-step-setup-guide)
6. [How Triggers Work](#how-triggers-work)
7. [How Views Work](#how-views-work)
8. [Demo Credentials](#demo-credentials)
9. [Screenshots](#screenshots)
10. [Troubleshooting](#troubleshooting)

---

## 🎯 Project Overview

This project demonstrates automated audit logging in an enterprise database environment. Whenever an employee record is **INSERTED** or **UPDATED**, MySQL triggers automatically capture:

- What action was performed (INSERT / UPDATE)
- Who performed the action
- When it was performed
- What the old values were (for updates)
- What the new values are
- The IP address of the user

A MySQL **VIEW** called `daily_activity_report` provides a daily summary of all database activities.

---

## ✨ Features

### Core Features
- ✅ Automated audit logging via MySQL Triggers
- ✅ Employee management (Add / Update / View)
- ✅ Complete audit trail with old & new values
- ✅ Daily activity report using MySQL Views
- ✅ Admin login system with session management

### UI Features
- ✅ Modern glassmorphism design
- ✅ Dark mode toggle
- ✅ Responsive admin dashboard
- ✅ Real-time notifications
- ✅ Auto-refreshing audit logs (every 5 seconds)
- ✅ Search & filter functionality
- ✅ Export logs to CSV

### Database Features
- ✅ AFTER INSERT trigger
- ✅ AFTER UPDATE trigger
- ✅ Daily activity VIEW
- ✅ Sample data included

---

## 🔧 Technologies Used

| Technology | Purpose |
|------------|---------|
| **MySQL** | Database, Triggers, Views |
| **PHP** | Backend logic & database connectivity |
| **HTML5** | Page structure |
| **CSS3** | Styling, animations, glassmorphism |
| **JavaScript** | Interactivity, AJAX, dark mode |
| **Font Awesome** | Icons |
| **XAMPP** | Local server environment |

---

## 📁 Folder Structure

```
audit-logging-project/
│
├── index.php              # Landing page (redirects to login)
├── login.php              # Admin login page
├── dashboard.php          # Main dashboard with statistics
├── add_employee.php       # Add new employee form
├── update_employee.php    # Update existing employee
├── logs.php               # View & filter audit logs
├── reports.php            # Daily activity reports (MySQL VIEW)
├── logout.php             # Logout script
├── config.php             # Database configuration
├── database.sql           # Complete database setup script
├── README.md              # This file
│
├── assets/
│   ├── css/
│   │   └── style.css      # Main stylesheet (glassmorphism theme)
│   └── js/
│       └── script.js      # JavaScript interactivity
│
└── ajax/
    └── get_logs.php       # AJAX endpoint for auto-refreshing logs
```

---

## 🚀 Step-by-Step Setup Guide

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) installed on your system
- Web browser (Chrome, Firefox, Edge)

### Step 1: Install XAMPP
1. Download XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Install it to `C:\xampp` (default location)
3. Start **Apache** and **MySQL** from XAMPP Control Panel

### Step 2: Extract the Project
1. Copy the `audit-logging-project` folder
2. Paste it into `C:\xampp\htdocs\`
3. Full path should be: `C:\xampp\htdocs\audit-logging-project`

### Step 3: Import the Database
1. Open your browser and go to: `http://localhost/phpmyadmin`
2. Click on **"Import"** tab
3. Click **"Choose File"** and select `C:\xampp\htdocs\audit-logging-project\database.sql`
4. Click **"Go"** button at the bottom
5. The database `audit_db` will be created with all tables, triggers, views, and sample data

### Step 4: Verify Database Credentials
Open `config.php` and ensure these settings match your XAMPP setup:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');      // Default XAMPP username
define('DB_PASS', '');           // Default XAMPP password (empty)
define('DB_NAME', 'audit_db');
```

> **Note:** If you set a MySQL root password in XAMPP, update `DB_PASS` accordingly.

### Step 5: Access the Application
1. Open your browser
2. Navigate to: `http://localhost/audit-logging-project/`
3. You will be redirected to the login page

### Step 6: Login
- **Username:** `admin`
- **Password:** `admin123`

---

## ⚡ How Triggers Work

### What is a Trigger?
A **Trigger** is a stored program in MySQL that automatically executes when a specific event (INSERT, UPDATE, DELETE) occurs on a table.

### Our AFTER INSERT Trigger
```sql
CREATE TRIGGER trg_after_employee_insert
AFTER INSERT ON employees
FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (record_id, action_type, new_values, changed_by, changed_at, ip_address)
    VALUES (
        NEW.emp_id,
        'INSERT',
        CONCAT('{...new employee data...}'),
        COALESCE(@changed_by, 'admin'),
        NOW(),
        COALESCE(@ip_address, '127.0.0.1')
    );
END;
```

**How it works:**
1. When you insert a new employee into the `employees` table
2. MySQL automatically fires this trigger **AFTER** the insert completes
3. The trigger reads the new data using `NEW.column_name`
4. It inserts a log entry into `audit_logs` table
5. The `changed_by` is set from the PHP session via MySQL user variables

### Our AFTER UPDATE Trigger
```sql
CREATE TRIGGER trg_after_employee_update
AFTER UPDATE ON employees
FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (record_id, action_type, old_values, new_values, changed_by, changed_at, ip_address)
    VALUES (
        NEW.emp_id,
        'UPDATE',
        CONCAT('{...old data...}'),     -- Uses OLD.column_name
        CONCAT('{...new data...}'),     -- Uses NEW.column_name
        COALESCE(@changed_by, 'admin'),
        NOW(),
        COALESCE(@ip_address, '127.0.0.1')
    );
END;
```

**How it works:**
1. When you update an employee record
2. The trigger captures **both** old and new values
3. `OLD.column_name` contains the value before the update
4. `NEW.column_name` contains the value after the update
5. This allows complete audit trail of what changed

### Key Benefits of Triggers
- ✅ **Automatic** - No application code needed to create logs
- ✅ **Reliable** - Database-level enforcement, can't be bypassed
- ✅ **Complete** - Captures all changes regardless of how they occur
- ✅ **Secure** - Logs can't be tampered with from application layer

---

## 👁️ How Views Work

### What is a VIEW?
A **VIEW** is a virtual table based on the result of a SQL query. It doesn't store data physically but provides a pre-defined way to look at data.

### Our daily_activity_report VIEW
```sql
CREATE VIEW daily_activity_report AS
SELECT 
    DATE(changed_at) AS activity_date,
    SUM(CASE WHEN action_type = 'INSERT' THEN 1 ELSE 0 END) AS total_inserts,
    SUM(CASE WHEN action_type = 'UPDATE' THEN 1 ELSE 0 END) AS total_updates,
    COUNT(*) AS total_activities
FROM audit_logs
GROUP BY DATE(changed_at)
ORDER BY activity_date DESC;
```

**How it works:**
1. `DATE(changed_at)` extracts just the date part from the timestamp
2. `SUM(CASE WHEN...)` counts INSERT and UPDATE actions separately
3. `COUNT(*)` gives total activities per day
4. `GROUP BY` groups results by each unique date
5. `ORDER BY` shows newest dates first

### Benefits of Using Views
- ✅ **Simplified Queries** - Complex logic is hidden behind a simple table-like interface
- ✅ **Real-time Data** - Always shows current data from underlying tables
- ✅ **Security** - Can restrict access to specific columns
- ✅ **Consistency** - Same calculation logic everywhere
- ✅ **Performance** - MySQL optimizes VIEW queries automatically

---

## 🔐 Demo Credentials

| Field | Value |
|-------|-------|
| Username | `admin` |
| Password | `admin123` |

---

## 📸 Screenshots

### 1. Login Page
![Login Page](screenshots/login.png)
*Professional glassmorphism login page with shield icon and demo credentials*

### 2. Dashboard
![Dashboard](screenshots/dashboard.png)
*Dashboard showing statistics cards, recent employees, and recent audit logs*

### 3. Add Employee
![Add Employee](screenshots/add_employee.png)
*Form to add new employees with validation and department selection*

### 4. Update Employee
![Update Employee](screenshots/update_employee.png)
*Employee selection table and inline edit form*

### 5. Audit Logs
![Audit Logs](screenshots/logs.png)
*Filterable audit log table with action type badges and CSV export*

### 6. Daily Reports
![Reports](screenshots/reports.png)
*Daily activity report using MySQL VIEW with activity level indicators*

### 7. Dark Mode
![Dark Mode](screenshots/dark_mode.png)
*Complete dark mode theme toggle across all pages*

---

## 🔧 Troubleshooting

### Issue: "Connection failed"
**Solution:** Ensure MySQL is running in XAMPP Control Panel. Check `config.php` credentials.

### Issue: "Triggers not working"
**Solution:** Verify database was imported correctly in phpMyAdmin. Check if `audit_logs` table exists.

### Issue: "500 Internal Server Error"
**Solution:** Check that PHP is running in XAMPP. Look at Apache error logs in `C:\xampp\apache\logs\error.log`

### Issue: "CSS not loading"
**Solution:** Ensure the `assets` folder is correctly placed. Check browser console for 404 errors.

### Issue: "Cannot export CSV"
**Solution:** Make sure no output (spaces, HTML) exists before `<?php` tag in `logs.php`.

---

## 📝 License

This project is created for educational purposes. Free to use and modify.

## 🙏 Credits

Created as a Database Mini Project demonstrating:
- MySQL Triggers for automated auditing
- MySQL Views for reporting
- Modern web development stack (PHP, HTML, CSS, JS)

---

**Happy Coding! 💻**

