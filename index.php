<?php
require_once __DIR__ . '/config/database.php';

// Check if user is logged in, redirect to login
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Redirect to dashboard
header('Location: pages/dashboard.php');
exit;
