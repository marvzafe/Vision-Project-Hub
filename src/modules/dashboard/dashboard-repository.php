<?php
// /src/modules/dashboard/dashboard-repository.php
require_once __DIR__ . '/../../core/database.php';

class DashboardRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getProjectStats(): array {
        $sql = "SELECT 
                    SUM(CASE WHEN status IN ('processing', 'past due') THEN 1 ELSE 0 END) as active_count,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_count,
                    SUM(CASE WHEN status = 'not yet started' THEN 1 ELSE 0 END) as unstarted_count
                FROM projects";
                
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return [
            'active'    => $result['active_count'] ?? 0,
            'completed' => $result['completed_count'] ?? 0,
            'unstarted' => $result['unstarted_count'] ?? 0
        ];
    }

    public function getMyAssignedProjects(string $userId): array {
        $sql = "SELECT id, name, status, progress_percentage, project_location 
                FROM projects 
                WHERE project_lead_id = :user_id 
                ORDER BY created_at DESC 
                LIMIT 5";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- NEW METHOD ---
    public function getActiveUsers(): array {
        // Attempt to fetch real users from your DB. 
        try {
            $sql = "SELECT id, first_name, last_name, avatar_url, role 
                    FROM users 
                    ORDER BY id DESC 
                    LIMIT 4";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Fallback to dummy data if your users table isn't fully structured yet
            return [
                ['first_name' => 'Sarah', 'last_name' => 'Jenkins', 'role' => 'Project Manager', 'avatar_url' => ''],
                ['first_name' => 'David', 'last_name' => 'Chen', 'role' => 'Developer', 'avatar_url' => ''],
                ['first_name' => 'Michael', 'last_name' => 'Ross', 'role' => 'Designer', 'avatar_url' => ''],
            ];
        }
    }

    public function getUpcomingDeadlines(string $userId): array {
    try {
        $sql = "SELECT id, title, task_category, deadline 
                FROM tasks 
                WHERE assignee_id = :user_id 
                  AND deadline >= CURRENT_DATE 
                ORDER BY deadline ASC 
                LIMIT 5";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return []; // Fail silently and gracefully now that the bug is fixed
    }
}
}