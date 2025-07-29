<?php
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header("Location: ./project-root/login.php");
        exit;
    }
        
    // Database connection config
    $host = 'localhost';
    $dbname = 'rootboost_db';  // change as needed
    $user = 'root';            // change as needed
    $pass = '';                // change as needed
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        http_response_code(500);
        echo "Database connection failed: " . htmlspecialchars($e->getMessage());
        exit;
    }

    // Handle loadOrders request
    if (isset($_GET['action']) && $_GET['action'] === 'loadOrders') {
        $stmt = $pdo->query("SELECT id, username, game_name, quantity, status, ordered_at FROM orders ORDER BY ordered_at DESC");
        $orders = $stmt->fetchAll();

        echo '<table border="1" cellpadding="10" cellspacing="0" style="width:100%; background:#000; color:#fff; border-collapse: collapse;">';
        echo '<thead style="background-color: #222;">
                <tr>
                    <th>ID</th><th>Username</th><th>Game</th><th>Quantity</th><th>Status</th><th>Ordered At</th>
                </tr>
            </thead><tbody>';
        foreach ($orders as $row) {
            echo "<tr>";
            foreach ($row as $col) {
                echo "<td>" . htmlspecialchars($col) . "</td>";
            }
            echo "</tr>";
        }
        echo '</tbody></table>';
        exit;
    }

    // Handle loadGames request
    if (isset($_GET['action']) && $_GET['action'] === 'loadGames') {
        $stmt = $pdo->query("SELECT id, game_name, download_link FROM games ORDER BY game_name ASC");
        $games = $stmt->fetchAll();

        echo '<table border="1" cellpadding="10" cellspacing="0" style="width:100%; background:#000; color:#fff; border-collapse: collapse;">';
        echo '<thead style="background-color: #222;">
                <tr>
                    <th>ID</th><th>Game Name</th><th>Download Link</th>
                </tr>
            </thead><tbody>';
        foreach ($games as $game) {
            $safeName = htmlspecialchars($game['game_name']);
            $safeLink = htmlspecialchars($game['download_link']);
            echo "<tr>
                    <td>{$game['id']}</td>
                    <td>{$safeName}</td>
                    <td><a href='{$safeLink}' target='_blank' style='color:#0ff;'>Download</a></td>
                </tr>";
        }
        echo '</tbody></table>';
        exit;
    }

    // Handle gamer registration form submit
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register_submit') {
        $gamer_id = $_POST['gamer_id'] ?? '';
        $gamer_name = $_POST['gamer_name'] ?? '';
        $gamer_email = $_POST['gamer_email'] ?? '';
        $gamer_password = $_POST['gamer_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if ($gamer_password !== $confirm_password) {
            echo "<p style='color:red;'>Passwords do not match!</p>";
            exit;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO gamers (gamer_id, name, email, password) VALUES (?, ?, ?, ?)");
            $hashed_pw = password_hash($gamer_password, PASSWORD_BCRYPT);
            if ($stmt->execute([$gamer_id, $gamer_name, $gamer_email, $hashed_pw])) {
                echo "<p style='color:lime;'>Registration successful!</p>";
            } else {
                echo "<p style='color:red;'>Failed to register user.</p>";
            }
        } catch (PDOException $e) {
            echo "<p style='color:red;'>Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
        exit;
    }

    // Handle add game form submit
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_game_submit') {
        $game_name = $_POST['game_name'] ?? '';
        $description = $_POST['description'] ?? '';
        $google_drive_link = $_POST['google_drive_link'] ?? '';

        if (empty($game_name) || empty($description) || empty($google_drive_link)) {
            echo "<p style='color:red;'>All fields are required.</p>";
            exit;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO game_links (game_name, description, google_drive_link) VALUES (?, ?, ?)");
            $stmt->execute([$game_name, $description, $google_drive_link]);
            echo "<p style='color:lime;'>Game added successfully!</p>";
        } catch (PDOException $e) {
            echo "<p style='color:red;'>Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
        exit;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Root X Admin Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600&display=swap" rel="stylesheet" />
<style>
    body {
        font-family: 'Orbitron', sans-serif;
        background-color: #111;
        color: #fff;
        margin: 0;
    }
    h1 {
        text-align: center;
        padding: 20px;
        color: #0ff;
        border-bottom: 2px solid #0ff;
    }
    .nav-container {
        background: #1a1a1a;
        padding: 0 20px;
    }
    .menu-wrap {
        position: relative;
    }
    .main-menu {
        display: flex;
        list-style: none;
        flex-wrap: wrap;
    }
    .menu-item {
        position: relative;
        color: #0ff;
        padding: 15px 20px;
        cursor: pointer;
        font-weight: bold;
    }
    .submenu {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        background: #222;
        min-width: 200px;
        list-style: none;
        z-index: 100;
        border: 1px solid #0ff;
    }
    .menu-item:hover .submenu {
        display: block;
    }
    .submenu li {
        padding: 10px 15px;
        color: #fff;
        cursor: pointer;
    }
    .submenu li:hover {
        background: #0ff;
        color: #000;
    }
    .hamburger {
        display: none;
        color: #0ff;
        font-size: 24px;
        cursor: pointer;
        padding: 15px;
    }
    #content {
        padding: 30px;
        min-height: 400px;
        animation: fadeIn 0.5s ease-in-out;
        border-radius: 15px;
        background: linear-gradient(to right, #0f0c29, #302b63, #24243e);
        margin: 30px;
    }
    @media (max-width: 768px) {
        .hamburger { display: block; }
        .main-menu { display: none; flex-direction: column; width: 100%; }
        .main-menu.active { display: flex; }
        .submenu { position: static; width: 100%; }
        .menu-item { width: 100%; border-bottom: 1px solid #333; }
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    /* Register and Add Game form styling */
    #content form label {
        display: block;
        margin: 10px 0 5px;
    }
    #content form input, #content form textarea {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border-radius: 6px;
        border: none;
    }
    #content form button {
        padding: 12px 20px;
        background: #0ff;
        border: none;
        font-weight: bold;
        cursor: pointer;
        border-radius: 8px;
        color: #000;
    }
    #register-response, #add-game-response {
        margin-top: 10px;
    }
