<?php
// test-session.php
session_start();

// Check if the user ID is set in the session
$isLoggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Auth Test | Vision CRM</title>
    <link rel="stylesheet" href="/assets/css/global.css">
    <style>
        .test-card {
            max-width: 600px;
            margin: 50px auto;
            text-align: center;
        }
        .status-badge {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 700;
            margin-bottom: 20px;
        }
        .status-online { background: #d1fae5; color: #059669; }
        .status-offline { background: #fee2e2; color: #dc2626; }
        pre {
            text-align: left;
            background: #1e1e1e;
            color: #dcdcdc;
            padding: 15px;
            border-radius: 8px;
            overflow-x: auto;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card test-card">
        <h2 class="card-title">Authentication Debugger</h2>
        
        <?php if ($isLoggedIn): ?>
            <div class="status-badge status-online">● System Logged In</div>
            <p>The website recognizes your Google account.</p>
            <p><strong>Active User:</strong> <?php echo htmlspecialchars($_SESSION['user_email'] ?? 'Unknown'); ?></p>
        <?php else: ?>
            <div class="status-badge status-offline">○ Not Logged In</div>
            <p>No active session found. Please log in via Google.</p>
            <a href="/src/modules/auth/login-controller.php" class="btn-primary">Go to Login</a>
        <?php endif; ?>

        <hr style="margin: 20px 0; border: 0; border-top: 1px solid var(--border-color);">
        
        <h3 style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 10px;">Raw Session Data:</h3>
        <pre><?php print_r($_SESSION); ?></pre>
    </div>
</div>

</body>
</html>