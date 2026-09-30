<?php
require_once "../config/db.php";

$_SESSION = [];
session_destroy();

header("Location: ../student-teacher-login");
exit;