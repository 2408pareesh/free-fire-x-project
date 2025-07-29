<!-- topup.php -->
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login_form.php");
    exit();
}
?>

<form action="save_topup.php" method="post">
    <label>Enter Top-Up Amount:</label>
    <input type="number" name="amount" step="0.01" required>
    <button type="submit">Submit</button>
</form>

