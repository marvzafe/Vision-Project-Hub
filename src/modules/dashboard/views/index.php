<?php include __DIR__ . '/../../../core/views/header.php'; ?>

<style>
/* Layout Constraints to prevent full-page scroll on desktop */
/* Layout Constraints to prevent full-page scroll on desktop */
/* Layout Constraints to prevent full-page scroll on desktop */
@media (min-width: 900px) {
    /* 1. Aggressively kill the main page scrollbar */
    body { 
        overflow: hidden !important; 
    }

    .dashboard-wrapper {
        display: flex;
        flex-direction: column;
        /* Increased from 9rem to 11rem to pull the cards up, showing the bottom border-radius */
        height: calc(100vh - 8rem); 
        overflow: hidden; 
    }
    
    .widget-grid {
        flex: 1;
        min-height: 0; 
        padding-bottom: 0.5rem; /* Extra buffer so the bottom shadow isn't clipped */
    }
    
    .ios-card {
        display: flex;
        flex-direction: column;
        height: 100%;
        min-height: 0; /* Force cards to shrink instead of stretching */
    }
}

.dash-header { 
    margin-bottom: 1.5rem; 
    flex-shrink: 0;
}

.dash-greeting { 
    font-size: 2.25rem; 
    font-weight: 800; 
    color: #1d1d1f; 
    letter-spacing: -0.5px;
    margin-bottom: 0.25rem; 
}

.dash-subtitle { 
    color: #86868b; 
    font-size: 1.05rem; 
    font-weight: 500;
}

/* --- Stat Cards --- */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 1.5rem;
    flex-shrink: 0;
}

.stat-card {
    background: rgba(255, 255, 255, 0.65);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.8);
    border-radius: 24px;
    padding: 1.25rem 1.5rem;
    display: flex;
    align-items: center;
    gap: 1.25rem;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.03), 0 2px 6px rgba(0, 0, 0, 0.02);
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.06), 0 4px 8px rgba(0, 0, 0, 0.03);
}

