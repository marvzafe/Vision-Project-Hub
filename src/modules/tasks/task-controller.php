<?php
// /src/modules/tasks/task-controller.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/task-repository.php';
require_once __DIR__ . '/task-service.php';

// Tell the browser we are explicitly sending JSON back
header('Content-Type: application/json');

// Initialize the architecture
$repository = new TaskRepository();
$taskService = new TaskService($repository);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Check if we are deleting or creating
    $action = $_POST['action'] ?? 'create';

    // --- DELETE LOGIC ---
    if ($action === 'delete') {
        $taskId = $_POST['task_id'] ?? null;
        
        try {
            $taskService->deleteTask($taskId);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    // --- CREATE LOGIC ---
    if ($action === 'create') {
        $title       = $_POST['title'] ?? '';
        $category    = $_POST['task_category'] ?? '';
        $assigneeId  = $_POST['assignee_id'] ?? null;
        $description = $_POST['description'] ?? '';
        $deadline    = $_POST['deadline'] ?? null;

        try {
            // Capture the new UUID from the Service
            $newTaskId = $taskService->createTask($title, $category, $assigneeId, $description, $deadline);
            
            // Send the ID back to the Javascript!
            echo json_encode(['success' => true, 'task_id' => $newTaskId]);
            
        } catch (Exception $e) {
            // If the Service throws an error (like missing title) or the DB fails, it gets caught here
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
}