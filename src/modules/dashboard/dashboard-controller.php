<?php
// /src/modules/dashboard/dashboard-controller.php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /src/modules/auth/auth-controller.php?action=login");
    exit;
}

if (!isset($_SESSION['phone_verified']) || $_SESSION['phone_verified'] !== true) {
    require_once __DIR__ . '/../contact-verification/contact-repository.php';
    $contactRepo = new ContactVerificationRepository();
    
    if (empty($contactRepo->getUserPhone($_SESSION['user_id']))) {
        header("Location: /../../../src/modules/contact-verification/contact-controller.php");
        exit;
    } else {
        $_SESSION['phone_verified'] = true;
    }
}

require_once __DIR__ . '/dashboard-repository.php';
require_once __DIR__ . '/dashboard-service.php';
require_once __DIR__ . '/../notifications/notification-service.php'; // NEW

$repository = new DashboardRepository();
$dashboardService = new DashboardService($repository);
$notificationService = new NotificationService(); // NEW

$greeting = $dashboardService->getTimeBasedGreeting();
$userName = $dashboardService->getUserFirstName($_SESSION);
$loggedInUserId = $_SESSION['user_id']; 

$stats = $dashboardService->getProjectStats();
$myProjects = $dashboardService->getMyAssignedProjects($loggedInUserId);
$activeUsers = $dashboardService->getActiveUsers();

// NEW: Fetch notifications instead of deadlines
$recentNotifications = $notificationService->getUserNotifications($loggedInUserId);

$pageTitle = "Dashboard | Vision CRM";
require_once __DIR__ . '/views/index.php';