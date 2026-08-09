<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// /src/modules/discussions/discussion-controller.php
session_start();
require_once __DIR__ . '/discussion-service.php';

$service = new DiscussionService();

// ==========================================
// 1. GET HANDLER FOR REACT (Initial Load)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
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
}

// ==========================================
// 2. POST HANDLER FOR REACT (Adding/Editing/Deleting)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $action = $_POST['action'] ?? null;
    $userId = $_SESSION['user_id'] ?? null;
    
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized: Please log in.']);
        exit;
    }

    try {
        if ($action === 'add') {
            $projectId = $_POST['project_id'] ?? null;
            $content   = $_POST['content'] ?? '';
            $parentId  = !empty($_POST['parent_id']) ? $_POST['parent_id'] : null;
            $taskId    = !empty($_POST['task_id']) ? $_POST['task_id'] : null;
            
            $newId = $service->addComment($projectId, $userId, $content, $parentId, $taskId);
            echo json_encode(['success' => true, 'id' => $newId]);
            
        } elseif ($action === 'flag') {
            $discussionId = $_POST['discussion_id'] ?? null;
            $status       = !empty($_POST['status']) ? $_POST['status'] : null;
            
            $service->updateFlag($discussionId, $status);
            echo json_encode(['success' => true]);
            
        } elseif ($action === 'edit') {
            $discussionId = $_POST['discussion_id'] ?? null;
            $content      = $_POST['content'] ?? '';
            
            $service->editComment($discussionId, $userId, $content);
            echo json_encode(['success' => true]);
            
        } elseif ($action === 'delete') {
            $discussionId = $_POST['discussion_id'] ?? null;
            
            $service->deleteComment($discussionId, $userId);
            echo json_encode(['success' => true]);
            
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid action.']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}