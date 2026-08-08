// /public/assets/js/toast-notifications.js

let seenNotificationIds = new Set();
let isFirstLoad = true;

// 1. Process incoming data from the ReactiveEngine
window.processToastNotifications = function(notifications) {
    if (!notifications || notifications.length === 0) {
        isFirstLoad = false; // Turn off first load even if empty
        return;
    }

    // On the very first page load, just memorize the IDs so we don't spam 5 popups at once
    if (isFirstLoad) {
        notifications.forEach(n => seenNotificationIds.add(n.id));
        isFirstLoad = false;
        return;
    }

    // On subsequent polls, check for NEW ids
    notifications.forEach(n => {
        if (!seenNotificationIds.has(n.id)) {
            seenNotificationIds.add(n.id);
            spawnToast(n);
        }
    });
};

// 2. Build and animate the popup
function spawnToast(notif) {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = 'toast-popup';
    
    // Reusing your exact dashboard avatar and text logic
    const avatarHtml = notif.avatar_url 
        ? `<img src="${notif.avatar_url}" alt="Profile" style="width: 100%; height: 100%; object-fit: cover; border-radius: inherit;">`
        : (notif.actor_first ? notif.actor_first.charAt(0).toUpperCase() : 'U');
    
    const actionText = notif.type === 'assignment' ? 'assigned you to' : 'mentioned you in';

    toast.innerHTML = `
        <div class="avatar" style="width: 36px; height: 36px; flex-shrink: 0; border-radius: 12px; margin-right: 0;">
            ${avatarHtml}
        </div>
        <div style="flex: 1; min-width: 0; margin-top: 2px;">
            <div style="font-size: 0.9rem; color: var(--text-main); line-height: 1.3;">
                <strong>${notif.actor_first}</strong> ${actionText} <strong>${notif.project_name}</strong>
            </div>
            <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                "${notif.message}"
            </div>
        </div>
        <button class="modal-close" title="Dismiss">
            <i class="ph ph-x" style="font-size: 0.85rem;"></i>
        </button>
    `;

    container.appendChild(toast);

    // Trigger slide-in animation
    requestAnimationFrame(() => {
        toast.classList.add('show');
    });

    // Handle Manual Dismissal (The 'X' button)
    const closeBtn = toast.querySelector('.modal-close');
    closeBtn.addEventListener('click', () => dismissToast(toast));

    // Handle Auto Fade Out (5 seconds)
    setTimeout(() => {
        dismissToast(toast);
    }, 5000);
}

// 3. Smooth exit animation
function dismissToast(toastElement) {
    if (!toastElement.parentElement) return; // Prevent errors if already closed manually
    toastElement.classList.remove('show');
    
    // Wait for the CSS transition to finish before removing from DOM
    setTimeout(() => {
        if (toastElement.parentElement) {
            toastElement.remove();
        }
    }, 400); 
}

// 4. Initialize the container and standalone poller
document.addEventListener('DOMContentLoaded', () => {
    // Inject the container into the body
    const container = document.createElement('div');
    container.id = 'toast-container';
    document.body.appendChild(container);

    // ALWAYS run the notification poller globally, regardless of the page!
    // Notice we attach it directly to "window" so the dashboard can hook into it
    window.globalNotifEngine = new window.ReactiveEngine('/src/modules/notifications/notification-controller.php?action=get_json', 15000);
    window.globalNotifEngine.register('notifications', window.processToastNotifications);
    
    // NEW: Fetch immediately! This registers the initial notifications at 0s so the 15s poll actually catches NEW ones.
    window.globalNotifEngine.fetchNow();
    window.globalNotifEngine.start();
});