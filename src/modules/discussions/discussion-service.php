<?php
// /src/modules/discussions/discussion-service.php
require_once __DIR__ . '/discussion-repository.php';
require_once __DIR__ . '/../notifications/notification-service.php';
require_once __DIR__ . '/../projects/views/details-partials/helpers.php';

class DiscussionService {
    private $repo;
    private NotificationService $notificationService;

    public function __construct() {
        $this->repo = new DiscussionRepository();
        $this->notificationService = new NotificationService();
    }

    // Structures flat comments into a Parent -> Replies array
    public function getProjectDiscussions($projectId) {
        $flatComments = $this->repo->getByProjectId($projectId);
        $tree = [];
        $replies = [];

        // 1. Separate parents from replies and apply helpers
        foreach ($flatComments as $row) {
            $row['replies'] = []; 
            
            // Apply your PHP helpers here
            $row['formatted_time'] = getRelativeTime($row['created_at']);
            $row['formatted_content'] = formatDiscussionText($row['content']);
            
            if (!empty($row['parent_id'])) {
                $replies[$row['parent_id']][] = $row;
            } else {
                $tree[$row['id']] = $row;
            }
        }

        // 2. Map replies back to their parent thread
        foreach ($tree as $id => &$parent) {
            if (isset($replies[$id])) {
                $parent['replies'] = $replies[$id];
            }
        }

        return array_values($tree);
    }

    public function addComment($projectId, $userId, $content, $parentId = null, $taskId = null) {
        if (empty(trim($content)) && empty($taskId)) {
            throw new Exception("Comment must contain text or an attached task.");
        }

        $discussionId = $this->repo->create($projectId, $userId, trim($content), $parentId, $taskId);
        $this->notificationService->processMentions($content, $projectId, $userId, $discussionId);
        
        return $discussionId;
    }

    public function updateFlag($discussionId, $status) {
        $validStatuses = ['solved', 'attention'];
        if (!in_array($status, $validStatuses, true)) {
            $status = null; 
        }
        return $this->repo->updateFlag($discussionId, $status);
    }
    
    public function editComment($discussionId, $userId, $content) {
        if (empty(trim($content))) {
            throw new Exception("Comment content cannot be empty.");
        }
        $success = $this->repo->updateContent($discussionId, $userId, trim($content));
        if (!$success) {
            throw new Exception("Failed to edit. The comment may not exist or you don't have permission.");
        }
        return true;
    }

    public function deleteComment($discussionId, $userId) {
        $success = $this->repo->delete($discussionId, $userId);
        if (!$success) {
            throw new Exception("Failed to delete. The comment may not exist or you don't have permission.");
        }
        return true;
    }
}