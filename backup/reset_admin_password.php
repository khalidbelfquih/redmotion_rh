<?php
include 'config/connexion.php';

$email = "admin@patisserie.com";
$new_password = "admin123";
$hashed_password = md5($new_password);

try {
    // Check if user exists
    $check_sql = "SELECT * FROM utilisateur WHERE email = ?";
    $check_req = $connexion->prepare($check_sql);
    $check_req->execute([$email]);
    $user = $check_req->fetch();

    if ($user) {
        // Update password
        $sql = "UPDATE utilisateur SET password = ? WHERE email = ?";
        $req = $connexion->prepare($sql);
        $req->execute([$hashed_password, $email]);
        
        if ($req->rowCount() > 0) {
            echo "Password for $email has been successfully updated to '$new_password'.\n";
        } else {
            // It might be that the password was already the same
            echo "Password update executed, but no rows changed (password might be the same).\n";
        }
    } else {
        echo "User with email $email not found.\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