.stat-icon-wrapper {
    width: 50px;
    height: 50px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.icon-active { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }
.icon-completed { background: rgba(16, 185, 129, 0.15); color: #10b981; }
.icon-unstarted { background: rgba(134, 134, 139, 0.15); color: #86868b; }

.stat-info { display: flex; flex-direction: column; }
.stat-title { font-size: 0.8rem; text-transform: uppercase; font-weight: 700; color: #86868b; letter-spacing: 0.5px; }
.stat-value { font-size: 1.75rem; font-weight: 800; color: #1d1d1f; line-height: 1.2; }

/* --- Main Widget Areas (Updated to 3 Columns) --- */
.widget-grid {
    display: grid;
    /* Col 1: Projects (1.2fr), Col 2: Notifications (1fr), Col 3: Team (90px) */
    grid-template-columns: 1.2fr 1fr 90px; 
    gap: 1.5rem;
}

.ios-card {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(24px);
    -webkit-backdrop-filter: blur(24px);
    border: 1px solid rgba(255, 255, 255, 0.6);
    border-radius: 24px;
    padding: 1.5rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
    display: flex;
    flex-direction: column;
    height: 100%;
    min-height: 0;
}

.card-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1d1d1f;
    margin-bottom: 1rem;
    flex-shrink: 0; 
}

/* Avatar-Only Grid Layout */
.team-avatars-only {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    padding-top: 0.5rem;
}

/* --- Internal Scrolling Lists --- */
.scrollable-list {
    flex: 1;
    overflow-y: auto;
    padding-right: 0.5rem; /* Buffer for scrollbar */
    margin-right: -0.5rem; /* Offsets buffer so content aligns with title */
}

/* macOS Style Scrollbar */
.scrollable-list::-webkit-scrollbar { width: 6px; }
.scrollable-list::-webkit-scrollbar-track { background: transparent; }
.scrollable-list::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.15);
    border-radius: 10px;
}
.scrollable-list::-webkit-scrollbar-thumb:hover { background: rgba(0, 0, 0, 0.25); }

/* --- Project List Items --- */
.project-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.project-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.1rem;
    background: rgba(255, 255, 255, 0.5);
    border: 1px solid rgba(255, 255, 255, 0.4);
    border-radius: 16px;
    transition: all 0.2s ease;
    text-decoration: none; 
    color: inherit;
}

.project-item:hover {
    background: #ffffff;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    transform: scale(1.01);
}

.p-name { font-weight: 600; font-size: 1.05rem; display: block; color: #1d1d1f; margin-bottom: 0.2rem; }
.p-loc { font-size: 0.85rem; color: #86868b; display: flex; align-items: center; gap: 4px; }
.progress-text { margin-top: 0.5rem; font-size: 0.85rem; font-weight: 700; color: #86868b; text-align: right; }

@media (max-width: 900px) {
    .widget-grid { 
        grid-template-columns: 1fr; 
    }
    .team-avatars-only { 
        flex-direction: row; 
        flex-wrap: wrap; 
        justify-content: flex-start; 
    }
}
</style>

<div class="dashboard-wrapper">
    
    <div class="dash-header">
        <h1 class="dash-greeting"><?php echo $greeting . ', ' . htmlspecialchars($userName); ?>!</h1>
        <p class="dash-subtitle">Here is what's happening with your projects today.</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon-wrapper icon-active"><i class="ph-fill ph-trend-up"></i></div>
            <div class="stat-info">
                <span class="stat-title">Active Projects</span>
                <span class="stat-value"><?php echo $stats['active']; ?></span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon-wrapper icon-completed"><i class="ph-fill ph-check-circle"></i></div>
            <div class="stat-info">
                <span class="stat-title">Finished Projects</span>
                <span class="stat-value"><?php echo $stats['completed']; ?></span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon-wrapper icon-unstarted"><i class="ph-fill ph-pause-circle"></i></div>
            <div class="stat-info">
                <span class="stat-title">Not Yet Started</span>
                <span class="stat-value"><?php echo $stats['unstarted']; ?></span>
            </div>
        </div>
    </div>

<div class="widget-grid">
        
        <div class="ios-card">
            <h2 class="card-title">Assigned Projects</h2>
            
            <div class="scrollable-list">
                <?php if (empty($myProjects)): ?>
                    <p style="color: #86868b; padding: 1rem 0;">No projects currently assigned to you.</p>
                <?php else: ?>
                    <div class="project-list">
<?php foreach ($myProjects as $p): ?>
    <a href="/src/modules/projects/project-controller.php?action=view&id=<?php echo $p['id']; ?>" class="project-item">
        <div>
            <span class="p-name"><?php echo htmlspecialchars($p['name']); ?></span>
            <span class="p-loc"><i class="ph-fill ph-map-pin"></i> <?php echo htmlspecialchars($p['project_location']); ?></span>
        </div>
        <div style="text-align: right; min-width: 120px;">
            <span class="badge progress <?php echo $p['status'] === 'completed' ? 'completed' : ($p['status'] === 'not yet started' ? '' : 'progress'); ?>">
                <?php echo htmlspecialchars(ucfirst($p['status'])); ?>
            </span>
            <div class="progress-text"><?php echo $p['progress_percentage']; ?>% Complete</div>
        </div>
    </a>
<?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="ios-card card-deadlines">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h2 class="card-title" style="margin-bottom: 0;">Recent Notifications</h2>
                
                <?php if (!empty($recentNotifications)): ?>
                    <button type="button" class="see-more-btn" onclick="clearAllNotifications()" style="width: auto; margin-top: 0; padding: 0.4rem 0.85rem; background-color: rgba(255, 59, 48, 0.08); color: var(--status-attention); border-radius: 12px; border: none; cursor: pointer; display: flex; align-items: center; gap: 4px;">
                        <i class="ph ph-trash"></i> Clear All
                    </button>
                <?php endif; ?>
        </div>
            
            <div class="scrollable-list">
                <?php if (empty($recentNotifications)): ?>
                    <div style="text-align: center; padding: 1.5rem 0; color: #86868b;">
                        <i class="ph ph-bell-slash" style="font-size: 2.5rem; margin-bottom: 0.5rem; opacity: 0.5;"></i>
                        <p style="font-size: 0.95rem;">You're all caught up!</p>
                    </div>
                <?php else: ?>
                    <div class="project-list">
                    <?php foreach ($recentNotifications as $notif): ?>
                            <div class="notification-wrapper" id="dash-notif-<?php echo $notif['id']; ?>" style="position: relative; transition: opacity 0.3s ease;">
                                
                                <a href="/src/modules/notifications/notification-controller.php?action=read&id=<?php echo $notif['id']; ?>&project_id=<?php echo $notif['project_id']; ?>"
                                   class="project-item" 
                                   style="text-decoration: none; display: flex; align-items: flex-start; gap: 12px; padding: 1rem; padding-right: 3rem;">
                                    
                                    <div class="avatar global-avatar-hover" data-user-id="<?php echo $notif['actor_id']; ?>" style="width: 36px; height: 36px; flex-shrink: 0; border-radius: 12px; margin-right: 0; cursor: pointer;">
                                        <?php if (!empty($notif['avatar_url'])): ?>
                                            <img src="<?php echo htmlspecialchars($notif['avatar_url']); ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover; border-radius: inherit;">
                                        <?php else: ?>
                                            <?php echo strtoupper(substr($notif['actor_first'] ?? 'U', 0, 1)); ?>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div style="flex: 1; min-width: 0;">
                                        <div style="font-size: 0.9rem; color: #1d1d1f; line-height: 1.3;">
                                            <strong><?php echo htmlspecialchars($notif['actor_first'] . ' ' . $notif['actor_last']); ?></strong> 
                                            <?= $notif['type'] === 'assignment' ? 'assigned you to' : 'mentioned you in' ?> 
                                            <strong><?php echo htmlspecialchars($notif['project_name']); ?></strong>
                                        </div>
                                        
                                        <div style="font-size: 0.85rem; color: #86868b; margin-top: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            "<?php echo htmlspecialchars($notif['message']); ?>"
                                        </div>
                                        
                                        <div style="font-size: 0.75rem; color: #86868b; margin-top: 6px; font-weight: 500;">
                                            <?php 
                                                $timeAgo = strtotime($notif['created_at']);
                                                $diff = time() - $timeAgo;
                                                if ($diff < 60) echo 'Just now';
                                                elseif ($diff < 3600) echo floor($diff / 60) . 'm ago';
                                                elseif ($diff < 86400) echo floor($diff / 3600) . 'h ago';
                                                else echo floor($diff / 86400) . 'd ago';
                                            ?>
                                        </div>
                                    </div>
                                    
                                    <?php if (!$notif['is_read']): ?>
                                        <div style="width: 8px; height: 8px; background: var(--primary, #0066cc); border-radius: 50%; margin-top: 4px; flex-shrink: 0;"></div>
                                    <?php endif; ?>
                                </a>

                                <button type="button" onclick="clearNotification('<?php echo $notif['id']; ?>')" 
                                        class="modal-close" 
                                        style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); width: 26px; height: 26px; z-index: 10;" 
                                        title="Clear Notification">
                                    <i class="ph ph-x" style="font-size: 0.85rem;"></i>
                                </button>
                                
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="ios-card card-team" style="align-items: center; padding-left: 0.5rem; padding-right: 0.5rem;">           
            <div class="scrollable-list" style="width: 100%; margin-right: 0; padding-right: 0;">
                <?php if (empty($activeUsers)): ?>
                    <p style="color: #86868b; font-size: 0.8rem; text-align: center;">None</p>
                <?php else: ?>
                    <div class="team-avatars-only">
                        <?php foreach ($activeUsers as $user): ?>
                            <div class="avatar global-avatar-hover" 
                                data-user-id="<?php echo $user['id']; ?>"
                                title="<?php echo htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) . ' - ' . htmlspecialchars($user['role'] ?? '') . ' (' . $user['status_text'] . ')'; ?>" 
                                style="position: relative; overflow: visible; width: 44px; height: 44px; margin-right: 0; cursor: pointer;">
                                
                                <?php if (!empty($user['avatar_url'])): ?>
                                    <img src="<?php echo htmlspecialchars($user['avatar_url']); ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover; border-radius: inherit;">
                                <?php else: ?>
                                    <?php echo strtoupper(substr($user['first_name'] ?? 'U', 0, 1)); ?>
                                <?php endif; ?>
                                
                                <span class="status-dot <?php echo $user['status_class']; ?>" style="position: absolute; bottom: -2px; right: -2px; width: 14px; height: 14px; border: 2px solid var(--surface-color);"></span>

                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
    </div>

<script>
// 1. Define how to draw Notifications
function renderNotificationsDOM(notifications) {
    const container = document.querySelector('.card-deadlines .scrollable-list');
    const clearAllBtn = document.querySelector('.card-deadlines .see-more-btn');
    if (!container) return;

    if (notifications.length === 0) {
        container.innerHTML = `
            <div style="text-align: center; padding: 1.5rem 0; color: var(--text-muted);">
                <i class="ph ph-bell-slash" style="font-size: 2.5rem; margin-bottom: 0.5rem; opacity: 0.5;"></i>
                <p style="font-size: 0.95rem;">You're all caught up!</p>
            </div>`;
        if (clearAllBtn) clearAllBtn.style.display = 'none';
        return;
    }

    if (clearAllBtn) clearAllBtn.style.display = 'flex';

    let html = '<div class="project-list">';
    notifications.forEach(notif => {
        const avatarHtml = notif.avatar_url 
            ? `<img src="${notif.avatar_url}" alt="Profile" style="width: 100%; height: 100%; object-fit: cover; border-radius: inherit;">`
            : (notif.actor_first ? notif.actor_first.charAt(0).toUpperCase() : 'U');
        
        const actionText = notif.type === 'assignment' ? 'assigned you to' : 'mentioned you in';
        const isUnread = notif.is_read == 0 || notif.is_read === false;
        const unreadDot = isUnread ? `<div style="width: 8px; height: 8px; background: var(--primary); border-radius: 50%; margin-top: 4px; flex-shrink: 0;"></div>` : '';

        html += `
        <div class="notification-wrapper" id="dash-notif-${notif.id}" style="position: relative; transition: opacity 0.3s ease;">
            <a href="/src/modules/notifications/notification-controller.php?action=read&id=${notif.id}&project_id=${notif.project_id}" class="project-item" style="text-decoration: none; display: flex; align-items: flex-start; gap: 12px; padding: 1rem; padding-right: 3rem;">
                
                <div class="avatar global-avatar-hover" data-user-id="${notif.actor_id}" style="width: 36px; height: 36px; flex-shrink: 0; border-radius: 12px; margin-right: 0;">
                    ${avatarHtml}
                </div>
                
                <div style="flex: 1; min-width: 0;">
                    <div style="font-size: 0.9rem; color: var(--text-main); line-height: 1.3;">
                        <strong>${notif.actor_first} ${notif.actor_last || ''}</strong> ${actionText} <strong>${notif.project_name}</strong>
                    </div>
                    <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        "${notif.message}"
                    </div>
                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 6px; font-weight: 500;">
                        ${notif.relative_time || 'Recently'}
                    </div>
                </div>
                ${unreadDot}
            </a>
            <button type="button" onclick="clearNotification('${notif.id}')" class="modal-close" style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); width: 26px; height: 26px; z-index: 10;">
                <i class="ph ph-x" style="font-size: 0.85rem;"></i>
            </button>
        </div>`;
    });
    html += '</div>';
    container.innerHTML = html;
}

// 2. Define how to draw Team Avatars
function renderActiveUsersDOM(users) {
    const container = document.querySelector('.card-team .scrollable-list');
    if (!container) return;

    if (!users || users.length === 0) {
        container.innerHTML = `<p style="color: #86868b; font-size: 0.8rem; text-align: center;">None</p>`;
        return;
    }

    let html = '<div class="team-avatars-only">';
    users.forEach(user => {
        const avatarContent = user.avatar_url 
            ? `<img src="${user.avatar_url}" alt="Profile" style="width: 100%; height: 100%; object-fit: cover; border-radius: inherit;">` 
            : (user.first_name ? user.first_name.charAt(0).toUpperCase() : 'U');
        
        // NEW: ADDED global-avatar-hover and data-user-id
        html += `
        <div class="avatar global-avatar-hover" 
            data-user-id="<?php echo $user['id']; ?>"
            title="<?php echo htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) . ' - ' . htmlspecialchars($user['role'] ?? '') . ' (' . $user['status_text'] . ')'; ?>" 
            style="position: relative; overflow: visible; width: 44px; height: 44px; margin-right: 0; cursor: pointer;">
            
            <?php if (!empty($user['avatar_url'])): ?>
                <img src="<?php echo htmlspecialchars($user['avatar_url']); ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover; border-radius: inherit;">
            <?php else: ?>
                <?php echo strtoupper(substr($user['first_name'] ?? 'U', 0, 1)); ?>
            <?php endif; ?>
            
            <span class="status-dot <?php echo $user['status_class']; ?>" style="position: absolute; bottom: -2px; right: -2px; width: 14px; height: 14px; border: 2px solid var(--surface-color);"></span>
        </div>`;
    });
    html += '</div>';
    container.innerHTML = html;
}

// ==========================================
// INITIALIZE THE ENGINE FOR THIS PAGE
// ==========================================
const dashboardEngine = new ReactiveEngine('/src/modules/dashboard/dashboard-controller.php?action=poll', 15000);

// Map the JSON keys from the backend to the UI functions
dashboardEngine.register('notifications', renderNotificationsDOM);
dashboardEngine.register('activeUsers', renderActiveUsersDOM);

dashboardEngine.register('notifications', window.processToastNotifications);

// Start polling
dashboardEngine.start();


// --- Action Handlers ---
function clearNotification(id) {
    const formData = new FormData();
    formData.append('action', 'clear');
    formData.append('id', id);

    fetch('/src/modules/notifications/notification-controller.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const el = document.getElementById('dash-notif-' + id);
            if (el) el.style.opacity = '0';
            // Instantly force the engine to grab fresh data!
            setTimeout(() => dashboardEngine.fetchNow(), 300); 
        }
    });
}

function clearAllNotifications() {
    if (!confirm("Are you sure you want to clear all your notifications?")) return;
    const formData = new FormData();
    formData.append('action', 'clear_all');

    fetch('/src/modules/notifications/notification-controller.php', { method: 'POST', body: formData })
    .then(res => res.json())
    // Instantly force the engine to grab fresh data!
    .then(data => { if (data.success) dashboardEngine.fetchNow(); });
}
</script>

<?php include __DIR__ . '/../../../core/views/footer.php'; ?>