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
}