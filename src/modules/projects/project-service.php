<?php
// /src/modules/projects/project-service.php
require_once __DIR__ . '/project-repository.php';
require_once __DIR__ . '/../notifications/notification-service.php';
require_once __DIR__ . '/../attachments/attachment-service.php';

    class ProjectService {
        private ProjectRepository $repository;

        public function __construct(?ProjectRepository $repository = null) {
            $this->repository = $repository ?? new ProjectRepository();
        }

    // 1. Format all projects for the list view
    public function getFormattedProjectsList($sortBy = 'created_at') {
        $rawProjects = $this->repository->getAllProjects($sortBy);

        $allTeams = $this->repository->getAllProjectTeams();
        
        $teamLookup = [];
        foreach ($allTeams as $member) {
            $teamLookup[$member['project_id']][] = $member;
        }
        
        // Group them by project_id for instant lookup
        $teamLookup = [];
        foreach ($allTeams as $member) {
            $teamLookup[$member['project_id']][] = $member;
        }

        $formattedProjects = [];
        $now = new DateTime(); // Used for online status

        foreach ($rawProjects as $p) {
            $badgeClass = 'progress'; 
            if ($p['status'] === 'completed') $badgeClass = 'completed'; 
            if ($p['status'] === 'past due') $badgeClass = 'attention'; 
            if ($p['status'] === 'archived' || empty($p['status'])) $badgeClass = 'archived';

            // Calculate live online status
            $isOnline = false;
            if (!empty($p['lead_last_seen'])) {
                try {
                    $lastSeen = new DateTime($p['lead_last_seen']);
                    if (($now->getTimestamp() - $lastSeen->getTimestamp()) <= 300) {
                        $isOnline = true;
                    }
                } catch (Exception $e) {}
            }

            $team = $teamLookup[$p['id']] ?? [];

            $formattedProjects[] = [
                'id'           => $p['id'],
                'name'         => $p['name'],
                'location'     => $p['project_location'],
                
                // Pass the whole team array to the view
                'team_members' => $team, 
                
                'progress'     => $p['progress_percentage'],
                'badge_class'  => $badgeClass,
                'badge_text'   => $p['status'] ? $p['status'] : 'archived',
                'last_updated' => date('M d, Y', strtotime($p['created_at'])),
                'cover_photo'  => !empty($p['cover_photo_url']) ? $p['cover_photo_url'] : 'https://img.freepik.com/premium-photo/scenic-cartoon-view-mountains-fields-generative-ai_225446-6262.jpg'
            ];
        }
        return $formattedProjects;
    }

    // 2. Handle the heavy lifting of creation and file uploads
    public function createNewProject($data, $fileData, $currentUserId) {
        if (empty($data['name']) || empty($data['project_location']) || empty($data['project_area'])) {
            throw new Exception("Please fill in the Project Name, Location, and Area.");
        }

        $tasks = [
            'titles'     => $data['task_titles'] ?? [],
            'categories' => $data['task_categories'] ?? [],
            'assignees'  => $data['task_assignees'] ?? [],
            'deadlines'  => $data['task_deadlines'] ?? [],
            'weights'    => $data['task_weights'] ?? [] // Catch the hidden inputs
        ];

        $team = [
            'user_ids' => $data['team_user_ids'] ?? [],
            'roles'    => $data['team_roles'] ?? [],
            'department_ids'   => $data['team_department_ids'] ?? [],
            'department_roles' => $data['team_department_roles'] ?? []
        ];

        $projectId = $this->repository->createProjectTransaction(
            $data['name'], 
            $data['project_location'], 
            $data['project_area'], 
            $data['project_lead_id'] ?? null, 
            $tasks, 
            $team
        );

        // Cover photo upload handling
        $wasCompressed = false;
        
        if (isset($fileData) && !empty($fileData['name'])) {
            if ($fileData['error'] !== UPLOAD_ERR_OK) {
                $errorMessages = [
                    UPLOAD_ERR_INI_SIZE   => 'The cover photo exceeds the server upload limit.',
                    UPLOAD_ERR_FORM_SIZE  => 'The cover photo exceeds the form size limit.',
                    UPLOAD_ERR_PARTIAL    => 'The cover photo was only partially uploaded.',
                    UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder on the server.',
                    UPLOAD_ERR_CANT_WRITE => 'Failed to write cover photo to disk.',
                ];
                $msg = $errorMessages[$fileData['error']] ?? 'Unknown upload error.';
                throw new Exception("Cover Photo Error: " . $msg);
            }
            
            $wasCompressed = $this->handleCoverPhotoUpload($projectId, $fileData, $currentUserId);
        }

        // --- NEW: Trigger Assignment Notifications ---
        $notificationService = new NotificationService();
        
        // 1. Notify the Project Lead
        if (!empty($data['project_lead_id'])) {
            $notificationService->notifyProjectAssignment($data['project_lead_id'], $currentUserId, $projectId, 'Project Lead');
        }
        
        // 2. Notify the Team Members
        if (!empty($team['user_ids'])) {
            for ($i = 0; $i < count($team['user_ids']); $i++) {
                $uId = $team['user_ids'][$i];
                $role = !empty($team['roles'][$i]) ? $team['roles'][$i] : 'Team Member';
                
                if (!empty($uId)) {
                    $notificationService->notifyProjectAssignment($uId, $currentUserId, $projectId, $role);
                }
            }
        }
        // ---------------------------------------------
        
        // RETURN AT THE VERY END
        return [
            'id' => $projectId, 
            'compressed' => $wasCompressed
        ];
    }

    private function handleCoverPhotoUpload($projectId, $file, $currentUserId) {
        $formattedProjectId = 'PRJ-' . str_pad($projectId, 3, '0', STR_PAD_LEFT);
        $projectFolder = __DIR__ . '/../../../uploads/' . $formattedProjectId;
        
        if (!is_dir($projectFolder)) {
            mkdir($projectFolder, 0777, true);
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newFileName = 'cover.' . $ext;
        $targetPath = $projectFolder . '/' . $newFileName;
        $fileUrl = '/uploads/' . $formattedProjectId . '/' . $newFileName;
        
        $fileTmp  = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileType = mime_content_type($fileTmp);
        
        $maxSizeBytes = 5 * 1024 * 1024; // 5MB limit
        $wasCompressed = false;

        // Compression Logic
        if ($fileSize > $maxSizeBytes && strpos($fileType, 'image') !== false) {
            $image = null;
            if ($fileType == 'image/jpeg' || $fileType == 'image/jpg') {
                $image = imagecreatefromjpeg($fileTmp);
            } elseif ($fileType == 'image/png') {
                $image = imagecreatefrompng($fileTmp);
            }
            
            if ($image) {
                imagejpeg($image, $targetPath, 60); // Compress to 60% quality
                imagedestroy($image);
                $fileSize = filesize($targetPath); // Update to new smaller size
                $wasCompressed = true;
            } else {
                move_uploaded_file($fileTmp, $targetPath);
            }
        } else {
            // Move file normally if under 5MB
            move_uploaded_file($fileTmp, $targetPath);
        }

        // Save to database
        $this->repository->saveCoverPhotoRecord($projectId, $newFileName, $fileUrl, $fileSize, $currentUserId);
        
        return $wasCompressed;
    }

    // 3. Format specific project data for the detailed view
    public function getProjectViewDetails($projectId) {
        $project = $this->repository->getProjectById($projectId);
        if (!$project) return null;

        $teamMembers = [];
        $now = new DateTime();

        // Helper function to calculate online status based on 5-minute threshold
        $getOnlineStatus = function($lastSeen) use ($now) {
            $isOnline = false;
            if (!empty($lastSeen)) {
                try {
                    $lastSeenDate = new DateTime($lastSeen);
                    if (($now->getTimestamp() - $lastSeenDate->getTimestamp()) <= 300) {
                        $isOnline = true;
                    }
                } catch (Exception $e) {}
            }
            return [
                'class' => $isOnline ? 'active' : 'offline',
                'text'  => $isOnline ? 'Online' : 'Offline'
            ];
        };

        // 1. Add the Project Lead (if one exists)
        if (!empty($project['project_lead_id'])) {
            $status = $getOnlineStatus($project['lead_last_seen'] ?? null);
            $teamMembers[] = [
                'user_id'         => $project['project_lead_id'],
                'first_name'      => $project['lead_first_name'],
                'last_name'       => $project['lead_last_name'],
                'project_role'    => 'Project Lead',
                'is_lead'         => true,
                'avatar_url'      => $project['lead_avatar_url'],
                'email'           => $project['lead_email'],
                'phone'           => $project['lead_phone'],
                'department_name' => $project['lead_department_name'],
                'status_class'    => $status['class'],
                'status_text'     => $status['text']
            ];
        }

        // 2. Fetch and add the rest of the team
        $teamData = $this->repository->getProjectTeam($projectId);
        foreach ($teamData as $member) {
            if (($member['user_id'] ?? null) === $project['project_lead_id']) {
                continue;
            }

            $status = $getOnlineStatus($member['last_seen'] ?? null);
            $teamMembers[] = [
                'user_id'         => $member['user_id'],
                'department_id'   => $member['department_id'], // <-- ADD THIS LINE
                'first_name'      => $member['first_name'],
                'last_name'       => $member['last_name'],
                'project_role'    => $member['project_role'],
                'is_lead'         => false,
                'avatar_url'      => $member['avatar_url'],
                'email'           => $member['email'],
                'phone'           => $member['phone'],
                'department_name' => $member['department_name'],
                'status_class'    => $status['class'],
                'status_text'     => $status['text']
            ];
        }
        $rawTasks = $this->repository->getProjectTasks($projectId);
        
        $groupedTasks = [
            'general_works' => [],
            'project_progress' => [],
            'finishing_works' => []
        ];
        
        foreach ($rawTasks as $task) {
            $category = $task['task_category'];
            if (array_key_exists($category, $groupedTasks)) {
                $groupedTasks[$category][] = $task;
            }
        }

        $statusBadgeClass = 'progress'; 
        $statusText = ucwords(str_replace('_', ' ', $project['status']));
        if ($project['status'] === 'completed') $statusBadgeClass = 'completed';
        elseif ($project['status'] === 'past due' || $project['status'] === 'archived') $statusBadgeClass = 'attention';

        // --- STRICT ARCHITECTURE: Service-to-Service Communication ---
        $attachmentRepo = new AttachmentRepository();
        $attachmentService = new AttachmentService($attachmentRepo);
        
        $groupedAttachments = $attachmentService->getGroupedAttachmentsForProject($projectId);
        // -------------------------------------------------------------

        return [
            'project' => $project,
            'teamMembers' => $teamMembers,
            'groupedTasks' => $groupedTasks,
            'groupedAttachments' => $groupedAttachments, 
            'statusBadgeClass' => $statusBadgeClass,
            'statusText' => $statusText
        ];
    }

    public function getGroupedTaskTemplates() {
        $rawTemplates = $this->repository->getTaskTemplates();
        $grouped = [];

        foreach ($rawTemplates as $row) {
            $slug = $row['slug'];
            
            if (!isset($grouped[$slug])) {
                $grouped[$slug] = [
                    'name'              => $row['template_name'],
                    'material_category' => $row['material_category'] ?? 'Uncategorized',
                    'tasks'             => []
                ];
            }

            if (!empty($row['title'])) {
                $grouped[$slug]['tasks'][] = [
                    'title'      => $row['title'],
                    'category'   => $row['task_category'],
                    'daysOffset' => (int)$row['days_offset'],
                    'weight'     => (float)$row['weight'] // Pass it to the JSON response
                ];
            }
        }

        return $grouped;
    }

    public function deleteProject($projectId) {
        if (empty($projectId)) {
            throw new Exception("Project ID is required for deletion.");
        }
        
        return $this->repository->deleteProject($projectId);
    }

    public function updateExistingProject($projectId, $data, $fileData, $currentUserId) {
        if (empty($data['name']) || empty($data['project_location']) || empty($data['project_area'])) {
            throw new Exception("Please fill in the Project Name, Location, and Area.");
        }

        $tasks = [
            'ids'        => $data['task_ids'] ?? [], // (Only needed in updateExistingProject)
            'titles'     => $data['task_titles'] ?? [],
            'categories' => $data['task_categories'] ?? [],
            'assignees'  => $data['task_assignees'] ?? [],
            'deadlines'  => $data['task_deadlines'] ?? [],
            'weights'    => $data['task_weights'] ?? [] // Catch the hidden inputs
        ];

        $team = [
            'user_ids' => $data['team_user_ids'] ?? [],
            'roles'    => $data['team_roles'] ?? [],
            'department_ids'   => $data['team_department_ids'] ?? [],
            'department_roles' => $data['team_department_roles'] ?? []
        ];

        $this->repository->updateProjectTransaction(
            $projectId,
            $data['name'], 
            $data['project_location'], 
            $data['project_area'], 
            $data['project_lead_id'] ?? null, 
            $tasks, 
            $team
        );

        // Cover photo upload handling
        $wasCompressed = false;

        if (isset($fileData) && !empty($fileData['name'])) {
            if ($fileData['error'] !== UPLOAD_ERR_OK) {
                $errorMessages = [
                    UPLOAD_ERR_INI_SIZE   => 'The cover photo exceeds the server upload limit.',
                    UPLOAD_ERR_FORM_SIZE  => 'The cover photo exceeds the form size limit.',
                    UPLOAD_ERR_PARTIAL    => 'The cover photo was only partially uploaded.',
                    UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder on the server.',
                    UPLOAD_ERR_CANT_WRITE => 'Failed to write cover photo to disk.',
                ];
                $msg = $errorMessages[$fileData['error']] ?? 'Unknown upload error.';
                throw new Exception("Cover Photo Error: " . $msg);
            }
            
            $wasCompressed = $this->handleCoverPhotoUpload($projectId, $fileData, $currentUserId);
        }
        
        return ['compressed' => $wasCompressed];
    }

    public function updateProjectStatus($projectId, $status) {
        if (empty($projectId) || empty($status)) {
            throw new Exception("Project ID and Status are required.");
        }
        
        $validStatuses = ['archived', 'processing', 'completed', 'past due'];
        if (!in_array(strtolower($status), $validStatuses)) {
            throw new Exception("Invalid status provided.");
        }

        return $this->repository->updateProjectStatus($projectId, strtolower($status));
    }
}