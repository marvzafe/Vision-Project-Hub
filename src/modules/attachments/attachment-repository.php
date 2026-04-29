<?php
// /src/modules/attachments/attachment-repository.php
require_once __DIR__ . '/../../core/database.php';

class AttachmentRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // Verify if the user is allowed to upload to this task
    public function canUserUpload(string $taskId, string $userId): bool {
        $sql = "SELECT t.id 
                FROM tasks t
                LEFT JOIN project_team pt ON t.project_id = pt.project_id AND pt.user_id = :user_id
                LEFT JOIN projects p ON t.project_id = p.id
                WHERE t.id = :task_id 
                AND (
                    t.assignee_id = :user_id 
                    OR pt.user_id IS NOT NULL 
                    OR p.project_lead_id = :user_id
                )";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':task_id' => $taskId,
            ':user_id' => $userId
        ]);
        
        return $stmt->fetch() !== false;
    }

    // Save the attachment record to the database
    public function saveAttachment($taskId, $fileName, $fileUrl, $fileSize, $uploadedBy, $description = null): bool {
        $sql = "INSERT INTO task_attachments 
                (task_id, file_name, file_url, file_size, uploaded_by, description, uploaded_at) 
                VALUES 
                (:task_id, :file_name, :file_url, :file_size, :uploaded_by, :description, NOW())";
                
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':task_id'     => $taskId,
            ':file_name'   => $fileName,
            ':file_url'    => $fileUrl,
            ':file_size'   => $fileSize,
            ':uploaded_by' => $uploadedBy,
            ':description' => $description
        ]);
    }

    // Get all attachments for a specific task
    public function getAttachmentsByTask(string $taskId): array {
        $sql = "SELECT ta.*, u.first_name, u.last_name 
                FROM task_attachments ta
                LEFT JOIN users u ON ta.uploaded_by = u.user_id
                WHERE ta.task_id = :task_id
                ORDER BY ta.uploaded_at DESC";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':task_id' => $taskId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAttachmentsByTaskId($taskId) {
        if (empty($taskId)) return [];

        $sql = "SELECT a.*, u.first_name, u.last_name 
                FROM task_attachments a
                LEFT JOIN users u ON a.uploaded_by = u.user_id
                WHERE a.task_id::text = :task_id 
                AND a.is_cover_photo = FALSE
                ORDER BY a.uploaded_at DESC";
                
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':task_id' => (string)$taskId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Attachment fetch error: " . $e->getMessage());
            return [];
        }
    }
}