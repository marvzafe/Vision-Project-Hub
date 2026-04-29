<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
// /src/modules/projects/project-controller.php
require_once __DIR__ . '/project-service.php';
require_once __DIR__ . '/../users/user-service.php'; // Kept as-is based on your current setup
require_once __DIR__ . '/../users/user-repository.php';

session_start();

$action = $_GET['action'] ?? 'list';
$projectService = new ProjectService();

// ==========================================
// ROUTE 0: SEARCH ENDPOINT
// ==========================================
if ($action === 'search') {
    require_once __DIR__ . '/../search/search-service.php';
    require_once __DIR__ . '/../search/search-repository.php';
    
    $searchRepo = new SearchRepository();
    $searchService = new SearchService($searchRepo);
    $query = $_GET['q'] ?? '';
    
    header('Content-Type: application/json');
    try {
        $results = $searchService->globalSearch('projects', $query);
        echo json_encode($results);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// ==========================================
// ROUTE 1: THE CREATE PAGE
// ==========================================
if ($action === 'create') {
    // Instantiate the repository and service
    $userRepo = new UserRepository();
    $userService = new UserService($userRepo);
    
    // Call the method on the instance, not statically
    $users = $userService->getAllUsers();
    
    $error = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $currentUserId = $_SESSION['user_id'] ?? null;
            $coverPhoto = isset($_FILES['cover_photo']) ? $_FILES['cover_photo'] : null;
            
            $projectService->createNewProject($_POST, $coverPhoto, $currentUserId);
            
            header("Location: /src/modules/projects/project-controller.php?action=list");
            exit;
        } catch (Exception $e) {
            // Catch validation errors from the Service or DB errors from the Repo
            $error = $e->getMessage();
        }
    }

    $pageTitle = "Create New Project | CRM";
    require_once __DIR__ . '/views/create.php';
    exit;
}

// ==========================================
// ROUTE 2: THE LIST PAGE (Default)
// ==========================================
if ($action === 'list') {
    $projects = $projectService->getFormattedProjectsList();
    
    $pageTitle = "CRM - Projects Overview";
    require_once __DIR__ . '/views/list.php';
    exit;
}

// ==========================================
// ROUTE 3: THE VIEW DETAILS PAGE
// ==========================================
if ($action === 'view') {
    $projectId = $_GET['id'] ?? null;
    if (!$projectId) die("Error: Project ID is missing.");

    $viewData = $projectService->getProjectViewDetails($projectId);
    if (!$viewData) die("Error: Project not found.");

    // Extract variables so the existing views don't break
    $project          = $viewData['project'];
    $teamMembers      = $viewData['teamMembers'];
    $groupedTasks     = $viewData['groupedTasks'];
    $statusBadgeClass = $viewData['statusBadgeClass'];
    $statusText       = $viewData['statusText'];

    $pageTitle = "Project Details | " . htmlspecialchars($project['name']);
    
    require_once __DIR__ . '/views/details.php';
    exit;
}