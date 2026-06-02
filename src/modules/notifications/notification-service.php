<?php
// /src/modules/notifications/notification-service.php
require_once __DIR__ . '/notification-repository.php';

class NotificationService {
    private NotificationRepository $repo;

    public function __construct() {
        $this->repo = new NotificationRepository();
    }

    public function getUserNotifications($userId, $limit = 5) {
        return $this->repo->getUserNotifications($userId, $limit);
    }

    public function processMentions($content, $projectId, $actorId, $discussionId) {
        // Find all words starting with @ (e.g., @MarvJohnoelZafe)
        preg_match_all('/@([A-Za-z0-9_]+)/', $content, $matches);
        $tags = array_unique($matches[1]);
        
        if (empty($tags)) return;

        // Convert tags to lowercase to match the DB safely
        $lowerTags = array_map('strtolower', $tags);
        
        // Find all matching users
        $users = $this->repo->findUsersByMentionTags($lowerTags);
        
        foreach ($users as $user) {
            // Prevent users from notifying themselves
            if ($user['user_id'] != $actorId) {
                // Save a short preview of the message
                $preview = mb_strimwidth($content, 0, 80, "...");
                $this->repo->createNotification(
                    $user['user_id'], 
                    $actorId, 
                    $projectId, 
                    $discussionId, 
                    'mention', 
                    $preview
                );
            }
        }
    }

    public function notifyProjectAssignment($userId, $actorId, $projectId, $role) {
        // Don't notify the user if they are assigning themselves
        if ($userId == $actorId) return; 

        $roleText = !empty($role) ? ucwords($role) : 'Team Member';
        $message = "You have been assigned as a $roleText.";
        
        // Notice we pass null for discussionId, and set type to 'assignment'
        $this->repo->createNotification(
            $userId, 
            $actorId, 
            $projectId, 
            null, 
            'assignment', 
            $message
        );
    }

    public function markAsRead($notificationId, $userId) {
        if (!$notificationId) return false;
        return $this->repo->markAsRead($notificationId, $userId);
    }

    public function clearNotification($notificationId, $userId) {
        if (!$notificationId) return false;
        return $this->repo->deleteNotification($notificationId, $userId);
    }

    public function clearAllNotifications($userId) {
        return $this->repo->deleteAllUserNotifications($userId);
    }
}