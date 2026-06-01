<?php
// /src/modules/contact_verification/contact-repository.php
require_once __DIR__ . '/../../../src/core/database.php';

class ContactVerificationRepository {
    private PDO $db;

    public function __construct() {
        // Utilize the existing database connection core[cite: 8]
        $this->db = Database::getConnection(); 
    }

    public function getUserPhone(string $userId): ?string {
        $stmt = $this->db->prepare("SELECT phone FROM users WHERE user_id = :id");
        $stmt->execute([':id' => $userId]);
        $result = $stmt->fetch();
        
        return $result['phone'] ?? null;
    }

    public function updatePhoneNumber(string $userId, string $phoneNumber): bool {
        $stmt = $this->db->prepare("UPDATE users SET phone = :phone WHERE user_id = :id");
        return $stmt->execute([
            ':phone' => $phoneNumber,
            ':id' => $userId
        ]);
    }
}