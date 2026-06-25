<?php
// /src/modules/discussions/discussion-repository.php
require_once __DIR__ . '/../../core/database.php';

class DiscussionRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // Fetch all discussions and join with user and task data
    public function getByProjectId($projectId) {
        $sql = "SELECT d.id, d.project_id, d.user_id, d.parent_id, d.content, d.flag_status, d.task_id, 
                       COALESCE(d.created_at, NOW()) as created_at, 
                       u.first_name, u.last_name, u.avatar_url,
                       t.title as task_title -- Fetching the attached task title
                FROM discussions d
                JOIN users u ON d.user_id = u.user_id
                LEFT JOIN tasks t ON d.task_id = t.id -- Join the tasks table
                WHERE d.project_id = :pid
                ORDER BY d.created_at ASC";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':pid' => $projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Insert a new comment with an optional task_id
    public function create($projectId, $userId, $content, $parentId = null, $taskId = null) {
        $sql = "INSERT INTO discussions (project_id, user_id, content, parent_id, flag_status, task_id)
                VALUES (:pid, :uid, :content, :parent_id, NULL, :task_id) 
                RETURNING id";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':pid'       => $projectId,
            ':uid'       => $userId,
            ':content'   => $content,
            ':parent_id' => $parentId,
            ':task_id'   => $taskId
        ]);
        
        return $stmt->fetchColumn();
    }

    // Update the Solved/Needs Attention flag
    public function updateFlag($discussionId, $status) {
        $sql = "UPDATE discussions SET flag_status = :status WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':status' => $status, ':id' => $discussionId]);
    }

    // Edit comment content (Security: Checks user_id)
    public function updateContent($discussionId, $userId, $content) {
        $sql = "UPDATE discussions SET content = :content WHERE id = :id AND user_id = :uid";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':content' => $content, 
            ':id' => $discussionId, 
            ':uid' => $userId
        ]);
        return $stmt->rowCount() > 0;
    }

    // Delete a comment (Security: Checks user_id)
    public function delete($discussionId, $userId) {
        $sql = "DELETE FROM discussions WHERE id = :id AND user_id = :uid";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id' => $discussionId, 
            ':uid' => $userId
        ]);
        return $stmt->rowCount() > 0;
    }
}