<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Fetch daily activity report from VIEW
$reports = $conn->query("SELECT * FROM daily_activity_report LIMIT 30");

// Fetch summary statistics
$summary = $conn->query("
    SELECT 
        SUM(CASE WHEN action_type = 'INSERT' THEN 1 ELSE 0 END) as total_inserts,
        SUM(CASE WHEN action_type = 'UPDATE' THEN 1 ELSE 0 END) as total_updates,
        COUNT(*) as total_activities,
        COUNT(DISTINCT DATE(changed_at)) as active_days
    FROM audit_logs
")->fetch_assoc();

// Fetch activity by department (from new_values JSON)
$dept_activity = $conn->query("
    SELECT 
        SUBSTRING_INDEX(SUBSTRING_INDEX(new_values, '\"department\":\"', -1), '\"', 1) as department,
        COUNT(*) as activity_count
    FROM audit_logs
    WHERE new_values LIKE '%department%'
    GROUP BY department
    ORDER BY activity_count DESC
    LIMIT 10
");
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Reports - Audit Log System</title>
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
                <a href="update_employee.php" class="nav-link">
                    <i class="fas fa-user-edit"></i>
                    <span>Update Employee</span>
                </a>
                <a href="logs.php" class="nav-link">
                    <i class="fas fa-history"></i>
                    <span>Audit Logs</span>
                </a>
                <a href="reports.php" class="nav-link active">
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
                <h1><i class="fas fa-chart-bar"></i> Daily Activity Reports</h1>
                <div class="user-info">
                    <i class="fas fa-user-circle"></i>
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                </div>
            </header>

            <div class="content-wrapper">
                <!-- Summary Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="fas fa-plus-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo number_format($summary['total_inserts'] ?? 0); ?></h3>
                            <p>Total Inserts</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo number_format($summary['total_updates'] ?? 0); ?></h3>
                            <p>Total Updates</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo number_format($summary['total_activities'] ?? 0); ?></h3>
                            <p>Total Activities</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo number_format($summary['active_days'] ?? 0); ?></h3>
                            <p>Active Days</p>
                        </div>
                    </div>
                </div>

                <div class="dashboard-grid">
                    <!-- Daily Activity Report Table -->
                    <div class="card">
                        <div class="card-header">
                            <h2><i class="fas fa-table"></i> Daily Activity Report (MySQL VIEW)</h2>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Total Inserts</th>
                                            <th>Total Updates</th>
                                            <th>Total Activities</th>
                                            <th>Activity Level</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($report = $reports->fetch_assoc()): 
                                            $total = $report['total_activities'];
                                            $level = $total > 10 ? 'High' : ($total > 5 ? 'Medium' : 'Low');
                                            $levelClass = $total > 10 ? 'badge-danger' : ($total > 5 ? 'badge-warning' : 'badge-success');
                                        ?>
                                        <tr>
                                            <td><?php echo date('M d, Y', strtotime($report['activity_date'])); ?></td>
                                            <td>
                                                <span class="badge badge-success">
                                                    <i class="fas fa-plus"></i> <?php echo $report['total_inserts']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-edit"></i> <?php echo $report['total_updates']; ?>
                                                </span>
                                            </td>
                                            <td><strong><?php echo $report['total_activities']; ?></strong></td>
                                            <td><span class="badge <?php echo $levelClass; ?>"><?php echo $level; ?></span></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Department Activity -->
                    <div class="card">
                        <div class="card-header">
                            <h2><i class="fas fa-building"></i> Activity by Department</h2>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Department</th>
                                            <th>Activity Count</th>
                                            <th>Visual</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $max_count = 0;
                                        $dept_data = [];
                                        while ($dept = $dept_activity->fetch_assoc()) {
                                            $dept_data[] = $dept;
                                            if ($dept['activity_count'] > $max_count) $max_count = $dept['activity_count'];
                                        }
                                        foreach ($dept_data as $dept): 
                                            $percentage = $max_count > 0 ? ($dept['activity_count'] / $max_count) * 100 : 0;
                                        ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($dept['department']); ?></td>
                                            <td><?php echo $dept['activity_count']; ?></td>
                                            <td>
                                                <div class="progress-bar">
                                                    <div class="progress-fill" style="width: <?php echo $percentage; ?>%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- How Views Work Explanation -->
                <div class="card info-card">
                    <div class="card-header">
                        <h2><i class="fas fa-info-circle"></i> How the daily_activity_report VIEW Works</h2>
                    </div>
                    <div class="card-body">
                        <div class="explanation">
                            <p><strong>What is a VIEW?</strong> A VIEW is a virtual table based on the result-set of an SQL statement. It contains rows and columns, just like a real table, but doesn't store data physically.</p>
                            
                            <div class="code-block">
                                <pre>CREATE VIEW daily_activity_report AS
SELECT 
    DATE(changed_at) AS activity_date,
    SUM(CASE WHEN action_type = 'INSERT' THEN 1 ELSE 0 END) AS total_inserts,
    SUM(CASE WHEN action_type = 'UPDATE' THEN 1 ELSE 0 END) AS total_updates,
    COUNT(*) AS total_activities
FROM audit_logs
GROUP BY DATE(changed_at)
ORDER BY activity_date DESC;</pre>
                            </div>
                            
                            <ul>
                                <li><i class="fas fa-check-circle"></i> <strong>DATE(changed_at)</strong> - Extracts only the date part from the timestamp</li>
                                <li><i class="fas fa-check-circle"></i> <strong>SUM(CASE...)</strong> - Counts INSERT and UPDATE actions separately</li>
                                <li><i class="fas fa-check-circle"></i> <strong>COUNT(*)</strong> - Gets total activities per day</li>
                                <li><i class="fas fa-check-circle"></i> <strong>GROUP BY</strong> - Groups results by each unique date</li>
                                <li><i class="fas fa-check-circle"></i> Data is always up-to-date since VIEW queries the underlying table in real-time</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script src="assets/js/script.js"></script>
</body>
</html>

