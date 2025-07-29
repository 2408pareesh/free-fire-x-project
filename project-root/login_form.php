<?php
session_start();
?>

<form action="http://localhost/project/project-root/login.php" method="post">
    <input type="text" name="username" placeholder="Username" required><br><br>
    <input type="password" name="password" placeholder="Password" required><br><br>
    <button type="submit">Login</button>
    <p style="color: red;">
        <?= $_SESSION['error'] ?? '' ?>
        <?php unset($_SESSION['error']); ?>
    </p>
</form>
