<?php
// /src/modules/files/FileRepository.php
require_once __DIR__ . '/../../core/database.php';

class FileRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getProjectFolders(): array {
        $sql = "SELECT p.id, p.name, p.created_at, 
                       (SELECT COUNT(*) FROM task_attachments WHERE project_id = p.id OR task_id IN (SELECT id FROM tasks WHERE project_id = p.id)) as file_count 
                FROM projects p
                ORDER BY p.name ASC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProjectById($projectId) {
        $sql = "SELECT id, name FROM projects WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $projectId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTaskFoldersByProject($projectId): array {
            $sql = "SELECT id, title, task_category, 
                        (SELECT COUNT(*) FROM task_attachments WHERE task_id = tasks.id) as file_count 
                    FROM tasks 
                    WHERE project_id = :project_id 
                    ORDER BY title ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':project_id' => $projectId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

    public function getProjectRootFiles($projectId): array {
        $sql = "SELECT id, file_name, file_url, file_size, uploaded_at 
                FROM task_attachments 
                WHERE project_id = :project_id AND task_id IS NULL
                ORDER BY uploaded_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':project_id' => $projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTaskById($taskId) {
        $sql = "SELECT id, title FROM tasks WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $taskId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getFilesByTask($taskId): array {
        $sql = "SELECT id, file_name, file_url, file_size, uploaded_at 
                FROM task_attachments 
                WHERE task_id = :task_id 
                ORDER BY uploaded_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':task_id' => $taskId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}