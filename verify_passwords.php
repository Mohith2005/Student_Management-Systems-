<?php
require_once "config.php";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to verify and update password hashes
function verifyAndUpdatePasswords() {
    global $conn;
    
    // Get all student records
    $sql = "SELECT id, email, password FROM students";
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        die("Error fetching students: " . mysqli_error($conn));
    }
    
    $updates = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        $id = $row['id'];
        $email = $row['email'];
        $storedHash = $row['password'];
        
        // Check if the hash needs updating (empty or invalid format)
        if (empty($storedHash) || strlen($storedHash) < 60) {
            // Generate default password (STU + student ID)
            $defaultPassword = "STU" . $id;
            $newHash = password_hash($defaultPassword, PASSWORD_DEFAULT);
            
            // Update the password in the database
            $updateSql = "UPDATE students SET password = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $updateSql);
            
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "si", $newHash, $id);
                if (mysqli_stmt_execute($stmt)) {
                    $updates++;
                    echo "Updated password for student ID: $id (Email: $email)<br>";
                } else {
                    echo "Failed to update password for student ID: $id<br>";
                }
                mysqli_stmt_close($stmt);
            }
        }
    }
    
    echo "<br>Total passwords updated: $updates";
}

// Run the verification
echo "<h2>Password Verification and Update</h2>";
verifyAndUpdatePasswords();
?>
