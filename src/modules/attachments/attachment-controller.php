<?php
// /src/modules/attachments/attachment-controller.php
session_start();

require_once __DIR__ . '/attachment-repository.php';
require_once __DIR__ . '/attachment-service.php';

// Initialize the architecture
$repository = new AttachmentRepository();
$attachmentService = new AttachmentService($repository);

$action = $_GET['action'] ?? '';

// ==========================================
// ROUTE: UPLOAD TASK ATTACHMENT
// ==========================================
if ($action === 'upload_task_file' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Extract variables
    $currentUserId = $_SESSION['user_id'] ?? null; 
    $taskId        = $_POST['task_id'] ?? null;
    $projectId     = $_POST['project_id'] ?? null; 
    
    $customName    = trim($_POST['custom_name'] ?? '');
    $description   = trim($_POST['description'] ?? '');

    // Basic required data check
    if (!$taskId || !$projectId || !isset($_FILES['task_file'])) {
        die("Missing required data.");
    }

    try {
        // Pass everything to the service to handle the heavy lifting
        $attachmentService->uploadTaskFile(
            $taskId, 
            $projectId, 
            $currentUserId, 
            $_FILES['task_file'], 
            $customName, 
            $description
        );
        
        // Redirect back to the project details page on success
        header("Location: /src/modules/projects/project-controller.php?action=view&id=" . $projectId);
        exit;

    } catch (Exception $e) {
        // If the service throws an error (auth fail, file size, etc.), catch it here
        die($e->getMessage());
    }
}