</style>
</head>
<body>

<h1>ROOT X FREE FIRE Admin Panel</h1>

<nav class="nav-container">
    <div class="hamburger" onclick="toggleMenu()">☰</div>
    <div class="menu-wrap">
        <div style="padding: 50px; background: url('https://media.licdn.com/dms/image/D4D12AQEdGrLFtigZlg/article-cover_image-shrink_600_2000/0/1688530200322?e=2147483647&v=beta&t=Q_jp-X5BzY7XAdFVYjvN60R1iutf-q4YLwSSpt-eslA') no-repeat center center / cover;">
            <ul class="main-menu" id="mainMenu">
                <li class="menu-item">
                    Dashboard
                    <ul class="submenu">
                        <a onclick="loadContent('register.html')"><i class="fas fa-user-plus"></i> Register</a>
                        <li onclick="loadContent('google-links')">Google Link</li>
                        <li onclick="loadContent('orders')">Orders</li>
                        <li onclick="loadContent('downloads')">Downloads</li>
                        <li onclick="loadContent('cyber')">Cyber Net</li>
                        <li onclick="loadContent('settings')">Settings</li>
                        <li onclick="loadContent('add-game')">Add Game</li>
                        <li onclick="logout()">Logout</li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div id="content">
    <p>Welcome to the Root X Admin Dashboard. Select a section from the menu.</p>
</div>

<script>
    function loadContent(page) {
        if (page === 'orders') {
            fetch('dashboard.php?action=loadOrders')
                .then(response => response.text())
                .then(data => document.getElementById('content').innerHTML = data)
                .catch(() => document.getElementById('content').innerHTML = '<p style="color:red;">Error loading orders.</p>');
        } else if (page === 'google-links') {
            fetch('dashboard.php?action=loadGames')
                .then(response => response.text())
                .then(data => document.getElementById('content').innerHTML = data)
                .catch(() => document.getElementById('content').innerHTML = '<p style="color:red;">Error loading games.</p>');
        } else if (page === 'register') {
            const formHtml = `
                <h2 style="color:#0ff;">Register Gamer</h2>
                <form id="registerForm">
                    <label for="gamer_id">Gamer ID:</label>
                    <input type="text" id="gamer_id" name="gamer_id" required />
                    <label for="gamer_name">Gamer Name:</label>
                    <input type="text" id="gamer_name" name="gamer_name" required />
                    <label for="gamer_email">Email:</label>
                    <input type="email" id="gamer_email" name="gamer_email" required />
                    <label for="gamer_password">Password:</label>
                    <input type="password" id="gamer_password" name="gamer_password" required />
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required />
                    <button type="submit">Register</button>
                </form>
                <div id="register-response"></div>
            `;
            document.getElementById('content').innerHTML = formHtml;

            document.getElementById('registerForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const form = e.target;
                const formData = new FormData(form);
                formData.append('action', 'register_submit');

                fetch('dashboard.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.text())
                .then(data => {
                    document.getElementById('register-response').innerHTML = data;
                    if (!data.toLowerCase().includes('error') && !data.toLowerCase().includes('failed') && !data.toLowerCase().includes('do not match')) {
                        form.reset();
                    }
                })
                .catch(() => {
                    document.getElementById('register-response').innerHTML = '<p style="color:red;">Error submitting form.</p>';
                });
            });
        } else if (page === 'add-game') {
            const formHtml = `
                <h2 style="color:#0ff;">Add New Game</h2>
                <form id="addGameForm">
                    <label for="game_name">Game Name:</label>
                    <input type="text" id="game_name" name="game_name" required />
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" required></textarea>
                    <label for="google_drive_link">Google Drive Link:</label>
                    <input type="url" id="google_drive_link" name="google_drive_link" required />
                    <button type="submit">Add Game</button>
                </form>
                <div id="add-game-response"></div>
            `;
            document.getElementById('content').innerHTML = formHtml;

            document.getElementById('addGameForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const form = e.target;
                const formData = new FormData(form);
                formData.append('action', 'add_game_submit');

                fetch('dashboard.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.text())
                .then(data => {
                    document.getElementById('add-game-response').innerHTML = data;
                    if (data.toLowerCase().includes('success')) {
                        form.reset();
                    }
                })
                .catch(() => {
                    document.getElementById('add-game-response').innerHTML = '<p style="color:red;">Error submitting form.</p>';
                });
            });
        } else {
            fetch(`dashboard/${page}.html`)
                .then(res => {
                    if (!res.ok) throw new Error('Page not found');
                    return res.text();
                })
                .then(data => document.getElementById('content').innerHTML = data)
                .catch(() => document.getElementById('content').innerHTML = `<p style="color:red;">Error loading: ${page}</p>`);
        }
    }

    function logout() {
        window.location.href = './project-root/logout.php';
    }

    function toggleMenu() {
        document.getElementById('mainMenu').classList.toggle('active');
    }
</script>

</body>
</html>
