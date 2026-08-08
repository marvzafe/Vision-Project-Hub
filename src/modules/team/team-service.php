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

    public function createMembers($projectId, $leadId, $userIds, $userRoles, $departmentIds = [], $departmentRoles = [], $actorId = null) {
        if (!$projectId) throw new Exception("Project ID is missing.");

        $notificationService = new NotificationService(); 
        $newTeamIds = [];
        
        // 1. Insert Users
        for ($i = 0; $i < count($userIds); $i++) {
            $uId = $userIds[$i];
            $role = !empty($userRoles[$i]) ? $userRoles[$i] : 'Team Member'; 
            
            if (!empty($uId)) {
                $newTeamIds[] = $this->repository->addMember($projectId, $leadId, $uId, null, $role);
                if ($actorId) $notificationService->notifyProjectAssignment($uId, $actorId, $projectId, $role);
            }
        }

        // 2. Insert Departments
        for ($i = 0; $i < count($departmentIds); $i++) {
            $dId = $departmentIds[$i];
            $role = !empty($departmentRoles[$i]) ? $departmentRoles[$i] : 'Department'; 
            
            if (!empty($dId)) {
                $newTeamIds[] = $this->repository->addMember($projectId, $leadId, null, $dId, $role);
            }
        }
        
        return $newTeamIds;
    }
}