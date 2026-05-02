<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$message = '';
$messageType = '';
$edit_employee = null;

// Handle update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_employee'])) {
    $emp_id = intval($_POST['emp_id']);
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $designation = trim($_POST['designation'] ?? '');
    $salary = floatval($_POST['salary'] ?? 0);
    $joining_date = $_POST['joining_date'] ?? '';
    $status = $_POST['status'] ?? 'Active';

    // Validation
    $errors = [];
    if (empty($first_name)) $errors[] = "First name is required";
    if (empty($last_name)) $errors[] = "Last name is required";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required";
    if (empty($department)) $errors[] = "Department is required";
    if (empty($designation)) $errors[] = "Designation is required";
    if ($salary <= 0) $errors[] = "Valid salary is required";
    if (empty($joining_date)) $errors[] = "Joining date is required";

    if (empty($errors)) {
        // Check if email exists for another employee
        $stmt = $conn->prepare("SELECT emp_id FROM employees WHERE email = ? AND emp_id != ?");
        $stmt->bind_param("si", $email, $emp_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $message = "Email already exists for another employee!";
            $messageType = "error";
        } else {
            // Update employee - this will fire the AFTER UPDATE trigger
            $stmt = $conn->prepare("UPDATE employees SET first_name=?, last_name=?, email=?, phone=?, department=?, designation=?, salary=?, joining_date=?, status=? WHERE emp_id=?");
            $stmt->bind_param("ssssssdssi", $first_name, $last_name, $email, $phone, $department, $designation, $salary, $joining_date, $status, $emp_id);
            
            if ($stmt->execute()) {
                $message = "Employee updated successfully! Audit log created automatically by trigger.";
                $messageType = "success";
            } else {
                $message = "Error updating employee: " . $stmt->error;
                $messageType = "error";
            }
        }
        $stmt->close();
    } else {
        $message = implode("<br>", $errors);
        $messageType = "error";
    }
}

// Fetch employee for editing
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM employees WHERE emp_id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_employee = $result->fetch_assoc();
    $stmt->close();
}

// Fetch all employees
$employees = $conn->query("SELECT * FROM employees ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Employee - Audit Log System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <i class="fas fa-shield-alt"></i>
                <span>AuditLog</span>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-link">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="add_employee.php" class="nav-link">
                    <i class="fas fa-user-plus"></i>
                    <span>Add Employee</span>
                </a>
                <a href="update_employee.php" class="nav-link active">
                    <i class="fas fa-user-edit"></i>
                    <span>Update Employee</span>
                </a>
                <a href="logs.php" class="nav-link">
                    <i class="fas fa-history"></i>
                    <span>Audit Logs</span>
                </a>
                <a href="reports.php" class="nav-link">
                    <i class="fas fa-chart-bar"></i>
                    <span>Daily Reports</span>
                </a>
            </nav>
            <div class="sidebar-footer">
                <button class="theme-toggle" onclick="toggleTheme()" title="Toggle Dark Mode">
                    <i class="fas fa-moon"></i>
                </button>
                <a href="logout.php" class="nav-link logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="main-header">
                <h1><i class="fas fa-user-edit"></i> Update Employee</h1>
                <div class="user-info">
                    <i class="fas fa-user-circle"></i>
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                </div>
            </header>

            <div class="content-wrapper">
                <!-- Notification -->
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType; ?>" id="notification">
                        <i class="fas <?php echo $messageType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                        <?php echo $message; ?>
                        <button class="close-btn" onclick="this.parentElement.style.display='none'">&times;</button>
                    </div>
                <?php endif; ?>

                <?php if ($edit_employee): ?>
                <!-- Edit Form -->
                <div class="form-card">
                    <div class="card-header">
                        <h2><i class="fas fa-edit"></i> Edit Employee #<?php echo $edit_employee['emp_id']; ?></h2>
                        <a href="update_employee.php" class="btn btn-sm btn-secondary">Cancel</a>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="" class="employee-form">
                            <input type="hidden" name="emp_id" value="<?php echo $edit_employee['emp_id']; ?>">
                            <input type="hidden" name="update_employee" value="1">

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="first_name">First Name *</label>
                                    <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($edit_employee['first_name']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="last_name">Last Name *</label>
                                    <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($edit_employee['last_name']); ?>" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="email">Email *</label>
                                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($edit_employee['email']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="phone">Phone</label>
                                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($edit_employee['phone'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="department">Department *</label>
                                    <select id="department" name="department" required>
                                        <option value="">Select Department</option>
                                        <option value="Engineering" <?php echo $edit_employee['department'] === 'Engineering' ? 'selected' : ''; ?>>Engineering</option>
                                        <option value="Marketing" <?php echo $edit_employee['department'] === 'Marketing' ? 'selected' : ''; ?>>Marketing</option>
                                        <option value="Finance" <?php echo $edit_employee['department'] === 'Finance' ? 'selected' : ''; ?>>Finance</option>
                                        <option value="HR" <?php echo $edit_employee['department'] === 'HR' ? 'selected' : ''; ?>>HR</option>
                                        <option value="Sales" <?php echo $edit_employee['department'] === 'Sales' ? 'selected' : ''; ?>>Sales</option>
                                        <option value="Operations" <?php echo $edit_employee['department'] === 'Operations' ? 'selected' : ''; ?>>Operations</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="designation">Designation *</label>
                                    <input type="text" id="designation" name="designation" value="<?php echo htmlspecialchars($edit_employee['designation']); ?>" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="salary">Salary * ($)</label>
                                    <input type="number" id="salary" name="salary" step="0.01" min="0" value="<?php echo $edit_employee['salary']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="joining_date">Joining Date *</label>
                                    <input type="date" id="joining_date" name="joining_date" value="<?php echo $edit_employee['joining_date']; ?>" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select id="status" name="status">
                                        <option value="Active" <?php echo $edit_employee['status'] === 'Active' ? 'selected' : ''; ?>>Active</option>
                                        <option value="Inactive" <?php echo $edit_employee['status'] === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Employee
                                </button>
                                <a href="update_employee.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Employees Table -->
                <div class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-list"></i> Select Employee to Update</h2>
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="employeeSearch" placeholder="Search employees..." onkeyup="searchTable('employeeSearch', 'employeesTable')">
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="data-table" id="employeesTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Department</th>
                                        <th>Designation</th>
                                        <th>Salary</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($emp = $employees->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $emp['emp_id']; ?></td>
                                        <td><?php echo htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($emp['email']); ?></td>
                                        <td><?php echo htmlspecialchars($emp['department']); ?></td>
                                        <td><?php echo htmlspecialchars($emp['designation']); ?></td>
                                        <td>$<?php echo number_format($emp['salary'], 2); ?></td>
                                        <td>
                                            <span class="badge <?php echo $emp['status'] === 'Active' ? 'badge-success' : 'badge-danger'; ?>">
                                                <?php echo $emp['status']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="?edit=<?php echo $emp['emp_id']; ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script src="assets/js/script.js"></script>
    <script>
        // Auto-hide notification after 5 seconds
        setTimeout(function() {
            var notification = document.getElementById('notification');
            if (notification) {
                notification.style.opacity = '0';
                setTimeout(function() {
                    notification.style.display = 'none';
                }, 300);
            }
        }, 5000);
    </script>
</body>
</html>

