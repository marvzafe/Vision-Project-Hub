<?php
// /src/modules/departments/department-repository.php
require_once __DIR__ . '/../../core/database.php';

class DepartmentRepository {
    
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Fetch all departments for global use.
     */
    public function getAllDepartments(): array {
        $sql = "SELECT department_id, department_name 
                FROM departments 
                ORDER BY department_name ASC";
                
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Fetch a single department by its ID.
     */
    public function getDepartmentById(string $departmentId): ?array {
        $sql = "SELECT department_id, department_name 
                FROM departments 
                WHERE department_id = :id";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $departmentId]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
}