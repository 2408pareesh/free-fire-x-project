<?php
// Database connection
$host = 'localhost';
$dbname = 'root';
$username = 'mohamed_salim';
$password = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo "Database connection failed: " . $e->getMessage();
    exit;
}

// Handle POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gamer_id = $_POST['gamer_id'] ?? '';
    $fullname = $_POST['fullname'] ?? '';
    $email    = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    // Validation
    if (empty($gamer_id) || empty($fullname) || empty($email) || empty($password) || $password !== $confirm) {
        http_response_code(400);
        echo "Please fill all fields and ensure passwords match.";
        exit;
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert query
    $stmt = $pdo->prepare("INSERT INTO gamers (gamer_id, fullname, email, password) VALUES (?, ?, ?, ?)");
    
    try {
        $stmt->execute([$gamer_id, $fullname, $email, $hashedPassword]);
        echo "Gamer registered successfully!";
    } catch (PDOException $e) {
        http_response_code(500);
        echo "Insert failed: " . $e->getMessage();
    }
} else {
    http_response_code(405);
    echo "Method Not Allowed";
}

?>