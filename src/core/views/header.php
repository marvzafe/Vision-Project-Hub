<?php 
// 1. You MUST start the session at the very top for $_SESSION to work!
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}



// 2. Get the current URI to determine which sidebar link should be active
$current_uri = $_SERVER['REQUEST_URI'];

require_once __DIR__ . '/../require-auth.php';
require_once __DIR__ . '/../active-status.php';
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
  
  <script src="/assets/js/reactive-engine.js"></script>
  <script src="/assets/js/toast-notifications.js"></script>
</head>
<body>
<div class="app-layout" id="main-layout">
    
    <header class="top-nav-bar">
        <a href="#" class="nav-brand-box" data-modal-target="versionModal">
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
            <a href="/src/modules/template/template-controller.php" class="nav-item <?php echo strpos($current_uri, 'template') !== false ? 'active' : ''; ?>">
                <i class="ph ph-clipboard-text nav-icon"></i> 
                <span class="nav-text">Templates</span>
            </a>
            <a href="/src/modules/users/user-list-controller.php" class="nav-item <?php echo strpos($current_uri, 'user') !== false ? 'active' : ''; ?>">
                <i class="ph ph-users nav-icon"></i> 
                <span class="nav-text">Team</span>
            </a>
            <a href="/src/modules/file/file-controller.php" class="nav-item <?php echo strpos($current_uri, 'file') !== false ? 'active' : ''; ?>">
                <i class="ph ph-folder-notch nav-icon"></i> 
                <span class="nav-text">Documents</span>
            </a>
        </nav>

        <div class="nav-right-box" style="display: flex; gap: 8px;">
            
<div class="profile-widget" style="position: relative;">
                
                <button class="profile-trigger" id="nav-notif-btn" style="width: 46px; height: 46px; padding: 0; justify-content: center;">
                    <i class="ph ph-bell" style="font-size: 1.35rem; margin: 0;"></i>
                </button>
                
                <div class="dropdown-menu" id="nav-notif-menu" style="width: 320px; max-height: 400px; overflow-y: auto; padding: 0;">
                    <div style="padding: 12px 14px; font-size: 0.9rem; font-weight: 600; border-bottom: 1px solid rgba(0,0,0,0.05); color: var(--text-main);">
                        Recent Notifications
                    </div>
                    <div id="nav-notif-content">
                        <div style="padding: 20px; text-align: center; color: var(--text-muted); font-size: 0.85rem;">Loading...</div>
                    </div>
                </div>
            </div>

            <div class="profile-widget" style="position: relative;">
                <button class="profile-trigger" id="nav-profile-btn">
                    <div class="avatar" style="overflow: hidden;">
                        <?php if (!empty($_SESSION['avatar_url'])): ?>
                            <img src="<?php echo $_SESSION['avatar_url']; ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <?php echo strtoupper(substr($_SESSION['first_name'] ?? 'U', 0, 1)); ?>
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

                <div class="dropdown-menu" id="nav-profile-menu">
                    <div style="padding: 10px 14px; font-size: 0.8rem; color: var(--text-muted); border-bottom: 1px solid rgba(0,0,0,0.05);">
                        Logged in as:<br>
                        <strong style="color: var(--text-main);">
                            <?php echo htmlspecialchars($_SESSION['user_email'] ?? 'Unknown'); ?>
                        </strong>
                    </div>
                    <a href="/profile.php" class="dropdown-item"><i class="ph ph-user"></i> My Profile</a>
                    <a href="/settings.php" class="dropdown-item"><i class="ph ph-gear"></i> Settings</a>
                    <div class="dropdown-divider"></div>
                    <a href="/src/modules/auth/auth-controller.php?action=logout" class="dropdown-item text-danger" style="color: var(--status-attention);">
                        <i class="ph ph-sign-out"></i> Log Out
                    </a>
                </div>
            </div>
            
        </div>
    </header>

