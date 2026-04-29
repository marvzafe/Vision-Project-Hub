<?php
// /src/modules/dashboard/dashboard-controller.php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /src/modules/auth/auth-controller.php?action=login");
    exit;
}

require_once __DIR__ . '/dashboard-repository.php';
require_once __DIR__ . '/dashboard-service.php';

$repository = new DashboardRepository();
$dashboardService = new DashboardService($repository);

$greeting = $dashboardService->getTimeBasedGreeting();
$userName = $dashboardService->getUserFirstName($_SESSION);
$loggedInUserId = $_SESSION['user_id']; 

$stats = $dashboardService->getProjectStats();
$myProjects = $dashboardService->getMyAssignedProjects($loggedInUserId);

// --- NEW VARIABLE ---
$activeUsers = $dashboardService->getActiveUsers();

// Add this right under where you define $activeUsers
$upcomingDeadlines = $dashboardService->getUpcomingDeadlines($loggedInUserId);

$pageTitle = "Dashboard | Vision CRM";
require_once __DIR__ . '/views/index.php';