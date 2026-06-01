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
}