<?php
// Generate password hashes for testing
echo "Faculty Passwords:\n";
for($i = 1001; $i <= 1005; $i++) {
    $password = "FAC" . $i;
    $hash = password_hash($password, PASSWORD_DEFAULT);
    echo "$i: Password: $password, Hash: $hash\n";
}

echo "\nStudent Passwords:\n";
for($i = 2001; $i <= 2010; $i++) {
    $password = "STU" . $i;
    $hash = password_hash($password, PASSWORD_DEFAULT);
    echo "$i: Password: $password, Hash: $hash\n";
}
?>
