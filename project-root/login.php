<?php
session_start();
require_once 'config/db_connection.php'; // Make sure $pdo is here

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: ./dashboard/dashboard.php");
        exit();
    } else {
        $_SESSION['error'] = "Invalid login";
        header("Location: login_form.php");
        exit();
    }
}
