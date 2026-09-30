<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
 define('APP_ROOT', dirname(__DIR__));
$servername = "localhost";
$username = "root";
$password = "";
$database = "enrollment_system";

$conn = new mysqli ($servername, $username, $password, $database);
if($conn->connect_error) {
    die("Connection Failed: ". $conn->connect_error);
}

define('PAYMONGO_SECRET_KEY', 'sk_test_BAp2tEAnHBUzzag8s5bHBBMS');       // from PayMongo dashboard → Developers
define('PAYMONGO_WEBHOOK_SECRET', 'whsec_xxxxxxxxxxxx');      // shown once when you create the webhook
define('APP_PUBLIC_URL', 'https://your-ngrok-subdomain.ngrok-free.app/enrollment_system');
?>