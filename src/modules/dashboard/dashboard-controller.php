<?php
// /src/modules/dashboard/dashboard-controller.php
session_start();

// 1. Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /src/modules/auth/auth-controller.php?action=login");
    exit;
}

// 2. GATEKEEPER: Check for Phone Number
// We use a session variable so we only query the database once per login session
if (!isset($_SESSION['phone_verified']) || $_SESSION['phone_verified'] !== true) {
    
    // Require the repository we made earlier
    require_once __DIR__ . '/../contact-verification/contact-repository.php';
    $contactRepo = new ContactVerificationRepository();
    
    // Check if the phone field is empty
    if (empty($contactRepo->getUserPhone($_SESSION['user_id']))) {
        // Redirect to the form if empty
        // Note: Make sure the file name matches exactly (contact_controller.php vs contact-controller.php)
        header("Location: /../../../src/modules/contact-verification/contact-controller.php");
        exit;
    } else {
        // If they already have a phone number, remember it for this session!
        $_SESSION['phone_verified'] = true;
    }
}

// 3. Proceed with loading the dashboard as normal
require_once __DIR__ . '/dashboard-repository.php';
require_once __DIR__ . '/dashboard-service.php';

$repository = new DashboardRepository();
$dashboardService = new DashboardService($repository);

$greeting = $dashboardService->getTimeBasedGreeting();
$userName = $dashboardService->getUserFirstName($_SESSION);
$loggedInUserId = $_SESSION['user_id']; 

$stats = $dashboardService->getProjectStats();
$myProjects = $dashboardService->getMyAssignedProjects($loggedInUserId);

$activeUsers = $dashboardService->getActiveUsers();
$upcomingDeadlines = $dashboardService->getUpcomingDeadlines($loggedInUserId);

$pageTitle = "Dashboard | Vision CRM";
require_once __DIR__ . '/views/index.php';