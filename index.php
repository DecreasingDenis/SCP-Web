<?php

require_once 'includes/config.php'; // Include the database configuration

session_start(); // Start session before using $_SESSION

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

// User info for display
$isLoggedIn = isset($_SESSION['user_id']);
$username = $_SESSION['username'] ?? '';

// Function to check clearance level access
function hasClearanceLevel($userClearance, $requiredLevel) {
    $levels = ['scp_low' => 0, 'scp_medium' => 1, 'scp_high' => 2, 'scp_admin' => 3];
    $userLevel = $levels[$userClearance] ?? 0;
    $required = $levels[$requiredLevel] ?? 0;
    return $userLevel >= $required;
}

// Map clearance to database user for level checking
$clearanceToDb = [
    'O5' => 'scp_admin',
    '5' => 'scp_high',
    '4' => 'scp_high',
    '3' => 'scp_medium',
    '2' => 'scp_medium',
    '1' => 'scp_low',
    '0' => 'scp_low'
];

$currentDbLevel = $clearanceToDb[$clearance] ?? 'scp_low';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SCP Foundation - Secure, Contain, Protect</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%);
            color: #e0e0e0;
            min-height: 100vh;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 80%, rgba(255, 0, 0, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.05) 0%, transparent 50%);
            z-index: -1;
            animation: pulse 4s ease-in-out infinite alternate;
        }

        @keyframes pulse {
            0% { opacity: 0.3; }
            100% { opacity: 0.7; }
        }

        /* Top Navigation Bar */
        .topbar {
            background: linear-gradient(90deg, #2c2c2c 0%, #1a1a1a 100%);
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
            text-shadow: 0 0 10px rgba(255, 68, 68, 0.5);
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

        .nav-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .nav-btn:hover::before {
            left: 100%;
        }

        .nav-btn:hover {
            background: linear-gradient(45deg, #555, #777);
            border-color: #999;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .nav-btn.high-clearance {
            background: linear-gradient(45deg, #4a4a00, #6a6a00);
            border-color: #ffff00;
            color: #ffff00;
            text-shadow: 0 0 5px rgba(255, 255, 0, 0.5);
        }

        .nav-btn.high-clearance:hover {
            background: linear-gradient(45deg, #6a6a00, #8a8a00);
            box-shadow: 0 0 15px rgba(255, 255, 0, 0.4);
        }

        .nav-btn.admin-clearance {
            background: linear-gradient(45deg, #4a0000, #6a0000);
            border-color: #ff4444;
            color: #ff4444;
            text-shadow: 0 0 5px rgba(255, 68, 68, 0.5);
        }

        .nav-btn.admin-clearance:hover {
            background: linear-gradient(45deg, #6a0000, #8a0000);
            box-shadow: 0 0 15px rgba(255, 68, 68, 0.4);
        }

        /* Main Content */
        .container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 0 20px;
        }

        .welcome-section {
            background: rgba(30, 30, 30, 0.8);
            padding: 40px;
            border-radius: 10px;
            border: 2px solid #444;
            backdrop-filter: blur(10px);
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        h1 {
            font-size: 48px;
            text-align: center;
            margin-bottom: 30px;
            color: #ff4444;
            text-shadow: 0 0 20px rgba(255, 68, 68, 0.5);
            font-weight: bold;
        }

        .user-info {
            background: rgba(20, 20, 20, 0.9);
            padding: 25px;
            border-radius: 8px;
            border: 1px solid #666;
            margin-top: 20px;
        }

        .user-info p {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 10px;
        }

        .clearance-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            margin-left: 10px;
            text-shadow: none;
        }

        .clearance-o5 {
            background: linear-gradient(45deg, #ff0000, #cc0000);
            color: white;
            box-shadow: 0 0 10px rgba(255, 0, 0, 0.5);
        }

        .clearance-high {
            background: linear-gradient(45deg, #ffaa00, #cc8800);
            color: white;
            box-shadow: 0 0 10px rgba(255, 170, 0, 0.5);
        }

        .clearance-medium {
            background: linear-gradient(45deg, #0088ff, #0066cc);
            color: white;
            box-shadow: 0 0 10px rgba(0, 136, 255, 0.5);
        }

        .clearance-low {
            background: linear-gradient(45deg, #666, #444);
            color: white;
            box-shadow: 0 0 10px rgba(102, 102, 102, 0.5);
        }

        .login-prompt {
            text-align: center;
            font-size: 18px;
            padding: 30px;
            background: rgba(40, 40, 40, 0.8);
            border-radius: 8px;
            border: 2px solid #ff4444;
        }

        .login-prompt a {
            color: #ff4444;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .login-prompt a:hover {
            color: #ff6666;
            text-shadow: 0 0 5px rgba(255, 68, 68, 0.5);
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

        /* Responsive Design */
        @media (max-width: 768px) {
            .nav-container {
                flex-direction: column;
                gap: 20px;
            }
            
            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
                gap: 15px;
            }
            
            h1 {
                font-size: 32px;
            }
            
            .welcome-section {
                padding: 20px;
            }
        }

        /* Loading animation for page transitions */
        .page-transition {
            opacity: 0;
            animation: fadeIn 0.5s ease-in-out forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>

<body>
    <!-- Top Navigation Bar -->
    <nav class="topbar">
        <div class="nav-container">
            <div class="logo">SCP FOUNDATION</div>
            <div class="nav-links">
                <!-- Personnel - Available to medium clearance and higher -->
                <?php if (hasClearanceLevel($currentDbLevel, 'scp_medium')): ?>
                    <a href="personnel.php" class="nav-btn high-clearance" id="personnelBtn">
                        👥 PERSONNEL
                    </a>
                <?php endif; ?>
                
                <!-- SCP List - Available to all clearance levels -->
                <a href="scpList.php" class="nav-btn" id="scpListBtn">
                    📋 SCP DATABASE
                </a>
                
                <!-- Admin Panel - Available to high clearance and higher -->
                <?php if (hasClearanceLevel($currentDbLevel, 'scp_high')): ?>
                    <a href="adminPanel.php" class="nav-btn admin-clearance" id="adminBtn">
                        ⚙️ ADMIN PANEL
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container page-transition">
        <div class="welcome-section">
            <h1>Welcome to the SCP Foundation</h1>
            
            <?php if ($isLoggedIn): ?>
                <div class="user-info">
                    <p><strong>Hello, <?php echo htmlspecialchars($username); ?>!</strong></p>
                    <p>You are logged in with clearance level: 
                        <span class="clearance-badge <?php 
                            if ($clearance === 'O5') echo 'clearance-o5';
                            elseif (in_array($clearance, ['O3', 'O4'])) echo 'clearance-high';
                            elseif (in_array($clearance, ['O1', 'O2'])) echo 'clearance-medium';
                            else echo 'clearance-low';
                        ?>">
                            <?php echo htmlspecialchars($clearance); ?>
                        </span>
                    </p>
                    <p><strong>Database Access Level:</strong> <?php echo htmlspecialchars($db_user); ?></p>
                    <a href="includes/logout.php" class="logout-btn">🚪 LOGOUT</a>
                </div>
            <?php else: ?>
                <div class="login-prompt">
                    <p><strong>⚠️ UNAUTHORIZED ACCESS DETECTED ⚠️</strong></p>
                    <p>You are not logged in. Please <a href="login.html">authenticate</a> to access Foundation resources.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Add page transition effect
        document.addEventListener('DOMContentLoaded', function() {
            // Add smooth transitions to navigation buttons
            const navBtns = document.querySelectorAll('.nav-btn');
            navBtns.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    // Add loading effect
                    btn.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        btn.style.transform = '';
                    }, 150);
                });
            });

            // Add hover sound effect simulation (visual feedback)
            navBtns.forEach(btn => {
                btn.addEventListener('mouseenter', function() {
                    this.style.transition = 'all 0.2s ease';
                });
            });

            // Security check simulation
            if (window.location.protocol !== 'https:' && window.location.hostname !== 'localhost') {
                console.warn('⚠️ SCP Foundation Warning: Insecure connection detected');
            }

            // Add dynamic clearance level indicator
            const clearanceBadge = document.querySelector('.clearance-badge');
            if (clearanceBadge) {
                clearanceBadge.addEventListener('click', function() {
                    this.style.animation = 'pulse 0.5s ease-in-out';
                    setTimeout(() => {
                        this.style.animation = '';
                    }, 500);
                });
            }

            // Initialize access logging (placeholder)
            console.log(`🔒 Foundation Access Log: User authenticated with clearance level ${document.querySelector('.clearance-badge')?.textContent || 'NONE'}`);
        });

        // Simulate terminal-like typing effect for welcome message
        function typeWriter(element, text, speed = 50) {
            let i = 0;
            element.textContent = '';
            function type() {
                if (i < text.length) {
                    element.textContent += text.charAt(i);
                    i++;
                    setTimeout(type, speed);
                }
            }
            type();
        }

        // Add security warning for low clearance
        <?php if (!$isLoggedIn || $clearance == 0): ?>
        setTimeout(() => {
            console.log('🚨 SCP Foundation Security Notice: Limited access granted to unauthorized personnel');
        }, 1000);
        <?php endif; ?>
    </script>
</body>
</html>