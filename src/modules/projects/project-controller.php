<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

// /src/modules/projects/project-controller.php
require_once __DIR__ . '/project-repository.php';
require_once __DIR__ . '/project-service.php';
require_once __DIR__ . '/../users/user-repository.php';
require_once __DIR__ . '/../users/user-service.php'; 
require_once __DIR__ . '/../discussions/discussion-service.php';

// Implement Dependency Injection
$projectRepo = new ProjectRepository();
$projectService = new ProjectService($projectRepo);

// ==========================================
// ROUTE: API ENDPOINTS (POST REQUESTS)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? null;
    
    // Handle Delete Project
    if ($postAction === 'delete') {
        header('Content-Type: application/json'); 
        try {
            $projectService->deleteProject($_POST['project_id'] ?? null);
            echo json_encode(['success' => true]);
        } catch (Throwable $e) { 
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit; 
    }
    
    // Handle Update Project Status
    if ($postAction === 'update_project_status') {
        header('Content-Type: application/json');
        try {
            $projectService->updateProjectStatus($_POST['project_id'] ?? null, $_POST['status'] ?? null);
            echo json_encode(['success' => true]);
        } catch (Throwable $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
}

// ==========================================
// STANDARD GET ROUTING (VIEWS & API)
// ==========================================
$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'create':
        $userRepo = new UserRepository();
        $userService = new UserService($userRepo);
        $users = $userService->getAllUsers();
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $projectService->createNewProject($_POST, $_FILES['cover_photo'] ?? null, $_SESSION['user_id'] ?? null);
                header("Location: /src/modules/projects/project-controller.php?action=list");
                exit;
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }

        $pageTitle = "Create New Project | CRM";
        require_once __DIR__ . '/views/create.php';
        break;

    case 'edit':
        $projectId = $_GET['id'] ?? null;
        if (!$projectId) die("Error: Project ID is missing.");

        $userRepo = new UserRepository();
        $userService = new UserService($userRepo);
        $users = $userService->getAllUsers();
        
        $viewData = $projectService->getProjectViewDetails($projectId);
        if (!$viewData) die("Error: Project not found.");

        // Automatically unpacks $project, $teamMembers, $groupedTasks into variables
        extract($viewData); 
        $isEdit = true;
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $projectService->updateExistingProject($projectId, $_POST, $_FILES['cover_photo'] ?? null, $_SESSION['user_id'] ?? null);
                header("Location: /src/modules/projects/project-controller.php?action=view&id=" . $projectId);
                exit;
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }

        $pageTitle = "Edit Project | " . htmlspecialchars($project['name']);
        require_once __DIR__ . '/views/create.php';
        break;

    case 'view':
        $projectId = $_GET['id'] ?? null;
        if (!$projectId) die("Error: Project ID is missing.");

        $viewData = $projectService->getProjectViewDetails($projectId);
        if (!$viewData) die("Error: Project not found.");
        
        // Unpacks $project, $teamMembers, $groupedTasks, $statusBadgeClass, $statusText
        extract($viewData); 

        // Fetch Discussions
        $discussionService = new DiscussionService();
        $discussions = $discussionService->getProjectDiscussions($projectId);

        $pageTitle = "Project Details | " . htmlspecialchars($project['name']);
        require_once __DIR__ . '/views/details.php';
        break;

    case 'get_templates':
        header('Content-Type: application/json');
        try {
            echo json_encode($projectService->getGroupedTaskTemplates());
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        break;

    case 'list':
    default:
        // Grab the sort from the URL (defaulting to created_at)
        $sortBy = $_GET['sort'] ?? 'created_at';
        
        // Pass it into the service
        $projects = $projectService->getFormattedProjectsList($sortBy);
        
        $pageTitle = "CRM - Projects Overview";
        require_once __DIR__ . '/views/list.php';
        break;
}