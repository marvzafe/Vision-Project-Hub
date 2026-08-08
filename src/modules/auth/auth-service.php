<?php
// /src/modules/auth/auth-service.php
require_once __DIR__ . '/auth-repository.php';
// NEW: Require the core AvatarService
require_once __DIR__ . '/../../core/avatar-service.php'; 

class AuthService {
    private AuthRepository $repository; //[cite: 8]

    public function __construct(AuthRepository $repository) { //[cite: 8]
        $this->repository = $repository; //[cite: 8]
    }

    /**
     * Maps the Supabase payload and database record into the PHP Session
     */
    public function setupUserSession(array $data): bool { //[cite: 8]
        if (!isset($data['user_id'])) { //[cite: 8]
            return false; //[cite: 8]
        }

        // 1. Delegate downloading to the AvatarService
        $remoteAvatar = $data['avatar_url'] ?? null;
        $localAvatarUrl = AvatarService::downloadAndCacheAvatar($data['user_id'], $remoteAvatar);

        // 2. Set basic session data from Supabase[cite: 8]
        $_SESSION['user_id'] = $data['user_id']; //[cite: 8]
        $_SESSION['user_email'] = $data['email'] ?? ''; //[cite: 8]
        $_SESSION['avatar_url'] = $localAvatarUrl; // Use the local/placeholder URL

        // 3. Fetch the updated record from the database via the Repository[cite: 8]
        $user = $this->repository->getUserBySupabaseId($data['user_id']); //[cite: 8]

        if ($user) { //[cite: 8]
            $_SESSION['first_name'] = $user['first_name']; //[cite: 8]
            $_SESSION['last_name']  = $user['last_name']; //[cite: 8]
            $_SESSION['full_name']  = $user['first_name'] . ' ' . $user['last_name']; //[cite: 8]
            $_SESSION['user_role']  = $user['role'] ?? 'Member'; //[cite: 8]
            
            // 4. Save the new local URL to the database
            $this->repository->updateAvatarUrl($data['user_id'], $localAvatarUrl);
        } else { //[cite: 8]
            // Fallback if the database trigger hasn't created the row yet[cite: 8]
            $_SESSION['full_name'] = $data['name'] ?? 'User'; //[cite: 8]
            $_SESSION['user_role'] = 'Member'; //[cite: 8]
        }

        return true; //[cite: 8]
    }

    /**
     * Safely destroys all session data and cookies
     */
    public function logoutUser(): void { //[cite: 8]
        // Clear the session array[cite: 8]
        $_SESSION = array(); //[cite: 8]

        // Destroy the session cookie for security[cite: 8]
        if (ini_get("session.use_cookies")) { //[cite: 8]
            $params = session_get_cookie_params(); //[cite: 8]
            setcookie(session_name(), '', time() - 42000, //[cite: 8]
                $params["path"], $params["domain"], //[cite: 8]
                $params["secure"], $params["httponly"] //[cite: 8]
            ); //[cite: 8]
        } //[cite: 8]

        // Final destruction[cite: 8]
        session_destroy(); //[cite: 8]
    }
}