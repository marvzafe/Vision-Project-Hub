<?php
// /src/modules/team/team-service.php
require_once __DIR__ . '/team-repository.php';
require_once __DIR__ . '/../notifications/notification-service.php';

class TeamService {
    
    private TeamRepository $repository;

    public function __construct(TeamRepository $repository) {
        $this->repository = $repository;
    }

    public function deleteMember($teamId) {
        if (!$teamId) {
            throw new Exception("Team ID required for deletion.");
        }
        return $this->repository->deleteMember($teamId);
    }

    public function createMembers($projectId, $leadId, $userIds, $roles, $actorId = null) {
        if (!$projectId) {
            throw new Exception("Project ID is missing. Cannot assign team to a non-existent project.");
        }

        $notificationService = new NotificationService(); // Instantiate the service
        $newTeamIds = [];
        
        for ($i = 0; $i < count($userIds); $i++) {
            $uId = $userIds[$i];
            $role = !empty($roles[$i]) ? $roles[$i] : 'Team Member'; 
            
            if (!empty($uId)) {
                $newId = $this->repository->addMember($projectId, $leadId, $uId, $role);
                $newTeamIds[] = $newId;
                
                // Trigger the alert!
                if ($actorId) {
                    $notificationService->notifyProjectAssignment($uId, $actorId, $projectId, $role);
                }
            }
        }
        
        return $newTeamIds;
    }
}