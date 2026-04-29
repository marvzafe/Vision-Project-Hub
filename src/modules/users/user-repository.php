<?php
// /src/modules/users/user-repository.php
require_once __DIR__ . '/../../core/database.php';

class UserRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getDepartments(): array {
        $stmt = $this->db->query("SELECT department_id, department_name FROM departments ORDER BY department_name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createUser(string $first, string $middle, string $last, string $email, string $phone, int $deptId): bool {
        $sql = "INSERT INTO users 
                (first_name, middle_name, last_name, email, phone, department_id) 
                VALUES 
                (:first, :middle, :last, :email, :phone, :dept)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':first'  => $first,
            ':middle' => $middle,
            ':last'   => $last,
            ':email'  => $email,
            ':phone'  => $phone,
            ':dept'   => $deptId
        ]);
    }

    public function getAllUsers(): array {
        $stmt = $this->db->query("SELECT user_id, first_name, last_name FROM users ORDER BY last_name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllUserDetails(): array {
        $sql = "SELECT u.user_id, u.first_name, u.middle_name, u.last_name, u.email, u.phone, u.role, u.avatar_url, d.department_name
                FROM users u
                LEFT JOIN departments d ON u.department_id = d.department_id
                ORDER BY u.last_name ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateUserAssignment(int $userId, int $deptId, string $role): bool {
        $sql = "UPDATE users 
                SET department_id = :deptId, role = :role 
                WHERE user_id = :userId";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':deptId' => $deptId, 
            ':role'   => $role, 
            ':userId' => $userId
        ]);
    }

    // NEW: Fetch specific user details for the Edit form
    public function getUserById(int $userId) {
        $sql = "SELECT * FROM users WHERE user_id = :userId";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':userId' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}