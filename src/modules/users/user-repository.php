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

    public function updateUserAssignment(string $userId, int $deptId, string $role): bool {
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
    public function getUserById(string $userId) {
        $sql = "SELECT * FROM users WHERE user_id = :userId";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':userId' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getActiveUsers(int $limit = 20): array {
        try {
            $sql = "SELECT user_id as id, first_name, last_name, avatar_url, role, last_seen 
                    FROM users 
                    ORDER BY last_seen DESC NULLS LAST 
                    LIMIT :limit";
            $stmt = $this->db->prepare($sql);
            // PDO requires binding integers explicitly if emulation is off
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return []; 
        }
    }

    // Change 'int $userId' to 'string $userId'
    public function updateLastSeen(string $userId): bool {
        try {
            $sql = "UPDATE users SET last_seen = NOW() WHERE user_id = :userId";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':userId' => $userId]);
        } catch (PDOException $e) {
            error_log("Failed to update last_seen: " . $e->getMessage());
            return false;
        }
    }
}