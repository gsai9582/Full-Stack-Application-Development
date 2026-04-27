<?php
/**
 * AJAX endpoint for auto-refreshing logs table
 * Returns HTML table rows for logs
 */

require_once '../config.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(403);
    exit('Unauthorized');
}

// Handle filters from query string
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

// Output table rows
while ($log = $logs->fetch_assoc()):
?>
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

