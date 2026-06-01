<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
// /src/modules/projects/project-controller.php
require_once __DIR__ . '/project-service.php';
require_once __DIR__ . '/../users/user-service.php'; 
require_once __DIR__ . '/../users/user-repository.php';

session_start();

$projectService = new ProjectService();

// ==========================================
// ROUTE: API ENDPOINTS (POST REQUESTS)
// This MUST come before any $_GET action routing!
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? null;
    
    // Handle Delete Project
    if ($postAction === 'delete') {
        header('Content-Type: application/json'); 
        $projectId = $_POST['project_id'] ?? null;
        
        try {
            $projectService->deleteProject($projectId);
            echo json_encode(['success' => true]);
        } catch (Throwable $e) { 
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit; // Crucial: This stops the HTML below from rendering!
    }
}

// ==========================================
// STANDARD GET ROUTING
// ==========================================
$action = $_GET['action'] ?? 'list';

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
// ROUTE 1.5: THE EDIT PAGE
// ==========================================
if ($action === 'edit') {
    $projectId = $_GET['id'] ?? null;
    if (!$projectId) die("Error: Project ID is missing.");

    $userRepo = new UserRepository();
    $userService = new UserService($userRepo);
    $users = $userService->getAllUsers();
    
    $viewData = $projectService->getProjectViewDetails($projectId);
    if (!$viewData) die("Error: Project not found.");

    // Extract variables to prepopulate the form
    $project      = $viewData['project'];
    $teamMembers  = $viewData['teamMembers'];
    $groupedTasks = $viewData['groupedTasks'];
    $isEdit       = true;
    $error        = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $currentUserId = $_SESSION['user_id'] ?? null;
            $coverPhoto = isset($_FILES['cover_photo']) ? $_FILES['cover_photo'] : null;
            
            // Call the new update method
            $projectService->updateExistingProject($projectId, $_POST, $coverPhoto, $currentUserId);
            
            header("Location: /src/modules/projects/project-controller.php?action=view&id=" . $projectId);
            exit;
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }

    $pageTitle = "Edit Project | " . htmlspecialchars($project['name']);
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

// ==========================================
// ROUTE 4: GET TEMPLATES (API ENDPOINT)
// ==========================================
if ($action === 'get_templates') {
    header('Content-Type: application/json');
    try {
        $templates = $projectService->getGroupedTaskTemplates();
        echo json_encode($templates);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// ==========================================
// ROUTE: API ENDPOINTS (POST REQUESTS)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? null;
    
    // Handle Delete Project
    if ($postAction === 'delete') {
        header('Content-Type: application/json'); 
        $projectId = $_POST['project_id'] ?? null;
        
        try {
            $projectService->deleteProject($projectId);
            echo json_encode(['success' => true]);
        } catch (Throwable $e) { // <-- Changed to Throwable to catch fatal PHP errors
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
}