<main class="main-content">
<?php 
// Adjust the relative path depending on where your header/layout file is located
include_once __DIR__ . '/../../../src/core/views/components/version-modal.php'; 
?>
<script>

    document.addEventListener('DOMContentLoaded', () => {
        // Global Main Navigation Scroll Tracker
        const topNav = document.querySelector('.top-nav-bar');
        
        if (topNav) {
            window.addEventListener('scroll', () => {
                // 30px threshold creates a smooth trigger point
                if (window.scrollY > 30) {
                    topNav.classList.add('is-scrolled');
                } else {
                    topNav.classList.remove('is-scrolled');
                }
            }, { passive: true });
        }
    });

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

    // --- DROPDOWN WIDGET CONTROLLERS ---
    document.addEventListener('DOMContentLoaded', () => {
        const notifBtn = document.getElementById('nav-notif-btn');
        const notifMenu = document.getElementById('nav-notif-menu');
        const notifContent = document.getElementById('nav-notif-content');
        
        const profileBtn = document.getElementById('nav-profile-btn');
        const profileMenu = document.getElementById('nav-profile-menu');

        // Toggle Profile
        if (profileBtn) {
            profileBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                if (notifMenu) notifMenu.classList.remove('active');
                profileMenu.classList.toggle('active');
            });
        }

        // Toggle Notifications & Fetch
        if (notifBtn) {
            notifBtn.addEventListener('click', async (e) => {
                e.stopPropagation();
                if (profileMenu) profileMenu.classList.remove('active');
                notifMenu.classList.toggle('active');

                if (notifMenu.classList.contains('active')) {
                    try {
                        const response = await fetch('/src/modules/notifications/notification-controller.php?action=get_json');
                        const data = await response.json();
                        
                        if (data.length === 0) {
                            notifContent.innerHTML = '<div style="padding: 20px; text-align: center; color: var(--text-muted); font-size: 0.85rem;">No new notifications</div>';
                        } else {
                            notifContent.innerHTML = data.map(n => `
                                <a href="/src/modules/notifications/notification-controller.php?action=read&id=${n.id}&project_id=${n.project_id}" 
                                   class="dropdown-item" style="flex-direction: column; align-items: flex-start; gap: 4px; padding: 12px 14px; border-bottom: 1px solid rgba(0,0,0,0.03); border-radius: 0;">
                                    
                                    <div style="font-size: 0.85rem; color: var(--text-main); line-height: 1.3; white-space: normal;">
                                        <strong>${n.actor_first} ${n.actor_last}</strong> 
                                        ${n.type === 'assignment' ? 'assigned you to' : 'mentioned you in'}
                                        <strong>${n.project_name}</strong>
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted); font-style: italic; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 100%;">
                                        "${n.message}"
                                    </div>
                                </a>
                            `).join('') + `
                                <div style="padding: 8px; text-align: center; background: rgba(0,0,0,0.02);">
                                    <button type="button" onclick="clearNavNotifications(event)" style="background:none; border:none; color:var(--primary); cursor:pointer; font-size:0.8rem; font-weight:600;">Clear All Notifications</button>
                                </div>
                            `;
                        }
                    } catch (err) {
                        notifContent.innerHTML = '<div style="padding: 20px; text-align: center; color: var(--status-attention); font-size: 0.85rem;">Error loading.</div>';
                    }
                }
            });
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', (e) => {
            if (notifMenu && !notifBtn.contains(e.target) && !notifMenu.contains(e.target)) {
                notifMenu.classList.remove('active');
            }
            if (profileMenu && !profileBtn.contains(e.target) && !profileMenu.contains(e.target)) {
                profileMenu.classList.remove('active');
            }
        });
    });

    // Global clear function for the dropdown
    window.clearNavNotifications = function(e) {
        e.preventDefault();
        e.stopPropagation();
        if (!confirm("Clear all notifications?")) return;
        
        const formData = new FormData();
        formData.append('action', 'clear_all');
        
        fetch('/src/modules/notifications/notification-controller.php', {
            method: 'POST',
            body: formData
        }).then(res => res.json()).then(data => {
            if (data.success) {
                document.getElementById('nav-notif-content').innerHTML = '<div style="padding: 20px; text-align: center; color: var(--text-muted); font-size: 0.85rem;">No new notifications</div>';
            }
        });
    };
</script>