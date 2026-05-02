<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Fetch statistics
$stats = [
    'total_employees' => 0,
    'total_logs' => 0,
    'today_inserts' => 0,
    'today_updates' => 0
];

// Total employees
$result = $conn->query("SELECT COUNT(*) as count FROM employees");
if ($result) $stats['total_employees'] = $result->fetch_assoc()['count'];

// Total logs
$result = $conn->query("SELECT COUNT(*) as count FROM audit_logs");
if ($result) $stats['total_logs'] = $result->fetch_assoc()['count'];

// Today's inserts
$today = date('Y-m-d');
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM audit_logs WHERE action_type = 'INSERT' AND DATE(changed_at) = ?");
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();
if ($result) $stats['today_inserts'] = $result->fetch_assoc()['count'];
$stmt->close();

// Today's updates
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM audit_logs WHERE action_type = 'UPDATE' AND DATE(changed_at) = ?");
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();
if ($result) $stats['today_updates'] = $result->fetch_assoc()['count'];
$stmt->close();

// Recent employees
$recent_employees = $conn->query("SELECT * FROM employees ORDER BY created_at DESC LIMIT 5");

// Recent audit logs
$recent_logs = $conn->query("SELECT * FROM audit_logs ORDER BY changed_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Audit Log System</title>
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
                <a href="dashboard.php" class="nav-link active">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="add_employee.php" class="nav-link">
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
                <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
                <div class="user-info">
                    <i class="fas fa-user-circle"></i>
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                </div>
            </header>

            <div class="content-wrapper">
                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo number_format($stats['total_employees']); ?></h3>
                            <p>Total Employees</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo number_format($stats['total_logs']); ?></h3>
                            <p>Total Audit Logs</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                            <i class="fas fa-plus-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo number_format($stats['today_inserts']); ?></h3>
                            <p>Today's Inserts</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo number_format($stats['today_updates']); ?></h3>
                            <p>Today's Updates</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity Section -->
                <div class="dashboard-grid">
                    <div class="card">
                        <div class="card-header">
                            <h2><i class="fas fa-users"></i> Recent Employees</h2>
                            <a href="add_employee.php" class="btn btn-sm">View All</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Department</th>
                                            <th>Designation</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($emp = $recent_employees->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $emp['emp_id']; ?></td>
                                            <td><?php echo htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']); ?></td>
                                            <td><?php echo htmlspecialchars($emp['department']); ?></td>
                                            <td><?php echo htmlspecialchars($emp['designation']); ?></td>
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

                    <div class="card">
                        <div class="card-header">
                            <h2><i class="fas fa-history"></i> Recent Audit Logs</h2>
                            <a href="logs.php" class="btn btn-sm">View All</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Action</th>
                                            <th>Record ID</th>
                                            <th>Changed By</th>
                                            <th>Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($log = $recent_logs->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <span class="badge <?php echo $log['action_type'] === 'INSERT' ? 'badge-success' : 'badge-warning'; ?>">
                                                    <i class="fas <?php echo $log['action_type'] === 'INSERT' ? 'fa-plus' : 'fa-edit'; ?>"></i>
                                                    <?php echo $log['action_type']; ?>
                                                </span>
                                            </td>
                                            <td># <?php echo $log['record_id']; ?></td>
                                            <td><?php echo htmlspecialchars($log['changed_by']); ?></td>
                                            <td><?php echo date('M d, Y H:i', strtotime($log['changed_at'])); ?></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script src="assets/js/script.js"></script>
</body>
</html>

