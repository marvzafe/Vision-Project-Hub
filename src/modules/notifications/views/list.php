<?php include __DIR__ . '/../../../core/views/header.php'; ?>

<style>
/* Notification specific styles extending global.css */
.notification-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.notification-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1.25rem;
    background: rgba(255, 255, 255, 0.5);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    transition: all 0.2s ease;
    text-decoration: none;
    color: inherit;
    position: relative;
}

.notification-item:hover {
    background: #ffffff;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    transform: translateY(-1px) scale(1.005);
}

.notification-item.unread {
    background: rgba(255, 255, 255, 0.85);
    border-color: rgba(0, 102, 204, 0.2); /* Soft blue border for unread */
}

.notif-content {
    flex: 1;
    min-width: 0;
}

.notif-text {
    font-size: 0.95rem;
    color: var(--text-main);
    line-height: 1.4;
    margin-bottom: 0.25rem;
}

.notif-message-preview {
    font-size: 0.85rem;
    color: var(--text-muted);
    font-style: italic;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 0.5rem;
}

.notif-meta {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    gap: 8px;
}

.unread-indicator {
    width: 10px;
    height: 10px;
    background-color: var(--primary);
    border-radius: 50%;
    flex-shrink: 0;
    margin-top: 6px;
    box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.15);
}

.empty-state {
    text-align: center;
    padding: 4rem 1rem;
    color: var(--text-muted);
}
</style>

<div class="container">
    <header class="header">
        <div>
            <h1 class="title">Notifications</h1>
            <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 0.25rem;">Stay updated on your projects and mentions.</p>
        </div>
    </header>

    <div class="card">
        <div class="notification-list">
            <?php if (empty($notifications)): ?>
                <div class="empty-state">
                    <i class="ph ph-bell-slash" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                    <h3>All Caught Up!</h3>
                    <p>You don't have any new notifications at the moment.</p>
                </div>
            <?php else: ?>
                <?php foreach ($notifications as $notif): 
                    $isUnread = !$notif['is_read'];
                    $unreadClass = $isUnread ? 'unread' : '';
                ?>
                    <a href="/src/modules/notifications/notification-controller.php?action=read&id=<?= htmlspecialchars($notif['id']) ?>&project_id=<?= htmlspecialchars($notif['project_id']) ?>"
                       class="notification-item <?= $unreadClass ?>">
                        
                        <div class="avatar" style="width: 48px; height: 48px; flex-shrink: 0; border-radius: 14px;">
                            <?php if (!empty($notif['avatar_url'])): ?>
                                <img src="<?= htmlspecialchars($notif['avatar_url']) ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover; border-radius: inherit;">
                            <?php else: ?>
                                <?= strtoupper(substr($notif['actor_first'] ?? 'U', 0, 1)) ?>
                            <?php endif; ?>
                        </div>

                        <div class="notif-content">
                            <div class="notif-text">
                                <strong><?= htmlspecialchars($notif['actor_first'] . ' ' . $notif['actor_last']) ?></strong> 
                                <?= $notif['type'] === 'assignment' ? 'assigned you to' : 'mentioned you in' ?>
                                <strong><?= htmlspecialchars($notif['project_name']) ?></strong>
                            </div>
                            
                            <div class="notif-message-preview">
                                "<?= htmlspecialchars($notif['message']) ?>"
                            </div>
                            
                            <div class="notif-meta">
                                <i class="ph ph-clock"></i>
                                <?= getRelativeTime($notif['created_at']) ?>
                            </div>
                        </div>

                        <?php if ($isUnread): ?>
                            <div class="unread-indicator"></div>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../../core/views/footer.php'; ?>