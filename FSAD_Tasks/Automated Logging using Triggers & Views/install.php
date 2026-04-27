<?php
/**
 * Database Installer Script
 * Run this first to set up the database, tables, triggers, and views
 * Access via: http://localhost/audit-logging-project/install.php
 */

// Database connection without selecting a database first
$conn = new mysqli('localhost', 'root', '', '', 3307);

if ($conn->connect_error) {
    die("<div style='padding:20px; font-family:Arial;'><h2 style='color:red'>❌ Connection Failed</h2><p>" . $conn->connect_error . "</p></div>");
}

echo "<!DOCTYPE html><html><head><title>Database Installer</title><style>
body { font-family: 'Segoe UI', Arial; background: #f1f5f9; padding: 40px; }
.container { max-width: 800px; margin: 0 auto; background: white; padding: 40px; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
h1 { color: #4f46e5; }
.step { padding: 12px 16px; margin: 8px 0; border-radius: 8px; background: #f8fafc; border-left: 4px solid #cbd5e1; }
.step.success { background: #ecfdf5; border-left-color: #10b981; }
.step.error { background: #fef2f2; border-left-color: #ef4444; }
.btn { display: inline-block; padding: 14px 28px; background: linear-gradient(135deg, #4f46e5, #4338ca); color: white; text-decoration: none; border-radius: 10px; font-weight: 600; margin-top: 20px; }
.btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(79,70,229,0.4); }
</style></head><body><div class='container'><h1>🛡️ Audit Log System - Database Installer</h1>";

// Read SQL file
$sql_file = file_get_contents(__DIR__ . '/database.sql');

if ($sql_file === false) {
    die("<div class='step error'>❌ Could not read database.sql file</div>");
}

// Split SQL file into individual statements
$statements = array_filter(array_map('trim', explode(';', $sql_file)));

$success_count = 0;
$error_count = 0;

foreach ($statements as $statement) {
    if (empty($statement)) continue;
    
    if ($conn->multi_query($statement . ';')) {
        do {
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->more_results() && $conn->next_result());
        
        $success_count++;
        $short_stmt = substr($statement, 0, 60) . '...';
        echo "<div class='step success'>✅ Executed: " . htmlspecialchars($short_stmt) . "</div>";
    } else {
        $error_count++;
        echo "<div class='step error'>❌ Error: " . htmlspecialchars($conn->error) . "</div>";
        // Continue anyway - some errors might be from CREATE IF NOT EXISTS
    }
}

echo "<hr><p><strong>Installation Complete!</strong></p>";
echo "<p>Successful queries: {$success_count} | Errors: {$error_count}</p>";
echo "<a href='login.php' class='btn'>🚀 Go to Login Page</a>";
echo "</div></body></html>";

$conn->close();
?>

