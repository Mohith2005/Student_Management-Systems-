<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Test password
$password = "STU2001";

// Generate a new hash
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Generated hash for $password: " . $hash . "\n";

// Verify the password against the hash
$verify = password_verify($password, $hash);
echo "Verification result: " . ($verify ? "SUCCESS" : "FAILED") . "\n";

// Now let's update the database with this hash
require_once "config.php";

$sql = "UPDATE students SET password = ? WHERE id = 2001";
if($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $hash);
    if(mysqli_stmt_execute($stmt)) {
        echo "Successfully updated password for student ID 2001\n";
    } else {
        echo "Error updating password: " . mysqli_error($conn) . "\n";
    }
    mysqli_stmt_close($stmt);
} else {
    echo "Error preparing statement: " . mysqli_error($conn) . "\n";
}

// Do the same for faculty
$faculty_password = "FAC1001";
$faculty_hash = password_hash($faculty_password, PASSWORD_DEFAULT);

$sql = "UPDATE faculty SET password = ? WHERE id = 1001";
if($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $faculty_hash);
    if(mysqli_stmt_execute($stmt)) {
        echo "Successfully updated password for faculty ID 1001\n";
    } else {
        echo "Error updating password: " . mysqli_error($conn) . "\n";
    }
    mysqli_stmt_close($stmt);
}

// Now verify from database
$sql = "SELECT password FROM students WHERE id = 2001";
$result = mysqli_query($conn, $sql);
if($row = mysqli_fetch_assoc($result)) {
    $db_hash = $row['password'];
    echo "\nVerifying password from database:\n";
    echo "Stored hash: " . $db_hash . "\n";
    echo "Verification result: " . (password_verify($password, $db_hash) ? "SUCCESS" : "FAILED") . "\n";
}

mysqli_close($conn);
?>
