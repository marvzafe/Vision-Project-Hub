<?php
// /src/modules/team/team-service.php
require_once __DIR__ . '/team-repository.php';

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

    public function createMembers($projectId, $leadId, $userIds, $roles) {
        if (!$projectId) {
            throw new Exception("Project ID is missing. Cannot assign team to a non-existent project.");
        }

        $newTeamIds = [];
        
        // Loop through all the rows the user added in the modal
        for ($i = 0; $i < count($userIds); $i++) {
            $uId = $userIds[$i];
            $role = !empty($roles[$i]) ? $roles[$i] : 'Team Member'; // Fallback role
            
            // Only save if they actually selected a user from the dropdown
            if (!empty($uId)) {
                $newId = $this->repository->addMember($projectId, $leadId, $uId, $role);
                $newTeamIds[] = $newId;
            }
        }
        
        return $newTeamIds;
    }
}