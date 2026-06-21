<?php
// /src/modules/users/api-get-profile.php
session_start();
require_once __DIR__ . '/../../core/database.php';

header('Content-Type: application/json');

$userId = $_GET['id'] ?? null;
if (!$userId) { echo json_encode(['success' => false]); exit; }

try {
    $db = Database::getConnection();
    // Fetch user info including department
    $sql = "SELECT u.user_id, u.first_name, u.last_name, u.avatar_url, u.email, u.phone, u.role, u.last_seen, d.department_name
            FROM users u
            LEFT JOIN departments d ON u.department_id = d.department_id
            WHERE u.user_id = :uid";
    $stmt = $db->prepare($sql);
    $stmt->execute([':uid' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Calculate 5-minute online status
        $isOnline = false;
        if (!empty($user['last_seen'])) {
            try {
                $lastSeen = new DateTime($user['last_seen']);
                if ((time() - $lastSeen->getTimestamp()) <= 300) {
                    $isOnline = true;
                }
            } catch (Exception $e) {}
        }
        $user['status_class'] = $isOnline ? 'active' : 'offline';
        $user['status_text'] = $isOnline ? 'Online' : 'Offline';

        echo json_encode(['success' => true, 'data' => $user]);
    } else {
        echo json_encode(['success' => false]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false]);
}