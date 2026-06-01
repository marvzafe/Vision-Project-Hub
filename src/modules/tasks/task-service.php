<?php
// /src/modules/tasks/task-service.php
require_once __DIR__ . '/task-repository.php';

class TaskService {
    private TaskRepository $repository;

    public function __construct(TaskRepository $repository) {
        $this->repository = $repository;
    }

    public function createTask($title, $category, $assigneeId, $description, $deadline) {
        // Business Logic Validation
        if (empty($title) || empty($category)) {
            throw new Exception("Title and Category are required.");
        }

        return $this->repository->createTask($title, $category, $assigneeId, $description, $deadline);
    }

    public function deleteTask($taskId) {
        // Business Logic Validation
        if (empty($taskId)) {
            throw new Exception("Task ID required for deletion.");
        }

        return $this->repository->deleteTask($taskId);
    }

    public function updateTaskStatus($taskId, $projectId, $status) {
        $allowedStatuses = ['not yet started', 'processing', 'done'];
        
        if (empty($taskId) || empty($projectId) || empty($status)) {
            throw new Exception("Missing required data to update task.");
        }

        if (!in_array($status, $allowedStatuses)) {
            throw new Exception("Invalid status value provided.");
        }

        // 1. Update the specific task's status
        $this->repository->updateTaskStatus($taskId, $status);

        // 2. Trigger the project progress recalculation
        $this->repository->recalculateProjectProgress($projectId);
    }
}