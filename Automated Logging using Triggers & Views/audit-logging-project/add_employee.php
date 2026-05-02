<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize inputs
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
        // Check if email already exists
        $stmt = $conn->prepare("SELECT emp_id FROM employees WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $message = "Email already exists!";
            $messageType = "error";
        } else {
            // Insert employee
            $stmt = $conn->prepare("INSERT INTO employees (first_name, last_name, email, phone, department, designation, salary, joining_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssdss", $first_name, $last_name, $email, $phone, $department, $designation, $salary, $joining_date, $status);
            
            if ($stmt->execute()) {
                $message = "Employee added successfully! Audit log created automatically by trigger.";
                $messageType = "success";
                // Clear form
                $_POST = [];
            } else {
                $message = "Error adding employee: " . $stmt->error;
                $messageType = "error";
            }
        }
        $stmt->close();
    } else {
        $message = implode("<br>", $errors);
        $messageType = "error";
    }
}

// Fetch all employees for the table
$employees = $conn->query("SELECT * FROM employees ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Employee - Audit Log System</title>
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
                <a href="add_employee.php" class="nav-link active">
                    <i class="fas fa-user-plus"></i>
                    <span>Add Employee</span>
                </a>
                <a href="update_employee.php" class="nav-link">
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
                <h1><i class="fas fa-user-plus"></i> Add Employee</h1>
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

                <div class="form-card">
                    <div class="card-header">
                        <h2><i class="fas fa-user-plus"></i> New Employee Details</h2>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="" class="employee-form">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="first_name">First Name *</label>
                                    <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>" required placeholder="Enter first name">
                                </div>
                                <div class="form-group">
                                    <label for="last_name">Last Name *</label>
                                    <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>" required placeholder="Enter last name">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="email">Email *</label>
                                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required placeholder="Enter email address">
                                </div>
                                <div class="form-group">
                                    <label for="phone">Phone</label>
                                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" placeholder="Enter phone number">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="department">Department *</label>
                                    <select id="department" name="department" required>
                                        <option value="">Select Department</option>
                                        <option value="Engineering" <?php echo ($_POST['department'] ?? '') === 'Engineering' ? 'selected' : ''; ?>>Engineering</option>
                                        <option value="Marketing" <?php echo ($_POST['department'] ?? '') === 'Marketing' ? 'selected' : ''; ?>>Marketing</option>
                                        <option value="Finance" <?php echo ($_POST['department'] ?? '') === 'Finance' ? 'selected' : ''; ?>>Finance</option>
                                        <option value="HR" <?php echo ($_POST['department'] ?? '') === 'HR' ? 'selected' : ''; ?>>HR</option>
                                        <option value="Sales" <?php echo ($_POST['department'] ?? '') === 'Sales' ? 'selected' : ''; ?>>Sales</option>
                                        <option value="Operations" <?php echo ($_POST['department'] ?? '') === 'Operations' ? 'selected' : ''; ?>>Operations</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="designation">Designation *</label>
                                    <input type="text" id="designation" name="designation" value="<?php echo htmlspecialchars($_POST['designation'] ?? ''); ?>" required placeholder="Enter designation">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="salary">Salary * ($)</label>
                                    <input type="number" id="salary" name="salary" step="0.01" min="0" value="<?php echo htmlspecialchars($_POST['salary'] ?? ''); ?>" required placeholder="Enter salary">
                                </div>
                                <div class="form-group">
                                    <label for="joining_date">Joining Date *</label>
                                    <input type="date" id="joining_date" name="joining_date" value="<?php echo htmlspecialchars($_POST['joining_date'] ?? ''); ?>" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select id="status" name="status">
                                        <option value="Active" <?php echo ($_POST['status'] ?? 'Active') === 'Active' ? 'selected' : ''; ?>>Active</option>
                                        <option value="Inactive" <?php echo ($_POST['status'] ?? '') === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add Employee
                                </button>
                                <button type="reset" class="btn btn-secondary">
                                    <i class="fas fa-undo"></i> Reset
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Employees Table -->
                <div class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-list"></i> All Employees</h2>
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
                                        <th>Joining Date</th>
                                        <th>Status</th>
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
                                        <td><?php echo date('M d, Y', strtotime($emp['joining_date'])); ?></td>
                                        <td>
                                            <span class="badge <?php echo $emp['status'] === 'Active' ? 'badge-success' : 'badge-danger'; ?>">
                                                <?php echo $emp['status']; ?>
                                            </span>
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

