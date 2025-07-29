<?php
session_start();

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . './config/db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Safely get the email
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

    if (!$email) {
        $_SESSION['error'] = 'Invalid email address.';
        header('Location: http://localhost/project/project-root/reset_password.php');
        exit();
    }

    try {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Generate reset token and expiry time
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Store token and expiry in DB
            $stmt = $pdo->prepare("
                UPDATE users 
                SET reset_token = :token, reset_expires = :expires 
                WHERE email = :email
            ");
            $stmt->execute([
                ':token' => $token,
                ':expires' => $expires,
                ':email' => $email
            ]);

            // Save success message
            $_SESSION['message'] = 'Password reset link has been sent to your email.';
        } else {
            $_SESSION['error'] = 'Email not found.';
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Database error: ' . $e->getMessage();
    }

    $_SESSION['message'] = 'Password reset link has been sent to your email.';
    header('Location: success.php');
    
    // Redirect back to reset_password.php to show message
    header('Location: http://localhost/project/project-root/reset_password.php');
    exit();
    
}
