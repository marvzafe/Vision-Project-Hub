<?php
// /src/modules/tasks/task-repository.php
require_once __DIR__ . '/../../core/database.php';

class TaskRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function createTask($title, $category, $assigneeId, $description, $deadline) {
        // 'RETURNING id' at the end to get the UUID
        $sql = "INSERT INTO tasks 
                (title, task_category, assignee_id, description, deadline) 
                VALUES 
                (:title, :category, :assignee, :description, :deadline) 
                RETURNING id";
                
        $stmt = $this->db->prepare($sql);
        
        $stmt->execute([
            ':title'       => $title,
            ':category'    => $category,
            ':assignee'    => $assigneeId ?: null, 
            ':description' => $description,
            ':deadline'    => $deadline ?: null
        ]);
        
        // Fetch and return the newly generated UUID
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['id']; 
    }

    public function deleteTask($taskId): bool {
        $sql = "DELETE FROM tasks WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $taskId]);
    }

    public function updateTaskStatus($taskId, $status) {
        $sql = "UPDATE tasks SET status = :status, updated_at = NOW() WHERE id = :task_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':status' => $status,
            ':task_id' => $taskId
        ]);
    }

    public function recalculateProjectProgress($projectId) {
        // Find total tasks and completed tasks for the project
        $sql = "SELECT 
                    COUNT(*) as total_tasks, 
                    SUM(CASE WHEN status = 'done' THEN 1 ELSE 0 END) as completed_tasks 
                FROM tasks 
                WHERE project_id = :project_id";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':project_id' => $projectId]);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);

        $totalTasks = (int)$stats['total_tasks'];
        $completedTasks = (int)$stats['completed_tasks'];
        
        // Protect against Division by Zero
        $progressPercentage = 0;
        if ($totalTasks > 0) {
            $progressPercentage = round(($completedTasks / $totalTasks) * 100);
        }

        // Update the main project record
        $updateSql = "UPDATE projects 
                      SET progress_percentage = :progress, updated_at = NOW() 
                      WHERE id = :project_id";
        
        $updateStmt = $this->db->prepare($updateSql);
        $updateStmt->execute([
            ':progress' => $progressPercentage,
            ':project_id' => $projectId
        ]);
    }
}