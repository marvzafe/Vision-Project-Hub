<?php
// /src/modules/users/user-service.php
require_once __DIR__ . '/user-repository.php';

class UserService {
    private UserRepository $repository;

    public function __construct(UserRepository $repository = null) {
        $this->repository = $repository ?? new UserRepository();
    }

    public function getDepartments() {
        return $this->repository->getDepartments();
    }

    public function createUser($first, $middle, $last, $email, $phone, $deptId) {
        return $this->repository->createUser($first, $middle, $last, $email, $phone, $deptId);
    }

    public function getAllUsers() {
        return $this->repository->getAllUsers();
    }

    public function getAllUserDetails() {
        return $this->repository->getAllUserDetails();
    }

    public function updateUserAssignment($userId, $deptId, $role) {
        return $this->repository->updateUserAssignment($userId, $deptId, $role);
    }

    public function getUserById($userId) {
        if (!$userId) return null;
        return $this->repository->getUserById($userId);
    }

    public function getActiveUsers(int $limit = 20): array {
        $users = $this->repository->getActiveUsers($limit);
        $now = new DateTime();
        
        foreach ($users as &$user) {
            $isOnline = false;
            
            if (!empty($user['last_seen'])) {
                try {
                    $lastSeen = new DateTime($user['last_seen']);
                    $diffInSeconds = $now->getTimestamp() - $lastSeen->getTimestamp();
                    
                    if ($diffInSeconds <= 300) {
                        $isOnline = true;
                    }
                } catch (Exception $e) {
                    // Fail silently, defaults to offline
                }
            }
            
            $user['is_online'] = $isOnline;
            $user['status_class'] = $isOnline ? 'active' : 'offline';
            $user['status_text'] = $isOnline ? 'Online' : 'Offline';
        }
        
        usort($users, function($a, $b) {
            if ($a['is_online'] === $b['is_online']) {
                return strcmp($a['first_name'] ?? '', $b['first_name'] ?? '');
            }
            return $a['is_online'] ? -1 : 1;
        });

        return $users;
    }

    // Change 'int $userId' to 'string $userId'
    public function pingLastSeen(string $userId): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $now = time();
        $lastPing = $_SESSION['last_seen_ping'] ?? 0;

        if ($now - $lastPing >= 60) {
            $this->repository->updateLastSeen($userId);
            $_SESSION['last_seen_ping'] = $now;
        }
    }
}