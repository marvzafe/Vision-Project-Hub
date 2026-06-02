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
        <?php if (!empty($notifications)): ?>
            <div>
                <button type="button" class="see-more-btn" onclick="clearAllNotifications()" style="width: auto; margin-top: 0; padding: 0.4rem 0.85rem; background-color: rgba(255, 59, 48, 0.08); color: var(--status-attention); border-radius: 12px; border: none; cursor: pointer; display: flex; align-items: center; gap: 4px;">
                    <i class="ph ph-trash"></i> Clear All
                </button>
            </div>
        <?php endif; ?>
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
                    <div class="notification-wrapper" id="notif-<?= htmlspecialchars($notif['id']) ?>" style="position: relative; transition: opacity 0.3s ease;">
                        
                        <a href="/src/modules/notifications/notification-controller.php?action=read&id=<?= htmlspecialchars($notif['id']) ?>&project_id=<?= htmlspecialchars($notif['project_id']) ?>"
                           class="notification-item <?= $unreadClass ?>" style="padding-right: 3.5rem;">
                            
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

                        <button type="button" onclick="clearNotification('<?= htmlspecialchars($notif['id']) ?>')" 
                                class="modal-close" 
                                style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); width: 28px; height: 28px; z-index: 10;" 
                                title="Clear Notification">
                            <i class="ph ph-x" style="font-size: 0.9rem;"></i>
                        </button>

                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function clearNotification(id) {
    const formData = new FormData();
    formData.append('action', 'clear');
    formData.append('id', id);

    fetch('/src/modules/notifications/notification-controller.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const el = document.getElementById('notif-' + id);
            if (el) {
                // Smoothly fade it out
                el.style.opacity = '0';
                
                setTimeout(() => {
                    el.remove();
                    // If we cleared the last one on the screen, reload to show the "All Caught Up" empty state
                    if (document.querySelectorAll('.notification-wrapper').length === 0) {
                        location.reload(); 
                    }
                }, 300); 
            }
        } else {
            alert('Error clearing notification.');
        }
    })
    .catch(err => console.error(err));
}

function clearAllNotifications() {
    if (!confirm("Are you sure you want to clear all your notifications?")) return;

    const formData = new FormData();
    formData.append('action', 'clear_all');

    fetch('/src/modules/notifications/notification-controller.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload immediately to show the empty state
            location.reload();
        } else {
            alert('Error clearing notifications.');
        }
    })
    .catch(err => console.error(err));
}
</script>

<?php include __DIR__ . '/../../../core/views/footer.php'; ?>