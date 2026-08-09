<?php
// /src/modules/dashboard/dashboard-controller.php
session_start();
require_once __DIR__ . '/../discussions/discussion-service.php';

$service = new DiscussionService();
$userId = $_SESSION['user_id'] ?? null;

// Gated behind the 'get_discussions' action so it doesn't break normal page loads
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_discussions') {
    header('Content-Type: application/json');
    $projectId = $_GET['project_id'] ?? null;
    
    if (!$projectId) {
         echo json_encode(['success' => false, 'message' => 'Project ID required.']);
         exit;
    }
    
    try {
        $discussions = $service->getProjectDiscussions($projectId);
        echo json_encode(['success' => true, 'data' => $discussions]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
} else {
    $_SESSION['phone_verified'] = true;
}

require_once __DIR__ . '/dashboard-repository.php';
require_once __DIR__ . '/dashboard-service.php';
require_once __DIR__ . '/../notifications/notification-service.php'; // NEW
require_once __DIR__ . '/../users/user-service.php';    // NEW

// Instantiate the User Service
$userRepository = new UserRepository();
$userService = new UserService($userRepository);

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

// Fetch active users directly from the User domain
$activeUsers = $userService->getActiveUsers(20);

// NEW: Unified Polling Endpoint
if (isset($_GET['action']) && $_GET['action'] === 'poll') {
    header('Content-Type: application/json');
    echo json_encode([
        'success'       => true,
        'notifications' => $notificationService->getUserNotifications($loggedInUserId),
        'activeUsers'   => $userService->getActiveUsers(20)
    ]);
    exit; // Stop executing so we don't render the HTML view
}

$pageTitle = "Dashboard | Vision CRM";
require_once __DIR__ . '/views/index.php';