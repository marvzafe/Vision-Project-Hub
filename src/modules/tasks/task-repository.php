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


    public function updateTaskQuantities($taskId, $quantity, $installed) {
        // We no longer need to recalculate in PHP because the SQL Trigger handles it!
        $sql = "UPDATE tasks 
                SET quantity = :quantity, 
                    installed = :installed, 
                    updated_at = NOW() 
                WHERE id = :task_id";
                
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':quantity'  => (float)$quantity,
            ':installed' => (float)$installed,
            ':task_id'   => $taskId
        ]);
    }
}