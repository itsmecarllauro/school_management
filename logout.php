<?php
require 'config.php';
require 'functions.php';

session_start();

$user_id = $_SESSION['user_id'] ?? null;

if ($user_id) {
    log_action($pdo, $user_id, 'User logged out');
}

session_unset();
session_destroy();
header('Location: login.php');
exit;
?>
