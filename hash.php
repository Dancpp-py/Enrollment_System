<?php 
echo password_hash('admin123', PASSWORD_DEFAULT);


$input_password = "chrlz0911";
$stored_hash = "$2y$10$5RzT8SNP73xIOC2qTz5g/emAJMm1k2hL.RumRlv4mRBpSWMYW/AjS"; // From your database

if (password_verify($input_password, $stored_hash)) {
    echo 'Password Verified!';
} else {
    echo 'Incorrect Password!';
}
?>