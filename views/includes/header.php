<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title><?= isset($page_title) ? $page_title : "Masinag Senior High School - Begin Your Journey With Us." ?></title>
    
    <!-- LOGO -->
    <link rel="icon" href="/enrollment_system/assets/img/logo.png" type="image/png">

    <!-- GLOBAL CSS -->
    <link rel="stylesheet" href="/enrollment_system/assets/css/output.css">
    <link rel="stylesheet" href="/enrollment_system/assets/icons/bootstrap-icons.css">
    <link rel="stylesheet" href="/enrollment_system/assets/css/navbar.css">
    <link rel="stylesheet" href="/enrollment_system/assets/css/index.css">
    <link rel="stylesheet" href="/enrollment_system/assets/css/footer.css">
    <link rel="stylesheet" href="/enrollment_system/assets/css/exam-notice.css">

    <!-- STUDENT SIDE CSS -->
    <link rel="stylesheet" href="/enrollment_system/assets/css/reg-form.css">
    <link rel="stylesheet" href="/enrollment_system/assets/css/sidebar.css">

    <!-- REG FORM PATHING -->
    <style>
        <?php
        $cssFiles = [
            APP_ROOT . '/assets/css/output.css',
            APP_ROOT . '/assets/css/index.css',
            APP_ROOT . '/assets/css/reg-form.css',
            APP_ROOT . '/assets/css/sidebar.css',
            APP_ROOT . '/assets/css/admin-summary.css',
            APP_ROOT . '/assets/css/exam-notice.css',
        ];

        foreach ($cssFiles as $file) {
            if (file_exists($file)) {
                echo file_get_contents($file) . "\n";
            }
        }
        ?>
    </style>
</head>

<body class="bg-slate-50">

