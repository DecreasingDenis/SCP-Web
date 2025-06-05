<?php
// filepath: c:\xampp\htdocs\scp\adminPanel.php
require_once '../includes/config.php';

session_start();

$isLoggedIn = isset($_SESSION['user_id']);
$clearance = 0;
$username = '';

// 1. Connect as root (or your generic user from config.php)
$rootPdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);

if ($isLoggedIn) {
    // 2. Fetch user info using user_ID from session
    $stmt = $rootPdo->prepare("SELECT * FROM personale WHERE user_ID = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if ($user) {
        $clearance = $user['clearance_Level'];
        $username = $user['username'];
    } else {
        // User not found, force logout
        session_unset();
        session_destroy();
        header("Location: login.html");
        exit;
    }
} else {
    // Not logged in
    header("Location: login.html");
    exit;
}

// 3. Choose DB Credentials based on clearance level
switch ($clearance) {
    case 'O5':
        $db_user = 'scp_admin';
        $db_pass = 'AdminO5';
        break;
    case 'O4':
    case 'O3':
        $db_user = 'scp_high';
        $db_pass = 'User45';
        break;
    case 'O2':
    case 'O1':
        $db_user = 'scp_medium';
        $db_pass = 'User23';
        break;
    default:
        $db_user = 'scp_low';
        $db_pass = 'User01';
        break;
}

// 4. Now connect to the DB with the right credentials for the rest of the page
$pdo = new PDO("mysql:host=$host;dbname=$dbname", $db_user, $db_pass);

// Only allow access to scp_admin (O5)
if ($db_user !== 'scp_admin') {
    // Forbidden
    http_response_code(403);
    echo "<h1>403 Forbidden</h1><p>You do not have permission to access this page.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SCP Foundation Admin Panel</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            background: #18191c;
            color: #e0e0e0;
            margin: 0;
            padding: 0;
        }
        .topbar {
            background: #2c2c2c;
            border-bottom: 3px solid #ff4444;
            padding: 15px 0;
            box-shadow: 0 4px 20px rgba(255, 68, 68, 0.3);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #ff4444;
        }
        .nav-links {
            display: flex;
            gap: 30px;
            align-items: center;
        }
        .nav-btn {
            padding: 10px 20px;
            background: linear-gradient(45deg, #333, #555);
            border: 2px solid #666;
            color: #e0e0e0;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s ease;
            font-family: 'Courier New', monospace;
            font-weight: bold;
            position: relative;
            overflow: hidden;
        }
        .nav-btn:hover {
            background: linear-gradient(45deg, #555, #777);
            border-color: #999;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }
        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 30px;
            background: rgba(30,30,30,0.95);
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        h1 {
            color: #ff4444;
            text-align: center;
            margin-bottom: 30px;
        }
        .logout-btn {
            background: linear-gradient(45deg, #aa0000, #cc0000);
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-family: 'Courier New', monospace;
            font-weight: bold;
            transition: all 0.3s ease;
            display: inline-block;
            margin-top: 10px;
        }
        .logout-btn:hover {
            background: linear-gradient(45deg, #cc0000, #ee0000);
            box-shadow: 0 0 10px rgba(255, 0, 0, 0.3);
            transform: translateY(-1px);
        }
    </style>
</head>
<body>
    <nav class="topbar">
        <div class="nav-container">
            <div class="logo">SCP FOUNDATION</div>
            <div class="nav-links">
                <a href="index.php" class="nav-btn">🏠 Home</a>
                <a href="includes/logout.php" class="logout-btn">🚪 LOGOUT</a>
            </div>
        </div>
    </nav>
    <div class="container">
        <h1>Admin Panel</h1>
        <p>Welcome, <strong><?php echo htmlspecialchars($username); ?></strong> (O5 - SCP Admin).</p>
        <p>This area is restricted to O5/Administrator personnel only.</p>
        <!-- Admin features go here -->
        <ul style="list-style: none; padding: 0; margin: 0;">
            <li style="margin-bottom: 22px;">
                <a href="personnel.php" class="nav-btn">Manage Personnel</a>
            </li>
            <li style="margin-bottom: 22px;">
                <a href="scpList.php" class="nav-btn">Manage SCP Database</a>
            </li>
            <li>
                <a href="reports.php" class="nav-btn">Incident Reports</a>
            </li>
            <!-- Add more admin links as needed -->
        </ul>
    </div>
</body>
</html>