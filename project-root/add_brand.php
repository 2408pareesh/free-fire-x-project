<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login_form.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Gaming Brand</title>
</head>
<body>
    <h2>Add a New Gaming Brand</h2>
    <form action="save_brand.php" method="post">
        <label>Brand Name:</label>
        <input type="text" name="brand_name" required><br><br>

        <label>Website:</label>
        <input type="url" name="brand_website"><br><br>

        <button type="submit">Save Brand</button>
    </form>
</body>
</html>
