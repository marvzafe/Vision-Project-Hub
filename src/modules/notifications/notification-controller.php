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

// Fetch up to 50 recent notifications for the dedicated page
$notifications = $notificationService->getUserNotifications($loggedInUserId, 50);

// Helper function for time formatting
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