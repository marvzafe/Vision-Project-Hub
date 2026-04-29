<?php 
// 1. You MUST start the session at the very top for $_SESSION to work!
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Get the current URI to determine which sidebar link should be active
$current_uri = $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $pageTitle ?? 'CRM'; ?></title>
  
  <link rel="stylesheet" href="/../assets/css/global.css"> 
  <link rel="stylesheet" href="/../assets/css/sidebar.css"> 
  
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
  <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
</head>
<body>
<div class="app-layout" id="main-layout">
    
    <header class="top-nav-bar">
        <a href="/../src/modules/dashboard/dashboard-controller.php" class="nav-brand-box">
            <i class="ph-fill ph-hexagon brand-icon"></i>
            <span class="brand-text">VISION</span>
        </a>

        <nav class="nav-links-box">
            <a href="/../src/modules/dashboard/dashboard-controller.php" class="nav-item <?php echo strpos($current_uri, 'dashboard') !== false ? 'active' : ''; ?>">
                <i class="ph ph-squares-four nav-icon"></i> 
                <span class="nav-text">Dashboard</span>
            </a>
            <a href="/src/modules/projects/project-controller.php" class="nav-item <?php echo strpos($current_uri, 'project') !== false ? 'active' : ''; ?>">
                <i class="ph ph-buildings nav-icon"></i> 
                <span class="nav-text">Projects</span>
            </a>
            <a href="#" class="nav-item <?php echo strpos($current_uri, 'task') !== false ? 'active' : ''; ?>">
                <i class="ph ph-check-square-offset nav-icon"></i> 
                <span class="nav-text">Tasks</span>
            </a>
            <a href="#" class="nav-item <?php echo strpos($current_uri, 'supplier') !== false ? 'active' : ''; ?>">
                <i class="ph ph-briefcase nav-icon"></i> 
                <span class="nav-text">Suppliers</span>
            </a>
            <a href="/src/modules/users/user-list-controller.php" class="nav-item <?php echo strpos($current_uri, 'user') !== false ? 'active' : ''; ?>">
                <i class="ph ph-users nav-icon"></i> 
                <span class="nav-text">Team</span>
            </a>
            <a href="/src/modules/file/file-controller.php" class="nav-item <?php echo strpos($current_uri, 'document') !== false ? 'active' : ''; ?>">
                <i class="ph ph-folder-notch nav-icon"></i> 
                <span class="nav-text">Documents</span>
            </a>
        </nav>

        <div class="nav-right-box">
            <div class="profile-widget">
                <button class="profile-trigger" data-dropdown-toggle="profile-menu">
                    <div class="avatar" style="overflow: hidden;">
                        <?php if (!empty($_SESSION['avatar_url'])): ?>
                            <img src="<?php echo $_SESSION['avatar_url']; ?>" 
                                alt="Profile" 
                                style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <?php 
                                $initial = !empty($_SESSION['first_name']) 
                                        ? substr($_SESSION['first_name'], 0, 1) 
                                        : 'U';
                                echo strtoupper($initial); 
                            ?>
                        <?php endif; ?>
                    </div>
                    <div class="profile-info-text" style="text-align: left;">
                        <div class="profile-name">
                            <?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Account'); ?>
                        </div>
                        <div style="font-size: 0.7rem; color: var(--text-muted); margin-top: -2px;">
                            <?php echo htmlspecialchars($_SESSION['user_role'] ?? 'Member'); ?>
                        </div>
                    </div>
                    <i class="ph ph-caret-down dropdown-icon"></i>
                </button>

                <div class="dropdown-menu" id="profile-menu">
                    <div style="padding: 10px 14px; font-size: 0.8rem; color: var(--text-muted); border-bottom: 1px solid rgba(0,0,0,0.05);">
                        Logged in as:<br>
                        <strong style="color: var(--text-main);">
                            <?php echo htmlspecialchars($_SESSION['user_email'] ?? 'Unknown'); ?>
                        </strong>
                    </div>
                    
                    <a href="/profile.php" class="dropdown-item"><i class="ph ph-user"></i> My Profile</a>
                    <a href="/settings.php" class="dropdown-item"><i class="ph ph-gear"></i> Settings</a>
                    
                    <div class="dropdown-divider"></div>
                    
                    <a href="/src/modules/auth/auth-controller.php?action=logout" class="dropdown-item text-danger">
                        <i class="ph ph-sign-out"></i> Log Out
                    </a>
                </div>
            </div>
        </div>
    </header>

<main class="main-content">

<script>
    (function() {
        // Grab the button using querySelector
        const toggleBtn = document.querySelector('#sidebar-toggle');

        if (toggleBtn) {
            // Guarantee we grab the visible layout wrapping THIS specific button
            const mainLayout = toggleBtn.closest('.app-layout') || document.querySelector('.app-layout');
            const sidebar = document.querySelector('.sidebar');

            setTimeout(() => {
                if (mainLayout) mainLayout.classList.add('transitions-enabled');
            }, 50);

            toggleBtn.addEventListener('click', function(e) {
                e.preventDefault(); 
                
                if (mainLayout) {
                    // 1. Toggle the class
                    mainLayout.classList.toggle('collapsed');
                    
                    // 2. The Browser "Kick": Force a style recalculation on the sidebar itself
                    // This fixes the 'position: fixed' render freeze bug.
                    if (sidebar) {
                        sidebar.style.transform = 'translateZ(0)'; // Hardware acceleration kick
                        setTimeout(() => { sidebar.style.transform = ''; }, 300); // Clean up after animation
                    }
                    
                    // 3. Save the state
                    const isCollapsed = mainLayout.classList.contains('collapsed');
                    localStorage.setItem('sidebarState', isCollapsed ? 'collapsed' : 'expanded');
                }
            });
        }
    })();
</script>