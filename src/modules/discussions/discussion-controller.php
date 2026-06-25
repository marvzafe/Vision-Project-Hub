<?php
// /src/modules/discussions/discussion-controller.php
session_start();
require_once __DIR__ . '/discussion-service.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $service = new DiscussionService();
    
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
            $taskId    = !empty($_POST['task_id']) ? $_POST['task_id'] : null; // Capture task ID
            
            // Pass $taskId into the service
            $newId = $service->addComment($projectId, $userId, $content, $parentId, $taskId);
            echo json_encode(['success' => true, 'id' => $newId]);
            
        } elseif ($action === 'flag') {
            $discussionId = $_POST['discussion_id'] ?? null;
            $status       = !empty($_POST['status']) ? $_POST['status'] : null;
            
            $service->updateFlag($discussionId, $status);
            echo json_encode(['success' => true]);
            
        } elseif ($action === 'edit') { // NEW
            $discussionId = $_POST['discussion_id'] ?? null;
            $content      = $_POST['content'] ?? '';
            
            $service->editComment($discussionId, $userId, $content);
            echo json_encode(['success' => true]);
            
        } elseif ($action === 'delete') { // NEW
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