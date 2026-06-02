<?php
// /src/modules/notifications/notification-controller.php
session_start();

// 1. Check authentication
if (!isset($_SESSION['user_id'])) {
    header("Location: /src/modules/auth/auth-controller.php?action=login");
    exit;
}

require_once __DIR__ . '/notification-service.php';

$notificationService = new NotificationService();
$loggedInUserId = $_SESSION['user_id']; 
$action = $_GET['action'] ?? 'list';

// --- NEW: Handle the Click/Redirect ---
if ($action === 'read') {
    $notifId = $_GET['id'] ?? null;
    $projectId = $_GET['project_id'] ?? null;

    if ($notifId) {
        $notificationService->markAsRead($notifId, $loggedInUserId);
    }

    // Instantly bounce them to the project details page
    if ($projectId) {
        header("Location: /src/modules/projects/project-controller.php?action=view&id=" . urlencode($projectId));
    } else {
        header("Location: /src/modules/dashboard/dashboard-controller.php");
    }
    exit;
}

// --- Default Action: Show the List Page ---
$notifications = $notificationService->getUserNotifications($loggedInUserId, 50);

function getRelativeTime($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;

    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . 'm ago';
    if ($diff < 86400) return floor($diff / 3600) . 'h ago';
    if ($diff < 604800) return floor($diff / 86400) . 'd ago';
    return date('M j, Y', $time);
}

$pageTitle = "Notifications | Vision CRM";
require_once __DIR__ . '/views/list.php';