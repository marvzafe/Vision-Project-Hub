<?php
// /src/modules/team/team-repository.php
require_once __DIR__ . '/../../core/database.php';

class TeamRepository {
    
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // 1. Create a Team Member (Returns the new ID)
    public function addMember($projectId, $projectLeadId, $userId, $projectRole = 'Member') {
        $sql = "INSERT INTO project_team 
                (project_id, project_lead_id, user_id, project_role) 
                VALUES 
                (:project_id, :project_lead_id, :user_id, :project_role) 
                RETURNING id";
                
        $stmt = $this->db->prepare($sql);
        
        $stmt->execute([
            ':project_id'      => $projectId,
            ':project_lead_id' => $projectLeadId ?: null,
            ':user_id'         => $userId ?: null,
            ':project_role'    => $projectRole
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['id']; 
    }

    // 2. Delete a Team Member
    public function deleteMember($teamId) {
        $sql = "DELETE FROM project_team WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $teamId]);
    }
}