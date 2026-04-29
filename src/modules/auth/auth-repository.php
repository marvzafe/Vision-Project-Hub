<?php
// /src/modules/auth/auth-repository.php
require_once __DIR__ . '/../../core/database.php';

class AuthRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // Fetch user details from the public.users table using their Supabase UUID
    public function getUserBySupabaseId(string $userId) {
        $stmt = $this->db->prepare("SELECT first_name, last_name, role FROM users WHERE user_id = :id");
        $stmt->execute([':id' => $userId]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}