<?php
// /src/modules/notifications/notification-repository.php
require_once __DIR__ . '/../../core/database.php';

class NotificationRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function createNotification($userId, $actorId, $projectId, $discussionId, $type, $message) {
        $sql = "INSERT INTO notifications (user_id, actor_id, project_id, discussion_id, type, message) 
                VALUES (:user_id, :actor_id, :project_id, :discussion_id, :type, :message)";
        $stmt = $this->db->prepare($sql);
        
        $success = $stmt->execute([
            ':user_id' => $userId,
            ':actor_id' => $actorId,
            ':project_id' => $projectId,
            ':discussion_id' => $discussionId, // It is perfectly fine for this to be null
            ':type' => $type,
            ':message' => $message
        ]);

        // FORCE PHP TO TELL US IF THE SQL FAILS:
        if (!$success) {
            $errorInfo = $stmt->errorInfo();
            throw new Exception("Notification SQL Error: " . $errorInfo[2]);
        }

        return $success;
    }

    // Looks up user IDs based on the @Name tags parsed from the comment
    public function findUsersByMentionTags(array $tags) {
        if (empty($tags)) return [];
        
        // Lowercase the tags for case-insensitive matching
        $placeholders = implode(',', array_fill(0, count($tags), '?'));
        
        // Matches the frontend logic where first and last names are concatenated without spaces
        $sql = "SELECT user_id 
                FROM users 
                WHERE LOWER(REPLACE(first_name || last_name, ' ', '')) IN ($placeholders)";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute($tags);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserNotifications($userId, $limit = 5) {
        $sql = "SELECT n.*, 
                       u.first_name as actor_first, u.last_name as actor_last, u.avatar_url,
                       p.name as project_name
                FROM notifications n
                JOIN users u ON n.actor_id = u.user_id
                JOIN projects p ON n.project_id = p.id
                WHERE n.user_id = :uid
                ORDER BY n.created_at DESC
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':uid', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markAsRead($notificationId, $userId) {
        $sql = "UPDATE notifications SET is_read = TRUE WHERE id = :id AND user_id = :uid";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $notificationId,
            ':uid' => $userId
        ]);
    }

    public function deleteNotification($notificationId, $userId) {
        $sql = "DELETE FROM notifications WHERE id = :id AND user_id = :uid";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $notificationId,
            ':uid' => $userId
        ]);
    }

    public function deleteAllUserNotifications($userId) {
        $sql = "DELETE FROM notifications WHERE user_id = :uid";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':uid' => $userId]);
    }
}