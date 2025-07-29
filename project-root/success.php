<?php
session_start();
$message = $_SESSION['message'] ?? 'Operation completed.';
unset($_SESSION['message']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Success</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding-top: 50px;
        }
        img {
            width: 150px;
            margin-top: 20px;
        }
        .message {
            font-size: 20px;
            color: green;
        }
    </style>
</head>
<body>
    <div class="message"><?= htmlspecialchars($message) ?></div>
    <img src="https://creazilla-store.fra1.digitaloceanspaces.com/icons/7911823/cdn-icon-md.png" alt="Success">
</body>
</html>
