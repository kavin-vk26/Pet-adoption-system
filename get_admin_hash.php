    <?php
    // Define the password you are using for the admin account
    $password = 'admin123';

    // Generate a new hash
    $hash = password_hash($password, PASSWORD_DEFAULT);

    echo "<h1>Admin Password Hashing Utility</h1>";
    echo "<p>Use the following hash string to update your admin user's password field in phpMyAdmin.</p>";
    echo "<p><strong>Password:</strong> $password</p>";
    echo "<hr>";
    echo "<p><strong>NEW HASH TO COPY:</strong></p>";
    // IMPORTANT: Copy everything between the <pre> tags, including the $2y...
    echo "<pre>".$hash."</pre>"; 
    echo "<hr>";

    // You can test the hash immediately with password_verify
    $test = password_verify('admin123', $hash) ? 'Success' : 'Failure';
    echo "<p>Hash Test: <span style='color: green;'>$test</span> (If this fails, your PHP environment is corrupted.)</p>";
    ?>
    
