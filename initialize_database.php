<?php
// Only allow initialization from localhost
if ($_SERVER['REMOTE_ADDR'] !== '127.0.0.1' && $_SERVER['REMOTE_ADDR'] !== '::1') {
    die("This script can only be run from localhost for security reasons.");
}

// Database configuration
$host = "localhost";
$username = "root";
$password = "";

try {
    // Create connection without database
    $conn = new mysqli($host, $username, $password);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>";
    echo "<h2 style='color: #2196F3;'>Database Initialization</h2>";
    echo "<div style='background: #E3F2FD; padding: 10px; border-radius: 4px; margin: 10px 0;'>Connected successfully</div>";
    
    // Read SQL file
    $sql = file_get_contents('complete_database.sql');
    
    // Split SQL into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)), 'strlen');
    
    // Execute each statement
    foreach ($statements as $statement) {
        if ($conn->query($statement) === FALSE) {
            throw new Exception("Error executing SQL: " . $conn->error . "\nStatement: " . $statement);
        }
    }
    
    echo "<div style='background: #E8F5E9; padding: 10px; border-radius: 4px; margin: 10px 0; color: #2E7D32;'>";
    echo "✅ Database initialized successfully!</div>";
    
    echo "<div style='background: #FFF3E0; padding: 20px; border-radius: 4px; margin: 20px 0;'>";
    echo "<h3 style='color: #F57C00;'>Test Accounts</h3>";
    
    echo "<div style='margin: 15px 0;'>";
    echo "<h4>Faculty Accounts:</h4>";
    echo "<ul style='list-style-type: none; padding: 0;'>";
    echo "<li>📧 Email: john.smith@example.com</li>";
    echo "<li>🔑 Password: password123</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<div style='margin: 15px 0;'>";
    echo "<h4>Student Accounts:</h4>";
    echo "<ul style='list-style-type: none; padding: 0;'>";
    echo "<li>📧 Email: alice.brown@example.com</li>";
    echo "<li>🔑 Password: password123</li>";
    echo "</ul>";
    echo "</div>";
    echo "</div>";
    
    echo "<div style='margin-top: 20px;'>";
    echo "<a href='index.html' style='display: inline-block; background: #2196F3; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;'>Go to Login Page</a>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background: #FFEBEE; padding: 10px; border-radius: 4px; margin: 10px 0; color: #C62828;'>";
    echo "❌ Error: " . htmlspecialchars($e->getMessage());
    echo "</div>";
} finally {
    if (isset($conn)) {
        $conn->close();
    }
    echo "</div>";
}
?>
