<?php
// /src/modules/auth/auth-service.php
require_once __DIR__ . '/auth-repository.php';

class AuthService {
    private AuthRepository $repository;

    public function __construct(AuthRepository $repository) {
        $this->repository = $repository;
    }

    /**
     * Maps the Supabase payload and database record into the PHP Session
     */
    public function setupUserSession(array $data): bool {
        if (!isset($data['user_id'])) {
            return false;
        }

        // 1. Set basic session data from Supabase
        $_SESSION['user_id'] = $data['user_id'];
        $_SESSION['user_email'] = $data['email'] ?? '';
        $_SESSION['avatar_url'] = $data['avatar_url'] ?? null; 

        // 2. Fetch the updated record from the database via the Repository
        $user = $this->repository->getUserBySupabaseId($data['user_id']);

        if ($user) {
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name']  = $user['last_name'];
            $_SESSION['full_name']  = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['user_role']  = $user['role'] ?? 'Member';
        } else {
            // Fallback if the database trigger hasn't created the row yet
            $_SESSION['full_name'] = $data['name'] ?? 'User';
            $_SESSION['user_role'] = 'Member';
        }

        return true;
    }

    /**
     * Safely destroys all session data and cookies
     */
    public function logoutUser(): void {
        // Clear the session array
        $_SESSION = array();

        // Destroy the session cookie for security
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Final destruction
        session_destroy();
    }
}