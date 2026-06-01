<?php include __DIR__ . '/../../../core/views/header.php'; ?>

<style>
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
        height: calc(100vh - 9rem); 
        overflow: hidden; /* 2. Trap all inner content */
    }
    
    .widget-grid {
        flex: 1;
        min-height: 0; 
    }
    
    .ios-card {
        display: flex;
        flex-direction: column;
        height: 100%;
        min-height: 0; /* 3. Force cards to shrink instead of stretching */
    }
    
    .right-column {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        height: 100%;
        min-height: 0; 
    }

    .card-deadlines, 
    .card-team { 
        flex: 1; 
        min-height: 0; 
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

/* --- Main Widget Areas --- */
.widget-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
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
}

.card-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1d1d1f;
    margin-bottom: 1rem;
    flex-shrink: 0; /* Keeps title fixed at top */
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
    .widget-grid { grid-template-columns: 1fr; }
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
                            <div class="project-item">
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
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="right-column">
            
<div class="ios-card card-deadlines">
                <h2 class="card-title">Recent Notifications</h2>
                
                <div class="scrollable-list">
                    <?php if (empty($recentNotifications)): ?>
                        <div style="text-align: center; padding: 1.5rem 0; color: #86868b;">
                            <i class="ph ph-bell-slash" style="font-size: 2.5rem; margin-bottom: 0.5rem; opacity: 0.5;"></i>
                            <p style="font-size: 0.95rem;">You're all caught up!</p>
                        </div>
                    <?php else: ?>
                        <div class="project-list">
                            <?php foreach ($recentNotifications as $notif): ?>
                                <a href="/src/modules/projects/project-controller.php?action=view&id=<?php echo $notif['project_id']; ?>" 
                                   class="project-item" 
                                   style="text-decoration: none; display: flex; align-items: flex-start; gap: 12px; padding: 1rem;">
                                    
                                    <div class="avatar" style="width: 36px; height: 36px; flex-shrink: 0; border-radius: 12px;">
                                        <?php if (!empty($notif['avatar_url'])): ?>
                                            <img src="<?php echo htmlspecialchars($notif['avatar_url']); ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover; border-radius: inherit;">
                                        <?php else: ?>
                                            <?php echo strtoupper(substr($notif['actor_first'] ?? 'U', 0, 1)); ?>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div style="flex: 1; min-width: 0;">
                                        <div style="font-size: 0.9rem; color: #1d1d1f; line-height: 1.3;">
                                            <strong><?php echo htmlspecialchars($notif['actor_first'] . ' ' . $notif['actor_last']); ?></strong> 
                                            mentioned you in 
                                            <strong><?php echo htmlspecialchars($notif['project_name']); ?></strong>
                                        </div>
                                        
                                        <div style="font-size: 0.85rem; color: #86868b; margin-top: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            "<?php echo htmlspecialchars($notif['message']); ?>"
                                        </div>
                                        
                                        <div style="font-size: 0.75rem; color: #86868b; margin-top: 6px; font-weight: 500;">
                                            <?php 
                                                // Simple time-ago format
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
                                        <div style="width: 8px; height: 8px; background: var(--primary, #0066cc); border-radius: 50%; margin-top: 4px;"></div>
                                    <?php endif; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="ios-card card-team">
                <h2 class="card-title">Team Online</h2>
                
                <div class="scrollable-list">
                    <?php if (empty($activeUsers)): ?>
                        <p style="color: #86868b; font-size: 0.95rem;">No team members currently online.</p>
                    <?php else: ?>
                        <ul class="people-list">
                            <?php foreach ($activeUsers as $user): ?>
                                <li class="person" style="padding: 0.5rem 0;">
                                    <div class="avatar" style="position: relative; overflow: visible;">
                                        <?php if (!empty($user['avatar_url'])): ?>
                                            <img src="<?php echo htmlspecialchars($user['avatar_url']); ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover; border-radius: inherit;">
                                        <?php else: ?>
                                            <?php echo strtoupper(substr($user['first_name'] ?? 'U', 0, 1)); ?>
                                        <?php endif; ?>
                                        <span class="status-dot active" style="position: absolute; bottom: -2px; right: -2px; width: 12px; height: 12px; border: 2px solid var(--surface-color);"></span>
                                    </div>
                                    <div class="person-info">
                                        <h4><?php echo htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')); ?></h4>
                                        <p><?php echo htmlspecialchars($user['role'] ?? 'Member'); ?></p>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
            
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../../core/views/footer.php'; ?>