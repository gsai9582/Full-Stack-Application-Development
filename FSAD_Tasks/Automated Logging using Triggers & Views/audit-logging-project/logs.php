<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Handle filters
$action_filter = $_GET['action'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';
$search = $_GET['search'] ?? '';

// Build query
$query = "SELECT * FROM audit_logs WHERE 1=1";
$params = [];
$types = '';

if ($action_filter) {
    $query .= " AND action_type = ?";
    $params[] = $action_filter;
    $types .= 's';
}

if ($date_from) {
    $query .= " AND DATE(changed_at) >= ?";
    $params[] = $date_from;
    $types .= 's';
}

if ($date_to) {
    $query .= " AND DATE(changed_at) <= ?";
    $params[] = $date_to;
    $types .= 's';
}

if ($search) {
    $query .= " AND (record_id LIKE ? OR changed_by LIKE ? OR new_values LIKE ? OR old_values LIKE ?)";
    $search_term = "%{$search}%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= 'ssss';
}

$query .= " ORDER BY changed_at DESC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$logs = $stmt->get_result();

// Handle CSV Export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=audit_logs_' . date('Y-m-d') . '.csv');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Log ID', 'Record ID', 'Action Type', 'Old Values', 'New Values', 'Changed By', 'Changed At', 'IP Address']);
    
    // Re-run query for export
    $stmt->execute();
    $export_logs = $stmt->get_result();
    
    while ($row = $export_logs->fetch_assoc()) {
        fputcsv($output, [
            $row['log_id'],
            $row['record_id'],
            $row['action_type'],
            $row['old_values'],
            $row['new_values'],
            $row['changed_by'],
            $row['changed_at'],
            $row['ip_address']
        ]);
    }
    fclose($output);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Logs - Audit Log System</title>
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
                <a href="logs.php" class="nav-link active">
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
                <h1><i class="fas fa-history"></i> Audit Logs</h1>
                <div class="user-info">
                    <i class="fas fa-user-circle"></i>
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                </div>
            </header>

            <div class="content-wrapper">
                <!-- Filters -->
                <div class="card filter-card">
                    <div class="card-header">
                        <h2><i class="fas fa-filter"></i> Filters</h2>
                        <a href="logs.php" class="btn btn-sm btn-secondary"><i class="fas fa-undo"></i> Reset</a>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="" class="filter-form">
                            <div class="filter-row">
                                <div class="form-group">
                                    <label>Action Type</label>
                                    <select name="action">
                                        <option value="">All Actions</option>
                                        <option value="INSERT" <?php echo $action_filter === 'INSERT' ? 'selected' : ''; ?>>INSERT</option>
                                        <option value="UPDATE" <?php echo $action_filter === 'UPDATE' ? 'selected' : ''; ?>>UPDATE</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Date From</label>
                                    <input type="date" name="date_from" value="<?php echo htmlspecialchars($date_from); ?>">
                                </div>
                                <div class="form-group">
                                    <label>Date To</label>
                                    <input type="date" name="date_to" value="<?php echo htmlspecialchars($date_to); ?>">
                                </div>
                                <div class="form-group">
                                    <label>Search</label>
                                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search logs...">
                                </div>
                                <div class="form-group filter-actions">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-filter"></i> Apply
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Logs Table -->
                <div class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-clipboard-list"></i> Audit Log Entries</h2>
                        <div class="header-actions">
                            <span class="refresh-indicator" id="refreshIndicator">
                                <i class="fas fa-sync-alt fa-spin"></i> Refreshing...
                            </span>
                            <a href="?export=csv<?php echo !empty($_SERVER['QUERY_STRING']) ? '&' . $_SERVER['QUERY_STRING'] : ''; ?>" class="btn btn-success">
                                <i class="fas fa-file-csv"></i> Export CSV
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="data-table log-table" id="logsTable">
                                <thead>
                                    <tr>
                                        <th>Log ID</th>
                                        <th>Record ID</th>
                                        <th>Action</th>
                                        <th>Changed By</th>
                                        <th>Changed At</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>
                                <tbody id="logsTableBody">
                                    <?php while ($log = $logs->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $log['log_id']; ?></td>
                                        <td># <?php echo $log['record_id']; ?></td>
                                        <td>
                                            <span class="badge <?php echo $log['action_type'] === 'INSERT' ? 'badge-success' : 'badge-warning'; ?>">
                                                <i class="fas <?php echo $log['action_type'] === 'INSERT' ? 'fa-plus' : 'fa-edit'; ?>"></i>
                                                <?php echo $log['action_type']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($log['changed_by']); ?></td>
                                        <td><?php echo date('M d, Y H:i:s', strtotime($log['changed_at'])); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-info" onclick="showLogDetails('<?php echo htmlspecialchars(addslashes($log['old_values'] ?? 'N/A')); ?>', '<?php echo htmlspecialchars(addslashes($log['new_values'] ?? 'N/A')); ?>')">
                                                <i class="fas fa-eye"></i> View
                                            </button>
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

    <!-- Log Details Modal -->
    <div id="logModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-info-circle"></i> Log Details</h2>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="log-details">
                    <div class="detail-section">
                        <h3><i class="fas fa-history"></i> Old Values</h3>
                        <pre id="oldValues"></pre>
                    </div>
                    <div class="detail-section">
                        <h3><i class="fas fa-file-alt"></i> New Values</h3>
                        <pre id="newValues"></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="assets/js/script.js"></script>
    <script>
        // Auto refresh logs every 5 seconds
        setInterval(function() {
            refreshLogsTable();
        }, 5000);

        function refreshLogsTable() {
            var indicator = document.getElementById('refreshIndicator');
            indicator.style.display = 'inline-flex';
            
            fetch('ajax/get_logs.php?' + new URLSearchParams(window.location.search))
                .then(response => response.text())
                .then(html => {
                    document.getElementById('logsTableBody').innerHTML = html;
                    indicator.style.display = 'none';
                })
                .catch(error => {
                    console.error('Error refreshing logs:', error);
                    indicator.style.display = 'none';
                });
        }

        function showLogDetails(oldValues, newValues) {
            document.getElementById('oldValues').textContent = oldValues;
            document.getElementById('newValues').textContent = newValues;
            document.getElementById('logModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('logModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            var modal = document.getElementById('logModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>

